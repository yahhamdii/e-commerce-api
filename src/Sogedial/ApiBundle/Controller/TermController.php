<?php

namespace Sogedial\ApiBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sogedial\ApiBundle\Exception\EntityNotFoundException;
use Sogedial\ApiBundle\Exception\UploadException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use Symfony\Component\HttpFoundation\Request;
use Sogedial\ApiBundle\Entity\Term;
use Sogedial\ApiBundle\Entity\Platform;
use Google\Cloud\Storage\StorageClient;

/**
 * Term controller.
 *
 * @Security("has_role('ROLE_ADMIN')")
 * @Rest\Route(path="/api/term")
 */
class TermController extends Controller
{


    const LIMIT = 10;

    /**
     * Lists all Term entities.
     *
     * @Security("has_role('ROLE_SUPER_ADMIN') or has_role('ROLE_ADMIN') or has_role('ROLE_COMMERCIAL')")
     * @Rest\Get("", name="get_all_term")
     * @Rest\View(StatusCode = 200, serializerEnableMaxDepthChecks=true, serializerGroups={"list"})
     * @QueryParam(name="offset", requirements="\d+", default="0", description="Index de début de la pagination")
     * @QueryParam(name="limit", requirements="\d+", default="10", description="Nombre d'éléments à afficher")
     * @QueryParam(name="orderBy", default=null, description="nom de champ de l'ordre")
     * @QueryParam(name="orderByDesc", default=null, description="nom de champ de l'ordre descendant")
     * @QueryParam(name="filter", default=null, description="filtre sur les champs")
     */
    public function getAllAction($orderBy = null, $orderByDesc = null, $limit = self::LIMIT, $offset = 0, $filter = null)
    {
        $order = 'asc';
        $em = $this->getDoctrine()->getManager();

        if ($orderBy == null) {
            $meta = $em->getClassMetadata(Term::class);
            $orderBy = $meta->getSingleIdentifierFieldName();
        }

        if ($orderByDesc != null) {
            $orderBy = $orderByDesc;
            $order = 'desc';
        }

        $filter = ($filter != null) ? json_decode($filter, true) : [];

        return $em->getRepository('SogedialApiBundle:Term')->findBy($filter, [$orderBy => $order], $limit, $offset);
    }

    /**
     * Count Term available after filter
     *
     * @Security("has_role('ROLE_SUPER_ADMIN') or has_role('ROLE_ADMIN') or has_role('ROLE_COMMERCIAL')")
     * @Rest\Get("/count", name="count_term")
     * @Rest\View(StatusCode = 200)
     * @QueryParam(name="filter", default=null, description="filtre sur les champs")
     */
    public function countAction($filter = null)
    {
        $filter = ($filter != null) ? json_decode($filter, true) : [];

        return $this->getDoctrine()->getManager()->getRepository('SogedialApiBundle:Term')->getCount($filter);
    }

    /**
     * Finds and displays a Term entity.
     *
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @Rest\Get("/{id}", name="get_term")
     * @Rest\View(statusCode = 200, serializerEnableMaxDepthChecks=true, serializerGroups={"detail"})
     */
    public function getAction(Term $term = null, $id)
    {
        if (empty($term)) {
            throw new EntityNotFoundException('term with id : '. $id . ' was not found.');
        }

        return $term;
    }

    /**
     * create a new Term entity.
     *
     * @Security("has_role('ROLE_SUPER_ADMIN') or has_role('ROLE_ADMIN')")
     * @Rest\Post("/add", name="add_term")
     * @Rest\View(StatusCode = 201, serializerEnableMaxDepthChecks=true, serializerGroups={"add"})
     * @ParamConverter("term", converter="fos_rest.request_body")     
     */
    public function addAction(Term $term)
    {
        $em = $this->getDoctrine()->getManager();                
        $em->persist($term);
        $em->flush();

        return $term;
    }

    /**
     * Displays a form to edit an existing Term entity.
     *
     * @Security("has_role('ROLE_SUPER_ADMIN') or has_role('ROLE_ADMIN')")
     * @Rest\Put("/update", name="update_term")
     * @Rest\View(StatusCode = 200, serializerEnableMaxDepthChecks=true, serializerGroups={"update"})
     * @ParamConverter("term", converter="fos_rest.request_body")
     * 
     */
    public function updateAction(Term $term)
    {
        if($term->getStatus()==TERM::STATUS_INACTIVE){
            $term->setDateUnactivated(new \DateTime());
        }

        $em = $this->getDoctrine()->getManager();
        $em->merge($term);
        $em->flush();

        return $term;
    }

    /**
     * Delete Term by id
     *
     * @Security("has_role('ROLE_SUPER_ADMIN') or has_role('ROLE_ADMIN')")
     * @Rest\Delete("/delete/{id}", name="delete_term")
     * @Rest\View(StatusCode = 200)
     */
    public function deleteAction(Term $term = null, $id)
    {
        if (empty($term)) {
            throw new EntityNotFoundException('term with id : '. $id . ' was not found.');
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($term);
        $em->flush();

        return new JsonResponse(sprintf("term with id: %s  was removed.", $id), 200);
    }

    /**
     * Upload File
     *
     * @Security("has_role('ROLE_SUPER_ADMIN') or has_role('ROLE_ADMIN')")
     * @Rest\Post("/upload/{id}", name="upload_term_update")     
     * @Rest\Post("/upload", name="upload_term_add")
     * @Rest\View(StatusCode = 200, serializerEnableMaxDepthChecks=true, serializerGroups={"update"})
     */
    public function upload(Request $request, Term $term = null)
    {        
        $em = $this->getDoctrine()->getManager();

        $file = $request->files->get( 'file' );
        
        if($file && is_file($file->getRealPath()) && $file->isValid()){

            if( $term == null){
                $term = new Term();
                $em = $this->getDoctrine()->getManager();
                $em->persist($term);
                $em->flush();
            }

            if($request->request->get('title')){
                $term->setTitle($request->request->get('title'));       
            }
            
            if($request->request->get('platform')){
                $platform = json_decode($request->request->get('platform'), true);                                            
                if(is_array($platform)){                    
                    if(isset($platform["id"])){
                        $entity = $em->getRepository(Platform::class)->find($platform["id"]);
                        if($entity){
                            $term->setPlatform($entity);                            
                        }
                    }                   
                }               
            }

            $project =  $this->getParameter ( 'gcp_gs_project' );  
            $bucket =  $this->getParameter ( 'gcp_gs_files_bucket' );
            $key = $this->getParameter ( 'gcp_gs_key' );
            $folder = $this->getParameter ( 'gcp_gs_term_folder' );        
    
            $term->setName($file->getClientOriginalName());
            $term->setExt($file->getClientOriginalExtension());
            $term->setType($file->getClientMimeType());

            $em->persist($term);
            $em->flush();

            $fileName = $term->getFullFileName();
            
            $storage = new StorageClient([
                'projectId' => $project,
                'keyFilePath' =>  $key
            ]);
    
            $bucket = $storage->bucket($bucket);        
            $bucket->upload(
                fopen($file->getRealPath(), 'r'),
                [
                    'predefinedAcl' => 'publicRead',
                    'name' => $folder.$fileName,
                ]
            );
        }

        if($file && !$file->isValid()){
            throw new UploadException($file->getErrorMessage());
        }

        return $term;
    }

}
