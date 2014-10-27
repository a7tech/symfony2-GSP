<?php

namespace App\InvoiceBundle\Controller;

use App\InvoiceBundle\Entity\Returns\ProductReturnReason;
use App\InvoiceBundle\Entity\Returns\ProductReturnReasonRepository;
use App\InvoiceBundle\Form\Type\Returns\ProductReturnReasonType;
use Doctrine\ORM\ORMException;
use Symfony\Component\HttpFoundation\Request;
use App\CoreBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use App\InvoiceBundle\Entity\SaleOrder;
use JMS\SecurityExtraBundle\Annotation\Secure;
use App\UserBundle\Annotation\RoleInfo;

/**
 * SaleOrder controller.
 * @RoleInfo(role="ROLE_BACKEND_INVOICE_RETURN_REASON_ALL", parent="ROLE_BACKEND_INVOICE_RETURN_REASON_ALL", desc="Return reasons all access", module="Invoice returns")
 * @Route("/backend/sale_order/return-reasons/product")
 */
class ProductReturnReasonController extends Controller
{
    /** @var ProductReturnReasonRepository */
    protected $entityRepository;

    protected function getEntityRepository()
    {
        if($this->entityRepository === null){
            $this->entityRepository = $this->getRepository('AppInvoiceBundle:Returns\ProductReturnReason');
        }

        return $this->entityRepository;
    }

    /**
     * @param null $id
     *
     * @return ProductReturnReason
     */
    protected function getEntity($id = null)
    {
        if($id === null){
            return new ProductReturnReason();
        } else {
            $entity = $this->getEntityRepository()->getById($id);

            if($entity === null) {
                throw $this->createNotFoundException('Unable to find Product Return Reason entity.');
            }

            return $entity;
        }
    }

    /**
     * @Secure(roles="ROLE_BACKEND_INVOICE_RETURN_REASON_LIST")
     * @RoleInfo(role="ROLE_BACKEND_INVOICE_RETURN_REASON_LIST", parent="ROLE_BACKEND_INVOICE_RETURN_REASON_ALL", desc="List return reasons", module="Invoice returns")
     * @Route("/", name="backend_sale_order_product_return_reasons")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $repository = $this->getEntityRepository();
        $entities = $repository->getAll();

        return array(
            'entities' => $entities
        );
    }

    /**
     * @Secure(roles="ROLE_BACKEND_INVOICE_RETURN_REASON_CREATE")
     * @RoleInfo(role="ROLE_BACKEND_INVOICE_RETURN_REASON_CREATE", parent="ROLE_BACKEND_INVOICE_RETURN_REASON_ALL", desc="Create return reason", module="Invoice returns")
     * @Route("/create", name="backend_sale_order_product_return_reasons_create")
     * @Method({"GET","POST"})
     * @Template("AppInvoiceBundle:ProductReturnReason:edit.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = $this->getEntity(null);

        $form = $this->createForm(new ProductReturnReasonType(), $entity);

        if ($request->getMethod() == 'POST') {
            $form->submit($request);

            if ($form->isValid()) {
                $this->save($entity);

                return $this->redirectAfterSave($entity);
            }
        }

        return [
            'entity' => $entity,
            'form'   => $form->createView(),
        ];
    }

    /**
     * @Secure(roles="ROLE_BACKEND_INVOICE_RETURN_REASON_SHOW")
     * @RoleInfo(role="ROLE_BACKEND_INVOICE_RETURN_REASON_SHOW", parent="ROLE_BACKEND_INVOICE_RETURN_REASON_ALL", desc="Show return reason", module="Invoice returns")
     * @Route("/show/{id}", name="backend_sale_order_product_return_reasons_show")
     * @Template()
     */
    public function showAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $this->getEntity($id);

        return array(
            'entity' => $entity,
        );
    }

    /**
     * @Secure(roles="ROLE_BACKEND_INVOICE_RETURN_REASON_EDIT")
     * @RoleInfo(role="ROLE_BACKEND_INVOICE_RETURN_REASON_EDIT", parent="ROLE_BACKEND_INVOICE_RETURN_REASON_ALL", desc="Edit return reason", module="Invoice returns")
     * @Route("/edit/{id}", name="backend_sale_order_product_return_reasons_edit")
     * @Method({"GET", "POST"})
     * @Template("AppInvoiceBundle:ProductReturnReason:edit.html.twig")
     */
    public function editAction(Request $request, $id)
    {
        $entity  = $this->getEntity($id);

        $form = $this->createForm(new ProductReturnReasonType(), $entity);

        if ($request->getMethod() == 'POST') {
            $form->submit($request);

            if ($form->isValid()) {
                $this->save($entity);

                return $this->redirectAfterSave($entity);
            }
        }

        return [
            'entity' => $entity,
            'form'   => $form->createView(),
        ];
    }

    protected function redirectAfterSave(ProductReturnReason $entity)
    {
        return $this->redirect($this->generateUrl('backend_sale_order_product_return_reasons_show', ['id' => $entity->getId()]));
    }

    protected function save(ProductReturnReason $entity)
    {
        $em      = $this->getEntityManager();

        $em->persist($entity);
        $em->flush();
    }

    /**
     * Deletes a SaleOrder entity.
     * @Secure(roles="ROLE_BACKEND_INVOICE_RETURN_REASON_DELETE")
     * @RoleInfo(role="ROLE_BACKEND_INVOICE_RETURN_REASON_DELETE", parent="ROLE_BACKEND_INVOICE_RETURN_REASON_ALL", desc="Delete return reasons", module="Invoice returns")
     * @Route("/delete/{id}", name="backend_sale_order_product_return_reasons_delete")
     * @Method({"DELETE", "GET"})
     */
    public function deleteAction(Request $request, $id)
    {
        $em     = $this->getDoctrine()->getManager();
        /** @var SaleOrder $entity */
        $entity = $this->getEntity($id);

        try {
            $em->remove($entity);
            $em->flush();
        } catch(ORMException $e) {
            $this->addAdminMessage($this->get('translator')->trans('cannot_remove_entity', ['name' => $entity->__toString()], 'Backend'), 'error');
        }

        return $this->redirect($this->generateUrl('backend_sale_order_product_return_reasons'));
    }
}