<?php

/**
 * Created by Sublime Text 3
 * Author: ricardo <ricardo@technologias.com>
 * Date:   Mon Dec 2 01:45:11 2013
 */

namespace App\ProjectBundle\Controller;

use App\AccountBundle\Entity\AccountProfile;
use App\CategoryBundle\Entity\CategoryRepository;
use App\CoreBundle\Controller\Controller;
use App\InvoiceBundle\Entity\SaleOrder;
use App\InvoiceBundle\Entity\SaleOrderRepository;
use App\ProjectBundle\Entity\ContractCategory;
use App\ProjectBundle\Entity\Project;
use App\ProjectBundle\Entity\ProjectManager;
use App\ProjectBundle\Entity\ProjectOpportunity;
use App\ProjectBundle\Entity\Category;
use App\ProjectBundle\Entity\ProjectRepository;
use App\ProjectBundle\Exception\LogicException;
use App\ProjectBundle\Form\ProjectType;
use App\ProjectBundle\Form\ProjectMemberType;
use App\TaskBundle\Entity\Task;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as App;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use JMS\SecurityExtraBundle\Annotation\Secure;
use App\UserBundle\Annotation\RoleInfo;

/**
 * @App\Route("/backend/project")
 * @RoleInfo(role="ROLE_BACKEND_PROJECT_ALL", parent="ROLE_BACKEND_PROJECT_ALL", desc="Projects all access", module="Project")
 */
class ProjectController extends Controller
{

    /**
     * getEntityRepository
     *
     * @return ProjectRepository
     */
    protected function getEntityRepository()
    {
        return $this->getDoctrine()->getManager()->getRepository('AppProjectBundle:Project');
    }

