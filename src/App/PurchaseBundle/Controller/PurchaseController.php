<?php

namespace App\PurchaseBundle\Controller;

use App\AccountBundle\Utils\ProductSearch;
use App\AccountProductBundle\Entity\AccountProduct;
use App\AccountProductBundle\Entity\AccountProductRepository;
use App\CoreBundle\Controller\Controller;
use App\InvoiceBundle\Entity\SaleOrder;
use App\PurchaseBundle\Entity\Purchase;
use App\PurchaseBundle\Entity\PurchaseRepository;
use App\PurchaseBundle\Form\PurchaseWrapper;
use App\PurchaseBundle\Form\Type\NewPurchaseType;
use App\PurchaseBundle\Form\Type\ProductSearchType;
use App\PurchaseBundle\Form\Type\PurchasePaymentsType;
use App\PurchaseBundle\Form\Type\PurchaseType;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use JMS\SecurityExtraBundle\Annotation\Secure;
use App\UserBundle\Annotation\RoleInfo;

/**
 * Class PurchaseController
 * @package App\PurchaseBundle\Controller
 * @RoleInfo(role="ROLE_BACKEND_PURCHASE_ALL", parent="ROLE_BACKEND_PURCHASE_ALL", desc="Purchase orders all access", module="Purchase order")
 * @Route("/backend/orders")
 */
class PurchaseController extends Controller
{
    /**
     * @var PurchaseRepository|null
     */
    protected $repository;

    /**
     * @return PurchaseRepository
     */
    protected function getEntityRepository()
    {
        if($this->repository === null){
            $this->repository = $this->getRepository('AppPurchaseBundle:Purchase');
        }

        return $this->repository;
    }


    /**
     * @Route("/", name="backend_purchase")
     * @Template
     * @Secure(roles="ROLE_BACKEND_PURCHASE_LIST")
     * @RoleInfo(role="ROLE_BACKEND_PURCHASE_LIST", parent="ROLE_BACKEND_PURCHASE_ALL", desc="List Purchase orders", module="Purchase order")
     */
    public function indexAction()
    {
//        $entities = $this->getEntityRepository()->getAll();

        return [
            'entities' => []
        ];
    }
    
    /**
     * indexAction
     *
     * @Secure(roles="ROLE_BACKEND_PURCHASE_LIST")
     * @RoleInfo(role="ROLE_BACKEND_PURCHASE_LIST", parent="ROLE_BACKEND_PURCHASE_ALL", desc="List Purchase orders", module="Purchase order")
     * @Route("/datatables", name="backend_purchase_datatables")
     * @Method("GET")
     */
    public function indexDatatablesAction(Request $request)
    {
        $filter = $this->get('app_purchase.filter.purchasefilter');

        if ($response = $filter->processRequest($request)) {
            return $response;
        }
    }

    /**
     * @Route("/create", name="backend_purchase_create")
     * @Template("AppPurchaseBundle:Purchase:edit.html.twig")
     * @Secure(roles="ROLE_BACKEND_PURCHASE_CREATE")
     * @RoleInfo(role="ROLE_BACKEND_PURCHASE_CREATE", parent="ROLE_BACKEND_PURCHASE_ALL", desc="Create Purchase order", module="Purchase order")
     */
    public function createAction(Request $request)
    {
        $purchase = new Purchase($this->getUser());

        $purchase_wrapper = new PurchaseWrapper($purchase, $this->getEntityManager());
        $form = $this->createForm('backend_purchase', $purchase_wrapper);

        $search_form = $this->createForm(new ProductSearchType(), []);

        if($request->getMethod() == 'POST'){
            $form->submit($this->getRequest());

            if($form->isValid()){
                $this->save($purchase, $purchase_wrapper);

                return $this->redirect($this->generateUrl('backend_purchase_show', [
                    'id' => $purchase->getId()
                ]));
            }
        }

        return [
            'search_form' => $search_form->createView(),
            'form' => $form->createView(),
            'entity' => $purchase
        ];
    }

