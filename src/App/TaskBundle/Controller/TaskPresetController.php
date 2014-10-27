<?php

/**
 * Created by Sublime Text 3
 * Author: ricardo <ricardo@technologias.com>
 * Date:   Mon Dec 2 01:45:11 2013
 */

namespace App\TaskBundle\Controller;

use App\TaskBundle\Entity\TaskPreset;
use App\TaskBundle\Entity\TaskPresetRepository;
use App\TaskBundle\Form\TaskPresetType;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as App;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use JMS\SecurityExtraBundle\Annotation\Secure;
use App\UserBundle\Annotation\RoleInfo;
use Doctrine\ORM\EntityRepository;


/**
 * @App\Route("/backend/settings/custom/task_preset")
 * @RoleInfo(role="ROLE_BACKEND_TASK_PRESET_ALL", parent="ROLE_BACKEND_TASK_PRESET_ALL", desc="Task presets all access", module="Task preset")
 */
class TaskPresetController extends Controller
{

   /**
     * getRepository
     *
     * @return EntityRepository
     */
    protected function getRepository()
    {
        return $this->getDoctrine()->getManager()->getRepository('AppTaskBundle:TaskPreset');
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
        $entity = $this->getRepository()->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Task preset is not found');
        }
        return $entity;
    }

    /**
     * indexAction
     *
     * @Secure(roles="ROLE_BACKEND_TASK_PRESET_LIST")
     * @RoleInfo(role="ROLE_BACKEND_TASK_PRESET_LIST", parent="ROLE_BACKEND_TASK_PRESET_ALL", desc="List Task presets", module="Task preset")
     * @App\Route("/", name="backend_settings_custom_task_preset")
     * @App\Method("GET")
     */
    public function indexAction()
    {
//        $em = $this->getDoctrine()->getManager();
//        $entities = $em->getRepository('AppTaskBundle:TaskPreset')->findAll();

        return $this->render('AppTaskBundle:TaskPreset:index.html.twig', array(
            'entities' => [],
        ));   

    }

    /**
     * createAction
     *
     * @Secure(roles="ROLE_BACKEND_TASK_PRESET_CREATE")
     * @RoleInfo(role="ROLE_BACKEND_TASK_PRESET_CREATE", parent="ROLE_BACKEND_TASK_PRESET_ALL", desc="Create Task preset", module="Task preset")
     * @App\Route("/create", name="backend_settings_custom_task_preset_create")
     * @App\Method({"GET", "POST"})
     */
    public function createAction()
    {
        $entity = new TaskPreset();
        $translator = $this->container->get('app_status.translator');
        $form = $this->createForm(new TaskPresetType($translator), $entity);

        $request = $this->getRequest();        

        if ($request->getMethod() == 'POST') {
            $form->bind($request);

            if ($form->isValid()) {
                $preset_manager = $this->get('task_presets.manager');
                $preset_manager->create($entity);

                $em = $this->getDoctrine()->getManager();
                $em->flush();

                return $this->redirect($this->generateUrl('backend_settings_custom_task_preset_show', array('id' => $entity->getId())));
            }
        }

        return $this->render('AppTaskBundle:TaskPreset:create.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView()
        ));

    }

    /**
     * editAction
     *
     * @Secure(roles="ROLE_BACKEND_TASK_PRESET_EDIT")
     * @RoleInfo(role="ROLE_BACKEND_TASK_PRESET_EDIT", parent="ROLE_BACKEND_TASK_PRESET_ALL", desc="Edit Task preset", module="Task preset")
     * @App\Route("/edit/{id}", name="backend_settings_custom_task_preset_edit")
     * @App\Method({"GET", "POST"})
     */
    public function editAction($id)
    {

        $entity = $this->getEntity($id); 
        $translator = $this->container->get('app_status.translator');
        $form = $this->createForm(new TaskPresetType($translator ), $entity);

        $request = $this->getRequest();
        if ($request->getMethod() == 'POST') {

            $form->bind($request);

            if ($form->isValid()) {
                $preset_manager = $this->get('task_presets.manager');
                $preset_manager->update($entity);
                $em = $this->getDoctrine()->getManager();
                $em->flush();

                return $this->redirect($this->generateUrl('backend_settings_custom_task_preset_show', array('id' => $id)));
            }
        }

        return $this->render('AppTaskBundle:TaskPreset:create.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView()
        ));
    }

    /**
     * showAction
     *
     * @Secure(roles="ROLE_BACKEND_TASK_PRESET_SHOW")
     * @RoleInfo(role="ROLE_BACKEND_TASK_PRESET_SHOW", parent="ROLE_BACKEND_TASK_PRESET_ALL", desc="Show Task preset", module="Task preset")
     * @App\Route("/show/{id}", name="backend_settings_custom_task_preset_show")
     * @App\Method("GET")
     */
    public function showAction($id=0)
    {
        //$request = $this->getRequest();
        //$id = $request->get('id');
        //$pid = $request->get('pid');
        //print_r($pid); die;
        return $this->render('AppTaskBundle:TaskPreset:show.html.twig', array(
            'entity' => $this->getEntity($id)
        ));
    }

    /**
     * deleteAction
     * @Secure(roles="ROLE_BACKEND_TASK_PRESET_DELETE")
     * @RoleInfo(role="ROLE_BACKEND_TASK_PRESET_DELETE", parent="ROLE_BACKEND_TASK_PRESET_ALL", desc="Delete Task preset", module="Task preset")
     * @App\Route("/delete/{id}", name="backend_settings_custom_task_preset_delete")
     * @App\Method("GET")
     */
    public function deleteAction($id)
    {
        $entity = $this->getEntity($id);

        $preset_manager = $this->get('task_presets.manager');
        $preset_manager->remove($entity);
        $em = $this->getDoctrine()->getManager();
        $em->flush();

        return $this->redirect($this->generateUrl('backend_settings_custom_task_preset'));
    }
    

}
