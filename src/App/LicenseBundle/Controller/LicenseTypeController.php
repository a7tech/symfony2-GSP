<?php

namespace App\LicenseBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use App\LicenseBundle\Entity\LicenseType;
use App\LicenseBundle\Form\LicenseTypeType;
use JMS\SecurityExtraBundle\Annotation\Secure;
use App\UserBundle\Annotation\RoleInfo;

/**
 * LicenseType controller.
 * @RoleInfo(role="ROLE_BACKEND_LICENSETYPE_ALL", parent="ROLE_BACKEND_LICENSETYPE_ALL", desc="License types all access", module="License type")
 * @Route("/backend/settings/custom/license_type")
 */
class LicenseTypeController extends Controller
{
    /**
     * Lists all LicenseType entities.
     * @Secure(roles="ROLE_BACKEND_LICENSETYPE_LIST")
     * @RoleInfo(role="ROLE_BACKEND_LICENSETYPE_LIST", parent="ROLE_BACKEND_LICENSETYPE_ALL", desc="List License types", module="License type")
     * @Route("/", name="backend_settings_custom_license_type")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
//        $em = $this->getDoctrine()->getManager();
//
//        $dql   = "SELECT a FROM AppLicenseBundle:LicenseType a";
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
     * @Secure(roles="ROLE_BACKEND_LICENSETYPE_LIST")
     * @RoleInfo(role="ROLE_BACKEND_LICENSETYPE_LIST", parent="ROLE_BACKEND_LICENSETYPE_ALL", desc="List License types", module="License type")
     * @Route("/datatables", name="backend_license_type_datatables")
     * @Method("GET")
     */
    public function indexDatatablesAction(Request $request)
    {
        $filter = $this->get('app_license.filter.licensetypefilter');

        if ($response = $filter->processRequest($request)) {
            return $response;
        }
    }

    /**
     * Creates a new LicenseType entity.
     * @Secure(roles="ROLE_BACKEND_LICENSETYPE_CREATE")
     * @Route("/", name="backend_settings_custom_license_type_create")
     * @Method("POST")
     * @Template("AppLicenseBundle:LicenseType:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = new LicenseType();
        $form = $this->createForm(new LicenseTypeType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('backend_settings_custom_license_type_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new LicenseType entity.
     * @Secure(roles="ROLE_BACKEND_LICENSETYPE_CREATE")
     * @RoleInfo(role="ROLE_BACKEND_LICENSETYPE_CREATE", parent="ROLE_BACKEND_LICENSETYPE_ALL", desc="Create License type", module="License type")
     * @Route("/new", name="backend_settings_custom_license_type_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new LicenseType();
        $form   = $this->createForm(new LicenseTypeType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a LicenseType entity.
     * @Secure(roles="ROLE_BACKEND_LICENSETYPE_SHOW")
     * @RoleInfo(role="ROLE_BACKEND_LICENSETYPE_SHOW", parent="ROLE_BACKEND_LICENSETYPE_ALL", desc="Show License type", module="License type")
     * @Route("/show/{id}", name="backend_settings_custom_license_type_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppLicenseBundle:LicenseType')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find LicenseType entity.');
        }

        return array(
            'entity'      => $entity,
        );
    }

    /**
     * Displays a form to edit an existing LicenseType entity.
     * @Secure(roles="ROLE_BACKEND_LICENSETYPE_EDIT")
     * @RoleInfo(role="ROLE_BACKEND_LICENSETYPE_EDIT", parent="ROLE_BACKEND_LICENSETYPE_ALL", desc="Edit License type", module="License type")
     * @Route("/edit/{id}", name="backend_settings_custom_license_type_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppLicenseBundle:LicenseType')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find LicenseType entity.');
        }

        $editForm = $this->createForm(new LicenseTypeType(), $entity);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        );
    }

    /**
     * Edits an existing LicenseType entity.
     * @Secure(roles="ROLE_BACKEND_LICENSETYPE_EDIT")
     * @Route("/{id}", name="backend_settings_custom_license_type_update")
     * @Method({"POST","PUT"})
     * @Template("AppLicenseBundle:LicenseType:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppLicenseBundle:LicenseType')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find LicenseType entity.');
        }


        $editForm = $this->createForm(new LicenseTypeType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('backend_settings_custom_license_type_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        );
    }

    /**
     * Deletes a LicenseType entity.
     * @Secure(roles="ROLE_BACKEND_LICENSETYPE_DELETE")
     * @RoleInfo(role="ROLE_BACKEND_LICENSETYPE_DELETE", parent="ROLE_BACKEND_LICENSETYPE_ALL", desc="Delete License type", module="License type")
     * @Route("/delete/{id}", name="backend_settings_custom_license_type_delete")
     * @Method({"GET","DELETE"})
     */
    public function deleteAction(Request $request, $id)
    {

            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('AppLicenseBundle:LicenseType')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find LicenseType entity.');
            }

            $em->remove($entity);
            $em->flush();


        return $this->redirect($this->generateUrl('backend_settings_custom_license_type'));
    }
}
