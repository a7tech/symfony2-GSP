<?php

/**
 * Created by Sublime Text 3
 * Author: ricardo <ricardo@technologias.com>
 * Date:   Mon Dec 2 01:45:11 2013
 */

namespace App\ProjectBundle\Controller;

use App\ProjectBundle\Entity\ProjectOpportunity;
use App\ProjectBundle\Form\ProjectOpportunityType;
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
 * @App\Route("/backend/project/opportunity")
 * @RoleInfo(role="ROLE_BACKEND_PROJECT_OPPORTUNITY_ALL", parent="ROLE_BACKEND_PROJECT_OPPORTUNITY_ALL", desc="Project Opportunities all access", module="Project Opportunity")
 */
class ProjectOpportunityController extends Controller
{

    /**
     * getRepository
     *
     * @return EntityRepository
     */
    protected function getRepository()
    {
        return $this->getDoctrine()->getManager()->getRepository('AppProjectBundle:ProjectOpportunity');
    }

    /**
     * getEntity
     *
     * @param int $id
     * @return Product
     * @throws NotFoundHttpException
     */
    protected function getEntity($id)
    {
        $entity = $this->getRepository()->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Opportunity is not found');
        }
        return $entity;
    }

    /**
     * indexAction
     *
     * @Secure(roles="ROLE_BACKEND_PROJECT_OPPORTUNITY_LIST")
     * @RoleInfo(role="ROLE_BACKEND_PROJECT_OPPORTUNITY_LIST", parent="ROLE_BACKEND_PROJECT_OPPORTUNITY_ALL", desc="List Project Opportunities", module="Project Opportunity")
     * @App\Route("/", name="backend_project_opportunity")
     * @App\Method("GET")
     */
    public function indexAction()
    {

//        $em = $this->getDoctrine()->getManager();
//
//        $entities = $em->getRepository('AppProjectBundle:ProjectOpportunity')->findAll();

        return $this->render('AppProjectBundle:ProjectOpportunity:index.html.twig', array(
            'entities' => []
        ));   

    }
    
    /**
     * indexAction
     *
     * @Secure(roles="ROLE_BACKEND_PROJECT_OPPORTUNITY_LIST")
     * @RoleInfo(role="ROLE_BACKEND_PROJECT_OPPORTUNITY_LIST", parent="ROLE_BACKEND_PROJECT_OPPORTUNITY_ALL", desc="Project Opportunities", module="Project Opportunity")
     * @App\Route("/datatables", name="backend_project_opportunity_datatables")
     * @App\Method("GET")
     */
    public function indexDatatablesAction(Request $request)
    {
        $filter = $this->get('app_project.filter.projectopportunityfilter');
        
        if ($response = $filter->processRequest($request)) {
            return $response;
        }
    }

    

    /**
     * createAction
     *
     * @Secure(roles="ROLE_BACKEND_PROJECT_OPPORTUNITY_CREATE")
     * @RoleInfo(role="ROLE_BACKEND_PROJECT_OPPORTUNITY_CREATE", parent="ROLE_BACKEND_PROJECT_OPPORTUNITY_ALL", desc="Create Project Opportunity", module="Project Opportunity")
     * @App\Route("/create", name="backend_project_opportunity_create")
     * @App\Method({"GET", "POST"})
     */
    public function createAction()
    {
        $entity = new ProjectOpportunity();
        $em = $this->getDoctrine()->getManager();

        $milestone = $this->getDoctrine()->getManager()->getRepository('AppProjectBundle:ProjectMilestone')->find(1);
        $entity->setMilestone($milestone);

        $form = $this->createForm(new ProjectOpportunityType($em), $entity);
        $request = $this->getRequest();        

        if ($request->getMethod() == 'POST') {

            $form->bind($request);

            if ($form->isValid()) {

                $currUser = $this->get('security.context')->getToken()->getUser();
                $owner = $em->getRepository('App\UserBundle\Entity\User')->find($currUser->getId());
                $entity->setOwner($owner);
                $em->persist($entity);
                $em->flush();
                $pid = $entity->getId();

                return $this->redirect($this->generateUrl('backend_project_opportunity_show', array('id' => $pid)));
            }

        }

        return $this->render('AppProjectBundle:ProjectOpportunity:create.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView()
        ));

    }

    /**
     * ownerAction
     *
     * @Secure(roles="ROLE_BACKEND_PROJECT_OPPORTUNITY_LIST")
     * @RoleInfo(role="ROLE_BACKEND_PROJECT_OPPORTUNITY_LIST", parent="ROLE_BACKEND_PROJECT_OPPORTUNITY_ALL", desc="List Project Opportunities", module="Project Opportunity")
     * @App\Route("/owner", name="backend_project_opportunity_owner")
     * @App\Method({"GET", "POST"})
     */
    public function ownerAction()
    {
        $user = $this->container->get('security.context')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('AppProjectBundle:ProjectOpportunity')->findBy(array('owner'=> $user->getPerson()->getId()));

        return $this->render('AppProjectBundle:ProjectOpportunity:index.html.twig', array(
            'entities' => $entities
        ));  


    }

    /**
     * editAction
     *
     * @Secure(roles="ROLE_BACKEND_PROJECT_OPPORTUNITY_EDIT")
     * @RoleInfo(role="ROLE_BACKEND_PROJECT_OPPORTUNITY_EDIT", parent="ROLE_BACKEND_PROJECT_OPPORTUNITY_ALL", desc="Edit Project Opportunity", module="Project Opportunity")
     * @App\Route("/edit/{id}", name="backend_project_opportunity_edit")
     * @App\Method({"GET", "POST"})
     */
    public function editAction($id)
    {
        $entity = $this->getEntity($id);

        $form = $this->createForm(new ProjectOpportunityType($this->getDoctrine()->getManager()), $entity);
        $request = $this->getRequest();

        if ($request->getMethod() == 'POST') {

            $form->bind($request);

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();

                if (!$entity->getOwner()){
                    $currUser = $this->get('security.context')->getToken()->getUser();
                    $owner = $em->getRepository('App\PersonBundle\Entity\Person')->find($currUser->getId());
                    $entity->setOwner($owner);
                }

                $em->flush();

                return $this->redirect($this->generateUrl('backend_project_opportunity_show', array('id' => $id)));
            }
        }

        return $this->render('AppProjectBundle:ProjectOpportunity:create.html.twig', array(
            'entity' => $entity,
            'id' => $id,
            'name' => $entity->getName(),
            'form' => $form->createView()
        ));
    }

    /**
     * showAction
     *
     * @Secure(roles="ROLE_BACKEND_PROJECT_OPPORTUNITY_SHOW")
     * @RoleInfo(role="ROLE_BACKEND_PROJECT_OPPORTUNITY_SHOW", parent="ROLE_BACKEND_PROJECT_OPPORTUNITY_ALL", desc="Show Project Opportunity", module="Project Opportunity")
     * @App\Route("/show/{id}", name="backend_project_opportunity_show")
     * @App\Method("GET")
     */
    public function showAction($id)
    {
        return $this->render('AppProjectBundle:ProjectOpportunity:show.html.twig', array(
            'entity' => $this->getEntity($id),
            'id' => $id
        ));
    }

    /**
     * deleteAction
     *
     * @Secure(roles="ROLE_BACKEND_PROJECT_OPPORTUNITY_DELETE")
     * @RoleInfo(role="ROLE_BACKEND_PROJECT_OPPORTUNITY_DELETE", parent="ROLE_BACKEND_PROJECT_OPPORTUNITY_ALL", desc="Delete Project Opportunity", module="Project Opportunity")
     * @App\Route("/delete/{id}", name="backend_project_opportunity_delete")
     * @App\Method("GET")
     */
    public function deleteAction($id)
    {
        $entity = $this->getEntity($id);

        $em = $this->getDoctrine()->getManager();
        $em->remove($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('backend_project_opportunity'));
    }

}
