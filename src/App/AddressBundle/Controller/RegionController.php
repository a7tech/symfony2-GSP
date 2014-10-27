<?php

namespace App\AddressBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use App\AddressBundle\Entity\Region;
use App\AddressBundle\Form\RegionType;
use JMS\SecurityExtraBundle\Annotation\Secure;
use App\UserBundle\Annotation\RoleInfo;

/**
 * Region controller.
 *
 * @RoleInfo(role="ROLE_BACKEND_ADDRESS_REGION_ALL", parent="ROLE_BACKEND_ADDRESS_REGION_ALL", desc="Region all access", module="Region")
 * @Route("/backend/settings/region")
 */
class RegionController extends Controller
{
    /**
     * Lists all Region entities.
     * @Secure(roles="ROLE_BACKEND_ALL_SETTINGS")
     * @RoleInfo(role="ROLE_BACKEND_ADDRESS_REGION_LIST", parent="ROLE_BACKEND_ADDRESS_REGION_ALL", desc="List regions", module="Region")
     * @Route("/", name="backend_settings_region")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
//        $em = $this->getDoctrine()->getManager();

//        $entities = $em->getRepository('AppAddressBundle:Region')->findAll();

        return array(
            'entities' => [],
        );
    }
    
    /**
     * indexAction
     *
     * @Secure(roles="ROLE_BACKEND_ALL_SETTINGS")
     * @RoleInfo(role="ROLE_BACKEND_ADDRESS_REGION_LIST", parent="ROLE_BACKEND_ADDRESS_REGION_ALL", desc="List regions", module="Region")
     * @Route("/datatables", name="backend_region_datatables")
     * @Method("GET")
     */
    public function indexDatatablesAction(Request $request)
    {
        $filter = $this->get('app_address.filter.regionfilter');

        if ($response = $filter->processRequest($request)) {
            return $response;
        }
    }

    /**
     * Creates a new Region entity.
     * @Secure(roles="ROLE_BACKEND_ALL_SETTINGS")
     * @RoleInfo(role="ROLE_BACKEND_ADDRESS_REGION_CREATE", parent="ROLE_BACKEND_ADDRESS_REGION_ALL", desc="Create regions", module="Region")
     * @Route("/", name="backend_settings_region_create")
     * @Method("POST")
     * @Template("AppAddressBundle:Region:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = new Region();
        $form = $this->createForm(new RegionType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('backend_settings_region_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new Region entity.
     * @Secure(roles="ROLE_BACKEND_ALL_SETTINGS")
     * @RoleInfo(role="ROLE_BACKEND_ADDRESS_REGION_CREATE", parent="ROLE_BACKEND_ADDRESS_REGION_ALL", desc="Create regions", module="Region")
     * @Route("/new", name="backend_settings_region_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Region();
        $form   = $this->createForm(new RegionType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Region entity.
     * @Secure(roles="ROLE_BACKEND_ALL_SETTINGS")
     * @RoleInfo(role="ROLE_BACKEND_ADDRESS_REGION_SHOW", parent="ROLE_BACKEND_ADDRESS_REGION_ALL", desc="Show regions", module="Region")
     * @Route("/show/{id}", name="backend_settings_region_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppAddressBundle:Region')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Region entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Region entity.
     * @Secure(roles="ROLE_BACKEND_ALL_SETTINGS")
     * @RoleInfo(role="ROLE_BACKEND_ADDRESS_REGION_EDIT", parent="ROLE_BACKEND_ADDRESS_REGION_ALL", desc="Edit regions", module="Region")
     * @Route("/edit/{id}", name="backend_settings_region_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppAddressBundle:Region')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Region entity.');
        }
        $editForm = $this->createForm(new RegionType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Region entity.
     * @Secure(roles="ROLE_BACKEND_ALL_SETTINGS")
     * @RoleInfo(role="ROLE_BACKEND_ADDRESS_REGION_EDIT", parent="ROLE_BACKEND_ADDRESS_REGION_ALL", desc="Edit regions", module="Region")
     * @Route("/{id}", name="backend_settings_region_update")
     * @Method("PUT")
     * @Template("AppAddressBundle:Region:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppAddressBundle:Region')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Region entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new RegionType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('backend_settings_region_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Region entity.
     * @Secure(roles="ROLE_BACKEND_ALL_SETTINGS")
     * @RoleInfo(role="ROLE_BACKEND_ADDRESS_REGION_DELETE", parent="ROLE_BACKEND_ADDRESS_REGION_ALL", desc="Delete regions", module="Region")
     * @Route("/delete/{id}", name="backend_settings_region_delete")
     * @Method({"GET","DELETE"})
     */
    public function deleteAction(Request $request, $id)
    {

            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('AppAddressBundle:Region')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Region entity.');
            }

            $em->remove($entity);
            $em->flush();


        return $this->redirect($this->generateUrl('backend_settings_region'));
    }

    /**
     * Creates a form to delete a Region entity by id.
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

    /**
     * @param $countryId
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/ajax_province/countryId={countryId}", name="ajax_province")
     * @Secure(roles="ROLE_BACKEND_ALL_SETTINGS")
     * @RoleInfo(role="ROLE_BACKEND_ADDRESS_REGION_LIST", parent="ROLE_BACKEND_ADDRESS_REGION_ALL", desc="List regions", module="Region")
     */
    public function ajaxProvincesByCountry($countryId) {

        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('AppAddressBundle:Province')->getProvincesByCountries($countryId);

        return new JsonResponse(array('res' => $entities));

    }

    /**
     * @param $countryId
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/ajax_region/provinceId={provinceId}", name="ajax_region")
     * @Secure(roles="ROLE_BACKEND_ALL_SETTINGS")
     */
    public function ajaxRegionsByProvince($provinceId) {

        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('AppAddressBundle:Region')->getRegionsByProvince($provinceId);

        return new JsonResponse(array('res' => $entities));

    }
}
