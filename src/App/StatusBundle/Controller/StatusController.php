<?php

namespace App\StatusBundle\Controller;

use App\CoreBundle\Controller\Controller;
use App\StatusBundle\Entity\Status;
use App\StatusBundle\Form\Type\StatusType;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use JMS\SecurityExtraBundle\Annotation\Secure;
use App\UserBundle\Annotation\RoleInfo;

/**
 * InvoiceStatus controller.
 *
 * @Route("/backend/settings/status")
 * @RoleInfo(role="ROLE_BACKEND_STATUS_ALL", parent="ROLE_BACKEND_STATUS_ALL", desc="Statuses all access", module="Status")
 * 
 */
class StatusController extends Controller
{

    /**
     * getCategoryRepository
     *
     * @return EntityRepository
     */
    protected function getEntityRepository()
    {
        return $this->getRepository('AppStatusBundle:Status');
    }

    /**
     * getEntity
     *
     * @param int $id
     * @return Status
     * @throws NotFoundHttpException
     */
    protected function getEntity($id)
    {
        $entity = $this->getEntityRepository()->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Invoice Status is not found');
        }
        return $entity;
    }

    /**
     * Lists all Status entities.
     * @Secure(roles="ROLE_BACKEND_STATUS_LIST")
     * @RoleInfo(role="ROLE_BACKEND_STATUS_LIST", parent="ROLE_BACKEND_STATUS_ALL", desc="List Statuses", module="Status")
     * @Route("/{className}/", name="backend_settings_status")
     * @Method("GET")
     * @Template()
     */
    public function indexAction($className)
    {
        $group = $this->getRepository('AppStatusBundle:Group')->getByClassName($className);

        if($group === null){
            throw new NotFoundHttpException();
        }
//
//        $entities = $group->getStatuses();

        return array(
            'entities' => [],
//            'entities' => $entities,
            'group' => $group,
            'className' => $className,
        );
    }
    
    /**
     * indexAction
     *
     * @Secure(roles="ROLE_BACKEND_STATUS_LIST")
     * @RoleInfo(role="ROLE_BACKEND_STATUS_LIST", parent="ROLE_BACKEND_STATUS_ALL", desc="List Statuses", module="Status")
     * @Route("/datatables/datatables", name="backend_settings_status_datatables")
     * @Method("GET")
     */
    public function indexDatatablesAction(Request $request)
    {
        $filter = $this->get('app_status.filter.settingsstatusfilter');

        if ($response = $filter->processRequest($request)) {
            return $response;
        }
    }

    /**
     * Finds and displays a InvoiceStatus entity.
     * @Secure(roles="ROLE_BACKEND_STATUS_SHOW")
     * @RoleInfo(role="ROLE_BACKEND_STATUS_SHOW", parent="ROLE_BACKEND_STATUS_ALL", desc="Show Status", module="Status")
     * @Route("/{className}/show/{id}", name="backend_settings_status_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $entity = $this->getEntity($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Status.');
        }

        return array(
            'entity' => $entity,
            'group' => $entity->getGroup()
        );
    }

    /**
     * Displays a form to edit an existing InvoiceStatus entity.
     * @Secure(roles="ROLE_BACKEND_STATUS_EDIT")
     * @RoleInfo(role="ROLE_BACKEND_STATUS_EDIT", parent="ROLE_BACKEND_STATUS_ALL", desc="Edit Status", module="Status")
     * @Route("/{className}/edit/{id}", name="backend_settings_status_edit")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function editAction($id)
    {
        $entity = $this->getEntity($id);
        $form = $this->createForm(new StatusType(), $entity);
        $request = $this->getRequest();

        if ($request->getMethod() == 'POST') {
            $form->submit($request);

            if ($form->isValid()) {

                $em = $this->getDoctrine()->getManager();
                $em->persist($entity);
                $em->flush();

                $statuses_translator = $this->get('app_status.translator');
                $statuses_translator->clearCache();
                $statuses_translator->getStatusInfo($entity->getGroup()->getClassName(), $entity->getValue());

                return $this->redirect($this->generateUrl('backend_settings_status_show', array('id' => $id, 'className' => $entity->getGroup()->getClassName())));
            }
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'group' => $entity->getGroup()
        );
    }
}
