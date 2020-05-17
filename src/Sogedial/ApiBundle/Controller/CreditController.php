<?php

namespace Sogedial\ApiBundle\Controller;

use Doctrine\Common\Persistence\ObjectManager;
use FOS\RestBundle\Controller\Annotations as Rest;
use Google\Cloud\Core\Exception\NotFoundException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sogedial\ApiBundle\Entity\Credit;
use Sogedial\ApiBundle\Entity\Invoice;
use Sogedial\ApiBundle\Entity\Order;
use Sogedial\ApiBundle\Exception\BadRequestException;
use Sogedial\ApiBundle\Exception\EntityNotFoundException;
use Sogedial\ApiBundle\Exception\ForbiddenException;
use Sogedial\ApiBundle\Service\RepositoryInjecter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Credit controller.
 *
 * @Rest\Route(path="/api/credit")
 */
class CreditController extends Controller
{
    const LIMIT = 60;

    /**
     * Lists all Credit entities.
     *
     * @Security("has_role('ROLE_CUSTOMER') or has_role('ROLE_ADMIN') or has_role('ROLE_COMMERCIAL')")
     * @Rest\Get("", name="get_all_credit")
     * @Rest\View(StatusCode = 200, serializerEnableMaxDepthChecks=true, serializerGroups={"list_credit"})
     * @Rest\QueryParam(name="offset", requirements="\d+", default="0", description="Index de début de la pagination")
     * @Rest\QueryParam(name="limit", requirements="\d+", default="10", description="Nombre d'éléments à afficher")
     * @Rest\QueryParam(name="orderBy", default=null, description="nom de champ de l'ordre")
     * @Rest\QueryParam(name="orderByDesc", default=null, description="nom de champ de l'ordre descendant")
     * @Rest\QueryParam(name="filter", default=null, description="filtre sur les champs")
     */
    public function getAllAction($orderBy = null, $orderByDesc = null, $limit = self::LIMIT, $offset = 0, $filter = null)
    {
        $order = 'asc';
        $em = $this->getDoctrine()->getManager();

        if ($orderBy == null) {
            $meta = $em->getClassMetadata(Credit::class);
            $orderBy = $meta->getSingleIdentifierFieldName();
        }

        if ($orderByDesc != null) {
            $orderBy = $orderByDesc;
            $order = 'desc';
        }

        $filter = ($filter != null) ? json_decode($filter, true) : [];

        $repo = $this->get('sogedial.repository_injecter')->getRepository(Credit::class);
        return $repo->findBy($filter, [$orderBy => $order], $limit, $offset);
    }

    /**
     * Count Credit available after filter
     *
     * @Security("has_role('ROLE_CUSTOMER') or has_role('ROLE_ADMIN') or has_role('ROLE_COMMERCIAL')")
     * @Rest\Get("/count", name="count_credit")
     * @Rest\View(StatusCode = 200)
     * @Rest\QueryParam(name="filter", default=null, description="filtre sur les champs")
     */
    public function countAction($filter = null)
    {
        $repoInjector = $this->get('sogedial.repository_injecter');
        $filter = ($filter != null) ? json_decode($filter, true) : [];

        return $repoInjector->getRepository('SogedialApiBundle:Credit')->getCount($filter);
    }

    /**
     * Finds and displays a Credit entity.
     *
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @Rest\Get("/{id}", name="get_credit")
     * @Rest\View(statusCode = 200, serializerEnableMaxDepthChecks=true)
     */
    public function getAction(Credit $credit = null, $id)
    {
        if (empty($credit)) {
            throw new EntityNotFoundException('credit with id : ' . $id . ' was not found.');
        }

        return $credit;
    }

