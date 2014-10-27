<?php
/**
 * BrandGroupController
 *
 * @author Andrey Zakharov <fuse5@yandex.ru>
 * @since 02.08.13 17:03
 */

namespace App\ProductBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use App\ProductBundle\Entity\BrandGroup;
use App\ProductBundle\Form\BrandGroupType;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as App;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use JMS\SecurityExtraBundle\Annotation\Secure;
use App\UserBundle\Annotation\RoleInfo;

/**
 * @App\Route("/backend/settings/custom/brand_group")
 * @RoleInfo(role="ROLE_BACKEND_BRANDGROUP_ALL", parent="ROLE_BACKEND_BRANDGROUP_ALL", desc="Brand groups all access", module="Brand Group")
 */
class BrandGroupController extends Controller
{
    /**
     * getCategoryRepository
     *
     * @return EntityRepository
     */
    protected function getRepository()
    {
        return $this->getDoctrine()->getManager()->getRepository('AppProductBundle:BrandGroup');
    }

    /**
     * getEntity
     *
     * @param int $id
     * @return BrandGroup
     * @throws NotFoundHttpException
     */
    protected function getEntity($id)
    {
        $entity = $this->getRepository()->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Brand Group is not found');
        }
        return $entity;
    }

    /**
     * indexAction
     *
     * @Secure(roles="ROLE_BACKEND_BRANDGROUP_LIST")
     * @RoleInfo(role="ROLE_BACKEND_BRANDGROUP_LIST", parent="ROLE_BACKEND_BRANDGROUP_ALL", desc="List Brand groups", module="Brand Group")
     * @App\Route("/", name="backend_settings_custom_brand_group")
     * @App\Method("GET")
     */
    public function indexAction()
    {
        return $this->render('AppProductBundle:BrandGroup:index.html.twig', array(
            'entities' => [],
        ));
    }
    
    /**
     * indexAction
     *
     * @Secure(roles="ROLE_BACKEND_BRANDGROUP_LIST")
     * @RoleInfo(role="ROLE_BACKEND_BRANDGROUP_LIST", parent="ROLE_BACKEND_BRANDGROUP_ALL", desc="List Brand groups", module="Brand Group")
     * @App\Route("/datatables", name="backend_brand_group_datatables")
     * @App\Method("GET")
     */
    public function indexDatatablesAction(Request $request)
    {
        $filter = $this->get('app_product.filter.brandgroupfilter');

        if ($response = $filter->processRequest($request)) {
            return $response;
        }
    }

    /**
     * createAction
     *
     * @Secure(roles="ROLE_BACKEND_BRANDGROUP_CREATE")
     * @RoleInfo(role="ROLE_BACKEND_BRANDGROUP_CREATE", parent="ROLE_BACKEND_BRANDGROUP_ALL", desc="Create Brand group", module="Brand Group")
     * @App\Route("/create", name="backend_settings_custom_brand_group_create")
     * @App\Method({"GET", "POST"})
     */
    public function createAction()
    {
        $entity  = new BrandGroup();
        $form = $this->createForm(new BrandGroupType(), $entity);

        if ($this->getRequest()->getMethod() == 'POST') {
            $form->bind($this->getRequest());

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($entity);
                $em->flush();

                return $this->redirect($this->generateUrl('backend_settings_custom_brand_group'));
            }
        }

        return $this->render('AppProductBundle:BrandGroup:create.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView()
        ));
    }

    /**
     * editAction
     *
     * @Secure(roles="ROLE_BACKEND_BRANDGROUP_EDIT")
     * @RoleInfo(role="ROLE_BACKEND_BRANDGROUP_EDIT", parent="ROLE_BACKEND_BRANDGROUP_ALL", desc="Edit Brand group", module="Brand Group")
     * @App\Route("/edit/{id}", name="backend_settings_custom_brand_group_edit")
     * @App\Method({"GET", "POST"})
     */
    public function editAction($id)
    {
        $entity = $this->getEntity($id);
        $form = $this->createForm(new BrandGroupType(), $entity);

        if ($this->getRequest()->getMethod() == 'POST') {
            $form->bind($this->getRequest());

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($entity);
                $em->flush();

                return $this->redirect($this->generateUrl('backend_settings_custom_brand_group_show', array('id' => $id)));
            }
        }

        return $this->render('AppProductBundle:BrandGroup:create.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView()
        ));
    }

    /**
     * showAction
     *
     * @Secure(roles="ROLE_BACKEND_BRANDGROUP_SHOW")
     * @RoleInfo(role="ROLE_BACKEND_BRANDGROUP_SHOW", parent="ROLE_BACKEND_BRANDGROUP_ALL", desc="Show Brand group", module="Brand Group")
     * @App\Route("/show/{id}", name="backend_settings_custom_brand_group_show")
     * @App\Method("GET")
     */
    public function showAction($id)
    {
        return $this->render('AppProductBundle:BrandGroup:show.html.twig', array(
            'entity' => $this->getEntity($id)
        ));
    }

    /**
     * deleteAction
     *
     * @Secure(roles="ROLE_BACKEND_BRANDGROUP_DELETE")
     * @RoleInfo(role="ROLE_BACKEND_BRANDGROUP_DELETE", parent="ROLE_BACKEND_BRANDGROUP_ALL", desc="Delete Brand group", module="Brand Group")
     * @App\Route("/delete/{id}", name="backend_settings_custom_brand_group_delete")
     * @App\Method("GET")
     */
    public function deleteAction($id)
    {
        $entity = $this->getEntity($id);

        $em = $this->getDoctrine()->getManager();
        $em->remove($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('backend_settings_custom_brand_group'));
    }
}