<?php

namespace Sogedial\ApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations\QueryParam;
use Sogedial\ApiBundle\Entity\Client;
use Sogedial\ApiBundle\Entity\Item;
use Sogedial\OAuthBundle\Entity\UserAdmin;
use Sogedial\OAuthBundle\Entity\UserCommercial;
use Sogedial\OAuthBundle\Entity\UserCustomer;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\Annotations as Rest;

/**
 * Search controller.
 *
 * @Rest\Route(path="/api/search")
 */

class SearchController extends Controller
{
    const LIMIT = 10;

    /**
     *
     * @Rest\Get("", name="get_all_search")
     * @QueryParam(name="q", description="Terme à rechercher")
     * @QueryParam(name="offset", requirements="\d+", default="0", description="Index de début de la pagination")
     * @QueryParam(name="limit", requirements="\d+", default="10", description="Nombre d'éléments à afficher")
     * @QueryParam(name="orderBy", default=null, description="nom de champ de l'ordre")
     * @QueryParam(name="orderByDesc", default=null, description="nom de champ de l'ordre descendant")
     * @QueryParam(name="filter", default=null, description="filtre sur les champs")
     * @QueryParam(name="showManufacturers", default=null, description="retourne les manufacturers groupés")
     * @QueryParam(name="showClients", default=null, description="retourne les clients")
     * @QueryParam(name="limitManufacturers", default= true, description="to controle limit number manufacturer returned")
     * @Rest\View(serializerEnableMaxDepthChecks=true, serializerGroups={"search"})
     */
    public function searchAction($q, $orderBy = null, $orderByDesc = null, $limit = self::LIMIT, $offset = 0, $filter = null, $showManufacturers = true, $limitManufacturers = true)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        $q = rawurldecode($q);

        if($user instanceof UserAdmin || $user instanceof UserCommercial){

            return $this->searchClient($q, $limit, $offset, $orderBy, $orderByDesc, $filter, $em);

        }elseif ($user instanceof UserCustomer){

            return $this->searchItems($q, $orderBy, $orderByDesc, $limit, $offset, $filter, $showManufacturers, $em, $limitManufacturers);
        }

    }


    /**
     * Count Results available after filter
     *
     * @Rest\Get("/count", name="count_search")     
     * @Rest\View(StatusCode = 200)
     * @QueryParam(name="q", description="Terme à rechercher")
     * @QueryParam(name="filter", default=null, description="filtre sur les champs")
     */
    public function countAction($q, $filter = null)
    {
        $filter = ($filter != null) ? json_decode($filter, true) : [];
        $user = $this->getUser();

        $q = rawurldecode($q);

        if($user instanceof UserAdmin || $user instanceof UserCommercial){
            return $this->get('sogedial.repository_injecter')
                ->getRepository('SogedialApiBundle:Client')
                ->getCountClientSuggestion( $q, $filter);

        }elseif ($user instanceof UserCustomer){
            return $this->get('sogedial.repository_injecter')
                ->getRepository('SogedialApiBundle:Item')
                ->getCountItemSuggestion( $q, $filter);

        }
    }


    private function searchClient($q, $limit, $offset, $clientOrderBy, $clientOrderByDesc, $clientFilter, $em)
    {
        $order = 'asc';
        if ($clientOrderBy == null) {
            $meta = $em->getClassMetadata(Client::class);
            $clientOrderBy = $meta->getSingleIdentifierFieldName();
        }

        if ($clientOrderByDesc != null) {
            $clientOrderBy = $clientOrderByDesc;
            $order = 'desc';
        }

        $clientFilter = ($clientFilter != null) ? json_decode($clientFilter, true) : [];

        return $this->get('sogedial.repository_injecter')
            ->getRepository('SogedialApiBundle:Client')
            ->getClientSuggestion($q, $clientFilter, [$clientOrderBy => $order], $limit, $offset);
    }


    private function searchItems($q, $orderBy, $orderByDesc, $limit, $offset, $filter, $showManufacturers, $em, $limitManufacturers): array
    {
        $order = 'asc';

        if ($orderBy == null) {
            /*$meta = $em->getClassMetadata(Item::class);
            $orderBy = $meta->getSingleIdentifierFieldName();*/
            $orderBy = "categories.name";
        }

        if ($orderByDesc != null) {
            $orderBy = $orderByDesc;
            $order = 'desc';
        }

        $filter = ($filter != null) ? json_decode($filter, true) : [];

        $showManufacturers = (strtolower($showManufacturers) == 'true');

        return $items = $this->get('sogedial.repository_injecter')
            ->getRepository('SogedialApiBundle:Item')
            ->getItemSuggestion($q, $filter, [$orderBy => $order], $limit, $offset, $showManufacturers, $limitManufacturers);

    }

}