<?php

namespace App\IndustryBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use App\IndustryBundle\Entity\Sector;
use App\IndustryBundle\Form\SectorType;
use JMS\SecurityExtraBundle\Annotation\Secure;
use App\UserBundle\Annotation\RoleInfo;

/**
 * Sector controller.
 * @RoleInfo(role="ROLE_BACKEND_SECTOR_ALL", parent="ROLE_BACKEND_SECTOR_ALL", desc="Industry Sectors all access", module="Industry Sector")
 * @Route("/backend/settings/industry_sector")
 */
class SectorController extends Controller
{
    /**
     * Lists all Sector entities.
     * @Secure(roles="ROLE_BACKEND_SECTOR_LIST")
     * @RoleInfo(role="ROLE_BACKEND_SECTOR_LIST", parent="ROLE_BACKEND_SECTOR_ALL", desc="List Industry Sectors", module="Industry Sector")
     * @Route("/", name="backend_settings_industry_sector")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
//        $em = $this->getDoctrine()->getManager();
//        $entities = $em->getRepository('AppIndustryBundle:Sector')->findAll();
        return array(
            'entities' => [],
        );
    }
    
    /**
     * indexAction
     *
     * @Secure(roles="ROLE_BACKEND_SECTOR_LIST")
     * @RoleInfo(role="ROLE_BACKEND_SECTOR_LIST", parent="ROLE_BACKEND_SECTOR_ALL", desc="List Industry Sectors", module="Industry Sector")
     * @Route("/datatables", name="backend_sector_datatables")
     * @Method("GET")
     */
    public function indexDatatablesAction(Request $request)
    {
        $filter = $this->get('app_industry.filter.sectorfilter');

        if ($response = $filter->processRequest($request)) {
            return $response;
        }
    }

    /**
     * Creates a new Sector entity.
     * @Secure(roles="ROLE_BACKEND_SECTOR_CREATE")
     * @Route("/", name="backend_settings_industry_sector_create")
     * @Method("POST")
     * @Template("AppIndustryBundle:Sector:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = new Sector();
        $form = $this->createForm(new SectorType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('backend_settings_industry_sector_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new Sector entity.
     * @Secure(roles="ROLE_BACKEND_SECTOR_CREATE")
     * @RoleInfo(role="ROLE_BACKEND_SECTOR_CREATE", parent="ROLE_BACKEND_SECTOR_ALL", desc="Create Industry Sector", module="Industry Sector")
     * @Route("/new", name="backend_settings_industry_sector_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Sector();
        $form   = $this->createForm(new SectorType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Sector entity.
     * @Secure(roles="ROLE_BACKEND_SECTOR_SHOW")
     * @RoleInfo(role="ROLE_BACKEND_SECTOR_SHOW", parent="ROLE_BACKEND_SECTOR_ALL", desc="Show Industry Sector", module="Industry Sector")
     * @Route("/show/{id}", name="backend_settings_industry_sector_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppIndustryBundle:Sector')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Sector entity.');
        }

        return array(
            'entity'      => $entity,
        );
    }

    /**
     * Displays a form to edit an existing Sector entity.
     * @Secure(roles="ROLE_BACKEND_SECTOR_EDIT")
     * @RoleInfo(role="ROLE_BACKEND_SECTOR_EDIT", parent="ROLE_BACKEND_SECTOR_ALL", desc="Edit Industry Sector", module="Industry Sector")
     * @Route("/edit/{id}", name="backend_settings_industry_sector_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppIndustryBundle:Sector')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Sector entity.');
        }

        $editForm = $this->createForm(new SectorType(), $entity);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        );
    }

    /**
     * Edits an existing Sector entity.
     * @Secure(roles="ROLE_BACKEND_SECTOR_EDIT")
     * @Route("/{id}", name="backend_settings_industry_sector_update")
     * @Method("PUT")
     * @Template("AppIndustryBundle:Sector:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppIndustryBundle:Sector')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Sector entity.');
        }

        $editForm = $this->createForm(new SectorType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('backend_settings_industry_sector_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        );
    }

    /**
     * Deletes a Sector entity.
     * @Secure(roles="ROLE_BACKEND_SECTOR_DELETE")
     * @RoleInfo(role="ROLE_BACKEND_SECTOR_DELETE", parent="ROLE_BACKEND_SECTOR_ALL", desc="Delete Industry Sector", module="Industry Sector")
     * @Route("/delete/{id}", name="backend_settings_industry_sector_delete")
     * @Method({"GET","DELETE"})
     */
    public function deleteAction(Request $request, $id)
    {

            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('AppIndustryBundle:Sector')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Sector entity.');
            }

            $em->remove($entity);
            $em->flush();


        return $this->redirect($this->generateUrl('backend_settings_industry_sector'));
    }
}
