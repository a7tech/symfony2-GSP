<?php

namespace App\InvoiceBundle\Controller;

use App\AccountBundle\Entity\AccountProfile;
use App\AccountBundle\Utils\ProductSearch;
use App\CoreBundle\Utils\ObjectsUtils;
use App\InvoiceBundle\Form\Type\SaleOrderPaymentsType;
use App\ProjectBundle\Entity\ProjectManager;
use App\PurchaseBundle\Form\Type\ProductSearchType;
use Symfony\Component\HttpFoundation\Request;
use App\CoreBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use App\InvoiceBundle\Entity\SaleOrder;
use App\InvoiceBundle\Form\SaleOrderType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use JMS\SecurityExtraBundle\Annotation\Secure;
use App\UserBundle\Annotation\RoleInfo;

/**
 * SaleOrder controller.
 * @RoleInfo(role="ROLE_BACKEND_INVOICE_ALL", parent="ROLE_BACKEND_INVOICE_ALL", desc="Inoices all access", module="Invoice")
 * @Route("/backend/sale_order")
 */
class SaleOrderController extends Controller
{

    /**
     * Lists all SaleOrder entities.
     * @Secure(roles="ROLE_BACKEND_INVOICE_LIST")
     * @RoleInfo(role="ROLE_BACKEND_INVOICE_LIST", parent="ROLE_BACKEND_INVOICE_ALL", desc="List Invoices", module="Invoice")
     * @Route("/", name="backend_sale_order")
     * @Route("/all-projects", name="backend_projects_sale_order", defaults={"all_projects" = true})
     * @Route("/project/{project_id}/", name="backend_project_sale_order")
     * @Method("GET")
     * @Template()
     */
    public function indexAction($project_id = null, $all_projects = false)
    {
        $repository = $this->getRepository('AppInvoiceBundle:SaleOrder');
        $project    = null;

        if ($project_id !== null) {
            $project  = $this->getRepository('AppProjectBundle:Project')->getById($project_id);
//            $entities = $repository->getAllByProject($project, true);
        } elseif ($all_projects === true) {
//            $entities = $repository->getAllProjectInvoices(true);
        } else {
//            $entities = $repository->getAll();
        }

        return array(
            'project'       => $project,
            'all_projects'  => $all_projects,
            'statuses'      => $this->get('app_status.translator')->getStatuses(SaleOrder::STATUSES_GROUP),
            'entities'      => [],
        );
    }
    
    /**
     * indexAction
     *
     * @Secure(roles="ROLE_BACKEND_INVOICE_LIST")
     * @RoleInfo(role="ROLE_BACKEND_INVOICE_LIST", parent="ROLE_BACKEND_INVOICE_ALL", desc="List Invoices", module="Invoice")
     * @Route("/datatables", name="backend_sale_order_datatables")
     * @Method("GET")
     */
    public function indexDatatablesAction(Request $request)
    {
        $filter = $this->get('app_invoice.filter.taskfilter');

        if ($response = $filter->processRequest($request)) {
            return $response;
        }
    }

    /**
     * Creates a new SaleOrder entity.
     * @Secure(roles="ROLE_BACKEND_INVOICE_CREATE")
     * @RoleInfo(role="ROLE_BACKEND_INVOICE_CREATE", parent="ROLE_BACKEND_INVOICE_ALL", desc="Create Invoice", module="Invoice")
     * @Route("/create", name="backend_sale_order_create")
     * @Route("/project/{project_id}/create", name="backend_project_sale_order_create")
     * @Route("/project/{project_id}/create-credit", name="backend_project_sale_order_create_credit", defaults={"credit"=true})
     * @Method({"GET","POST"})
     * @Template("AppInvoiceBundle:SaleOrder:create.html.twig")
     */
    public function createAction(Request $request, $project_id = null, $credit = false)
    {
        $em      = $this->getDoctrine()->getManager();
        $entity  = new SaleOrder();

        if($credit === true){
            $entity->setIsCredit(true);
        }

        $project = null;
        if ($project_id !== null) {
            $project = $this->getRepository('AppProjectBundle:Project')->getById($project_id);

            if ($project === null) {
                throw new NotFoundHttpException('Project doesn\'t exist');
            }

            $entity->setProject($project);
        }
        $form = $this->createForm(new SaleOrderType($em), $entity);

        $is_project_invoice = $entity->getProject() !== null;
        $search_form        = null;
        if (!$is_project_invoice) {
            $search_form = $this->createForm(new ProductSearchType(), []);
        }

        if ($request->getMethod() == 'POST') {
            $form->submit($request);

            if ($form->isValid()) {
                $this->save($entity);

                return $this->redirectAfterSave($entity);
            }
        }

        $tasks_categories = null;
        if ($is_project_invoice === true && $entity->getProjectCategory() !== null) {
            $tasks_categories = $entity->getProject()->getCategories(true, $this->getEntityManager());
        }

        return $this->render('AppInvoiceBundle:SaleOrder:create.html.twig', array(
                    'entity'             => $entity,
                    'form'               => $form->createView(),
                    'is_project_invoice' => $is_project_invoice,
                    'tasks_categories'   => $tasks_categories,
                    'search_form'        => $search_form !== null ? $search_form->createView() : null,
                    'project'            => $project
        ));
    }

