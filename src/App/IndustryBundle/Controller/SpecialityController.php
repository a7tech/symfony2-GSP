<?php

namespace App\IndustryBundle\Controller;


use App\IndustryBundle\Form\SpecialityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use App\IndustryBundle\Entity\Speciality;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use JMS\SecurityExtraBundle\Annotation\Secure;
use App\UserBundle\Annotation\RoleInfo;

/**
 * Speciality controller.
 * @RoleInfo(role="ROLE_BACKEND_SPECIALITY_ALL", parent="ROLE_BACKEND_SPECIALITY_ALL", desc="Industry Specialities all access", module="Industry Speciality")
 * @Route("/backend/settings/industry_speciality")
 */
class SpecialityController extends Controller
{
    /**
     * Lists all Speciality entities.
     * @Secure(roles="ROLE_BACKEND_SPECIALITY_LIST")
     * @RoleInfo(role="ROLE_BACKEND_SPECIALITY_LIST", parent="ROLE_BACKEND_SPECIALITY_ALL", desc="List Industry Specialities", module="Industry Speciality")
     * @Route("/", name="backend_settings_industry_speciality")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
//        $em = $this->getDoctrine()->getManager();
//        $entities = $em->getRepository('AppIndustryBundle:Speciality')->findAll();
        return array(
            'entities' => [],
        );
    }
    
    /**
     * indexAction
     *
     * @Secure(roles="ROLE_BACKEND_SPECIALITY_LIST")
     * @RoleInfo(role="ROLE_BACKEND_SPECIALITY_LIST", parent="ROLE_BACKEND_SPECIALITY_ALL", desc="List Industry Specialities", module="Industry Speciality")
     * @Route("/datatables", name="backend_speciality_datatables")
     * @Method("GET")
     */
    public function indexDatatablesAction(Request $request)
    {
        $filter = $this->get('app_industry.filter.specialityfilter');

        if ($response = $filter->processRequest($request)) {
            return $response;
        }
    }

    /**
     * Finds and displays a Speciality entity.
     * @Secure(roles="ROLE_BACKEND_SPECIALITY_SHOW")
     * @RoleInfo(role="ROLE_BACKEND_SPECIALITY_SHOW", parent="ROLE_BACKEND_SPECIALITY_ALL", desc="Show Industry Speciality", module="Industry Speciality")
     * @Route("/show/{id}", name="backend_settings_industry_speciality_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppIndustryBundle:Speciality')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Speciality entity.');
        }

        return array(
            'entity'      => $entity,
        );
    }

    /**
     * Creates a new Speciality entity.
     * @Secure(roles="ROLE_BACKEND_SPECIALITY_CREATE")
     * @Route("/", name="backend_settings_industry_speciality_create")
     * @Method("POST")
     * @Template("AppIndustryBundle:Speciality:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = new Speciality();
        $form = $this->createForm(new SpecialityType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('backend_settings_industry_speciality_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new Speciality entity.
     * @Secure(roles="ROLE_BACKEND_SPECIALITY_CREATE")
     * @RoleInfo(role="ROLE_BACKEND_SPECIALITY_CREATE", parent="ROLE_BACKEND_SPECIALITY_ALL", desc="Create Industry Speciality", module="Industry Speciality")
     * @Route("/new", name="backend_settings_industry_speciality_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Speciality();
        $form   = $this->createForm(new SpecialityType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Speciality entity.
     * @Secure(roles="ROLE_BACKEND_SPECIALITY_EDIT")
     * @RoleInfo(role="ROLE_BACKEND_SPECIALITY_EDIT", parent="ROLE_BACKEND_SPECIALITY_ALL", desc="Edit Industry Speciality", module="Industry Speciality")
     * @Route("/edit/{id}", name="backend_settings_industry_speciality_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppIndustryBundle:Speciality')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Speciality entity.');
        }

        $editForm = $this->createForm(new SpecialityType(), $entity);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        );
    }

    /**
     * Edits an existing Speciality entity.
     * @Secure(roles="ROLE_BACKEND_SPECIALITY_EDIT")
     * @Route("/update/{id}", name="backend_settings_industry_speciality_update")
     * @Method("POST")
     * @Template("AppIndustryBundle:Speciality:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppIndustryBundle:Speciality')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Speciality entity.');
        }

        $editForm = $this->createForm(new SpecialityType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('backend_settings_industry_speciality_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        );
    }

    /**
     * Deletes a Speciality entity.
     * @Secure(roles="ROLE_BACKEND_SPECIALITY_DELETE")
     * @RoleInfo(role="ROLE_BACKEND_SPECIALITY_DELETE", parent="ROLE_BACKEND_SPECIALITY_ALL", desc="Delete Industry Speciality", module="Industry Speciality")
     * @Route("/delete/{id}", name="backend_settings_industry_speciality_delete")
     * @Method({"GET","DELETE"})
     */
    public function deleteAction(Request $request, $id)
    {

            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('AppIndustryBundle:Speciality')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Speciality entity.');
            }

            $em->remove($entity);
            $em->flush();


        return $this->redirect($this->generateUrl('backend_settings_industry_speciality'));
    }

    /**
     * @Secure(roles="ROLE_BACKEND_ALL_SETTINGS")
     * @param $countryId
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/ajax_speciality/sectorId={sectorId}", name="ajax_speciality")
     */
    public function ajaxSpecialityBySector($sectorId) {

        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('AppIndustryBundle:Speciality')->getSpecialityBySector($sectorId);

        return new JsonResponse(array('res' => $entities));

    }

}
