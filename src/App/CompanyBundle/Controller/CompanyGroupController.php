<?php

namespace App\CompanyBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use App\CompanyBundle\Entity\CompanyGroup;
use App\CompanyBundle\Form\CompanyGroupType;
use JMS\SecurityExtraBundle\Annotation\Secure;
use App\UserBundle\Annotation\RoleInfo;

/**
 * CompanyGroup controller.
 *
 * @Route("/backend/settings/custom/company_group")
 * @RoleInfo(role="ROLE_BACKEND_COMPANYGROUP_ALL", parent="ROLE_BACKEND_COMPANYGROUP_ALL", desc="Company groups all access", module="Company Group")
 */
class CompanyGroupController extends Controller
{
    /**
     * Lists all CompanyGroup entities.
     * @Secure(roles="ROLE_BACKEND_COMPANYGROUP_LIST")
     * @Route("/", name="backend_settings_custom_company_group")
     * @RoleInfo(role="ROLE_BACKEND_COMPANYGROUP_LIST", parent="ROLE_BACKEND_COMPANYGROUP_ALL", desc="List Company gorups", module="Company Group")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
//        $em = $this->getDoctrine()->getManager();
//
//        $dql   = "SELECT a FROM AppCompanyBundle:CompanyGroup a";
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
     * @Secure(roles="ROLE_BACKEND_COMPANYGROUP_LIST")
     * @RoleInfo(role="ROLE_BACKEND_COMPANYGROUP_LIST", parent="ROLE_BACKEND_COMPANYGROUP_ALL", desc="List Company gorups", module="Company Group")
     * @Route("/datatables", name="backend_company_group_datatables")
     * @Method("GET")
     */
    public function indexDatatablesAction(Request $request)
    {
        $filter = $this->get('app_company.filter.companygroupfilter');

        if ($response = $filter->processRequest($request)) {
            return $response;
        }
    }

    /**
     * Creates a new CompanyGroup entity.
     * @Secure(roles="ROLE_BACKEND_COMPANYGROUP_CREATE")
     *
     * @Route("/", name="backend_settings_custom_company_group_create")
     * @Method("POST")
     * @Template("AppCompanyBundle:CompanyGroup:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = new CompanyGroup();
        $form = $this->createForm(new CompanyGroupType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('backend_settings_custom_company_group_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new CompanyGroup entity.
     * @Secure(roles="ROLE_BACKEND_COMPANYGROUP_CREATE")
     * @RoleInfo(role="ROLE_BACKEND_COMPANYGROUP_CREATE", parent="ROLE_BACKEND_COMPANYGROUP_ALL", desc="Create Company group", module="Company Group")
     * @Route("/new", name="backend_settings_custom_company_group_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new CompanyGroup();
        $form   = $this->createForm(new CompanyGroupType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a CompanyGroup entity.
     * @Secure(roles="ROLE_BACKEND_COMPANYGROUP_SHOW")
     * @RoleInfo(role="ROLE_BACKEND_COMPANYGROUP_SHOW", parent="ROLE_BACKEND_COMPANYGROUP_ALL", desc="Show Company group", module="Company Group")
     * @Route("/show/{id}", name="backend_settings_custom_company_group_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppCompanyBundle:CompanyGroup')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find CompanyGroup entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing CompanyGroup entity.
     * @Secure(roles="ROLE_BACKEND_COMPANYGROUP_EDIT")
     * @RoleInfo(role="ROLE_BACKEND_COMPANYGROUP_EDIT", parent="ROLE_BACKEND_COMPANYGROUP_ALL", desc="Edit Company group", module="Company Group")
     * @Route("/edit/{id}", name="backend_settings_custom_company_group_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppCompanyBundle:CompanyGroup')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find CompanyGroup entity.');
        }

        $editForm = $this->createForm(new CompanyGroupType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing CompanyGroup entity.
     * @Secure(roles="ROLE_BACKEND_COMPANYGROUP_EDIT")
     * @Route("/{id}", name="backend_settings_custom_company_group_update")
     * @Method({"POST","PUT"})
     * @Template("AppCompanyBundle:CompanyGroup:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppCompanyBundle:CompanyGroup')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find CompanyGroup entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new CompanyGroupType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('backend_settings_custom_company_group_show', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a CompanyGroup entity.
     * @Secure(roles="ROLE_BACKEND_COMPANYGROUP_DELETE")
     * @RoleInfo(role="ROLE_BACKEND_COMPANYGROUP_DELETE", parent="ROLE_BACKEND_COMPANYGROUP_ALL", desc="Delete Company group", module="Company Group")
     * @Route("/delete/{id}", name="backend_settings_custom_company_group_delete")
     * @Method({"GET","DELETE"})
     */
    public function deleteAction(Request $request, $id)
    {

            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('AppCompanyBundle:CompanyGroup')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find CompanyGroup entity.');
            }

            $em->remove($entity);
            $em->flush();


        return $this->redirect($this->generateUrl('backend_settings_custom_company_group'));
    }

    /**
     * Creates a form to delete a CompanyGroup entity by id.
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
