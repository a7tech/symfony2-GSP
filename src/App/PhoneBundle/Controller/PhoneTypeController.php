<?php

namespace App\PhoneBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use App\PhoneBundle\Entity\PhoneType;
use App\PhoneBundle\Form\PhoneTypeType;
use JMS\SecurityExtraBundle\Annotation\Secure;
use App\UserBundle\Annotation\RoleInfo;

/**
 * PhoneType controller.
 * @RoleInfo(role="ROLE_BACKEND_PHONETYPE_ALL", parent="ROLE_BACKEND_PHONETYPE_ALL", desc="Phone types all access", module="Phone type")
 * @Route("/backend/settings/custom/phone_type")
 */
class PhoneTypeController extends Controller
{
    /**
     * Lists all PhoneType entities.
     * @Secure(roles="ROLE_BACKEND_PHONETYPE_LIST")
     * @RoleInfo(role="ROLE_BACKEND_PHONETYPE_LIST", parent="ROLE_BACKEND_PHONETYPE_ALL", desc="List Phone types", module="Phone type")
     * @Route("/", name="backend_settings_custom_phone_type")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
//        $em = $this->getDoctrine()->getManager();
//
//        $dql   = "SELECT a FROM AppPhoneBundle:PhoneType a";
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
     * @Secure(roles="ROLE_BACKEND_PHONETYPE_LIST")
     * @RoleInfo(role="ROLE_BACKEND_PHONETYPE_LIST", parent="ROLE_BACKEND_PHONETYPE_ALL", desc="List Phone types", module="Phone type")
     * @Route("/datatables", name="backend_phone_type_datatables")
     * @Method("GET")
     */
    public function indexDatatablesAction(Request $request)
    {
        $filter = $this->get('app_phone.filter.phonetypefilter');

        if ($response = $filter->processRequest($request)) {
            return $response;
        }
    }

    /**
     * Creates a new PhoneType entity.
     * @Secure(roles="ROLE_BACKEND_PHONETYPE_CREATE")
     * @Route("/", name="backend_settings_custom_phone_type_create")
     * @Method("POST")
     * @Template("AppPhoneBundle:PhoneType:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = new PhoneType();
        $form = $this->createForm(new PhoneTypeType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('backend_settings_custom_phone_type_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new PhoneType entity.
     * @Secure(roles="ROLE_BACKEND_PHONETYPE_CREATE")
     * @RoleInfo(role="ROLE_BACKEND_PHONETYPE_CREATE", parent="ROLE_BACKEND_PHONETYPE_ALL", desc="Create Phone type", module="Phone type")
     * @Route("/new", name="backend_settings_custom_phone_type_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new PhoneType();
        $form   = $this->createForm(new PhoneTypeType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a PhoneType entity.
     * @Secure(roles="ROLE_BACKEND_PHONETYPE_SHOW")
     * @RoleInfo(role="ROLE_BACKEND_PHONETYPE_SHOW", parent="ROLE_BACKEND_PHONETYPE_ALL", desc="Show Phone type", module="Phone type")
     * @Route("/show/{id}", name="backend_settings_custom_phone_type_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppPhoneBundle:PhoneType')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PhoneType entity.');
        }


        return array(
            'entity'      => $entity,
        );
    }

    /**
     * Displays a form to edit an existing PhoneType entity.
     * @Secure(roles="ROLE_BACKEND_PHONETYPE_EDIT")
     * @RoleInfo(role="ROLE_BACKEND_PHONETYPE_EDIT", parent="ROLE_BACKEND_PHONETYPE_ALL", desc="Edit Phone type", module="Phone type")
     * @Route("/edit/{id}", name="backend_settings_custom_phone_type_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppPhoneBundle:PhoneType')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PhoneType entity.');
        }

        $editForm = $this->createForm(new PhoneTypeType(), $entity);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        );
    }

    /**
     * Edits an existing PhoneType entity.
     * @Secure(roles="ROLE_BACKEND_PHONETYPE_EDIT")
     * @Route("/{id}", name="backend_settings_custom_phone_type_update")
     * @Method({"POST","PUT"})
     * @Template("AppPhoneBundle:PhoneType:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppPhoneBundle:PhoneType')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PhoneType entity.');
        }

        $editForm = $this->createForm(new PhoneTypeType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('backend_settings_custom_phone_type_show', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        );
    }

    /**
     * Deletes a PhoneType entity.
     * @Secure(roles="ROLE_BACKEND_PHONETYPE_DELETE")
     * @RoleInfo(role="ROLE_BACKEND_PHONETYPE_DELETE", parent="ROLE_BACKEND_PHONETYPE_ALL", desc="Delete Phone type", module="Phone type")
     * @Route("/delete/{id}", name="backend_settings_custom_phone_type_delete")
     * @Method({"GET","DELETE"})
     */
    public function deleteAction(Request $request, $id)
    {

            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('AppPhoneBundle:PhoneType')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find PhoneType entity.');
            }

            $em->remove($entity);
            $em->flush();


        return $this->redirect($this->generateUrl('backend_settings_custom_phone_type'));
    }
}
