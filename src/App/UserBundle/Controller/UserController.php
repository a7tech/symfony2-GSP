<?php

namespace App\UserBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use App\UserBundle\Entity\User;
use App\UserBundle\Form\UserType;
use JMS\SecurityExtraBundle\Annotation\Secure;
use App\UserBundle\Annotation\RoleInfo;

/**
 * User controller.
 *
 * @Route("/backend/user")
 * @RoleInfo(role="ROLE_BACKEND_USER_ALL", parent="ROLE_BACKEND_USER_ALL", desc="Users all access", module="User")
 */
class UserController extends Controller
{
    /**
     * Lists all User entities.
     *
     * @Route("/", name="backend_settings_user")
     * @Secure(roles="ROLE_BACKEND_USER_LIST")
     * @RoleInfo(role="ROLE_BACKEND_USER_LIST", parent="ROLE_BACKEND_USER_ALL", desc="List Users", module="User")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
//        $em = $this->getDoctrine()->getManager();
//
//        $entities = $em->getRepository('AppUserBundle:User')->findAll();

        return array(
            'entities' => [],
        );
    }
    
    /**
     * indexAction
     *
     * @Secure(roles="ROLE_BACKEND_USER_LIST")
     * @RoleInfo(role="ROLE_BACKEND_USER_LIST", parent="ROLE_BACKEND_USER_ALL", desc="List Users", module="User")
     * @Route("/datatables", name="backend_user_datatables")
     * @Method("GET")
     */
    public function indexDatatablesAction(Request $request)
    {
        $filter = $this->get('app_user.filter.userfilter');

        if ($response = $filter->processRequest($request)) {
            return $response;
        }
    }

    /**
     * Creates a new User entity.
     * @Secure(roles="ROLE_BACKEND_USER_CREATE")
     * @RoleInfo(role="ROLE_BACKEND_USER_CREATE", parent="ROLE_BACKEND_USER_ALL", desc="Create User", module="User")
     * @Route("/create/", name="backend_settings_user_create")
     * @Method({"GET","POST"})
     * @Template("AppUserBundle:User:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = new User();
        $form = $this->createForm(new UserType(), $entity);


        if ($request->getMethod() == 'POST') {
            $form->submit($request);
            if ($form->isValid()) {

                $em = $this->getDoctrine()->getManager();
                $em->persist($entity);
                $em->flush();

                return $this->redirect($this->generateUrl('backend_settings_user_show', array('id' => $entity->getId())));
            }
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a User entity.
     * @Secure(roles="ROLE_BACKEND_USER_SHOW")
     * @RoleInfo(role="ROLE_BACKEND_USER_SHOW", parent="ROLE_BACKEND_USER_ALL", desc="Show User", module="User")
     * @Route("/show/{id}", name="backend_settings_user_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppUserBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }
            
        return array(
            'entity'      => $entity
        );
    }

    /**
     * Displays a form to edit an existing User entity.
     * @Secure(roles="ROLE_BACKEND_USER_EDIT")
     * @RoleInfo(role="ROLE_BACKEND_USER_EDIT", parent="ROLE_BACKEND_USER_ALL", desc="Edit User", module="User")
     * @Route("/edit/{id}", name="backend_settings_user_edit")
     * @Method({"GET", "POST"})
     * @Template("AppUserBundle:User:edit.html.twig")
     */
    public function editAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var User $entity */
        $entity = $em->getRepository('AppUserBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Person entity.');
        }

        $editForm = $this->createForm(new UserType(), $entity);

        if ($request->getMethod() == 'POST') {
            $editForm->submit($request);

            if ($editForm->isValid()) {
                $userManager = $this->container->get('fos_user.user_manager');
                $userManager->updatePassword($entity);

                $em->persist($entity);
                $em->flush();

                return $this->redirect($this->generateUrl('backend_settings_user_show', array('id' => $id)));
            }
        }

        return array(
            'entity' => $entity,
            'form'   => $editForm->createView(),
        );

    }

    /**
     * Deletes a User entity.
     * @Secure(roles="ROLE_BACKEND_USER_DELETE")
     * @RoleInfo(role="ROLE_BACKEND_USER_DELETE", parent="ROLE_BACKEND_USER_ALL", desc="Delete User", module="User")
     * @Route("/delete/{id}", name="backend_settings_user_delete")
     * @Method({"GET","DELETE"})
     */
    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('AppUserBundle:User')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }
        $em->remove($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('backend_settings_user'));
    }

}