    /**
     * getEntity
     *
     * @param int $id
     * @return Project
     * @throws NotFoundHttpException
     */
    protected function getEntity($id)
    {
        $entity = $this->getEntityRepository()->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Project is not found');
        }
        return $entity;
    }

    /**
     * @return ProjectManager
     */
    protected function getManager()
    {
        return $this->get('app_project.project_manager');
    }

    /**
     * indexAction
     *
     * @Secure(roles="ROLE_BACKEND_PROJECT_LIST")
     * @RoleInfo(role="ROLE_BACKEND_PROJECT_LIST", parent="ROLE_BACKEND_PROJECT_ALL", desc="List Projects", module="Project")
     * @App\Route("/", name="backend_project")
     * @App\Method("GET")
     */
    public function indexAction()
    {
        return $this->render('AppProjectBundle:Project:index.html.twig', array(
            'entities' => [],
        ));   

    }
    
    /**
     * indexAction
     *
     * @Secure(roles="ROLE_BACKEND_PROJECT_LIST")
     * @RoleInfo(role="ROLE_BACKEND_PROJECT_LIST", parent="ROLE_BACKEND_PROJECT_ALL", desc="List Projects", module="Project")
     * @App\Route("/datatables", name="backend_project_datatables")
     * @App\Method("GET")
     */
    public function indexDatatablesAction(Request $request)
    {
        $filter = $this->get('app_project.filter.projectfilter');
        
        if ($response = $filter->processRequest($request)) {
            return $response;
        }
    }

    /**
     * ajax
     *
     * @Secure(roles="ROLE_BACKEND_PROJECT_OPPORTUNITY_LIST")
     * @App\Route("/ajax/{id}/{project_id}", name="backend_project_ajax")
     * @App\Method("POST")
     */
    public function ajaxAction(Request $request, AccountProfile $accountProfile, $project_id)
    {
        if ($request->isXMLHttpRequest()) {
            $project = !empty($project_id) ? $this->getRepository('AppProjectBundle:Project')->getById($project_id) : null;

            $opportunities = $this->getRepository('AppProjectBundle:ProjectOpportunity')->getNotUsedOpportunities($accountProfile, $project);

            $opportunities_choices = [];
            foreach($opportunities as $opportunity){
                $opportunities_choices[$opportunity->getId()] = (string)$opportunity;
            }

            $response = new Response(json_encode($opportunities_choices));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }

        return new Response('This is not ajax!', 400);
    }

    /**
     * createAction
     *
     * @Secure(roles="ROLE_BACKEND_PROJECT_CREATE")
     * @RoleInfo(role="ROLE_BACKEND_PROJECT_CREATE", parent="ROLE_BACKEND_PROJECT_ALL", desc="Create Project", module="Project")
     * @App\Route("/create", name="backend_project_create")
     * @App\Method({"GET", "POST"})
     */
    public function createAction()
    {
        $entity = new Project();
        $em = $this->getDoctrine()->getManager();

        $catList = $em->getRepository('AppProjectBundle:Category')->getList();
        $translator = $this->container->get('app_status.translator');
        $form = $this->createForm(new ProjectType($em, $translator), $entity);
        $request = $this->getRequest();        

        if ($request->getMethod() == 'POST') {

            $submitProject = $request->get('submit_project', null);



            $taskIds = $request->get('preset_task');

            $form->bind($request);

            if ($form->isValid()) {
		
		if (!$entity->getOwner()){
                    $currUser = $this->get('security.context')->getToken()->getUser();
                    $owner = $em->getRepository('App\UserBundle\Entity\User')->find($currUser->getId());
                    $entity->setOwner($owner);
                }



                try{
                    $this->getManager()->create($entity);

                    if ($submitProject){
                        $this->getManager()->lockProject($entity);
                    }

                    $em->flush();

                    $pid = $entity->getId();

                    if ( !empty($taskIds)){
                        $this->container->get('task.manager')->createTasks($taskIds, $pid);
                    }

                    return $this->redirect($this->generateUrl('backend_project_show', array('id' => $pid)));
                } catch (LogicException $e){
                    $this->addAdminMessage($e->getMessage(), 'error');
                }
            }
        }

        $presetTasks = $this->getEntityRepository()->getPresetTasksByCategories();

        return $this->render('AppProjectBundle:Project:create.html.twig', array(
            'entity' => $entity,
            'presetTasks' => $presetTasks,
            'categories' => $catList,
            'form' => $form->createView()
        ));

    }

    /**
     * ownerAction
     *
     * @Secure(roles="ROLE_BACKEND_PROJECT_LIST")
     * @RoleInfo(role="ROLE_BACKEND_PROJECT_LIST", parent="ROLE_BACKEND_PROJECT_ALL", desc="List Projects", module="Project")
     * @App\Route("/owner", name="backend_project_owner")
     * @App\Method({"GET", "POST"})
     */
    public function ownerAction()
    {
        $user = $this->container->get('security.context')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();
        
        $entities = $em->getRepository('AppProjectBundle:Project')->findBy(array('owner'=> $user->getPerson()->getId()));

        return $this->render('AppProjectBundle:Project:index.html.twig', array(
            'entities' => $entities
        ));  

    }

    /**
     * editAction
     *
     * @Secure(roles="ROLE_BACKEND_PROJECT_EDIT")
     * @RoleInfo(role="ROLE_BACKEND_PROJECT_EDIT", parent="ROLE_BACKEND_PROJECT_ALL", desc="Edit Project", module="Project")
     * @App\Route("/edit/{id}", name="backend_project_edit")
     * @App\Method({"GET", "POST"})
     */
    public function editAction($id)
    {
        $entity = $this->getEntity($id);

        $translator = $this->container->get('app_status.translator');
        $form = $this->createForm(new ProjectType($this->getDoctrine()->getManager(), $translator), $entity);
        $request = $this->getRequest();

        if ($request->getMethod() == 'POST') {

            $taskIds = $request->get('preset_task');
            $submitProject = $request->get('submit_project', null);
            $form->bind($request);

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();

                if (!$entity->getOwner()){
                    $currUser = $this->get('security.context')->getToken()->getUser();
		            $owner = $em->getRepository('App\UserBundle\Entity\User')->find($currUser->getId());
                    $entity->setOwner($owner);
                }

                try{
                    $this->getManager()->update($entity);

                    if ($submitProject && !$entity->isProject()){
                        $this->getManager()->lockProject($entity);
                    }

                    $em->flush();

                    $pid = $entity->getId();
                    if ( !empty($taskIds)){
                        $this->container->get('task.manager')->createTasks($taskIds, $pid);
                    }

                    if($entity->isProject()){
                        //check integrity of contracted categories
                        $categories_repository = $this->getRepository('AppProjectBundle:ContractCategory');

                        $errors = $categories_repository->verify();
                        $iteration = 0;

                        while ($errors !== true && $iteration < 10) {
                            $categories_repository->recover();
                            $this->getEntityManager()->flush();
                            $errors = $categories_repository->verify();

                            $iteration++;
                        }
                    }

                    return $this->redirect($this->generateUrl('backend_project_show', array('id' => $id)));
                } catch (LogicException $e){
                    $this->addAdminMessage($e->getMessage(), 'error');
                }
            }
        }

        $presetTasks = $this->getEntityRepository()->getPresetTasksByCategories();

        return $this->render('AppProjectBundle:Project:create.html.twig', array(
            'entity' => $entity,
            'presetTasks' => $presetTasks,
            'id' => $id,
            'type' => $entity->getType(),
            'name' => $entity->getName(),
            'form' => $form->createView()
        ));
    }

    /**
     * showAction
     *
     * @Secure(roles="ROLE_BACKEND_PROJECT_SHOW")
     * @RoleInfo(role="ROLE_BACKEND_PROJECT_SHOW", parent="ROLE_BACKEND_PROJECT_ALL", desc="Show Project", module="Project")
     * @App\Route("/show/{id}", name="backend_project_show")
     * @App\Method("GET")
     */
    public function showAction($id)
    {
        $entity = $this->getEntity($id);

        $categories = $entity->getCategories(true, $this->getDoctrine()->getManager(), false, true, $entity->isProject());

        $taxes_total = [];

        foreach($categories as $root_category){
            foreach($root_category['cost']['taxes'] as $tax_id => $tax_value){
                if(is_int($tax_id)){
                    if(!isset($taxes_total[$tax_id])){
                        $taxes_total[$tax_id] = 0;
                    }

                    $taxes_total[$tax_id] += $tax_value;
                }
            }
        }

        return $this->render('AppProjectBundle:Project:show.html.twig', array(
            'entity' => $entity,
            'taxes_total' => $taxes_total,
            'id' => $id
        ));
    }

    /**
     * deleteAction
     *
     * @Secure(roles="ROLE_BACKEND_PROJECT_DELETE")
     * @RoleInfo(role="ROLE_BACKEND_PROJECT_DELETE", parent="ROLE_BACKEND_PROJECT_ALL", desc="Delete Project", module="Project")
     * @App\Route("/delete/{id}", name="backend_project_delete")
     * @App\Method("GET")
     */
    public function deleteAction($id)
    {
        $entity = $this->getEntity($id);

        $em = $this->getDoctrine()->getManager();
        $this->getManager()->remove($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('backend_project'));
    }

    /**
     * memberAction
     *
     * @Secure(roles="ROLE_BACKEND_PROJECT_MEMBER_LIST")
     * @RoleInfo(role="ROLE_BACKEND_PROJECT_MEMBER_LIST", parent="ROLE_BACKEND_PROJECT_ALL", desc="Project members list", module="Project")
     * @App\Route("/{pid}/member/", name="backend_project_member")
     * @App\Method({"GET", "POST"})
     */
    public function memberAction($pid=0)
    {
        $entity = $this->getEntity($pid);
        $form = $this->createForm(new ProjectMemberType(), $entity);
        $request = $this->getRequest();

        if ($request->getMethod() == 'POST') {
            $form->bind($request);

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($entity);
                $em->flush();
            }
        }

        $clients = [];
        $managers = [];
        
        if ($entity->getClient() ){
            $clients = [$entity->getClient()];
        }
        if ($entity->getManager() ){
            $managers = [$entity->getManager()];
        }

        return $this->render('AppProjectBundle:Project:member.html.twig', array(
            'client' => $clients,
            'manager' => $managers,
            'entities' => $entity->getMembers(),
            'form' => $form->createView(),
            'pid' => $pid
        ));
    }

    /**
     * printAction
     *
     * @Secure(roles="ROLE_BACKEND_PROJECT_PRINT")
     * @RoleInfo(role="ROLE_BACKEND_PROJECT_PRINT", parent="ROLE_BACKEND_PROJECT_ALL", desc="Print Project", module="Project")
     * @App\Route("/print/{id}", name="backend_project_print")
     * @App\Method("GET")
     */
    public function printAction($id)
    {
        $entity = $this->getEntity($id);

        $categories = $entity->getCategories(true, $this->getDoctrine()->getManager(), false, true, $entity->isProject());

        $taxes_total = [];

        foreach($categories as $root_category){
            foreach($root_category['cost']['taxes'] as $tax_id => $tax_value){
                if(is_int($tax_id)){
                    if(!isset($taxes_total[$tax_id])){
                        $taxes_total[$tax_id] = 0;
                    }

                    $taxes_total[$tax_id] += $tax_value;
                }
            }
        }

        $pdf = $this->container->get("white_october.tcpdf")->create('P','pt', 'USLEGAL', true, 'UTF-8');

        // set document information
        $pdf->SetCreator("GSP");
        $pdf->SetAuthor('GSP');
        $pdf->SetTitle('GSP project');
        $pdf->SetSubject('GSP project');
        $pdf->SetKeywords('GSP, PDF');

        // set default header data
        //$pdf->SetHeaderData(null, null, null, '', null, array(0,64,255), array(0,64,128));
        $pdf->setPrintHeader(false);
        $pdf->setFooterData(array(179,178,178), array(179,178,178)); //#b3b2b2 RGB=179,178,178

        // set header and footer fonts
        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
//        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // set margins
        $pdf->SetMargins(28, 28, 28, true);
        $pdf->SetHeaderMargin(28);
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

        $pdf->resetHeaderTemplate();
        
        // set text shadow effect
        //$pdf->setTextShadow(array('enabled'=>true, 'depth_w'=>0, 'depth_h'=>0.2, 'color'=>array(196,196,196), 'opacity'=>1, 'blend_mode'=>'Normal'));

        $pdf->setPrintHeader(false);
        $pdf->SetMargins(28, 28, 28, true);
        $html = $this->renderView('AppProjectBundle:Project:showPrintClientInfo.html.twig', array( 'entity' => $entity, 'pdf' => true));
        $pdf->writeHTML($html, false, false, true, false, '');

        //$pdf->AddPage();
        $html = $this->renderView('AppProjectBundle:Project:showPrintCategories.html.twig', array( 'entity' => $entity, 'pdf' => true));
        $pdf->writeHTML($html, false, false, true, false, '');
        
        //$pdf->AddPage();
        $html = $this->renderView('AppProjectBundle:Project:showSummary.html.twig', array( 'entity' => $entity, 'taxes_total' => $taxes_total, 'id' => $id, 'showSignature'=> true, 'pdf' => true));
        $pdf->writeHTML($html, false, false, true, false, '');

        //$pdf->AddPage();
        $html = $this->renderView('AppProjectBundle:Project:showTerms.html.twig', array( 'entity' => $entity, 'pdf' => true));
        $pdf->writeHTML($html, true, false, true, false, 'L');

        $html = $this->renderView('AppProjectBundle:Project:showSignature.html.twig');
        $pdf->writeHTML($html, false, false, true, false, '');

        $name = ($entity->isEstimate() ? 'Estimate' : 'project').'_'.$entity->getId()."_".$entity->getName();

            // Close and output PDF document
        // This method has several options, check the source code documentation for more information.
        $pdf->Output($name.'.pdf', 'I');
        return;
    }

    /**
     * taskAssignedAction
     *
     * @Secure(roles="ROLE_BACKEND_PROJECT_TASK_ASSIGNED")
     * @RoleInfo(role="ROLE_BACKEND_PROJECT_TASK_ASSIGNED", parent="ROLE_BACKEND_PROJECT_ALL", desc="Project Task Assigned", module="Project")
     * @App\Route("/task/assigned", name="backend_project_task_assigned")
     * @App\Method("GET")
     */
    public function taskAssignedAction()
    {

        $request = $this->get('request');

        return $this->render('AppProjectBundle:Project:taskAssigned.html.twig', array(
            'name' => "assignedAction",
        ));        

    }

    /**
     * taskOwnerAction
     *
     * @Secure(roles="ROLE_BACKEND_PROJECT_TASK_OWNER")
     * @RoleInfo(role="ROLE_BACKEND_PROJECT_TASK_OWNER", parent="ROLE_BACKEND_PROJECT_ALL", desc="Project Task Owner", module="Project")
     * @App\Route("/task/owner", name="backend_project_task_owner")
     * @App\Method("GET")
     */
    public function taskOwnerAction()
    {

        $request = $this->get('request');

        return $this->render('AppProjectBundle:Project:taskOwner.html.twig', array(
            'name' => "ownerAction",
        ));        

    }

    /**************************************************
     * Categories migration start
     *************************************************/

    /**
     * @App\Route("/migrate-categories")
     */
    public function migrateAction()
    {
        /** @var ProjectRepository $projects_repositories */
        $projects_repositories = $this->getRepository('AppProjectBundle:Project');
        /** @var CategoryRepository $categories_repository */
        $categories_repository = $this->getRepository('AppProjectBundle:Category');

        $entity_manager = $this->getEntityManager();

        foreach($projects_repositories->getAll() as $project){
            /** @var Project $project */
            if(!$project->isEstimate()) {
                $categories = [];

                //prepare categories to migration
                foreach ($project->getTasks() as $task) {
                    /** @var Task $task */
                    $path = $categories_repository->getPath($task->getCategory());

                    $this->addCategoryToTree($categories, $path, $task);
                }

                //do actual migration
                //locking contract categories
                if(count($categories) > 0) {
                    $this->copyCategories($categories, $project);

                    foreach($project->getInvoices() as $invoice){
                        /** @var SaleOrder $invoice */

                        $project_category = $invoice->getProjectCategory();
                        if($project_category !== null){
                            $invoice->setProjectCategory($categories[$project_category->getId()]['contract_category']);
                            $entity_manager->persist($invoice);
                        }
                    }
                }
            }
        }

        $entity_manager->flush();

        return new Response('Done');
    }

    protected function addCategoryToTree(&$structure, $path, Task $task)
    {
        if(count($path) > 0){
            $root = array_shift($path);
            if(!isset($structure[$root->getId()])){
                $structure[$root->getId()] = [
                    'category' => $root,
                    'tasks' => [],
                    'children' => [],
                    'places' => [],
                    'without_place' => []
                ];
            }

            if(count($path) > 0){
                $this->addCategoryToTree($structure[$root->getId()]['children'], $path, $task);
            } else {
                $structure[$root->getId()]['tasks'][] = $task;

                $place = $task->getPlace();
                if($place !== null){
                    if(!isset($structure[$root->getId()]['places'][$place->getId()])) {
                        $structure[$root->getId()]['places'][$place->getId()] = [
                            'place' => $place,
                            'tasks' => []
                        ];
                    }

                    $structure[$root->getId()]['places'][$place->getId()]['tasks'][] = $task;
                } else {
                    $structure[$root->getId()]['without_place'][] = $task;
                }
            }

        }
    }

    protected function sortCategories(&$categories)
    {
        uasort($categories, function($a, $b){
            /** @var Category $a_category */
            $a_category = $a['category'];
            /** @var Category $b_category */
            $b_category = $b['category'];

            if($a_category->getLeftValue() == $b_category->getLeftValue()){
                return 0;
            } else {
                return $a_category->getLeftValue() < $b_category->getLeftValue() ? -1 : 1;
            }
        });

        foreach($categories as &$category){
            if(count($category['children']) > 0){
                $this->sortCategories($category['children']);
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

            $this->getEntityManager()->persist($contract_category);

            foreach($category_node['tasks'] as $task){
                /** @var Task $task */
                $task->setContractCategory($contract_category);
                $this->getEntityManager()->persist($task);
            }

            if(count($category_node['children']) > 0){
                $this->copyCategories($category_node['children'], $project, $contract_category);
            }
        }
    }

    /**
     * @Route("/fix-invoices-categories")
     */
    public function fixInvoicesCategoriesAction()
    {
        /** @var ProjectRepository $projects_repository */
        $projects_repository = $this->getRepository('AppProjectBundle:Project');
        $projects = $projects_repository->getProjects();

        /** @var SaleOrderRepository $invoices_repository */
        $invoices_repository = $this->getRepository('AppInvoiceBundle:SaleOrder');

        $entity_manager = $this->getEntityManager();
        $tasks_manager = $this->get('task.manager');

        foreach($projects as $project){
            /** @var Project $project */
            $categories = $project->getCategories(true, $this->getEntityManager());

            foreach($categories as $root_category_node){
                $invoice = $invoices_repository->getByProjectCategory($project, $root_category_node['category']);

                if($invoice !== null){
                    /** @var Task $first_task */
                    $first_task = $root_category_node['first_task'];
                    $invoices_tasks = $first_task->getInvoicesTasks();

                    if(count($invoices_tasks) > 0){
                        $invoice = $invoices_tasks[0]->getOrder();
                        $invoice->setProjectCategory($root_category_node['category']);

                        $tasks_manager->showInvoice($first_task);

                        $entity_manager->persist($invoice);
                    }
                }
            }
        }

        $entity_manager->flush();

        return new Response('Done');
    }

    /**************************************************
     * Categories migration end
     *************************************************/

    /**
     * @Route("/fix-missing-invoices/{id}")
     */
    public function fixMissingInvoicesAction(Project $project)
    {
        $project_manager = $this->get('app_project.project_manager');
        $project_manager->fixMissingInvoices($project);

        $this->getEntityManager()->flush();

        return new Response('Done');
    }

    /**
     * @Route("/back-to-estimate/{id}")
     * @Template()
     *
     * @param Request $request
     * @param Project $project
     *
     * @return array
     */
    public function backToEstimateAction(Request $request, Project $project)
    {
        if($request->getMethod() == 'POST'){
            $projectManager = $this->get('app_project.project_manager');
            $projectManager->backToEstimate($project);
            $this->getEntityManager()->flush();

            $projectManager->fixProjectCategories();
            $this->getEntityManager()->flush();
        }

        return [
            'project' => $project
        ];
    }

}
