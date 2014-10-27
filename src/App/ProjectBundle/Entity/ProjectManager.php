<?php
/**
 * Created by PhpStorm.
 * User: Maciej
 * Date: 19.02.14
 * Time: 17:20
 */

namespace App\ProjectBundle\Entity;


use App\InvoiceBundle\Entity\InvoiceTask;
use App\InvoiceBundle\Entity\SaleOrder;
use App\ProjectBundle\Exception\LogicException;
use App\TaskBundle\Entity\Task;
use App\TaskBundle\Form\Subscriber\TaskLockSubscriber;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityNotFoundException;

class ProjectManager {

    /**
     * @var EntityManager
     */
    protected $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function create(Project $project)
    {
        if($project->getType() == Project::TYPE_PROJECT && $project->getProjectDate() === null) {
            $this->lockProject($project);
        }
        $this->entityManager->persist($project);
    }

    public function update(Project $project)
    {
        if($project->getType() == Project::TYPE_PROJECT && $project->getProjectDate() === null) {
            $this->lockProject($project);
        }
        $this->entityManager->persist($project);
    }

    public function remove(Project $project)
    {
        $this->entityManager->remove($project);
    }

    public function lockProject(Project $project)
    {
        if($project->isProject()){
            throw new \LogicException('Project already locked');
        }

        $project->setType(Project::TYPE_PROJECT);
        $project->setProjectDate(new \DateTime());

        //make sure categories are OK, just before lock
        $categories_repository = $this->entityManager->getRepository('AppProductBundle:Category');

        $categories_errors = $categories_repository->verify();
        if($categories_errors !== true){
            $categories_repository->recover();
        }

        $categories = $project->getCategories(true, $this->entityManager, false, false);

        if($project->getClient() === null){
            throw new LogicException('Client have to be set!');
        }

        $client = $project->getClient()->getPerson();

        if($client === null){
            throw new LogicException('Client have to have person assigned!');
        }

        //addresses
        $main_address = $client->getMainAddress();
        $billing_address = $main_address !== null && $main_address->getIsBilling() ? $main_address : $client->getBillingAddress();

        if($billing_address === null){
            throw new LogicException('Customer have to have billing address!');
        }

        //locking contract categories
        if(count($categories) > 0) {
            $this->copyCategories($categories, $project);
        }

        //invoices
        $this->createDepositInvoice($project);
        $this->createMainCategoriesInvoices($project, $categories, true);
    }

    protected function createDepositInvoice(Project $project)
    {
        if($project->getClient() === null){
            throw new LogicException('Client have to be set!');
        }

        $client = $project->getClient()->getPerson();

        //addresses
        $main_address = $client->getMainAddress();
        $billing_address = $main_address !== null && $main_address->getIsBilling() ? $main_address : $client->getBillingAddress();

        $project_owner = $project->getOwner();
        $account_profile = $project->getAccountProfile();

        $depositAmount = $project->getDepositAmount();
        if(!empty($depositAmount)){
            //create deposit invoice
            $invoice = new SaleOrder();
            $invoice->setProject($project);
            $invoice->setCustomer($client);
            $invoice->setStatus(SaleOrder::STATUS_UNPAID);
            if($project_owner !== null){
                $invoice->setVendor($project_owner->getPerson());
            }
            $invoice->setVendorCompany($account_profile);
            $invoice->setBilling($billing_address);
            $invoice->setInvoiceDate(new \DateTime());
            $invoice->setIsVisible(true);
            $invoice->setDepositPosition(round($project->getContractNetCost($this->entityManager)*$depositAmount, 2));
            $invoice->setDepositTaxes($account_profile->getTaxation()->toArray());

            $this->entityManager->persist($invoice);
            $project->setDepositInvoice($invoice);
        }
    }

    protected function createMainCategoriesInvoices(Project $project, array $categories, $from_project_draft = true)
    {
        if($project->getClient() === null){
            throw new LogicException('Client have to be set!');
        }

        $client = $project->getClient()->getPerson();

        //addresses
        $main_address = $client->getMainAddress();
        $billing_address = $main_address !== null && $main_address->getIsBilling() ? $main_address : $client->getBillingAddress();

        $project_owner = $project->getOwner();
        $account_profile = $project->getAccountProfile();

        $category_source = $from_project_draft ? 'contract_category' : 'category';
        $invoices_repository = $this->entityManager->getRepository('AppInvoiceBundle:SaleOrder');

        $depositAmount = $project->getDepositAmount();

        foreach($categories as $category){
            if($from_project_draft === false){
                $invoice = $invoices_repository->getByProjectCategory($project, $category[$category_source]);

                if($invoice !== null){
                    //invoice already exist - continue
                    continue;
                }
            }


            //create one invoice per category
            $invoice = new SaleOrder();
            $invoice->setProject($project);
            $invoice->setProjectCategory($category[$category_source]);
            $invoice->setCustomer($client);
            $invoice->setStatus(SaleOrder::STATUS_UNPAID);

            if($project_owner !== null) {
                $invoice->setVendor($project_owner->getPerson());
            }
            $invoice->setVendorCompany($account_profile);

            $invoice->setBilling($billing_address);

            //TODO: Refactor this shit to CONSTs!; 1=on delivery, 2=before delivery
            $on_delivery = $project->getInvoiceDeliveryType() == 1;
            $invoice_date = $on_delivery ?  $category['last_task']->getDueDate() : $category['first_task']->getStartDate();
            $invoice->setInvoiceDate($invoice_date);
            $invoice->setIsVisible(!$on_delivery);

            if(!empty($depositAmount)){
                $invoice->setDepositReturn(round($category['cost']['net']*$depositAmount, 2));
            }

            $all_tasks_complete = true;
            $has_tasks = false;
            foreach($category['all_tasks'] as $task){
                /** @var Task $task */
                if($task->getType() == Task::TYPE_PAYABLE && $task->isContracted()){
                    $has_tasks = true;
                    $invoice_task = new InvoiceTask();
                    $invoice_task->setTask($task);

                    $invoice->addTaskItem($invoice_task);

                    if(!$task->isFinished()){
                        $all_tasks_complete = false;
                    }
                }
            }

            if($has_tasks) {
                //fix visibility when creating from real project (not estimate)
                if ($all_tasks_complete && $on_delivery) {
                    $invoice->setIsVisible(true);
                }

                $this->entityManager->persist($invoice);
            }
        }
    }

