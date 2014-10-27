<?php

namespace App\PaymentBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use App\PaymentBundle\Entity\PaymentMethod;
use App\PaymentBundle\Form\PaymentMethodType;
use JMS\SecurityExtraBundle\Annotation\Secure;
use App\UserBundle\Annotation\RoleInfo;

/**
 * PaymentMethod controller.
 * @RoleInfo(role="ROLE_BACKEND_PAYMENTMETHOD_ALL", parent="ROLE_BACKEND_PAYMENTMETHOD_ALL", desc="Payment methods all access", module="Payment method")
 * @Route("/backend/settings/custom/payment_method")
 */
class PaymentMethodController extends Controller
{
    /**
     * Lists all PaymentMethod entities.
     * @Secure(roles="ROLE_BACKEND_PAYMENTMETHOD_LIST")
     * @RoleInfo(role="ROLE_BACKEND_PAYMENTMETHOD_LIST", parent="ROLE_BACKEND_PAYMENTMETHOD_ALL", desc="List Payment methods", module="Payment method")
     * @Route("/", name="backend_settings_custom_payment_method")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
//        $em = $this->getDoctrine()->getManager();
//
//        $entities = $em->getRepository('AppPaymentBundle:PaymentMethod')->findAll();

        return array(
            'entities' => [],
        );
    }
    
    /**
     * indexAction
     *
     * @Secure(roles="ROLE_BACKEND_PAYMENTMETHOD_LIST")
     * @RoleInfo(role="ROLE_BACKEND_PAYMENTMETHOD_LIST", parent="ROLE_BACKEND_PAYMENTMETHOD_ALL", desc="List Payment methods", module="Payment method")
     * @Route("/datatables", name="backend_payment_method_datatables")
     * @Method("GET")
     */
    public function indexDatatablesAction(Request $request)
    {
        $filter = $this->get('app_payment.filter.paymentmethodfilter');

        if ($response = $filter->processRequest($request)) {
            return $response;
        }
    }

    /**
     * Creates a new PaymentMethod entity.
     * @Secure(roles="ROLE_BACKEND_PAYMENTMETHOD_CREATE")
     * @RoleInfo(role="ROLE_BACKEND_PAYMENTMETHOD_CREATE", parent="ROLE_BACKEND_PAYMENTMETHOD_ALL", desc="Create Payment method", module="Payment method")
     * @Route("/create", name="backend_settings_custom_payment_method_create")
     * @Method({"GET","POST"})
     * @Template("AppPaymentBundle:PaymentMethod:create.html.twig")
     */
    public function createAction(Request $request)
    {

        $entity  = new PaymentMethod();
        $form = $this->createForm(new PaymentMethodType(), $entity);


        if ($request->getMethod() == 'POST') {
            $form->bind($request);
            if ($form->isValid()) {

                $em = $this->getDoctrine()->getManager();
                $em->persist($entity);
                $em->flush();

                return $this->redirect($this->generateUrl('backend_settings_custom_payment_method_show', array('id' => $entity->getId())));
            }
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }


    /**
     * Finds and displays a PaymentMethod entity.
     * @Secure(roles="ROLE_BACKEND_PAYMENTMETHOD_SHOW")
     * @RoleInfo(role="ROLE_BACKEND_PAYMENTMETHOD_SHOW", parent="ROLE_BACKEND_PAYMENTMETHOD_ALL", desc="Show Payment method", module="Payment method")
     * @Route("/show/{id}", name="backend_settings_custom_payment_method_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppPaymentBundle:PaymentMethod')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PaymentMethod entity.');
        }

        return array(
            'entity'      => $entity
        );
    }

    /**
     * Displays a form to edit an existing PaymentMethod entity.
     * @Secure(roles="ROLE_BACKEND_PAYMENTMETHOD_EDIT")
     * @RoleInfo(role="ROLE_BACKEND_PAYMENTMETHOD_EDIT", parent="ROLE_BACKEND_PAYMENTMETHOD_ALL", desc="Edit Payment method", module="Payment method")
     * @Route("/edit/{id}", name="backend_settings_custom_payment_method_edit")
     * @Method({"POST","GET"})
     * @Template("AppPaymentBundle:PaymentMethod:edit.html.twig")
     */
    public function editAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('AppPaymentBundle:PaymentMethod')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Person entity.');
        }

        $editForm = $this->createForm(new PaymentMethodType(), $entity);

        if ($request->getMethod() == 'POST') {
            $editForm->bind($request);

            if ($editForm->isValid()) {

                $em->persist($entity);
                $em->flush();

                return $this->redirect($this->generateUrl('backend_settings_custom_payment_method_show', array('id' => $id)));
            }
        }

        return array(
            'entity' => $entity,
            'form'   => $editForm->createView(),
        );
    }


    /**
     * Deletes a PaymentMethod entity.
     * @Secure(roles="ROLE_BACKEND_PAYMENTMETHOD_DELETE")
     * @RoleInfo(role="ROLE_BACKEND_PAYMENTMETHOD_DELETE", parent="ROLE_BACKEND_PAYMENTMETHOD_ALL", desc="Delete Payment method", module="Payment method")
     * @Route("/delete/{id}", name="backend_settings_custom_payment_method_delete")
     * @Method({"GET","DELETE"})
     */
    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('AppPaymentBundle:PaymentMethod')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PaymentMethod entity.');
        }

        $em->remove($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('backend_settings_custom_payment_method'));
    }
}
