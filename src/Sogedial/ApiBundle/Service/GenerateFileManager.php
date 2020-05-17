<?php

namespace Sogedial\ApiBundle\Service;

use Doctrine\ORM\EntityManager;
use Google\Cloud\Storage\StorageClient;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\Finder\Finder;

class GenerateFileManager {

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * Region List
     * @var array
     */
    protected $listRegion = array(
        array('1', 'Guadeloupe', ''),
        array('2', 'Martinique', ''),
        array('3', 'Guyane_Française', ''),
        array('4', 'Sogedial', ''),
    );

    /**
     * GenerateFileManager constructor.
     * @param $rootDir
     * @param EntityManager $entityManager
     * @param ContainerInterface $container
     */
    public function __construct($rootDir, EntityManager $entityManager, Container $container) {
        $this->webRoot = realpath($rootDir . '/../web');
        $this->em = $entityManager;
        $this->container = $container;
    }

    /**
     * Generate AS400 File orders
     * @param $approvedOrder
     * @throws \Exception
     */
    public function generateIbmFile($approvedOrder) {
        $detailsApprovedOrder = array();        
        foreach ($approvedOrder as $orderId => $singleOrder) {
            $userOrder = $singleOrder->getUser();
            $platform = $singleOrder->getPlatform();
            $orderItems = $singleOrder->getOrderItems();
            $statusOrder = $singleOrder->getStatus();

            $detailsApprovedOrder[$orderId]["user"] = $userOrder;
            $detailsApprovedOrder[$orderId]["platform"] = $platform->getExtCode();
            $detailsApprovedOrder[$orderId]["items"] = $orderItems;
            $detailsApprovedOrder[$orderId]["status"] = $statusOrder->getId();

            /**
             * Order element
             */
            $detailsApprovedOrder[$orderId]["number"] = $singleOrder->getNumber();
            $detailsApprovedOrder[$orderId]["totalPriceVat"] = $singleOrder->getTotalPriceVat();
            $detailsApprovedOrder[$orderId]["totalPrice"] = $singleOrder->getTotalPrice();
            $detailsApprovedOrder[$orderId]["weight"] = $singleOrder->getWeight();
            $detailsApprovedOrder[$orderId]["volume"] = $singleOrder->getVolume();
            $detailsApprovedOrder[$orderId]["comment"] = $singleOrder->getComment();
            if ($singleOrder->getIsPreorder()) {
                $detailsApprovedOrder[$orderId]["dateDelivery"] = ($singleOrder->getPreOrderDeliveryDate() == null) ? ' ' : $singleOrder->getPreOrderDeliveryDate()->format("dmY");
            } else {
                $detailsApprovedOrder[$orderId]["dateDelivery"] = ($singleOrder->getDateDelivery() == null) ? ' ' : $singleOrder->getDateDelivery()->format("dmY");
            }
            $detailsApprovedOrder[$orderId]["isPreorder"] = $singleOrder->getIsPreorder();
        }

        foreach ($detailsApprovedOrder as $id => $detailOrder) {
            /**
             * Important vars
             */
            $extCode = $detailOrder["platform"];
            $comment = $detailOrder["comment"];
            $user = $detailOrder["user"];

            $isPreorder = $detailOrder["isPreorder"];
            $dateDelivery = $detailOrder["dateDelivery"];
            $orderNumber = substr($detailOrder['number'], -8);

            $enterpriseNumber = substr($extCode, 1, 2);
            $enterpriseCode = substr($extCode, 0, 1);
            $numberOrder = substr($orderNumber, -3);
            $codeUser = $this->fullfilWithSpaces(substr($user->getClient()->getExtCode(), 1), 10);

            /**
             * Step 0 : Prepare directories and sub-folder.
             */
            $listRegion = $this->listRegion;
            foreach ($listRegion as $region) {
                if (!is_dir(sprintf('%s%s', 'command/region', $region[0]))) {
                    mkdir(sprintf('%s%s', 'command/region', $region[0]), 0777, true);
                }
            }

            if (strlen($extCode) != 3 && strlen($extCode) != 4)
                die("Code entreprise invalide. La commande n'a pas été envoyée. Veuillez contacter votre commercial.");

            if ($detailOrder["isPreorder"] === true) {
                $directory = sprintf('/%s/%s/%s', 'precommand', sprintf('%s%s', 'region', substr($extCode, 0, 1)), 'CD_WEB2.C');
                $directoryPreCommand[] = sprintf('/%s/%s', 'precommand', sprintf('%s%d', 'region', substr($extCode, 0, 1)));
            } else {
                $directory = sprintf('/%s/%s/%s', 'command', sprintf('%s%d', 'region', substr($extCode, 0, 1)), 'CD_WEB2.C');
                $directoryCommand[] = sprintf('/%s/%s', 'command', sprintf('%s%d', 'region', substr($extCode, 0, 1)));
            }

            $fileName = $directory . $extCode . substr($detailOrder['number'], -6);
            $filePath = $this->webRoot . $fileName;

            /**
             * Back up old generated files of order to AS400
             */
            if (!is_dir(dirname($filePath))) {
                mkdir(dirname($filePath) . '/', 0777, TRUE);
            } else {
                /**
                 * Delete old generated file to back up folder
                 */
                if (file_exists($filePath)) {
                    unlink($filePath);
                };
            }

            $ibmFile = fopen($filePath, "a+") or die("Unable to open file!");
            $commentaire = str_replace('-', ' ', $comment);

            /**
             * Step 1 : Prepare head of file (Sales)
             * Syntaxe:
             *         ‘A’+n° de société+NOT USED+n° Vendeur
             *          1car+2Car+3car+2Car
             * Exemple:
             *         A+20+040+17
             */
            $salesLine = 'A' . $enterpriseNumber . $numberOrder . 'A7';
            $salesHead = $this->fullfilWithSpaces($salesLine, 80);

            /**
             * Put sales information into file
             */
            fwrite($ibmFile, $this->getLastCharacter($salesHead));

            /**
             * Step 2 : Build customer line
             * Syntaxe:
             *      ‘B’+N° de société+ NOTUSED+Code Client +FILLER+n°Commande+FILLER+flag
             * Example:
             *      1 car+2car+3car+8car+ 2car+ 8car+5car+1car
             */
            $codeUserFilled = $codeUser;
            $orderNumberFilled = $this->fullfilWithSpaces($orderNumber, 13);

            $preOrderFlag = null;
            if ($isPreorder) {
                $preOrderFlag = '2';
            }
            $customerLine = 'B' . $enterpriseNumber . $numberOrder . $codeUserFilled . $orderNumberFilled . $preOrderFlag;

            $customerHead = $this->fullfilWithSpaces($customerLine, 80);

            /**
             * Put customer information into file
             */
            fwrite($ibmFile, $this->getLastCharacter($customerHead));

            /**
             * Step 3: Items order will be formatted and put it into same file
             */
            foreach ($detailOrder["items"] as $orderItems) {
                /**
                 * Step 3-1 : Prepare items line for AS400
                 * Syntaxe:
                 *      ’D’+N°société+NOTUSED+code Article+Quantité Colis+prix article+type article+flag négoce+quantité Uc
                 *      1car+2car+3car+13car+3car+9car+1car+1car+9car
                 * Exemple :
                 *      D+20+040+045501+001+000000455+I+O+ 000004000
                 */
                $item = $orderItems->getItem();
                $pcb = $item->getPcb();
                $codeItem = $this->fullfilWithSpaces($item->getReference(), 13);
                $quantityItem = $this->fullfilWithSpaces($orderItems->getQuantity(), 3);
                $priceHvatItem = $this->fullfilWithSpaces($orderItems->getFinalPrice(), 9);
                if ($extCode == 301) {
                    $quantiteUc = $quantityItem;
                    $quantityItem = 0;
                } else {
                    $quantiteUc = ($quantityItem * $pcb);
                }
                $quantiteUcItem = $this->fullfilWithSpaces($quantiteUc, 9);

                $itemLine = 'D' . $enterpriseNumber . $numberOrder . $codeItem . $quantityItem . $priceHvatItem . 'IN' . $quantiteUcItem;
                $itemLine = $this->fullfilWithSpaces($itemLine, 80);

                /**
                 * Put item into file
                 */
                fwrite($ibmFile, $this->getLastCharacter($itemLine));
            }

            /**
             * Step 4: Prepare Footer of order
             * Syntaxe:
             *      ‘C’+N° société+NOTUSED+date livraison+commentaire+flag livraison
             *      1car+2car+3car+8car+30car+1car
             * Exemple :
             *      C+20+400+20130927+RAS+ L
             */
            //ticket CC-1211 =>  add 6 spaces after deliveryDate, dateDelivery have 8 car
            $dateDeliveryFilled = $this->fullfilWithSpaces($dateDelivery, 14);
            $footer = 'C' . $enterpriseNumber . $numberOrder . $dateDeliveryFilled . $this->fullfilWithSpaces($commentaire, 40) . 'L';
            $footerLine = $this->fullfilWithSpaces($footer, 80);

            /**
             * Put footer into file
             */
            fwrite($ibmFile, $this->getLastCharacter($footerLine));
            fclose($ibmFile);
        }

        $this->sendGeneratedFilesToGoogleCloud();
    }

