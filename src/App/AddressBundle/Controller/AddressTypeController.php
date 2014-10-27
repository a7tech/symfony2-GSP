<?php

namespace App\AddressBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use App\AddressBundle\Entity\AddressType;
use App\AddressBundle\Form\AddressTypeType;
use JMS\SecurityExtraBundle\Annotation\Secure;
use App\UserBundle\Annotation\RoleInfo;

/**
 * AddressType controller.
 *
 * @RoleInfo(role="ROLE_BACKEND_ADDRESSTYPE_ALL", parent="ROLE_BACKEND_ADDRESSTYPE_ALL", desc="Address types all access", module="Address type")
 * @Route("/backend/settings/custom/address_type")
 */
class AddressTypeController extends Controller
{
    /**
     * Lists all AddressType entities.
     * @Secure(roles="ROLE_BACKEND_ALL_SETTINGS")     
     * @RoleInfo(role="ROLE_BACKEND_ADDRESSTYPE_LIST", parent="ROLE_BACKEND_ADDRESSTYPE_ALL", desc="List address types", module="Address type")
     * @Route("/", name="backend_settings_custom_address_type")
     * @Method({"GET", "PUT"})
     * @Template()
     */
    public function indexAction()
    {
//        $em = $this->getDoctrine()->getManager();
        $dql   = "SELECT a FROM AppAddressBundle:AddressType a";
//        $query = $em->createQuery($dql);
//
//        $paginator  = $this->get('knp_paginator');
//        $pPaginator = $paginator->paginate(
//            $query,
//            $this->get('request')->query->get('page', 1),
//            10);
        return array(
            'entities' => [],
        );
    }
    
    /**
     * indexAction
     *
     * @Secure(roles="ROLE_BACKEND_ALL_SETTINGS")     
     * @RoleInfo(role="ROLE_BACKEND_ADDRESSTYPE_LIST", parent="ROLE_BACKEND_ADDRESSTYPE_ALL", desc="List address types", module="Address type")
     * @Route("/datatables", name="backend_address_type_datatables")
     * @Method("GET")
     */
    public function indexDatatablesAction(Request $request)
    {
        $filter = $this->get('app_address.filter.addresstypefilter');

        if ($response = $filter->processRequest($request)) {
            return $response;
        }
    }

    /**
     * Creates a new AddressType entity.
     * @Secure(roles="ROLE_BACKEND_ALL_SETTINGS")
     * @RoleInfo(role="ROLE_BACKEND_ADDRESSTYPE_CREATE", parent="ROLE_BACKEND_ADDRESSTYPE_ALL", desc="Create address types", module="Address type")
     * @Route("/", name="backend_settings_custom_address_type_create")
     * @Method("POST")
     * @Template("AppAddressBundle:AddressType:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = new AddressType();
        $form = $this->createForm(new AddressTypeType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('backend_settings_custom_address_type_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new AddressType entity.
     * @Secure(roles="ROLE_BACKEND_ALL_SETTINGS")
     * @RoleInfo(role="ROLE_BACKEND_ADDRESSTYPE_CREATE", parent="ROLE_BACKEND_ADDRESSTYPE_ALL", desc="Create address types", module="Address type")
     * @Route("/new", name="backend_settings_custom_address_type_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new AddressType();
        $form   = $this->createForm(new AddressTypeType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a AddressType entity.
     * @Secure(roles="ROLE_BACKEND_ALL_SETTINGS")
     * @RoleInfo(role="ROLE_BACKEND_ADDRESSTYPE_SHOW", parent="ROLE_BACKEND_ADDRESSTYPE_ALL", desc="Show address types", module="Address type")
     * @Route("/show/{id}", name="backend_settings_custom_address_type_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppAddressBundle:AddressType')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AddressType entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing AddressType entity.
     * @Secure(roles="ROLE_BACKEND_ALL_SETTINGS")
     * @RoleInfo(role="ROLE_BACKEND_ADDRESSTYPE_EDIT", parent="ROLE_BACKEND_ADDRESSTYPE_ALL", desc="Edit address types", module="Address type")
     * @Route("/edit/{id}", name="backend_settings_custom_address_type_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppAddressBundle:AddressType')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AddressType entity.');
        }

        $editForm = $this->createForm(new AddressTypeType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing AddressType entity.
     * @Secure(roles="ROLE_BACKEND_ALL_SETTINGS")
     * @RoleInfo(role="ROLE_BACKEND_ADDRESSTYPE_EDIT", parent="ROLE_BACKEND_ADDRESSTYPE_ALL", desc="Edit address types", module="Address type")
     * @Route("/{id}", name="backend_settings_custom_address_type_update")
     * @Method({"POST","PUT"})
     * @Template("AppAddressBundle:AddressType:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppAddressBundle:AddressType')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AddressType entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new AddressTypeType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('backend_settings_custom_address_type_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a AddressType entity.
     * @Secure(roles="ROLE_BACKEND_ALL_SETTINGS")
     * @RoleInfo(role="ROLE_BACKEND_ADDRESSTYPE_DELETE", parent="ROLE_BACKEND_ADDRESSTYPE_ALL", desc="Delete address types", module="Address type")
     * @Route("/delete/{id}", name="backend_settings_custom_address_type_delete")
     * @Method({"GET","DELETE"})
     */
    public function deleteAction(Request $request, $id)
    {

            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('AppAddressBundle:AddressType')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find AddressType entity.');
            }

            $em->remove($entity);
            $em->flush();


        return $this->redirect($this->generateUrl('backend_settings_custom_address_type'));
    }

    /**
     * Creates a form to delete a AddressType entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
