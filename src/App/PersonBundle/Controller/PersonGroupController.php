<?php

namespace App\PersonBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use App\PersonBundle\Entity\PersonGroup;
use App\PersonBundle\Form\PersonGroupType;
use JMS\SecurityExtraBundle\Annotation\Secure;
use App\UserBundle\Annotation\RoleInfo;

/**
 * PersonGroup controller.
 * @RoleInfo(role="ROLE_BACKEND_PERSONGROUP_ALL", parent="ROLE_BACKEND_PERSONGROUP_ALL", desc="Contact groups all access", module="Contact group")
 * @Route("/backend/settings/custom/person_group")
 */
class PersonGroupController extends Controller
{
    /**
     * Lists all PersonGroup entities.
     * @Secure(roles="ROLE_BACKEND_PERSONGROUP_LIST")
     * @RoleInfo(role="ROLE_BACKEND_PERSONGROUP_LIST", parent="ROLE_BACKEND_PERSONGROUP_ALL",  desc="List Contact groups", module="Contact group")
     * @Route("/", name="backend_settings_custom_person_group")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('AppPersonBundle:PersonGroup')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    
    /**
     * indexAction
     *
     * @Secure(roles="ROLE_BACKEND_PERSONGROUP_LIST")
     * @RoleInfo(role="ROLE_BACKEND_PERSONGROUP_LIST", parent="ROLE_BACKEND_PERSONGROUP_ALL",  desc="List Contact groups", module="Contact group")
     * @Route("/datatables", name="backend_settings_custom_person_group_datatables")
     * @Method("GET")
     */
    public function indexDatatablesAction(Request $request)
    {
        $filter = $this->get('app_person.filter.persongroupfilter');

        if ($response = $filter->processRequest($request)) {
            return $response;
        }
    }

    /**
     * Creates a new PersonGroup entity.
     *
     * @Route("/", name="backend_settings_custom_person_group_create")
     * @Secure(roles="ROLE_BACKEND_PERSONGROUP_CREATE")
     * @Method("POST")
     * @Template("AppPersonBundle:PersonGroup:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = new PersonGroup();
        $form = $this->createForm(new PersonGroupType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('backend_settings_custom_person_group_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new PersonGroup entity.
     *
     * @Route("/new", name="backend_settings_custom_person_group_new")
     * @Secure(roles="ROLE_BACKEND_PERSONGROUP_CREATE")
     * @RoleInfo(role="ROLE_BACKEND_PERSONGROUP_CREATE", parent="ROLE_BACKEND_PERSONGROUP_ALL", desc="Create Contact group", module="Contact group")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new PersonGroup();
        $form   = $this->createForm(new PersonGroupType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a PersonGroup entity.
     * @Secure(roles="ROLE_BACKEND_PERSONGROUP_SHOW")
     * @RoleInfo(role="ROLE_BACKEND_PERSONGROUP_SHOW", parent="ROLE_BACKEND_PERSONGROUP_ALL", desc="Show Contact group", module="Contact group")
     * @Route("/show/{id}", name="backend_settings_custom_person_group_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppPersonBundle:PersonGroup')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PersonGroup entity.');
        }


        return array(
            'entity'      => $entity,);
    }

    /**
     * Displays a form to edit an existing PersonGroup entity.
     * @Secure(roles="ROLE_BACKEND_PERSONGROUP_EDIT")
     * @RoleInfo(role="ROLE_BACKEND_PERSONGROUP_EDIT", parent="ROLE_BACKEND_PERSONGROUP_ALL", desc="Edit Contact group", module="Contact group")
     * @Route("/edit/{id}", name="backend_settings_custom_person_group_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppPersonBundle:PersonGroup')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PersonGroup entity.');
        }

        $editForm = $this->createForm(new PersonGroupType(), $entity);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        );
    }

    /**
     * Edits an existing PersonGroup entity.
     * @Secure(roles="ROLE_BACKEND_PERSONGROUP_EDIT")
     * @Route("/{id}", name="backend_settings_custom_person_group_update")
     * @Method({"POST","PUT"})
     * @Template("AppPersonBundle:PersonGroup:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppPersonBundle:PersonGroup')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PersonGroup entity.');
        }

        $editForm = $this->createForm(new PersonGroupType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('backend_settings_custom_person_group_show', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        );
    }

    /**
     * Deletes a PersonGroup entity.
     * @Secure(roles="ROLE_BACKEND_PERSONGROUP_DELETE")
     * @RoleInfo(role="ROLE_BACKEND_PERSONGROUP_DELETE", parent="ROLE_BACKEND_PERSONGROUP_ALL", desc="Delete Contact group", module="Contact group")
     * @Route("/delete/{id}", name="backend_settings_custom_person_group_delete")
     * @Method({"GET","DELETE"})
     */
    public function deleteAction(Request $request, $id)
    {

            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('AppPersonBundle:PersonGroup')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find PersonGroup entity.');
            }

            $em->remove($entity);
            $em->flush();


        return $this->redirect($this->generateUrl('backend_settings_custom_person_group'));
    }

}
