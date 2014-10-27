<?php
/**
 * ProductTypeController
 *
 * @author Andrey Zakharov <fuse5@yandex.ru>
 * @since 15.07.13 15:04
 */

namespace App\ProductBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as App;
use App\ProductBundle\Entity\ProductType;
use App\ProductBundle\Form\ProductTypeType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use JMS\SecurityExtraBundle\Annotation\Secure;
use App\UserBundle\Annotation\RoleInfo;

/**
 * @App\Route("/backend/settings/custom/product_type")
 * @RoleInfo(role="ROLE_BACKEND_PRODUCTTYPE_ALL", parent="ROLE_BACKEND_PRODUCTTYPE_ALL", desc="Product types all access", module="Product type")
 */
class ProductTypeController extends Controller
{
    /**
     * getCategoryRepository
     *
     * @return EntityRepository
     */
    protected function getRepository()
    {
        return $this->getDoctrine()->getManager()->getRepository('AppProductBundle:ProductType');
    }

    /**
     * getEntity
     *
     * @param int $id
     * @return ProductType
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
     *@Secure(roles="ROLE_BACKEND_PRODUCTTYPE_LIST")
     * @RoleInfo(role="ROLE_BACKEND_PRODUCTTYPE_LIST", parent="ROLE_BACKEND_PRODUCTTYPE_ALL", desc="List Product types", module="Product type")
     * @App\Route("/", name="backend_settings_custom_product_type")
     * @App\Method("GET")
     */
    public function indexAction()
    {
        return $this->render('AppProductBundle:ProductType:index.html.twig', array(
            'entities' => [],
        ));
    }
    
    /**
     * indexAction
     *
     * @Secure(roles="ROLE_BACKEND_PRODUCTTYPE_LIST")
     * @RoleInfo(role="ROLE_BACKEND_PRODUCTTYPE_LIST", parent="ROLE_BACKEND_PRODUCTTYPE_ALL", desc="List Product types", module="Product type")
     * @App\Route("/datatables", name="backend_product_type_datatables")
     * @App\Method("GET")
     */
    public function indexDatatablesAction(Request $request)
    {
        $filter = $this->get('app_product.filter.producttypefilter');

        if ($response = $filter->processRequest($request)) {
            return $response;
        }
    }

    /**
     * createAction
     *
     * @Secure(roles="ROLE_BACKEND_PRODUCTTYPE_CREATE")
     * @RoleInfo(role="ROLE_BACKEND_PRODUCTTYPE_CREATE", parent="ROLE_BACKEND_PRODUCTTYPE_ALL", desc="Create Product type", module="Product type")
     * @App\Route("/create", name="backend_settings_custom_product_type_create")
     * @App\Method({"GET", "POST"})
     */
    public function createAction()
    {
        $entity = new ProductType();
        $form = $this->createForm(new ProductTypeType(), $entity);

        $request = $this->getRequest();
        if ($request->getMethod() == 'POST') {
            $form->bind($request);

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($entity);
                $em->flush();

                return $this->redirect($this->generateUrl('backend_settings_custom_product_type'));
            }
        }

        return $this->render('AppProductBundle:ProductType:create.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView()
        ));
    }

    /**
     * editAction
     *
     * @Secure(roles="ROLE_BACKEND_PRODUCTTYPE_EDIT")
     * @RoleInfo(role="ROLE_BACKEND_PRODUCTTYPE_EDIT", parent="ROLE_BACKEND_PRODUCTTYPE_ALL", desc="Edit Product type", module="Product type")
     * @App\Route("/edit/{id}", name="backend_settings_custom_product_type_edit")
     * @App\Method({"GET", "POST"})
     */
    public function editAction($id)
    {
        $entity = $this->getEntity($id);
        $form = $this->createForm(new ProductTypeType(), $entity);

        $request = $this->getRequest();
        if ($request->getMethod() == 'POST') {
            $form->bind($request);

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($entity);
                $em->flush();

                return $this->redirect($this->generateUrl('backend_settings_custom_product_type_edit', array('id' => $id)));
            }
        }

        return $this->render('AppProductBundle:ProductType:create.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView()
        ));
    }

    /**
     * showAction
     *
     * @Secure(roles="ROLE_BACKEND_PRODUCTTYPE_SHOW")
     * @RoleInfo(role="ROLE_BACKEND_PRODUCTTYPE_SHOW", parent="ROLE_BACKEND_PRODUCTTYPE_ALL", desc="Show Product type", module="Product type")
     * @App\Route("/show/{id}", name="backend_settings_custom_product_type_show")
     * @App\Method("GET")
     */
    public function showAction($id)
    {
        return $this->render('AppProductBundle:ProductType:show.html.twig', array(
            'entity' => $this->getEntity($id)
        ));
    }

    /**
     * deleteAction
     *
     * @Secure(roles="ROLE_BACKEND_PRODUCTTYPE_DELETE")
     * @RoleInfo(role="ROLE_BACKEND_PRODUCTTYPE_DELETE", parent="ROLE_BACKEND_PRODUCTTYPE_ALL", desc="Delete Product type", module="Product type")
     * @App\Route("/delete/{id}", name="backend_settings_custom_product_type_delete")
     * @App\Method("GET")
     */
    public function deleteAction($id)
    {
        $entity = $this->getEntity($id);

        $em = $this->getDoctrine()->getManager();
        $em->remove($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('backend_settings_custom_product_type'));
    }
}