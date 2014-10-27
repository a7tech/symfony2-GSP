<?php

namespace App\AddressBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use App\AddressBundle\Entity\Province;
use App\AddressBundle\Form\ProvinceType;
use JMS\SecurityExtraBundle\Annotation\Secure;
use App\UserBundle\Annotation\RoleInfo;
/**
 * Province controller.
 *
 * @RoleInfo(role="ROLE_BACKEND_ADDRESS_PROVINCE_ALL", parent="ROLE_BACKEND_ADDRESS_PROVINCE_ALL", desc="Province all access", module="Province")
 * @Route("/backend/settings/province")
 */
class ProvinceController extends Controller
{
    /**
     * Lists all Province entities.
     * @Secure(roles="ROLE_BACKEND_ALL_SETTINGS")
     * @RoleInfo(role="ROLE_BACKEND_ADDRESS_PROVINCE_LIST", parent="ROLE_BACKEND_ADDRESS_PROVINCE_ALL", desc="List provinces", module="Province")
     * @Route("/", name="backend_settings_province")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
//        $em = $this->getDoctrine()->getManager();
//
//        $entities = $em->getRepository('AppAddressBundle:Province')->findAll();

        return array(
            'entities' => [],
        );
    }
    
    /**
     * indexAction
     *
     * @Secure(roles="ROLE_BACKEND_ALL_SETTINGS")
     * @RoleInfo(role="ROLE_BACKEND_ADDRESS_PROVINCE_LIST", parent="ROLE_BACKEND_ADDRESS_PROVINCE_ALL", desc="List provinces", module="Province")
     * @Route("/datatables", name="backend_province_datatables")
     * @Method("GET")
     */
    public function indexDatatablesAction(Request $request)
    {
        $filter = $this->get('app_address.filter.provincefilter');

        if ($response = $filter->processRequest($request)) {
            return $response;
        }
    }

    /**
     * Creates a new Province entity.
     * @Secure(roles="ROLE_BACKEND_ALL_SETTINGS")
     * @RoleInfo(role="ROLE_BACKEND_ADDRESS_PROVINCE_CREATE", parent="ROLE_BACKEND_ADDRESS_PROVINCE_ALL", desc="Create provinces", module="Province")
     * @Route("/", name="backend_settings_province_create")
     * @Method("POST")
     * @Template("AppAddressBundle:Province:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = new Province();
        $form = $this->createForm(new ProvinceType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('backend_settings_province_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new Province entity.
     * @Secure(roles="ROLE_BACKEND_ALL_SETTINGS")
     * @RoleInfo(role="ROLE_BACKEND_ADDRESS_PROVINCE_CREATE", parent="ROLE_BACKEND_ADDRESS_PROVINCE_ALL", desc="Create provinces", module="Province")
     * @Route("/new", name="backend_settings_province_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Province();
        $form   = $this->createForm(new ProvinceType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Province entity.
     * @Secure(roles="ROLE_BACKEND_ALL_SETTINGS")
     * @RoleInfo(role="ROLE_BACKEND_ADDRESS_PROVINCE_SHOW", parent="ROLE_BACKEND_ADDRESS_PROVINCE_ALL", desc="Show provinces", module="Province")
     * @Route("/show/{id}", name="backend_settings_province_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppAddressBundle:Province')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Province entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Province entity.
     * @Secure(roles="ROLE_BACKEND_ALL_SETTINGS")
     * @RoleInfo(role="ROLE_BACKEND_ADDRESS_PROVINCE_EDIT", parent="ROLE_BACKEND_ADDRESS_PROVINCE_ALL", desc="Edit provinces", module="Province")
     * @Route("/edit/{id}", name="backend_settings_province_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppAddressBundle:Province')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Province entity.');
        }

        $editForm = $this->createForm(new ProvinceType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Province entity.
     * @Secure(roles="ROLE_BACKEND_ALL_SETTINGS")
     * @RoleInfo(role="ROLE_BACKEND_ADDRESS_PROVINCE_EDIT", parent="ROLE_BACKEND_ADDRESS_PROVINCE_ALL", desc="Edit provinces", module="Province")
     * @Route("/{id}", name="backend_settings_province_update")
     * @Method("PUT")
     * @Template("AppAddressBundle:Province:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppAddressBundle:Province')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Province entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new ProvinceType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('backend_settings_province_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Province entity.
     * @Secure(roles="ROLE_BACKEND_ALL_SETTINGS")
     * @RoleInfo(role="ROLE_BACKEND_ADDRESS_PROVINCE_DELETE", parent="ROLE_BACKEND_ADDRESS_PROVINCE_ALL", desc="Delete provinces", module="Province")
     * @Route("/delete/{id}", name="backend_settings_province_delete")
     * @Method({"GET","DELETE"})
     */
    public function deleteAction(Request $request, $id)
    {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('AppAddressBundle:Province')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Province entity.');
            }

            $em->remove($entity);
            $em->flush();


        return $this->redirect($this->generateUrl('backend_settings_province'));
    }

    /**
     * Creates a form to delete a Province entity by id.
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
