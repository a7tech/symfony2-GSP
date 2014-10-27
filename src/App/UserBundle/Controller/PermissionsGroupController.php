<?php

namespace App\UserBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use App\UserBundle\Entity\PermissionsGroup;
use App\UserBundle\Form\PermissionsGroupType;
use App\UserBundle\Form\PermissionsGroupModuleType;
use JMS\SecurityExtraBundle\Annotation\Secure;
use App\UserBundle\Annotation\RoleInfo;

/**
 * PermissionsGroup controller.
 * @RoleInfo(role="ROLE_BACKEND_GROUPS_ALL", parent="ROLE_BACKEND_GROUPS_ALL", desc="Permission groups all access", module="Permission group")
 * @Route("/backend/groups")
 */
class PermissionsGroupController extends Controller
{

    /**
     * Lists all PermissionsGroup entities.
     *
     * @Route("/", name="backend_settings_groups")
     * @Secure(roles="ROLE_BACKEND_GROUPS_LIST")
     * @RoleInfo(role="ROLE_BACKEND_GROUPS_LIST", parent="ROLE_BACKEND_GROUPS_ALL", desc="List Permission groups", module="Permission group")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
//        $em = $this->getDoctrine()->getManager();
//
//        $entities = $em->getRepository('AppUserBundle:PermissionsGroup')->findAll();

        return array(
            'entities' => [],
        );
    }
    
    /**
     * indexAction
     *
     * @Secure(roles="ROLE_BACKEND_GROUPS_LIST")
     * @RoleInfo(role="ROLE_BACKEND_GROUPS_LIST", parent="ROLE_BACKEND_GROUPS_ALL", desc="List Permission groups", module="Permission group")
     * @Route("/datatables", name="backend_group_datatables")
     * @Method("GET")
     */
    public function indexDatatablesAction(Request $request)
    {
        $filter = $this->get('app_user.filter.groupfilter');

        if ($response = $filter->processRequest($request)) {
            return $response;
        }
    }

    /**
    * Creates a form to create a PermissionsGroup entity.
    *
    * @param PermissionsGroup $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(PermissionsGroup $entity)
    {
        $form = $this->createForm(new PermissionsGroupType(), $entity, array(
            'action' => $this->generateUrl('backend_settings_groups_create'),
            'method' => 'POST',
        ));

        return $form;
    }

    /**
     * Displays a form to create a new PermissionsGroup entity.
     * @Secure(roles="ROLE_BACKEND_GROUPS_CREATE")
     * @RoleInfo(role="ROLE_BACKEND_GROUPS_CREATE", parent="ROLE_BACKEND_GROUPS_ALL", desc="Create Permission group", module="Permission group")
     * @Route("/new", name="backend_settings_groups_create")
     * @Method({"GET","POST"})
     * @Template()
     */
    public function newAction(Request $request)
    {
        $entity = new PermissionsGroup();
        $form   = $this->createCreateForm($entity);

        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('backend_settings_groups_show', array('id' => $entity->getId())));
        }
        
        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a PermissionsGroup entity.
     * @Secure(roles="ROLE_BACKEND_GROUPS_SHOW")
     * @RoleInfo(role="ROLE_BACKEND_GROUPS_SHOW", parent="ROLE_BACKEND_GROUPS_ALL", desc="Show Permission group", module="Permission group")
     * @Route("/show/{id}", name="backend_settings_groups_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppUserBundle:PermissionsGroup')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PermissionsGroup entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing PermissionsGroup entity.
     * @Secure(roles="ROLE_BACKEND_GROUPS_EDIT")
     * @RoleInfo(role="ROLE_BACKEND_GROUPS_EDIT", parent="ROLE_BACKEND_GROUPS_ALL", desc="Edit Permission group", module="Permission group")
     * @Route("/edit/{id}", name="backend_settings_groups_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppUserBundle:PermissionsGroup')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PermissionsGroup entity.');
        }

        $editForm = $this->createEditForm($entity);

        return array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        );
    }

    /**
    * Creates a form to edit a PermissionsGroup entity.
    *
    * @param PermissionsGroup $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(PermissionsGroup $entity)
    {
        $form = $this->createForm(new PermissionsGroupType(), $entity, array(
            'action' => $this->generateUrl('backend_settings_groups_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        return $form;
    }
    /**
     * Edits an existing PermissionsGroup entity.
     * @Secure(roles="ROLE_BACKEND_GROUPS_EDIT")
     * @Route("/edit/{id}", name="backend_settings_groups_update")
     * @Method({"PUT", "POST"})
     * @Template("AppUserBundle:PermissionsGroup:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppUserBundle:PermissionsGroup')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PermissionsGroup entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->submit($request); // For some reason, handleRequest doens't  work so replaced with submit
        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('backend_settings_groups_show', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'form'   => $editForm->createView()
        );
    }
    /**
     * Deletes a PermissionsGroup entity.
     * @Secure(roles="ROLE_BACKEND_GROUPS_DELETE")
     * @RoleInfo(role="ROLE_BACKEND_GROUPS_DELETE", parent="ROLE_BACKEND_GROUPS_ALL", desc="Delete Permission group", module="Permission group")
     * @Route("/{id}", name="backend_settings_groups_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('AppUserBundle:PermissionsGroup')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find PermissionsGroup entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('backend_settings_groups'));
    }

    /**
     * Creates a form to delete a PermissionsGroup entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('backend_settings_groups_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