    /**
     * Finds and displays a SaleOrder entity.
     * @Secure(roles="ROLE_BACKEND_INVOICE_SHOW")
     * @RoleInfo(role="ROLE_BACKEND_INVOICE_SHOW", parent="ROLE_BACKEND_INVOICE_ALL", desc="Show Invoices", module="Invoice")
     * @Route("/show/{id}", name="backend_sale_order_show")
     * @Route("/project/{project_id}/show/{id}", name="backend_project_sale_order_show")
     * @Template()
     */
    public function showAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var SaleOrder $entity */
        $entity = $em->getRepository('AppInvoiceBundle:SaleOrder')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find SaleOrder entity.');
        }

        $project = $entity->getProject();
        $form      = $this->createForm(new SaleOrderPaymentsType(), $entity);
        $goto_form = false;

        if ($request->getMethod() == 'POST') {
            $old_payments = $entity->getPayments()->toArray();

            $form->submit($request);
            $payments_to_remove = ObjectsUtils::getEntitiesToRemove($old_payments, $entity->getPayments()->toArray());

            if ($form->isValid()) {
                $entity_manager = $this->getEntityManager();

                foreach ($payments_to_remove as $payment) {
                    $entity_manager->remove($payment);
                }

                $entity_manager->persist($entity);
                $entity_manager->flush();
            } else {
                $goto_form = true;
            }
        }

        $tasks_categories = $entity->getTasksCategories($this->getEntityManager());


        $invoiceTasksById = [];
        foreach($entity->getTasks() as $task){
            $invoiceTasksById[$task->getTask()->getId()] = $task;
        }


        return array(
            'entity'           => $entity,
            'tasks_categories' => $tasks_categories,
            'form'             => $form->createView(),
            'project'          => $project,
            'goto_form'        => $goto_form,
            'invoice_tasks'     => $invoiceTasksById
        );
    }

    /**
     * Finds and displays a SaleOrder entity.
     * @Secure(roles="ROLE_BACKEND_INVOICE_PRINT")
     * @RoleInfo(role="ROLE_BACKEND_INVOICE_PRINT", parent="ROLE_BACKEND_INVOICE_ALL", desc="Print Invoices", module="Invoice")
     * @Route("/print/{id}", name="backend_sale_order_print")
     * @Route("/project/{project_id}/print/{id}", name="backend_project_sale_order_print")
     */
    public function printAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var SaleOrder $entity */
        $entity = $em->getRepository('AppInvoiceBundle:SaleOrder')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find SaleOrder entity.');
        }

        $project          = $entity->getProject();
        $tasks_categories = $entity->getTasksCategories($this->getEntityManager());

        $invoiceTasksById = [];
        foreach($entity->getTasks() as $task){
            $invoiceTasksById[$task->getTask()->getId()] = $task;
        }

        $html = $this->renderView("AppInvoiceBundle:SaleOrder:print.html.twig", [
            'entity'           => $entity,
            'tasks_categories' => $tasks_categories,
            'project'          => $project,
            'invoice_tasks'     => $invoiceTasksById
        ]);
        
        $pdf = $this->container->get("white_october.tcpdf")->create('P', 'pt', 'USLETTER', true, 'UTF-8', false);
        
        // set document information
        $pdf->SetCreator("GSP");
        $pdf->SetAuthor('GSP');
        $pdf->SetTitle('GSP invoice Id ' . $entity->getId());

        // set default header data
        //$pdf->SetHeaderData(null, null, null, '', null, array(0,64,255), array(0,64,128));
        $pdf->setPrintHeader(false);
        $pdf->setFooterData(array(179, 178, 178), array(221, 221, 221)); //#b3b2b2 RGB=179,178,178 #dddddd 221, 221, 221
        // set header and footer fonts
        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // set margins
        $pdf->SetMargins(28, 28, 28, true);