    /**
     * @Route("/edit/{id}", name="backend_purchase_edit", requirements={"id" = "\d+"})
     * @Template
     * @Secure(roles="ROLE_BACKEND_PURCHASE_EDIT")
     * @RoleInfo(role="ROLE_BACKEND_PURCHASE_EDIT", parent="ROLE_BACKEND_PURCHASE_ALL", desc="Edit Purchase order", module="Purchase order")
     */
    public function editAction(Request $request, $id)
    {
        /** @var Purchase $purchase */
        $purchase = $request->get('entity');

        if($purchase === null){
            $purchase = $this->getEntityRepository()->getById($id);
        }

        if($purchase === null){
            throw new NotFoundHttpException();
        }

        $purchase_wrapper = new PurchaseWrapper($purchase, $this->getEntityManager());
        $form = $this->createForm('backend_purchase', $purchase_wrapper);

        $search_form = $this->createForm(new ProductSearchType(), []);

        if($request->getMethod() == 'POST'){
            $form->submit($request);

            if($form->isValid()){
                $this->save($purchase, $purchase_wrapper);

                return $this->redirect($this->generateUrl('backend_purchase_show', [
                    'id' => $purchase->getId()
                ]));
            }
        }

        return [
            'search_form' => $search_form->createView(),
            'form' => $form->createView(),
            'entity' => $purchase
        ];
    }

    protected function save(Purchase $purchase, PurchaseWrapper $purchaseWrapper)
    {
        $purchases = $purchaseWrapper->getPurchases();
        $entity_manager = $this->getEntityManager();
        $extracted_purchases = [];


        foreach($purchases as $saved_purchase){
            $entity_manager->persist($saved_purchase);

            if($saved_purchase !== $purchase){
                $extracted_purchases[] = $saved_purchase;
            }
        }

        $entity_manager->flush();

        foreach($extracted_purchases as $extracted_purchase){
            $url = $this->generateUrl('backend_purchase_show', ['id' => $extracted_purchase->getId()]);
            $this->addAdminMessage('<a href="'.$url.'"> New Purchase</a> was extracted.');
        }
    }

    /**
     * @Route("/show/{id}", name="backend_purchase_show", requirements={"id" = "\d+"})
     * @Template
     * @Secure(roles="ROLE_BACKEND_PURCHASE_SHOW")
     * @RoleInfo(role="ROLE_BACKEND_PURCHASE_SHOW", parent="ROLE_BACKEND_PURCHASE_ALL", desc="Show Purchase order", module="Purchase order")
     */
    public function showAction(Request $request, $id)
    {
        /** @var SaleOrder $entity */
        $entity = $this->getEntityRepository()->getById($id);

        if($entity === null){
            throw new NotFoundHttpException();
        }

        $form = $this->createForm(new PurchasePaymentsType(), $entity);

        if($request->getMethod() == 'POST'){
            $form->submit($request);
            if($form->isValid()){
                $entity_manager = $this->getEntityManager();

                $entity_manager->persist($entity);
                $entity_manager->flush();
            }
        }


        return [
            'entity' => $entity,
            'form' => $form->createView()
        ];
    }

    /**
     * @Route("/delete/{id}", name="backend_purchase_delete", requirements={"id" = "\d+"})
     * @Template
     * @Secure(roles="ROLE_BACKEND_PURCHASE_DELETE")
     * @RoleInfo(role="ROLE_BACKEND_PURCHASE_DELETE", parent="ROLE_BACKEND_PURCHASE_ALL", desc="Delete Purchase order", module="Purchase order")
     */
    public function deleteAction(Request $request, $id)
    {
        $entity = $this->getEntityRepository()->getById($id);

        if($entity === null){
            throw new NotFoundHttpException();
        }

        $entity_manager = $this->getEntityManager();
        $entity_manager->remove($entity);
        $entity_manager->flush();

        $this->addAdminMessage('Purchase deleted');

        return $this->redirect($this->generateUrl('backend_purchase'));
    }

    /**
     * @Route("/search-products", name="backend_purchase_products_search")
     * @Template
     */
    public function productsSearchAction(Request $request)
    {
        $account_profile_id = $request->get('account_profile', null);

        if($account_profile_id === null){
            throw new NotFoundHttpException();
        }

        $account_profile = $this->getRepository('AppAccountBundle:AccountProfile')->getById($account_profile_id);

        if($account_profile === null){
            throw new NotFoundHttpException();
        }

        /** @var AccountProductRepository $account_product_repository */
        $account_product_repository = $this->getRepository('AppAccountProductBundle:AccountProduct');

        $search_parameters = $request->get('backend_purchase_search');

        /** @var QueryBuilder $query_builder */
        $query_builder = $account_product_repository->getAccountQueryBuilder($account_profile);

        $account_product_query_builder_filter = new ProductSearch($account_product_repository);
        $account_product_query_builder_filter->filter($query_builder, $search_parameters);

        $results_per_page = 20;
        $page = $request->get('page', 1);

        $paginator = $this->get('knp_paginator');

        $pagination = $paginator->paginate($query_builder, $page, $results_per_page);
        $pagination->setParam('backend_purchase_search', $search_parameters);
        $pagination->setParam('account_profile', $account_profile_id);

        return [
            'pagination' => $pagination
        ];
    }
}
