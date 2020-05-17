<?php

namespace Sogedial\OAuthBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sogedial\ApiBundle\Exception\Exception;
use Sogedial\ApiBundle\Helper\EntityHelper;
use Sogedial\OAuthBundle\Entity\User;
use Sogedial\OAuthBundle\Entity\UserAdmin;
use Sogedial\OAuthBundle\Entity\UserCustomer;
use Sogedial\OAuthBundle\Utils\PasswordUpdater;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * User controller.
 *
 * @Rest\Route(path="/api/user")
 */
class UserController extends Controller {

    const LIMIT = 10;

    /**
     * Lists all User entities.
     *
     * @Rest\Get("", name="get_all_user")
     * @Rest\View(StatusCode = 200,serializerEnableMaxDepthChecks=true)
     * @QueryParam(name="offset", requirements="\d+", default="0", description="Index de début de la pagination")
     * @QueryParam(name="limit", requirements="\d+", default="10", description="Nombre d'éléments à afficher")
     * @QueryParam(name="orderBy", default=null, description="nom de champ de l'ordre")
     * @QueryParam(name="orderByDesc", default=null, description="nom de champ de l'ordre descendant")
     * @QueryParam(name="filter", default=null, description="filtre sur les champs")
     * @QueryParam(name="type", default=null, description="type d\'utilisateur")
     */
    public function getAllAction($orderBy = null, $orderByDesc = null, $limit = self::LIMIT, $offset = 0, $filter = null, $type = null) {

        $order = 'asc';
        $em = $this->getDoctrine()->getManager();
        if ($orderBy == null) {
            $meta = $em->getClassMetadata(User::class);
            $orderBy = $meta->getSingleIdentifierFieldName();
        }
        if ($orderByDesc != null) {
            $orderBy = $orderByDesc;
            $order = 'desc';
        }

        $filter = ($filter != null) ? json_decode($filter, true) : [];

        $repo = $this->get('sogedial.repository_injecter')->getRepository(User::class);
        return $repo->findBy($filter, [$orderBy => $order], $limit, $offset, $type);
    }

    /**
     * Count User available after filter
     *
     * @Rest\Get("/count", name="user_count")
     * @Rest\View(StatusCode = 200)
     * @QueryParam(name="filter", default=null, description="filtre sur les champs")
     * @QueryParam(name="type", default=null, description="type d\'utilisateur")
     */
    public function countAction($filter = null, $type = null) {
        $filter = ($filter != null) ? json_decode($filter, true) : [];

        return $this->get('sogedial.repository_injecter')->getRepository(User::class)->getCount($filter, $type);
    }

    /**
     * Get commercial(s) sales numbers
     *
     * @Security("has_role('ROLE_SUPER_ADMIN') or has_role('ROLE_ADMIN') or has_role('ROLE_COMMERCIAL')")
     * @Rest\Get("/sales-numbers", name="user_sales_numbers")
     * @QueryParam(name="orderBy", default=null, description="nom de champ de l'ordre")
     * @QueryParam(name="orderByDesc", default=null, description="nom de champ de l'ordre descendant")
     * @QueryParam(name="filter", default=null, description="filtre sur les champs")
     * @Rest\View(StatusCode = 200)
     */
    public function salesNumbersAction($orderBy = null, $orderByDesc = null, $filter = null) {
        
        $order = 'asc';
        $em = $this->getDoctrine()->getManager();
        if ($orderBy == null) {
            $meta = $em->getClassMetadata(User::class);
            $orderBy = $meta->getSingleIdentifierFieldName();
        }
        if ($orderByDesc != null) {
            $orderBy = $orderByDesc;
            $order = 'desc';
        }

        $filter = ($filter != null) ? json_decode($filter, true) : [];

        return $this->get('sogedial.repository_injecter')->getRepository(User::class)->getSalesNumbers($filter, [$orderBy => $order]);
    }

    /**
     * create a new User entity.
     *
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @Rest\Post("/add", defaults={"_format": "json"}, name="user_add")
     * @Rest\View(StatusCode = 201,serializerEnableMaxDepthChecks=true)
     * @ParamConverter("user", converter="fos_rest.request_body")
     */
    public function addAction(User $user, Request $request) {

        $em = $this->getDoctrine()->getManager();
        $data = json_decode($request->getContent());

        if (!isset($data->username) || !isset($data->plainPassword) || !isset($data->type))
            throw new \Sogedial\ApiBundle\Exception\ParametersException(['username', 'plainPassword', 'type']);

        //if($em->getRepository('OAuthBundle:User')->getUserByUsername($data->username))
        //throw new \Sogedial\ApiBundle\Exception\InsertEntityException('The user with the username : '.$data->username.' already exists');

        $plainPassword = $data->plainPassword;
        $user->setPlainPassword($plainPassword);

        $passwordUpdater = $this->get('sogedial.oauth.utils.password_updater');
        $passwordUpdater->hashPassword($user);

        $user->setDefaultRoles();

        try {
 
            $em->persist($user);
            $em->flush();
            $em->refresh($user);
        } catch (Exception $exc) {
            throw new \Sogedial\ApiBundle\Exception\InsertEntityException;
        }

        //send mail to configur password
        if($user instanceof UserCustomer){
            $this->get('sogedial_client_activation')->sendConfigPasswordMail($user->getClient(), $user);
        }

        return $user;
    }