//        $pdf->SetLeftMargin(28);
        $pdf->SetRightMargin(28);
        $pdf->SetFooterMargin(28);

        // set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // set default font subsetting mode
        $pdf->setFontSubsetting(true);

        // Set font
        // dejavusans is a UTF-8 Unicode font, if you only need to
        // print standard ASCII chars, you can use core fonts like
        // helvetica or times to reduce file size.
        //$pdf->SetFont('dejavusans', '', 10, '', true);
        $pdf->SetFont('helvetica', '', 10, '', true);

        // Add a page
        // This method has several options, check the source code documentation for more information.
        $pdf->AddPage();

//        $pdf->resetHeaderTemplate();
        // set text shadow effect
        //$pdf->setTextShadow(array('enabled'=>true, 'depth_w'=>0.2, 'depth_h'=>0.2, 'color'=>array(196,196,196), 'opacity'=>1, 'blend_mode'=>'Normal'));

        $pdf->setPrintHeader(false);
        $pdf->SetMargins(28, 28, 28, true);

        $pdf->writeHTML($html, true, false, true, false, '');


        $customer = $entity->getCustomerCompanyName();
        if ($customer === null) {
            $customer = $entity->getCustomerName();
        }

        $name = ($entity->getIsDraft() ? 'Draft' : 'Invoice') . '_' . $entity->getId() . '_' . $customer;

        $project = $entity->getProject();
        if ($project !== null) {
            $name .= "_" . $project->getName() . '_' . $project->getId();
        }

        // Close and output PDF document
        // This method has several options, check the source code documentation for more information.
        $pdf->Output($name . '.pdf', 'I');
        return;
    }

    /**
     * Displays a form to edit an existing SaleOrder entity.
     * @Secure(roles="ROLE_BACKEND_INVOICE_EDIT")
     * @RoleInfo(role="ROLE_BACKEND_INVOICE_EDIT", parent="ROLE_BACKEND_INVOICE_ALL", desc="Edit Invoices", module="Invoice")
     * @Route("/edit/{id}", name="backend_sale_order_edit")
     * @Route("/project/{project_id}/edit/{id}", name="backend_project_sale_order_edit")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function editAction($id)
    {
        $em              = $this->getDoctrine()->getManager();
        /** @var SaleOrder $entity */
        $entity          = $em->getRepository('AppInvoiceBundle:SaleOrder')->find($id);
        $project         = $entity->getProject();
        $customer        = $entity->getCustomer();
        $customerCompany = $entity->getCustomerCompany();
        $form            = $this->createForm(new SaleOrderType($em, $customer, $customerCompany), $entity);

        $is_project_invoice = $entity->getProject() !== null;
        $search_form        = null;
        if (!$is_project_invoice) {
            $search_form = $this->createForm(new ProductSearchType(), []);
        }

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find SaleOrder entity.');
        }

        if (!$entity->getIsDraft() == true) {
            return $this->redirect($this->generateUrl('backend_sale_order_show', ['id' => $entity->getId()]));
        }

        if($entity->getCorrectionOf() !== null){
            return $this->redirectToRoute('backend_sale_order_returns_edit', ['id' => $entity->getId()]);
        }

        $request     = $this->getRequest();
        $oldProducts = $entity->getProducts()->toArray();
        $oldTasks    = $entity->getTasks()->toArray();
        if ($request->getMethod() == 'POST') {
            $form->submit($request);

            if ($form->isValid()) {

                $this->save($entity, $oldProducts, $oldTasks);


                return $this->redirectAfterSave($entity);
            } else {
                var_dump($form->getErrorsAsString());
            }
        }

        return $this->render('AppInvoiceBundle:SaleOrder:create.html.twig', array(
                    'entity'             => $entity,
                    'form'               => $form->createView(),
                    'is_project_invoice' => $is_project_invoice,
                    'search_form'        => $search_form !== null ? $search_form->createView() : null,
                    'project'            => $project,
        ));
    }

    protected function redirectAfterSave(SaleOrder $entity)
    {
        return $this->redirect($this->generateUrl('backend_sale_order_show', ['id' => $entity->getId()]));
    }

    protected function save(SaleOrder $entity, $oldProducts = [], $oldTasks = [])
    {
        $em      = $this->getEntityManager();
        $request = $this->getRequest();

        $removeProducts = array_udiff($oldProducts, $entity->getProducts()->toArray(), function($img1, $img2) {
            if ($img1->getId() == $img2->getId())
                return 0;
            return $img1->getId() > $img2->getId() ? 1 : -1;
        }
        );

        foreach ($removeProducts as $emp) {
            $entity->removeProductItem($emp);
            $em->remove($emp);
        }

        $removeTasks = array_udiff($oldTasks, $entity->getTasks()->toArray(), function($img1, $img2) {
            if ($img1->getId() == $img2->getId())
                return 0;
            return $img1->getId() > $img2->getId() ? 1 : -1;
        }
        );

        foreach ($removeTasks as $task) {
            $entity->removeTaskItem($task);
            $em->remove($task);
        }

        if ($entity->getVendor() === null) {
            $vendorId = $this->get('security.context')->getToken()->getUser()->getPerson()->getId();
            $entity->setVendor($em->getRepository('AppPersonBundle:Person')->findOneById($vendorId));
        }

        if($entity->isDepositInvoice()){
            /** @var ProjectManager $projectManager */
            $projectManager = $this->get('app_project.project_manager');
            $projectManager->correctDeposit($entity);
        }

        if ($request->request->get('proceed')) {
            $project     = $entity->getProject();
            $makeInvoice = true;

            if ($project !== null && !$entity->isDepositInvoice()) {
                $depositInvoice = $project->getDepositInvoice();
                while($depositInvoice !== null) {
                    if ($depositInvoice->getIsDraft()) {
                        $this->addAdminMessage('Cannot proceed invoice until deposit invoice is not proceed', 'error');
                        $makeInvoice = false;
                    }

                    $depositInvoice = $depositInvoice->getCorrectedBy();
                }
            }

            if ($makeInvoice === true) {
                $entity->makeInvoice();

                if ($entity->isDepositInvoice()) {
                    if ($project !== null) {
                        foreach ($project->getInvoices() as $invoice) {
                            /** @var SaleOrder $invoice */
                            if ($invoice->isContractedInvoice()) {
                                $invoice->setDepositTaxesCopies($entity->getDepositTaxesCopies());
                                $em->persist($invoice);
                            }
                        }
                    }
                }
            }
        }

        $em->persist($entity);
        $em->flush();
    }

    /**
     * Deletes a SaleOrder entity.
     * @Secure(roles="ROLE_BACKEND_INVOICE_DELETE")
     * @RoleInfo(role="ROLE_BACKEND_INVOICE_DELETE", parent="ROLE_BACKEND_INVOICE_ALL", desc="Delete Invoices", module="Invoice")
     * @Route("/delete/{id}", name="backend_sale_order_delete")
     * @Route("/project/{project_id}/delete/{id}", name="backend_project_sale_order_delete")
     * @Method({"DELETE", "GET"})
     */
    public function deleteAction(Request $request, $id)
    {
        $em     = $this->getDoctrine()->getManager();
        /** @var SaleOrder $entity */
        $entity = $em->getRepository('AppInvoiceBundle:SaleOrder')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find SaleOrder entity.');
        }

        if ($entity->getIsDraft() == true) {
            if ($entity->getProjectCategory() === null || $entity->getCorrectionOf() !== null) {
                $correctionOf = $entity->getCorrectionOf();

                if($correctionOf !== null){
                    $correctionOf->setCorrectedBy(null);
                    $em->persist($correctionOf);
                }

                $em->remove($entity);
                $em->flush();
            } else {
                $this->addAdminMessage('Cannot remove automatically generated project invoices', 'error');
            }
        } else {
            $this->addAdminMessage('Cannot remove invoice', 'error');
        }

        $redirectUrl = $entity->getProject() !== null ?
            $this->generateUrl('backend_project_sale_order', ['project_id' => $entity->getProject()->getId()]) :
            $this->generateUrl('backend_sale_order');

        return $this->redirect($redirectUrl);
    }

    /**
     * @param Request $request
     * @Route("/search-products", name="backend_sale_order_products_search")
     * @Template
     */
    public function searchProductsAction(Request $request)
    {

        $account_profile_id = $request->get('account_profile', null);


        if ($account_profile_id === null) {
            throw new NotFoundHttpException();
        }

        /** @var AccountProfile $account_profile */
        $account_profile = $this->getRepository('AppAccountBundle:AccountProfile')->getById($account_profile_id);

        if ($account_profile === null) {
            throw new NotFoundHttpException();
        }

        /** @var AccountProductRepository $account_product_repository */
        $account_product_repository = $this->getRepository('AppAccountProductBundle:AccountProduct');
        $qb                         = $account_product_repository->getAccountQueryBuilder($account_profile);
        $productSearch              = new ProductSearch($account_product_repository, $qb);

        $search_parameters = $request->get('backend_purchase_search');

        $query_builder = $productSearch->filter($qb, $search_parameters);

        $results_per_page = 20;
        $page             = $request->get('page', 1);

        $paginator = $this->get('knp_paginator');

        $pagination = $paginator->paginate($query_builder, $page, $results_per_page);
        $pagination->setParam('backend_purchase_search', $search_parameters);
        $pagination->setParam('account_profile', $account_profile_id);

        return [
            'pagination' => $pagination
        ];
    }

}