<?php
/**
 * Created by PhpStorm.
 * User: Maciej
 * Date: 2014-06-14
 * Time: 17:18
 */

namespace App\InvoiceBundle\Controller;


use App\CoreBundle\Controller\Controller;
use App\InvoiceBundle\Entity\InvoiceProduct;
use App\InvoiceBundle\Entity\SaleOrder;
use App\InvoiceBundle\Form\Type\InvoiceReturnType;
use App\ProjectBundle\Entity\ProjectManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Class RefundsController
 * @package App\InvoiceBundle\Controller
 *
 * @Route("/backend/sale_order/return")
 */
class ReturnsController extends Controller
{
    /**
     * @Route("/create/{id}", name="backend_sale_order_returns_create")
     * @Template("AppInvoiceBundle:Returns:edit.html.twig")
     *
     * @param SaleOrder $saleOrder
     *
     * @return array
     */
    public function createAction(SaleOrder $saleOrder)
    {
        if($saleOrder->getIsDraft()){
            $this->addAdminMessage($this->get('translator')->trans('return.cannot_create_return_from_draft', ['%id%' => $saleOrder->getId()], 'Invoice'), 'error');
            return $this->redirect($this->generateUrl('backend_sale_order'));
        } else if ($saleOrder->getCorrectedBy() !== null) {
            return $this->redirect($this->generateUrl('backend_sale_order_returns_edit', ['id' => $saleOrder->getCorrectedBy()->getId()]));
        } else if($saleOrder->isCredit()) {
            $this->addAdminMessage($this->get('translator')->trans('return.cannot_create_return_from_credit', ['%id%' => $saleOrder->getId()], 'Invoice'), 'error');
            return $this->redirect($this->generateUrl('backend_project_sale_orderer', ['project_id' => $saleOrder->getProject()->getId()]));
        }

        if($saleOrder->isDepositInvoice()){
            $canMakeReturn = true;
            foreach($saleOrder->getProject()->getInvoices() as $projectInvoice){
                /** @var SaleOrder $projectInvoice */
                if(!$projectInvoice->isDepositInvoice() && !$projectInvoice->getIsDraft()){
                    $canMakeReturn = false;
                    break;
                }
            }

            if(!$canMakeReturn){
                $this->addAdminMessage($this->get('translator')->trans('return.cannot_create_deposit_return', ['id' => $saleOrder->getId()], 'Invoice'), 'error');
                return $this->redirect($this->generateUrl('backend_project_sale_order', ['project_id' => $saleOrder->getProject()->getId()]));
            }
        }

        //do cloning of not returned items
        $returnInvoice = clone $saleOrder;
        $returnInvoice->setCorrectionOf($saleOrder);

        return $this->createFormAndSave($returnInvoice);
    }

    /**
     * @Route("/edit/{id}", name="backend_sale_order_returns_edit")
     * @Template("AppInvoiceBundle:Returns:edit.html.twig")
     *
     * @param SaleOrder $saleOrder
     *
     * @return array
     */
    public function editAction(SaleOrder $saleOrder)
    {
        if(!$saleOrder->getIsDraft()){
            return $this->redirectToRoute('backend_sale_order_show', ['id' => $saleOrder->getId()]);
        }

        return $this->createFormAndSave($saleOrder);
    }


    protected function createFormAndSave(SaleOrder $returnInvoice)
    {
        $request = $this->getRequest();
        $form = $this->createForm('backend_invoice_return', $returnInvoice);

        if($request->getMethod() == 'POST'){
            $oldProductReturns = $returnInvoice->getProductReturns();

            $form->submit($request);

            if($form->isValid()) {
                $this->save($returnInvoice, $oldProductReturns);

                return $this->redirectToRoute('backend_sale_order_show', ['id' => $returnInvoice->getId()]);
            }
        }

        $invoiceTasksById = [];
        foreach($returnInvoice->getTasks() as $task){
            $invoiceTasksById[$task->getTask()->getId()] = $task;
        }

        return [
            'entity' => $returnInvoice,
            'form' => $form->createView(),
            'tasks_categories' => $returnInvoice->getTasksCategories($this->getEntityManager()),
            'invoice_tasks'     => $invoiceTasksById
        ];
    }

    protected function save(SaleOrder $saleOrder, array $oldProductReturns)
    {
        $entityManager = $this->getEntityManager();

        $this->removeOldEntities($saleOrder->getProductReturns(), $oldProductReturns);

        $request = $this->getRequest();
        if ($request->request->get('proceed')) {
            $saleOrder->makeInvoice();
        }

        if($saleOrder->isDepositInvoice()){
            /** @var ProjectManager $projectManager */
            $projectManager = $this->get('app_project.project_manager');
            $projectManager->correctDeposit($saleOrder);
        }

        $entityManager->persist($saleOrder);
        $entityManager->persist($saleOrder->getCorrectionOf());
        $entityManager->flush();
    }
}