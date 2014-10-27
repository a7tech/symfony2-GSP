<?php

namespace App\SocialMediaBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use App\SocialMediaBundle\Entity\SocialMediaType;
use App\SocialMediaBundle\Form\SocialMediaTypeType;
use JMS\SecurityExtraBundle\Annotation\Secure;
use App\UserBundle\Annotation\RoleInfo;
/**
 * SocialMediaType controller.
 *
 * @Route("/backend/settings/custom/social_media_type")
 * @RoleInfo(role="ROLE_BACKEND_SOCIAL_MEDIA_TYPE_ALL", parent="ROLE_BACKEND_SOCIAL_MEDIA_TYPE_ALL", desc="Social Media types all access", module="Social Media type")
 */
class SocialMediaTypeController extends Controller
{
    /**
     * Lists all SocialMediaType entities.
     * @Secure(roles="ROLE_BACKEND_SOCIAL_MEDIA_TYPE_LIST")
     * @RoleInfo(role="ROLE_BACKEND_SOCIAL_MEDIA_TYPE_LIST", parent="ROLE_BACKEND_SOCIAL_MEDIA_TYPE_ALL", desc="List Social Media types", module="Social Media type")
     * @Route("/", name="backend_settings_custom_social_media_type")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
//        $em = $this->getDoctrine()->getManager();
//
//        $dql   = "SELECT a FROM AppSocialMediaBundle:SocialMediaType a";
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
     * @Secure(roles="ROLE_BACKEND_SOCIAL_MEDIA_TYPE_LIST")
     * @RoleInfo(role="ROLE_BACKEND_SOCIAL_MEDIA_TYPE_LIST", parent="ROLE_BACKEND_SOCIAL_MEDIA_TYPE_ALL", desc="List Social Media types", module="Social Media type")
     * @Route("/datatables", name="backend_social_media_type_datatables")
     * @Method("GET")
     */
    public function indexDatatablesAction(Request $request)
    {
        $filter = $this->get('app_socialmedia.filter.socialmediatypefilter');

        if ($response = $filter->processRequest($request)) {
            return $response;
        }
    }

    /**
     * Creates a new SocialMediaType entity.
     * @Secure(roles="ROLE_BACKEND_SOCIAL_MEDIA_TYPE_CREATE")
     * @Route("/", name="backend_settings_custom_social_media_type_create")
     * @Method("POST")
     * @Template("AppSocialMediaBundle:SocialMediaType:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = new SocialMediaType();
        $form = $this->createForm(new SocialMediaTypeType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('backend_settings_custom_social_media_type_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new SocialMediaType entity.
     * @Secure(roles="ROLE_BACKEND_SOCIAL_MEDIA_TYPE_CREATE")
     * @RoleInfo(role="ROLE_BACKEND_SOCIAL_MEDIA_TYPE_CREATE", parent="ROLE_BACKEND_SOCIAL_MEDIA_TYPE_ALL", desc="Create Social Media type", module="Social Media type")
     * @Route("/new", name="backend_settings_custom_social_media_type_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new SocialMediaType();
        $form   = $this->createForm(new SocialMediaTypeType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a SocialMediaType entity.
     * @Secure(roles="ROLE_BACKEND_SOCIAL_MEDIA_TYPE_SHOW")
     * @RoleInfo(role="ROLE_BACKEND_SOCIAL_MEDIA_TYPE_SHOW", parent="ROLE_BACKEND_SOCIAL_MEDIA_TYPE_ALL", desc="Show Social Media type", module="Social Media type")
     * @Route("/show/{id}", name="backend_settings_custom_social_media_type_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppSocialMediaBundle:SocialMediaType')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find SocialMediaType entity.');
        }


        return array(
            'entity'      => $entity,
        );
    }

    /**
     * Displays a form to edit an existing SocialMediaType entity.
     * @Secure(roles="ROLE_BACKEND_SOCIAL_MEDIA_TYPE_EDIT")
     * @RoleInfo(role="ROLE_BACKEND_SOCIAL_MEDIA_TYPE_EDIT", parent="ROLE_BACKEND_SOCIAL_MEDIA_TYPE_ALL", desc="Edit Social Media type", module="Social Media type")
     *
     * @Route("/edit/{id}", name="backend_settings_custom_social_media_type_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppSocialMediaBundle:SocialMediaType')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find SocialMediaType entity.');
        }

        $editForm = $this->createForm(new SocialMediaTypeType(), $entity);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        );
    }

    /**
     * Edits an existing SocialMediaType entity.
     * @Secure(roles="ROLE_BACKEND_SOCIAL_MEDIA_TYPE_EDIT")
     * @Route("/{id}", name="backend_settings_custom_social_media_type_update")
     * @Method({"POST","PUT"})
     * @Template("AppSocialMediaBundle:SocialMediaType:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppSocialMediaBundle:SocialMediaType')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find SocialMediaType entity.');
        }

        $editForm = $this->createForm(new SocialMediaTypeType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('backend_settings_custom_social_media_type_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        );
    }

    /**
     * Deletes a SocialMediaType entity.
     * @Secure(roles="ROLE_BACKEND_SOCIAL_MEDIA_TYPE_DELETE")
     * @RoleInfo(role="ROLE_BACKEND_SOCIAL_MEDIA_TYPE_DELETE", parent="ROLE_BACKEND_SOCIAL_MEDIA_TYPE_ALL", desc="Delete Social Media type", module="Social Media type")
     * @Route("/delete/{id}", name="backend_settings_custom_social_media_type_delete")
     * @Method({"GET","DELETE"})
     */
    public function deleteAction(Request $request, $id)
    {

            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('AppSocialMediaBundle:SocialMediaType')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find SocialMediaType entity.');
            }

            $em->remove($entity);
            $em->flush();


        return $this->redirect($this->generateUrl('backend_settings_custom_social_media_type'));
    }

}