    /**
     * Remplit la valeur passée en paramètre avec des espaces si elle ne correspond pas au critère
     * @param $value
     * @return mixed
     */
    protected function fullfilWithSpaceEighty($value) {
        $maxStrX = 80;

        if (strlen($value) < $maxStrX) {
            $value = str_pad($value, $maxStrX);
        }
        return $value;
    }

    /**
     * @param $pattern
     * @return string
     */
    public function getLastCharacter($pattern) {
        return $pattern . "\x0d\x0a";
    }

    /**
     * Remplit la valeur passée en paramètre avec des espaces si elle ne correspond pas au critère
     * @param $value
     * @param $minimumStrX
     * @return string
     */
    private function fullfilWithSpaces($value, $minimumStrX) {
        if (strlen($value) < $minimumStrX) {
            if ($minimumStrX == 3) {
                $valeur = str_pad($value, $minimumStrX, '0', STR_PAD_LEFT);
            } else if ($minimumStrX == 9) {
                $valeur = str_pad(str_replace(".", "", $value), $minimumStrX, '0', STR_PAD_LEFT);
            } else {
                $valeur = str_pad($value, $minimumStrX);
            }
        } else {
            $valeur = $value;
        }
        return $valeur;
    }

    /**
     * Delete folders recursiveley
     * @param $dir
     */
    private function deleteDirectory($dir) {
        foreach (scandir($dir) as $file) {
            if ('.' === $file || '..' === $file)
                continue;
            if (is_dir("$dir/$file"))
                $this->deleteDirectory("$dir/$file");
            else
                unlink("$dir/$file");
        }
        rmdir($dir);
    }


