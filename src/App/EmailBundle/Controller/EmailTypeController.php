<?php

namespace App\EmailBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use App\EmailBundle\Entity\EmailType;
use App\EmailBundle\Form\EmailTypeType;
use JMS\SecurityExtraBundle\Annotation\Secure;
use App\UserBundle\Annotation\RoleInfo;

/**
 * EmailType controller.
 * @RoleInfo(role="ROLE_BACKEND_EMAILTYPE_ALL", parent="ROLE_BACKEND_EMAILTYPE_ALL", desc="Email types all access", module="Email type")
 * @Route("/backend/settings/custom/email_type")
 */
class EmailTypeController extends Controller
{
    /**
     * Lists all EmailType entities.
     * @Secure(roles="ROLE_BACKEND_EMAILTYPE_LIST")
     * @RoleInfo(role="ROLE_BACKEND_EMAILTYPE_LIST", parent="ROLE_BACKEND_EMAILTYPE_ALL", desc="List Email types", module="Email type")
     * @Route("/", name="backend_settings_custom_email_type")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
//        $em = $this->getDoctrine()->getManager();
//
//        $dql   = "SELECT a FROM AppEmailBundle:EmailType a";
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
     * @Secure(roles="ROLE_BACKEND_EMAILTYPE_LIST")
     * @RoleInfo(role="ROLE_BACKEND_EMAILTYPE_LIST", parent="ROLE_BACKEND_EMAILTYPE_ALL", desc="List Email types", module="Email type")
     * @Route("/datatables", name="backend_email_type_datatables")
     * @Method("GET")
     */
    public function indexDatatablesAction(Request $request)
    {
        $filter = $this->get('app_email.filter.emailtypefilter');

        if ($response = $filter->processRequest($request)) {
            return $response;
        }
    }


    /**
     * Creates a new EmailType entity.
     * @Secure(roles="ROLE_BACKEND_EMAILTYPE_EDIT")
     * @Route("/", name="backend_settings_custom_email_type_create")
     * @Method("POST")
     * @Template("AppEmailBundle:EmailType:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = new EmailType();
        $form = $this->createForm(new EmailTypeType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('backend_settings_custom_email_type_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new EmailType entity.
     * @Secure(roles="ROLE_BACKEND_EMAILTYPE_EDIT")
     * @RoleInfo(role="ROLE_BACKEND_EMAILTYPE_EDIT", parent="ROLE_BACKEND_EMAILTYPE_ALL", desc="Create Email type", module="Email type")
     * @Route("/new", name="backend_settings_custom_email_type_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new EmailType();
        $form   = $this->createForm(new EmailTypeType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a EmailType entity.
     *@Secure(roles="ROLE_BACKEND_EMAILTYPE_SHOW")
     * @RoleInfo(role="ROLE_BACKEND_EMAILTYPE_SHOW", parent="ROLE_BACKEND_EMAILTYPE_ALL", desc="Show Email type", module="Email type")
     * @Route("/show/{id}", name="backend_settings_custom_email_type_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppEmailBundle:EmailType')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find EmailType entity.');
        }

        return array(
            'entity'      => $entity,
        );
    }

    /**
     * Displays a form to edit an existing EmailType entity.
     * @Secure(roles="ROLE_BACKEND_EMAILTYPE_EDIT")
     * @RoleInfo(role="ROLE_BACKEND_EMAILTYPE_EDIT", parent="ROLE_BACKEND_EMAILTYPE_ALL", desc="Edit Email type", module="Email type")
     * @Route("/edit/{id}", name="backend_settings_custom_email_type_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppEmailBundle:EmailType')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find EmailType entity.');
        }

        $editForm = $this->createForm(new EmailTypeType(), $entity);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        );
    }

    /**
     * Edits an existing EmailType entity.
     * @Secure(roles="ROLE_BACKEND_EMAILTYPE_EDIT")
     * @Route("/{id}", name="backend_settings_custom_email_type_update")
     * @Method({"POST","PUT"})
     * @Template("AppEmailBundle:EmailType:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppEmailBundle:EmailType')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find EmailType entity.');
        }

        $editForm = $this->createForm(new EmailTypeType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('backend_settings_custom_email_type_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        );
    }

    /**
     * Deletes a EmailType entity.
     * @Secure(roles="ROLE_BACKEND_EMAILTYPE_DELETE")
     * @RoleInfo(role="ROLE_BACKEND_EMAILTYPE_DELETE", parent="ROLE_BACKEND_EMAILTYPE_ALL", desc="Delete Email type", module="Email type")
     * @Route("/delete/{id}", name="backend_settings_custom_email_type_delete")
     * @Method({"GET","DELETE"})
     */
    public function deleteAction(Request $request, $id)
    {

            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('AppEmailBundle:EmailType')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find EmailType entity.');
            }

            $em->remove($entity);
            $em->flush();


        return $this->redirect($this->generateUrl('backend_settings_custom_email_type'));
    }

}
