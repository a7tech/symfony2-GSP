<?php
/**
 * Created by Sublime Text 3
 * Author: ricardo <ricardo@technologias.com>
 * Date:   Mon Dec 2 01:45:11 2013
 */

namespace App\ProjectBundle\Controller;

use App\ProjectBundle\Entity\ProjectMilestone;
use App\ProjectBundle\Form\ProjectMilestoneType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as App;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Doctrine\ORM\EntityRepository;
use App\UserBundle\Annotation\RoleInfo;


/**
 * @App\Route("/backend/settings/custom/project_milestone")
 * @RoleInfo(role="ROLE_BACKEND_PROJECT_MILESTONE_ALL", parent="ROLE_BACKEND_PROJECT_MILESTONE_ALL", desc="Project Milestone all access", module="Project Milestone")
 */
class ProjectMilestoneController extends Controller
{

    /**
     * getRepository
     *
     * @return EntityRepository
     */
    protected function getRepository()
    {
        return $this->getDoctrine()->getManager()->getRepository('AppProjectBundle:ProjectMilestone');
    }

    /**
     * getEntity
     *
     * @param int $id
     * @return TaskStatus
     * @throws NotFoundHttpException
     */
    protected function getEntity($id)
    {
        $entity = $this->getRepository()->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Task status is not found');
        }
        return $entity;
    }

    /**
     * indexAction
     *
     * @Secure(roles="ROLE_BACKEND_PROJECT_MILESTONE_LIST")
     * @RoleInfo(role="ROLE_BACKEND_PROJECT_MILESTONE_LIST", parent="ROLE_BACKEND_PROJECT_MILESTONE_ALL", desc="List Project Milestone", module="Project Milestone")
     * @App\Route("/", name="backend_settings_custom_project_milestone")
     * @App\Method("GET")
     */
    public function indexAction()
    {
//        $em = $this->getDoctrine()->getManager();
//        $entities = $em->getRepository('AppProjectBundle:ProjectMilestone')->findAll();

        return $this->render('AppProjectBundle:ProjectMilestone:index.html.twig', array(
            'entities' => [],
        ));   

    }
    
    /**
     * indexAction
     *
     * @Secure(roles="ROLE_BACKEND_PROJECT_MILESTONE_LIST")
     * @RoleInfo(role="ROLE_BACKEND_PROJECT_MILESTONE_LIST", parent="ROLE_BACKEND_PROJECT_MILESTONE_ALL", desc="List Project Milestone", module="Project Milestone")
     * @App\Route("/datatables", name="backend_project_milestone_datatables")
     * @App\Method("GET")
     */
    public function indexDatatablesAction(Request $request)
    {
        $filter = $this->get('app_project.filter.projectmilestonefilter');

        if ($response = $filter->processRequest($request)) {
            return $response;
        }
    }

    /**
     * createAction
     *
     * @Secure(roles="ROLE_BACKEND_PROJECT_MILESTONE_CREATE")
     * @RoleInfo(role="ROLE_BACKEND_PROJECT_MILESTONE_CREATE", parent="ROLE_BACKEND_PROJECT_MILESTONE_ALL", desc="Create Project Milestone", module="Project Milestone")
     * @App\Route("/create", name="backend_settings_custom_project_milestone_create")
     * @App\Method({"GET", "POST"})
     */
    public function createAction()
    {
        $entity = new ProjectMilestone();
        $form = $this->createForm('project_milestone_type', $entity);
        $request = $this->getRequest();        

        if ($request->getMethod() == 'POST') {
            $form->bind($request);

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($entity);
                $em->flush();

                return $this->redirect($this->generateUrl('backend_settings_custom_project_milestone_show', array('id' => $entity->getId())));
            }
        }

        return $this->render('AppProjectBundle:ProjectMilestone:create.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView()
        ));

    }

    /**
     * editAction
     *
     * @Secure(roles="ROLE_BACKEND_PROJECT_MILESTONE_EDIT")
     * @RoleInfo(role="ROLE_BACKEND_PROJECT_MILESTONE_EDIT", parent="ROLE_BACKEND_PROJECT_MILESTONE_ALL", desc="Edit Project Milestone", module="Project Milestone")
     * @App\Route("/edit/{id}", name="backend_settings_custom_project_milestone_edit")
     * @App\Method({"GET", "POST"})
     */
    public function editAction($id)
    {
        $entity = $this->getEntity($id);
        $form = $this->createForm('project_milestone_type', $entity);

        $request = $this->getRequest();
        if ($request->getMethod() == 'POST') {
            $form->bind($request);

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($entity);
                $em->flush();

                return $this->redirect($this->generateUrl('backend_settings_custom_project_milestone_show', array('id' => $id)));
            }
        }

        return $this->render('AppProjectBundle:ProjectMilestone:create.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView()
        ));
    }

    /**
     * showAction
     *
     * @Secure(roles="ROLE_BACKEND_PROJECT_MILESTONE_SHOW")
     * @RoleInfo(role="ROLE_BACKEND_PROJECT_MILESTONE_SHOW", parent="ROLE_BACKEND_PROJECT_MILESTONE_ALL", desc="Show Project Milestone", module="Project Milestone")
     * @App\Route("/show/{id}", name="backend_settings_custom_project_milestone_show")
     * @App\Method("GET")
     */
    public function showAction($id)
    {
        return $this->render('AppProjectBundle:ProjectMilestone:show.html.twig', array(
            'entity' => $this->getEntity($id)
        ));
    }

    /**
     * deleteAction
     * @Secure(roles="ROLE_BACKEND_PROJECT_MILESTONE_DELETE")
     * @RoleInfo(role="ROLE_BACKEND_PROJECT_MILESTONE_DELETE", parent="ROLE_BACKEND_PROJECT_MILESTONE_ALL", desc="Delete Project Milestone", module="Project Milestone")
     * @App\Route("/delete/{id}", name="backend_settings_custom_project_milestone_delete")
     * @App\Method("GET")
     */
    public function deleteAction($id)
    {
        $entity = $this->getEntity($id);

        $em = $this->getDoctrine()->getManager();
        $em->remove($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('backend_settings_custom_project_milestone'));
    }

}