    public function correctDeposit(SaleOrder $depositInvoice)
    {
        $project = $depositInvoice->getProject();
        $newPercent = $depositInvoice->getDepositPosition() / $project->getContractNetCost($this->entityManager, false);
        if(round($project->getDepositAmount(), 4) != round($newPercent, 4)) {
            $project->setCorrectedDepositAmount($newPercent);
            $this->entityManager->persist($project);

            foreach ($project->getInvoices() as $projectInvoice) {
                /** @var SaleOrder $projectInvoice */
                if (!$projectInvoice->isDepositInvoice()) {
                    if (!$projectInvoice->getIsDraft()) {
                        throw new \LogicException('Cannot change deposit return on real invoice');
                    }

                    $projectInvoice->setDepositReturn($projectInvoice->getNetTotal(false) * $newPercent);
                    $this->entityManager->persist($projectInvoice);
                }
            }
        }
    }

    /**
     * Copy categories and adds 'contract_category' index to root nodes
     *
     * @param array            $categories
     * @param Project          $project
     * @param ContractCategory $parent_category
     *
     * @throws \Doctrine\ORM\ORMInvalidArgumentException
     */
    protected function copyCategories(array &$categories, Project $project, ContractCategory $parent_category = null)
    {
        foreach($categories as &$category_node){
            $contract_category = new ContractCategory($category_node['category']);
            $contract_category->setProject($project);

            if($parent_category !== null){
                $contract_category->setParent($parent_category);
            } else {
                $category_node['contract_category'] = $contract_category;
            }

            $this->entityManager->persist($contract_category);


            foreach($category_node['tasks'] as $position => $task){
                /** @var Task $task */
                if(!$task->isCancelled()){
                    $task->setContractCategory($contract_category);
                } else {
                    $task->setIsContracted(false);
                    $task->setType(Task::TYPE_ADJUSTMENT);
                    unset($category_node['tasks'][$position]);
                }

                $this->entityManager->persist($task);
            }

            if(count($category_node['children']) > 0){
                $this->copyCategories($category_node['children'], $project, $contract_category);
            }
        }

        $categories_repository = $this->entityManager->getRepository('AppProjectBundle:ContractCategory');

        $errors = $categories_repository->verify();

        if($errors !== true) {
            $categories_repository->recover();
        }

    }

    public function fixMissingInvoices(Project $project)
    {
        if($project->isEstimate()){
            throw new \LogicException('Can fix only locked projects');
        }

        try{
            $project->getDepositInvoice()->getIsDraft();
        } catch(EntityNotFoundException $e) {
            $this->createDepositInvoice($project);
        }

        $categories = $project->getCategories(true, $this->entityManager, false, true);
        $this->createMainCategoriesInvoices($project, $categories, false);
    }

    public function backToEstimate(Project $project)
    {
        foreach($project->getTasks() as $task){
            /** @var Task $task */
            $task->setContractCategory(null);
            $task->setDoneRatio(0);
            $taxesCopies = $task->getTaxesCopies();
            foreach($taxesCopies as $taxCopy){
                $this->entityManager->remove($taxCopy);
            };
            $taxesCopies->clear();

            $this->entityManager->persist($task);
        }

        //remove invoices
        foreach($project->getInvoices() as $invoice){
            /** @var SaleOrder $invoice */
            $this->entityManager->remove($invoice);
        }

        //restore project properties
        $project->setType(Project::TYPE_ESTIMATE);
        $project->setProjectDate(null);
        $project->setDepositInvoice(null);

        $this->entityManager->persist($project);

        //remove contracted categories
        $contract_categories_repository = $this->entityManager->getRepository('AppProjectBundle:ContractCategory');
        $categories = $contract_categories_repository->findByProject($project);
        foreach($categories as $category){
            $this->entityManager->remove($category);
        }


    }

    public function fixProjectCategories()
    {
        $contract_categories_repository = $this->entityManager->getRepository('AppProjectBundle:ContractCategory');
        $errors = $contract_categories_repository->verify();
        if($errors !== true) {
            $contract_categories_repository->recover();
        }
    }

} 