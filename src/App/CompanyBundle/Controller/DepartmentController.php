<?php

namespace App\CompanyBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use App\CompanyBundle\Entity\Department;
use App\CompanyBundle\Form\DepartmentType;
use JMS\SecurityExtraBundle\Annotation\Secure;
use App\UserBundle\Annotation\RoleInfo;

/**
 * Department controller.
 * @RoleInfo(role="ROLE_BACKEND_DEPARTMENT_ALL", parent="ROLE_BACKEND_DEPARTMENT_ALL", desc="Departments all access", module="Company Departments")
 * @Route("/backend/settings/custom/department")
 */
class DepartmentController extends Controller
{
    /**
     * Lists all Department entities.
     * @Secure(roles="ROLE_BACKEND_DEPARTMENT_LIST")
     * @RoleInfo(role="ROLE_BACKEND_DEPARTMENT_LIST", parent="ROLE_BACKEND_DEPARTMENT_ALL", desc="List Departments", module="Company Departments")
     * @Route("/", name="backend_settings_custom_department")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $dql   = "SELECT a FROM AppCompanyBundle:Department a";
        $query = $em->createQuery($dql);

        $paginator  = $this->get('knp_paginator');
        $pPaginator = $paginator->paginate(
            $query,
            $this->get('request')->query->get('page', 1),
            10);
        return array(
            'entities' => $pPaginator,
        );
    }

    /**
     * Creates a new Department entity.
     * @Secure(roles="ROLE_BACKEND_DEPARTMENT_CREATE")
     * @Route("/", name="backend_settings_custom_department_create")
     * @Method("POST")
     * @Template("AppCompanyBundle:Department:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = new Department();
        $form = $this->createForm(new DepartmentType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('backend_settings_custom_department_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new Department entity.
     *
     * @Secure(roles="ROLE_BACKEND_DEPARTMENT_CREATE")
     * @RoleInfo(role="ROLE_BACKEND_DEPARTMENT_CREATE", parent="ROLE_BACKEND_DEPARTMENT_ALL", desc="Create Department", module="Company Departments")
     * @Route("/new", name="backend_settings_custom_department_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Department();
        $form   = $this->createForm(new DepartmentType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Department entity.
     *
     * @Route("/show/{id}", name="backend_settings_custom_department_show")
     * @Secure(roles="ROLE_BACKEND_DEPARTMENT_SHOW")
     * @RoleInfo(role="ROLE_BACKEND_DEPARTMENT_SHOW", parent="ROLE_BACKEND_DEPARTMENT_ALL", desc="Show Department", module="Company Departments")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppCompanyBundle:Department')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Department entity.');
        }

        return array(
            'entity'      => $entity,
        );
    }

    /**
     * Displays a form to edit an existing Department entity.
     * @Secure(roles="ROLE_BACKEND_DEPARTMENT_EDIT")
     * @RoleInfo(role="ROLE_BACKEND_DEPARTMENT_EDIT", parent="ROLE_BACKEND_DEPARTMENT_ALL", desc="Edit Department", module="Company Departments")
     * @Route("/edit/{id}", name="backend_settings_custom_department_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppCompanyBundle:Department')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Department entity.');
        }

        $editForm = $this->createForm(new DepartmentType(), $entity);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        );
    }

    /**
     * Edits an existing Department entity.
     * @Secure(roles="ROLE_BACKEND_DEPARTMENT_EDIT")
     * @Route("/{id}", name="backend_settings_custom_department_update")
     * @Method({"POST","PUT"})
     * @Template("AppCompanyBundle:Department:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppCompanyBundle:Department')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Department entity.');
        }

        $editForm = $this->createForm(new DepartmentType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('backend_settings_custom_department_show', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        );
    }

    /**
     * Deletes a Department entity.
     * @Secure(roles="ROLE_BACKEND_DEPARTMENT_DELETE")
     * @RoleInfo(role="ROLE_BACKEND_DEPARTMENT_DELETE", parent="ROLE_BACKEND_DEPARTMENT_ALL", desc="Delete Department", module="Company Departments")
     * @Route("/delete/{id}", name="backend_settings_custom_department_delete")
     * @Method({"GET","DELETE"})
     */
    public function deleteAction(Request $request, $id)
    {

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('AppCompanyBundle:Department')->find($id);

        if (!$entity) {
        throw $this->createNotFoundException('Unable to find Department entity.');
        }

        $em->remove($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('backend_settings_custom_department'));
    }
}