    /**
     * create a new Credit entity.
     *
     * @Security("has_role('ROLE_CUSTOMER')")
     * @Rest\Post("/add", name="add_credit")
     * @Rest\View(StatusCode = 201, serializerEnableMaxDepthChecks=true, serializerGroups={"credit"})
     * @ParamConverter("credits", class="array", converter="fos_rest.request_body")
     */
    public function addAction(array $credits)
    {
        $em = $this->getDoctrine()->getManager();
        $service = $this->get('sogedial.repository_injecter');

        $keys = array_keys($credits);
        if(empty($keys)){
            throw new BadRequestException("You must specify at least one credit");
        }      

        /*TO DO ACL ON Invoice & InvoiceItem*/
        $invoiceItem = $service->getRepository('SogedialApiBundle:InvoiceItem')->find($keys[0]);

        if(empty($invoiceItem)){
            throw new NotFoundException("This Invoice Item does not exists");
        }

        $order = $invoiceItem->getInvoice()->getOrder();
        $platform = $order->getPlatform();
        /** @var Invoice $invoice */
        $invoice = $invoiceItem->getInvoice();
        
        $status = $order->getStatus()->getName();

        if ($this->getUser() !== $order->getUser()) {
            throw new ForbiddenException("Unauthorized action: It's not your order");
        }

        if ($status != Order::STATUS_ORDER_DELIVERED) {
            throw new BadRequestException("this order is not delivered");
        }

        $currentDate = new \DateTime('now');
        if (date_diff($currentDate, $invoice->getDate())->days >= 3) {
            throw new BadRequestException("three days exceeded from delivery date!");
        }

        $invoiceItemsTab = array();
        /** @var array $values ... credits array of invoiceItem ID */
        foreach ($credits as $invoiceItemId => $values) {
            $invoiceItem = $service->getRepository('SogedialApiBundle:InvoiceItem')->findBy(array('id' => $invoiceItemId, 'platform'=>$platform));

            if ($invoiceItem[0] !== null) {
                //parcourir les credits de cette invoiceItem
                foreach ($values as $creditData) {
                    $credit = new Credit();
                    $credit->setQuantity($creditData['quantity'])
                        ->setComment($creditData['comment'])
                        ->setReason($creditData['reason'])
                        ->setChecked($creditData['checked']);
                    $credit->setInvoiceItem($invoiceItem[0]);

                    $invoiceItem[0]->addCredit($credit);
                    $em->persist($credit);
                }
                $em->persist($invoiceItem[0]);

                $invoiceItemsTab[] = $invoiceItem[0];
            }
        }
        //laisser flush ici car l'instruction juste apres recupere les credits qui sont enregistrés au bdd
        $em->flush();

        $this->sendCreditNotification($service, $invoice , $em);

        return $invoiceItemsTab;
    }

    /**
     * Update credit entity
     *
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @Rest\Put("/update", name="update_credit")
     * @Rest\View(StatusCode = 200, serializerEnableMaxDepthChecks=true)
     * @ParamConverter("credit", converter="fos_rest.request_body")
     */
    public function updateAction(Credit $credit)
    {
        $em = $this->getDoctrine()->getManager();
        $em->merge($credit);
        $em->flush();

        return $credit;
    }

    /**
     * Delete Credit by id
     *
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @Rest\Delete("/delete/{id}", name="delete_credit")
     * @Rest\View(StatusCode = 200)
     */
    public function deleteAction(Credit $credit = null, $id)
    {
        if (empty($credit)) {
            throw new EntityNotFoundException('credit with id : ' . $id . ' was not found.');
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($credit);
        $em->flush();

        return new JsonResponse(sprintf("credit with id: %s  was removed.", $id), 200);
    }

    private function sendCreditNotification(RepositoryInjecter $service, Invoice $invoice, ObjectManager $em): void
    {
        $order = $invoice->getOrder();
        $newCredits = $service->getRepository('SogedialApiBundle:Credit')->findBy(array('invoice' => $invoice->getId(), 'sended' => false, 'platform' => $order->getPlatform()));
        if (!is_null($newCredits)) {
            $clientName = $order->getUser()->getClient()->getName();
            $clientSiret = $order->getUser()->getClient()->getSiret();
            $invoiceNumber = $invoice->getNumber();
            $emailCredit = $order->getPlatform()->getEmailCredit();

            //generate BDC
            $pathFile = $this->get('sogedial.generate_pdf_order')->orderToPdf($order, true); 

            $this->get('sogedial.oauth.mailer')->sendCreditAlert($newCredits, $clientName, $clientSiret, $invoiceNumber, $emailCredit, $pathFile);

            //delete pdf
            unlink($pathFile);

            foreach ($newCredits as $newCredit) {
                $newCredit->setSended(true);
                $em->persist($newCredit);
            }
        }
        $em->flush();
    }
}