    /**
     * Finds and displays a User entity.
     *
     * @Rest\Get("/{id}", defaults={"_format": "json"}, name="user_get")
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     */
    public function getAction($id) {
        try {
            if ($id == 'me') {
                $user = $this->getUser();
            } else {
                $em = $this->getDoctrine()->getManager();
                $user = $em->getRepository('OAuthBundle:User')->find($id);
            }
        } catch (Exception $exc) {
            throw new \Sogedial\ApiBundle\Exception\EntityNotFoundException;
        }

        return $user;
    }

    /**
     * Update user's password
     *
     * @Rest\Put("/update/password", defaults={"_format": "json"}, name="user_update_password")
     * @Rest\View(StatusCode = 200,serializerEnableMaxDepthChecks=true)
     * @ParamConverter("user",class="array",converter="fos_rest.request_body")
     */
    public function updatePasswordAction(array $user) {

        //check if user impersonated so denied access
        if ($this->isGranted('ROLE_PREVIOUS_ADMIN')) {
            throw new \Sogedial\ApiBundle\Exception\ForbiddenException('Access denied');
        }

        $connectedUser = $this->getUser();

        if (!isset($user['id']) || !isset($user['plainPassword']))
            throw new \Sogedial\ApiBundle\Exception\ParametersException(['id', 'plainPassword']);

        $id = $user['id'];
        $plainPassword = $user['plainPassword'];

        if (!$connectedUser instanceof UserAdmin) {
            if (!isset($user['oldPassword']) || !isset($user['plainPasswordConfirm']))
                throw new \Sogedial\ApiBundle\Exception\ParametersException(['oldPassword', 'plainPasswordConfirm']);

            $oldPassword = $user['oldPassword'];
            $plainPasswordConfirm = $user['plainPasswordConfirm'];
        }

        $em = $this->getDoctrine()->getManager();
        if ($id == 'me' || !$connectedUser instanceof UserAdmin) {
            $user = $this->getUser();
        } else {
            $user = $em->getRepository('OAuthBundle:User')->find($id);
        }

        if ($user) {

            if (!$connectedUser instanceof UserAdmin) {
                $encoderService = $this->get('security.password_encoder');

                if (!$encoderService->isPasswordValid($user, $oldPassword))
                    throw new \Sogedial\ApiBundle\Exception\ForbiddenException('The old Password is incorrect');

                if (strcmp($plainPassword, $plainPasswordConfirm) != 0)
                    throw new \Sogedial\ApiBundle\Exception\BadRequestException('new password and confirm password do not match');
            }

            $user->setPlainPassword($plainPassword);

            $passwordUpdater = $this->get('sogedial.oauth.utils.password_updater');
            $passwordUpdater->hashPassword($user);

            try {
                $em->merge($user);
                $em->flush();
            } catch (Exception $exc) {
                throw new \Sogedial\ApiBundle\Exception\UpdateEntityException;
            }
        } else
            throw new \Sogedial\ApiBundle\Exception\EntityNotFoundException;

        return $user;
    }

    /**
     * Displays a form to edit an existing User entity.
     *
     * @Security("has_role('ROLE_SUPER_ADMIN') or has_role('ROLE_ADMIN')")
     * @Rest\Put("/update", defaults={"_format": "json"}, name="user_update")
     * @Rest\View(StatusCode = 200,serializerEnableMaxDepthChecks=true)
     * @ParamConverter("user",class="array",converter="fos_rest.request_body")
     */
    public function updateAction(array $user) {

        //check if user impersonated so denied access
        if ($this->isGranted('ROLE_PREVIOUS_ADMIN')) {
            throw new \Sogedial\ApiBundle\Exception\ForbiddenException('Access denied');
        }
        $id = $user['id'];

        if (!isset($id))
            throw new \Sogedial\ApiBundle\Exception\ParametersException(['id']);

        $em = $this->getDoctrine()->getManager();

        if ($id == 'me') {
            $entity = $this->getUser();
        } else {
            $entity = $em->getRepository('OAuthBundle:User')->find($id);
        }

        if (!$entity)
            throw new \Sogedial\ApiBundle\Exception\EntityNotFoundException('The user with the id : ' . $id . ' does not exists');

        EntityHelper::updateDatas($entity, $user, $em);
        
        try {           
             $em->persist($entity);
             $em->flush();
        } catch (Exception $exc) {
            throw new \Sogedial\ApiBundle\Exception\UpdateEntityException;
        }

         return $entity;
    }

    /**
     * Delete User by id
     *
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @Rest\Delete("/delete/{id}", defaults={"_format": "json"}, name="user_delete")
     * @Rest\View(StatusCode = 200)
     */
    public function deleteAction(User $user, Request $request) {
        $em = $this->getDoctrine()->getManager();
        $id = $request->attributes->get('id');
        try {
            $em->remove($user);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'The User was deleted successfully');
        } catch (Exception $exc) {
            throw new \Sogedial\ApiBundle\Exception\DeleteEntityException;
        }

        return "entity $id was removed";
    }

    public static function createUserProspect($lastname, $firstname, $username, $faxUtilisateur, $plainPassword, $plainPasswordConfirm, $email, $telNumber1, PasswordUpdater $passwordUpdater): UserCustomer {
        if (strcmp($plainPassword, $plainPasswordConfirm) != 0)
            throw new \Sogedial\ApiBundle\Exception\BadRequestException('Your confirmation password does not match');

        $user = new UserCustomer();
        $user->setPlainPassword($plainPassword)
                ->setFax($faxUtilisateur)
                ->setFirstname($firstname)
                ->setLastname($lastname)
                ->setUsername($username)
                ->setEmail($email)
                ->setTelNumber1($telNumber1)
                ->setEnabled(true)
                ->setStatus(UserCustomer::STATUS_PROSPECT);

        $passwordUpdater->hashPassword($user);

        return $user;
    }

}
