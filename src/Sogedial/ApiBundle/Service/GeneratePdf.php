<?php

namespace Sogedial\ApiBundle\Service;

use Dompdf\Dompdf;
use Dompdf\Options;
use Sogedial\ApiBundle\Entity\Order;
use Sogedial\ApiBundle\Entity\OrderItem;
use Symfony\Component\Templating\EngineInterface;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

class GeneratePdf
{

    private $kernelRootDir;

    /**
     * @var EngineInterface
     */
    private $templating;

    private $tempDir ;

    private $webRoot ;

    public function __construct($kernelRootDir, EngineInterface $templating)
    {
        $this->kernelRootDir = $kernelRootDir;
        $this->templating = $templating;
        $this->tempDir = sys_get_temp_dir();
        $this->webRoot = realpath($kernelRootDir . '/Resources/');
    }

    protected function fullfilWithZeroThirteen($value)
    {
        $padLength = 13;

        if(strlen($value) < $padLength){
            $value = str_pad($value,$padLength,'0', STR_PAD_LEFT);
        }

        return $value;
    }

    protected function generateBarCode($orderItems)
    {
        $pathToFont = sprintf('%s/%s', $this->kernelRootDir, '../web/images/FreeSansBold.ttf');

        /** @var OrderItem $orderItem */
        foreach ($orderItems as $orderItem) {
            $codeEan13 = $this->fullfilWithZeroThirteen($orderItem->getItemEan13());
            $barcode = new Barcode($codeEan13, 4, realpath($pathToFont));
            $output = sprintf('%s%s%s.%s', $this->tempDir, '/', $codeEan13, 'png');
            imagepng($barcode->image(), $output);
        }

    }

    public function orderToPdf(Order $order, $toMail = false)
    {
        $options = new Options();
        //Pour simplifier l'affichage des images, on autorise dompdf à utiliser des  url pour les nom de  fichier
        $options->set('isRemoteEnabled', TRUE);
        // On crée une instance de dompdf avec  les options définies
        $dompdf = new Dompdf($options);
        $dompdf->setPaper('A4', 'portrait');
        $orderItems = $order->getOrderItems();
        $tva = $this->calculateTva($orderItems);
        $this->generateBarCode($orderItems);
        $orderItemByCategory = $this->orderByCategory($orderItems);
        $commercial = $order->getUser()->getClient()->getCommercialsByPlatform($order->getPlatform())->first();

        //génération du code html correspondant à notre template
        $html = $this->templating->render(
            '@SogedialApi/ModelPdf/orderToPDF.html.twig',
            array('order' => $order,
                'commercial' => $commercial,
                'tva' => $tva,
                'orderItemByCategory' => $orderItemByCategory,
                'tempDir' => $this->tempDir
            )
        );
        // On envoie le code html  à notre instance de dompdf

        $dompdf->loadHtml($html);

        // On demande à dompdf de générer le  pdf
        $dompdf->render();
        $font = $dompdf->getFontMetrics()->getFont("arial");
        $footer = $order->getPlatform()->getLegals();

        $page = "page {PAGE_NUM} sur {PAGE_COUNT}";
        $canvas = $dompdf->getCanvas();
        if (strlen($footer) > 220) {
            $canvas->page_text(100, 820, substr($footer, 0, 203), $font, 4, array(.5, .5, .5));
            $canvas->page_text(250, 830, substr($footer, 203, strlen($footer)), $font, 4, array(.5, .5, .5));
        } else {
            $canvas->page_text(100, 820, $footer, $font, 4, array(.5, .5, .5));
        }
        $canvas->page_text(520, 818, $page, $font, 6, array(0, 0, 0));

        //on supprime les png du barcode
        $this->cleanBarcode($order);
        $filename = sprintf('BDC-%s.pdf', $order->getNumber());

        if($toMail){
            $output = $dompdf->output();
            $filePath =  $this->tempDir.'/' . $filename;
            file_put_contents($filePath, $output);

            return $filePath;
        }


        return $dompdf->stream($filename);
    }

    /**
     * @param $orderItems
     * @return array
     */
    private function orderByCategory($orderItems): array
    {
        $orderItemByCategory = array();
        /** @var OrderItem $orderItem */
        foreach ($orderItems as $orderItem) {

            if (!isset($orderItemByCategory[$orderItem->getItemCategoryName()])) {
                $orderItemByCategory[$orderItem->getItemCategoryName()] = array();
            }

            $orderItemByCategory[$orderItem->getItemCategoryName()][] = $orderItem;

        }
        return $orderItemByCategory;
    }

