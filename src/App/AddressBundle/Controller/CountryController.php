<?php

namespace App\AddressBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use App\AddressBundle\Entity\Country;
use App\AddressBundle\Form\CountryType;
use JMS\SecurityExtraBundle\Annotation\Secure;
use App\UserBundle\Annotation\RoleInfo;

/**
 * Country controller.
 * @RoleInfo(role="ROLE_BACKEND_ADDRESS_COUNTRY_ALL", parent="ROLE_BACKEND_ADDRESS_COUNTRY_ALL", desc="Country all access", module="Country")
 * @Route("/backend/settings/country")
 */
class CountryController extends Controller
{
    /**
     * Lists all Country entities.
     * @Secure(roles="ROLE_BACKEND_ALL_SETTINGS")
     * @RoleInfo(role="ROLE_BACKEND_ADDRESS_COUNTRY_LIST", parent="ROLE_BACKEND_ADDRESS_COUNTRY_ALL", desc="List countries", module="Country")
     * @Route("/", name="backend_settings_country")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
//        $em = $this->getDoctrine()->getManager();
//
//        $entities = $em->getRepository('AppAddressBundle:Country')->findAll();

        return array(
            'entities' => [],
        );
    }
    
    /**
     * indexAction
     *
     * @Secure(roles="ROLE_BACKEND_ALL_SETTINGS")
     * @RoleInfo(role="ROLE_BACKEND_ADDRESS_COUNTRY_LIST", parent="ROLE_BACKEND_ADDRESS_COUNTRY_ALL", desc="List countries", module="Country")
     * @Route("/datatables", name="backend_country_datatables")
     * @Method("GET")
     */
    public function indexDatatablesAction(Request $request)
    {
        $filter = $this->get('app_address.filter.countryfilter');

        if ($response = $filter->processRequest($request)) {
            return $response;
        }
    }


    /**
     * Creates a new Country entity.
     * @Secure(roles="ROLE_BACKEND_ALL_SETTINGS")
     * @RoleInfo(role="ROLE_BACKEND_ADDRESS_COUNTRY_CREATE", parent="ROLE_BACKEND_ADDRESS_COUNTRY_ALL", desc="Create countries", module="Country")
     * @Route("/", name="backend_settings_country_create")
     * @Method("POST")
     * @Template("AppAddressBundle:Country:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = new Country();
        $form = $this->createForm(new CountryType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('backend_settings_country_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new Country entity.
     * @Secure(roles="ROLE_BACKEND_ALL_SETTINGS")
     * @RoleInfo(role="ROLE_BACKEND_ADDRESS_COUNTRY_CREATE", parent="ROLE_BACKEND_ADDRESS_COUNTRY_ALL", desc="Create countries", module="Country")
     * @Route("/new", name="backend_settings_country_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Country();
        $form   = $this->createForm(new CountryType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Country entity.
     * @Secure(roles="ROLE_BACKEND_ALL_SETTINGS")
     * @RoleInfo(role="ROLE_BACKEND_ADDRESS_COUNTRY_SHOW", parent="ROLE_BACKEND_ADDRESS_COUNTRY_ALL", desc="Show countries", module="Country")
     * @Route("/show/{id}", name="backend_settings_country_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppAddressBundle:Country')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Country entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Country entity.
     * @Secure(roles="ROLE_BACKEND_ALL_SETTINGS")
     * @RoleInfo(role="ROLE_BACKEND_ADDRESS_COUNTRY_EDOT", parent="ROLE_BACKEND_ADDRESS_COUNTRY_ALL", desc="Edit countries", module="Country")
     * @Route("/edit/{id}", name="backend_settings_country_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppAddressBundle:Country')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Country entity.');
        }

        $editForm = $this->createForm(new CountryType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Country entity.
     * @Secure(roles="ROLE_BACKEND_ALL_SETTINGS")
     * @RoleInfo(role="ROLE_BACKEND_ADDRESS_COUNTRY_EDOT", parent="ROLE_BACKEND_ADDRESS_COUNTRY_ALL", desc="Edit countries", module="Country")
     * @Route("/{id}", name="backend_settings_country_update")
     * @Method({"POST","PUT"})
     * @Template("AppAddressBundle:Country:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppAddressBundle:Country')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Country entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new CountryType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('backend_settings_country_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Country entity.
     * @Secure(roles="ROLE_BACKEND_ALL_SETTINGS")
     * @RoleInfo(role="ROLE_BACKEND_ADDRESS_COUNTRY_DELETE", parent="ROLE_BACKEND_ADDRESS_COUNTRY_ALL", desc="Delete countries", module="Country")
     * @Route("/delete/{id}", name="backend_settings_country_delete")
     * @Method({"GET","DELETE"})
     */
    public function deleteAction(Request $request, $id)
    {


            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('AppAddressBundle:Country')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Country entity.');
            }

            $em->remove($entity);
            $em->flush();


        return $this->redirect($this->generateUrl('backend_settings_country'));
    }

    /**
     * Creates a form to delete a Country entity by id.
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
