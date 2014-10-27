<?php

namespace App\CompanyBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use App\CompanyBundle\Entity\CompanyType;
use App\CompanyBundle\Form\CompanyTypeType;
use JMS\SecurityExtraBundle\Annotation\Secure;
use App\UserBundle\Annotation\RoleInfo;

/**
 * CompanyType controller.
 * @RoleInfo(role="ROLE_BACKEND_COMPANYTYPE_ALL", parent="ROLE_BACKEND_COMPANYTYPE_ALL", desc="Company types all access", module="Company Type")
 * @Route("/backend/settings/custom/company_type")
 */
class CompanyTypeController extends Controller
{
    /**
     * Lists all CompanyType entities.
     * @Secure(roles="ROLE_BACKEND_COMPANYTYPE_LIST")
     * @RoleInfo(role="ROLE_BACKEND_COMPANYTYPE_LIST", parent="ROLE_BACKEND_COMPANYTYPE_ALL", desc="List Company types", module="Company Type")
     * @Route("/", name="backend_settings_custom_company_type")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
//        $em = $this->getDoctrine()->getManager();
//
//        $dql   = "SELECT a FROM AppCompanyBundle:CompanyType a";
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
     * @Secure(roles="ROLE_BACKEND_COMPANYTYPE_LIST")
     * @RoleInfo(role="ROLE_BACKEND_COMPANYTYPE_LIST", parent="ROLE_BACKEND_COMPANYTYPE_ALL", desc="List Company types", module="Company Type")
     * @Route("/datatables", name="backend_company_type_datatables")
     * @Method("GET")
     */
    public function indexDatatablesAction(Request $request)
    {
        $filter = $this->get('app_company.filter.companytypefilter');

        if ($response = $filter->processRequest($request)) {
            return $response;
        }
    }

    /**
     * Creates a new CompanyType entity.
     * @Secure(roles="ROLE_BACKEND_COMPANYTYPE_CREATE")
     *
     * @Route("/", name="backend_settings_custom_company_type_create")
     * @Method("POST")
     * @Template("AppCompanyBundle:CompanyType:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = new CompanyType();
        $form = $this->createForm(new CompanyTypeType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('backend_settings_custom_company_type_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new CompanyType entity.
     * @Secure(roles="ROLE_BACKEND_COMPANYTYPE_CREATE")
     * @RoleInfo(role="ROLE_BACKEND_COMPANYTYPE_CREATE", parent="ROLE_BACKEND_COMPANYTYPE_ALL", desc="Create Company type", module="Company Type")
     * @Route("/new", name="backend_settings_custom_company_type_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new CompanyType();
        $form   = $this->createForm(new CompanyTypeType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a CompanyType entity.
     * @Secure(roles="ROLE_BACKEND_COMPANYTYPE_SHOW")
     * @RoleInfo(role="ROLE_BACKEND_COMPANYTYPE_SHOW", parent="ROLE_BACKEND_COMPANYTYPE_ALL", desc="Show Company type", module="Company Type")
     * @Route("/show/{id}", name="backend_settings_custom_company_type_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppCompanyBundle:CompanyType')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find CompanyType entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing CompanyType entity.
     * @Secure(roles="ROLE_BACKEND_COMPANYTYPE_EDIT")
     * @RoleInfo(role="ROLE_BACKEND_COMPANYTYPE_EDIT", parent="ROLE_BACKEND_COMPANYTYPE_ALL", desc="Edit Company type", module="Company Type")
     * @Route("/edit/{id}", name="backend_settings_custom_company_type_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppCompanyBundle:CompanyType')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find CompanyType entity.');
        }

        $editForm = $this->createForm(new CompanyTypeType(), $entity);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        );
    }

    /**
     * Edits an existing CompanyType entity.
     * @Secure(roles="ROLE_BACKEND_COMPANYTYPE_EDIT")
     * @Route("/{id}", name="backend_settings_custom_company_type_update")
     * @Method({"POST","PUT"})
     * @Template("AppCompanyBundle:CompanyType:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppCompanyBundle:CompanyType')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find CompanyType entity.');
        }


        $editForm = $this->createForm(new CompanyTypeType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('backend_settings_custom_company_type_show', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        );
    }

    /**
     * Deletes a CompanyType entity.
     * @Secure(roles="ROLE_BACKEND_COMPANYTYPE_DELETE")
     * @RoleInfo(role="ROLE_BACKEND_COMPANYTYPE_DELETE", parent="ROLE_BACKEND_COMPANYTYPE_ALL", desc="Delete Company type", module="Company Type")
     * @Route("/delete/{id}", name="backend_settings_custom_company_type_delete")
     * @Method({"GET","DELETE"})
     */
    public function deleteAction(Request $request, $id)
    {

            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('AppCompanyBundle:CompanyType')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find CompanyType entity.');
            }

            $em->remove($entity);
            $em->flush();


        return $this->redirect($this->generateUrl('backend_settings_custom_company_type'));
    }

}
