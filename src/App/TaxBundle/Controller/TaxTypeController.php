<?php

namespace App\TaxBundle\Controller;

use App\CoreBundle\Controller\Controller;
use Doctrine\DBAL\DBALException;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use App\TaxBundle\Entity\TaxType;
use App\TaxBundle\Form\TaxTypeType;
use JMS\SecurityExtraBundle\Annotation\Secure;
use App\UserBundle\Annotation\RoleInfo;

/**
 * TaxType controller.
 * @RoleInfo(role="ROLE_BACKEND_TAX_ALL", parent="ROLE_BACKEND_TAX_ALL", desc="Taxes all access", module="Tax")
 * @Route("/backend/settings/taxes")
 */
class TaxTypeController extends Controller
{
    /**
     * Lists all TaxType entities.
     * @Secure(roles="ROLE_BACKEND_TAX_LIST")
     * @RoleInfo(role="ROLE_BACKEND_TAX_LIST", parent="ROLE_BACKEND_TAX_ALL", desc="List Taxes", module="Tax")
     * @Route("/", name="backend_settings_taxes")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
//        $em = $this->getDoctrine()->getManager();

//        $entities = $em->getRepository('AppTaxBundle:TaxType')->findAll();

        return array(
            'entities' => [],
        );
    }
    
    /**
     * indexAction
     *
     * @Secure(roles="ROLE_BACKEND_TAX_LIST")
     * @RoleInfo(role="ROLE_BACKEND_TAX_LIST", parent="ROLE_BACKEND_TAX_ALL", desc="List Taxes", module="Tax")
     * @Route("/datatables", name="backend_tax_type_datatables")
     * @Method("GET")
     */
    public function indexDatatablesAction(Request $request)
    {
        $filter = $this->get('app_tax.filter.taxtypefilter');

        if ($response = $filter->processRequest($request)) {
            return $response;
        }
    }

    /**
     * Creates a new TaxType entity.
     * @Secure(roles="ROLE_BACKEND_TAX_CREATE")
     * @RoleInfo(role="ROLE_BACKEND_TAX_CREATE", parent="ROLE_BACKEND_TAX_ALL", desc="Create Tax", module="Tax")
     * @Route("/create", name="backend_settings_taxes_create")
     * @Method({"POST", "GET"})
     * @Template("AppTaxBundle:TaxType:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $entity  = new TaxType();
        $form = $this->createForm(new TaxTypeType($em), $entity);

        $request = $this->getRequest();
        if ($request->getMethod() == 'POST') {
            $form->bind($request);

            if ($form->isValid()) {
                $em->persist($entity);
                $em->flush();
                return $this->redirect($this->generateUrl('backend_settings_taxes_show', array('id' => $entity->getId())));
            }
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a TaxType entity.
     * @Secure(roles="ROLE_BACKEND_TAX_SHOW")
     * @RoleInfo(role="ROLE_BACKEND_TAX_SHOW", parent="ROLE_BACKEND_TAX_ALL", desc="Show Tax", module="Tax")
     * @Route("/show/{id}", name="backend_settings_taxes_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppTaxBundle:TaxType')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find TaxType entity.');
        }

        return array(
            'entity'      => $entity
        );
    }

    /**
     * Displays a form to edit an existing TaxType entity.
     * @Secure(roles="ROLE_BACKEND_TAX_EDIT")
     * @RoleInfo(role="ROLE_BACKEND_TAX_EDIT", parent="ROLE_BACKEND_TAX_ALL", desc="Edit Tax", module="Tax")
     * @Route("/edit/{id}", name="backend_settings_taxes_edit")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppTaxBundle:TaxType')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find TaxType entity.');
        }

        $form = $this->createForm(new TaxTypeType($em), $entity);
        $request = $this->getRequest();
        if ($request->getMethod() == 'POST') {
            $form->bind($request);

            if ($form->isValid()) {
                $em->persist($entity);
                $em->flush();
                return $this->redirect($this->generateUrl('backend_settings_taxes_show', array('id' => $entity->getId())));
            }
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $form->createView()
        );
    }

    /**
     * Deletes a TaxType entity.
     * @Secure(roles="ROLE_BACKEND_TAX_DELETE")
     * @RoleInfo(role="ROLE_BACKEND_TAX_DELETE", parent="ROLE_BACKEND_TAX_ALL", desc="Delete Tax", module="Tax")
     * @Route("/delete/{id}", name="backend_settings_taxes_delete")
     * @Method({"GET","DELETE"})
     */
    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('AppTaxBundle:TaxType')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find TaxType entity.');
        }

        try{
            $em->remove($entity);
            $em->flush();
        } catch(DBALException $exception){
            $this->addAdminMessage('Tax "'.((string)$entity).'" cannot be deleted, because it\'s used in application.', 'error');
        }

        return $this->redirect($this->generateUrl('backend_settings_taxes'));
    }
}
