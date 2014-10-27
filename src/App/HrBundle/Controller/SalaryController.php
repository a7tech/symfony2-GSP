<?php
/**
 * SalaryController
 *
 * @author Andrey Zakharov <fuse5@yandex.ru>
 * @since 23.08.13 17:17
 */

namespace App\HrBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use App\HrBundle\Entity\SalaryType;
use App\HrBundle\Form\SalaryTypeType;
use Doctrine\ORM\EntityRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as App;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\UserBundle\Annotation\RoleInfo;
use JMS\SecurityExtraBundle\Annotation\Secure;

/**
 * @App\Route("/backend/settings/custom/salary_type")
 * @RoleInfo(role="ROLE_BACKEND_SALARYTYPE_ALL", parent="ROLE_BACKEND_SALARYTYPE_ALL", desc="Salary types all access", module="Salary type")
 */
class SalaryController extends Controller
{
    /**
     * getRepository
     * @return EntityRepository
     */
    protected function getRepository()
    {
        return $this->getDoctrine()->getManager()->getRepository('AppHrBundle:SalaryType');
    }

    /**
     * getEntity
     *
     * @param int $id
     * @return SalaryType
     * @throws NotFoundHttpException
     */
    protected function getEntity($id)
    {
        $entity = $this->getRepository()->find($id);
        if (!$entity) {
            throw $this->createNotFoundException();
        }
        return $entity;
    }

    /**
     * indexAction
     *
     * @App\Route("/", name="backend_settings_custom_salary_type")
     * @Secure(roles="ROLE_BACKEND_SALARYTYPE_LIST")
     * @RoleInfo(role="ROLE_BACKEND_SALARYTYPE_LIST", parent="ROLE_BACKEND_SALARYTYPE_ALL", desc="List Salary types", module="Salary type")
     * @App\Method("GET")
     */
    public function indexAction()
    {
        return $this->render('AppHrBundle:SalaryType:index.html.twig', array(
            'entities' => [],
        ));
    }
    
    /**
     * indexAction
     *
     * @Secure(roles="ROLE_BACKEND_SALARYTYPE_LIST")
     * @RoleInfo(role="ROLE_BACKEND_SALARYTYPE_LIST", parent="ROLE_BACKEND_SALARYTYPE_ALL", desc="List Salary types", module="Salary type")
     * @App\Route("/datatables", name="backend_salary_type_datatables")
     * @App\Method("GET")
     */
    public function indexDatatablesAction(Request $request)
    {
        $filter = $this->get('app_hr.filter.salarytypefilter');

        if ($response = $filter->processRequest($request)) {
            return $response;
        }
    }

    /**
     * createAction
     *
     * @App\Route("/create", name="backend_settings_custom_salary_type_create")
     * @Secure(roles="ROLE_BACKEND_SALARYTYPE_CREATE")
     * @RoleInfo(role="ROLE_BACKEND_SALARYTYPE_CREATE", parent="ROLE_BACKEND_SALARYTYPE_ALL", desc="Create Salary type", module="Salary type")
     * @App\Method({"GET", "POST"})
     */
    public function createAction()
    {
        $entity = new SalaryType();
        $form = $this->createForm(new SalaryTypeType(), $entity);

        $request = $this->getRequest();
        if ($request->getMethod() == 'POST') {
            $form->bind($request);

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($entity);
                $em->flush();

                return $this->redirect($this->generateUrl('backend_settings_custom_salary_type_show', array('id' => $entity->getId())));
            }
        }

        return $this->render('AppHrBundle:SalaryType:create.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView()
        ));
    }

    /**
     * editAction
     *
     * @App\Route("/edit/{id}", name="backend_settings_custom_salary_type_edit")
     * @Secure(roles="ROLE_BACKEND_SALARYTYPE_EDIT")
     * @RoleInfo(role="ROLE_BACKEND_SALARYTYPE_EDIT", parent="ROLE_BACKEND_SALARYTYPE_ALL", desc="Edit Salary type", module="Salary type")
     * @App\Method({"GET", "POST"})
     */
    public function editAction($id)
    {
        $entity = $this->getEntity($id);
        $form = $this->createForm(new SalaryTypeType(), $entity);

        $request = $this->getRequest();
        if ($request->getMethod() == 'POST') {
            $form->bind($request);

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($entity);
                $em->flush();

                return $this->redirect($this->generateUrl('backend_settings_custom_salary_type_edit', array('id' => $id)));
            }
        }

        return $this->render('AppHrBundle:SalaryType:create.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView()
        ));
    }

    /**
     * showAction
     *
     * @App\Route("/show/{id}", name="backend_settings_custom_salary_type_show")
     * @Secure(roles="ROLE_BACKEND_SALARYTYPE_SHOW")
     * @RoleInfo(role="ROLE_BACKEND_SALARYTYPE_SHOW", parent="ROLE_BACKEND_SALARYTYPE_ALL", desc="Show Salary type", module="Salary type")
     * @App\Method("GET")
     */
    public function showAction($id)
    {
        return $this->render('AppHrBundle:SalaryType:show.html.twig', array(
            'entity' => $this->getEntity($id)
        ));
    }

    /**
     * deleteAction
     *
     * @App\Route("/delete/{id}", name="backend_settings_custom_salary_type_delete")
     * @Secure(roles="ROLE_BACKEND_SALARYTYPE_DELETE")
     * @RoleInfo(role="ROLE_BACKEND_SALARYTYPE_DELETE", parent="ROLE_BACKEND_SALARYTYPE_ALL", desc="Delete Salary type", module="Salary type")
     * @App\Method("GET")
     */
    public function deleteAction($id)
    {
        $entity = $this->getEntity($id);

        $em = $this->getDoctrine()->getManager();
        $em->remove($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('backend_settings_custom_salary_type'));
    }
}