    private function sendGeneratedFilesToGoogleCloud()
    {

        $projectId = $this->container->getParameter('gcp_gs_project');
        $keyFile = $this->container->getParameter('gcp_gs_key');
        $bucketName = $this->container->getParameter('gcp_gs_etl_bucket');
        $directoryTarget = ($this->container->getParameter('gcp_gs_order_folder'))?$this->container->getParameter('gcp_gs_order_folder'):'export/';

        $storage = new StorageClient(
            array(
                'projectId' => $projectId,
                'keyFilePath' => $keyFile
            )
        );
        $bucket = $storage->bucket($bucketName);

        $directoryCommand = $this->webRoot . '/command';
        $directoryPreCommand = $this->webRoot . '/precommand';

        /**
         * Order files
         */
        if (is_dir($directoryCommand)) {
            $finderCommand = new Finder();
            $commandFolders = $finderCommand->directories()->in($directoryCommand);

            foreach ($commandFolders as $folder) {
                
                if ($finderCommand->files()->count() == 0) {
                    break;
                }

                foreach ($finderCommand->files()->in($folder->getPathname()) as $file) {
                    $codePlatform = substr($file->getFilename(), strlen('CD_WEB2.C'), -6);
                    $remoteFile = $directoryTarget . $codePlatform . "/order"."/". $file->getFilename();
                    $bucket->upload(fopen($file->getRealPath(), 'r'),
                        array(
                            'name' => $remoteFile
                        ));
                }
            }


            /**
             * Delete directory
             */
            $this->deleteDirectory($directoryCommand);
        }

        /**
         * Send PreOrder files
         */
        if (is_dir($directoryPreCommand)) {
            $preOrderFinder = new Finder();
            $preOrderFolders = $preOrderFinder->directories()->in($directoryPreCommand);
            foreach ($preOrderFolders as $folder) {
                if ($preOrderFinder->files()->count() == 0) {
                    break;
                }

                foreach ($preOrderFinder->files()->in($folder->getPathname()) as $file) {
                    $codePlatform = substr($file->getFilename(), strlen('CD_WEB2.C'), -6);
                    $remoteFile = $directoryTarget . $codePlatform . "/preorder"."/". $file->getFilename();
                    $bucket->upload(fopen($file->getRealPath(), 'r'),
                        array(
                            'name' => $remoteFile
                        ));
                }
            }

            /**
             * Delete directory
             */
            $this->deleteDirectory($directoryPreCommand);
        }

    }

}