    /**
     * @param $orderItems
     * @return array
     */
    private function calculateTva($orderItems): array
    {
        $tva = array('totalTva_21' => 0, 'totalTva_85' => 0);
        foreach ($orderItems as $orderItem) {
            $vat = $orderItem->getFinalPriceVat() - $orderItem->getFinalPrice();
            if ($orderItem->getItemVat() == '2.10') {
                $tva['totalTva_21'] += $vat;
            } elseif ($orderItem->getItemVat() == '8.50') {
                $tva['totalTva_85'] += $vat;
            }

        }
        $tva['totalTva'] = $tva['totalTva_21'] + $tva['totalTva_85'];

        return $tva;
    }


    /**
     * delete barcode png
     */
    protected function cleanBarcode(Order $order)
    {
        $path = $this->tempDir;
        $orderItems = $order->getOrderItems();
        /** @var OrderItem $orderItem */
        foreach ($orderItems as $orderItem){
            $file = sprintf('%s/%s.%s',$path,$orderItem->getItemEan13(),'png');
            if(file_exists($file)){
                unlink($file);
            }
        }
    }

   public function orderToExcel(Order $order)
   {
        $fileName = $this->webRoot.'/xlsx/Template_BdC_Excel.xlsx';
        $spreadsheet = IOFactory::load($fileName);
        $sheet = $spreadsheet->getActiveSheet();

        $orderItems = $order->getOrderItems()->toArray();
        $dateValidation =  $order->getDateValidate();
        $commercial =  $order->getUser()->getClient()->getCommercialsByPlatform($order->getPlatform())->first();
        $commercialFirstName = $commercial->getFirstname();
        $commercialLastName = $commercial->getLastname();
        $commercialName = $commercialFirstName.' '.$commercialLastName;
        $commercialEmail = $commercial->getEmail();
        $commercialFix = $commercial->getTelNumber1();
        $commercialMobile = $commercial->getTelNumber2();
        $commercialFax = $commercial->getFax();
        $clientName = $order->getUser()->getClient()->getName();
        $clientAdress = $order->getUser()->getClient()->getAddress();
        $clientEmail = $order->getUser()->getEmail();
        $clientTel = $order->getUser()->getTelNumber1();
        $dateDelivery = $order->getDateDelivery();
        $orderNumber = $order->getNumber();
        $platform = $order->getPlatform()->getName();
        $sheet->setTitle('Feuil1');
        $sheet->setCellValue('A2', 'Bon de commande N°'.$orderNumber);
        //$sheet->setCellValue('D2', 'le '.$dateValidation, DataValidation::TYPE_DATE);
        $sheet->setCellValue('A10', 'Fournisseur: '.$platform);
        //$sheet->setCellValue('C10', 'Date de livraison indicative: '.$dateDelivery, DataValidation::TYPE_DATE);
        $sheet->setCellValue('F10','Bon de commande N°: ' .$orderNumber);
        $sheet->setCellValue('C4', $clientName);
        $sheet->setCellValue('C5', $clientEmail);
        $sheet->setCellValue('C6', $clientAdress);
        $sheet->setCellValue('C7', $clientTel);
        $sheet->setCellValue('H4', $platform);
        $sheet->setCellValue('H5', $commercialName);
        $sheet->setCellValue('H6', $commercialEmail);
        $sheet->setCellValue('H7', $commercialFix);
        $sheet->setCellValue('H8', $commercialMobile);
        $sheet->setCellValue('H9', $commercialFax);

        $i = 12; // Beginning row for active sheet
        foreach ($orderItems as $orderItem) {
      
                $sheet->setCellValueExplicit('A'.$i, $orderItem->getItemEan13(), DataType::TYPE_STRING);
                $sheet->setCellValueExplicit('D'.$i, $orderItem->getItemReference(), DataType::TYPE_STRING);
                $sheet->setCellValue('F'.$i, $orderItem->getItemName());
                $sheet->setCellValue('E'.$i, $orderItem->getItemCategoryName());
                $sheet->setCellValue('I'.$i, $orderItem->getItemPcb());
                $sheet->setCellValue('J'.$i, $orderItem->getQuantity());
                $i++;
        }
        $j = count($orderItems)+13;
        $sheet->setCellValue('A'.$j, 'Nombre de colis : ');
        $sheet->setCellValue('B'.$j, $order->getReferences()['packages']);
        $writer = new Xlsx($spreadsheet);

        // In this case, we want to write the file in the public directory
        $filename = sprintf('BDC-%s.xlsx', $order->getNumber());
        $excelFilepath = $this->tempDir. '/'.$filename;

        // Create the file
        $writer->save($excelFilepath);
        
        // Return a text response to the browser saying that the excel was succesfully created
        return $excelFilepath;
    }

}