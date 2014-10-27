<?php
/**
 * ProductController
 *
 * @author Andrey Zakharov <fuse5@yandex.ru>
 * @since 10.07.13 0:13
 */

namespace App\ProductBundle\Controller;

use App\ProductBundle\Entity\Product;
use App\ProductBundle\Form\ProductType;
use App\ProductBundle\Form\SearchFilterType;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as App;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use JMS\SecurityExtraBundle\Annotation\Secure;
use App\UserBundle\Annotation\RoleInfo;

/**
 * @App\Route("/backend/product")
 * @RoleInfo(role="ROLE_BACKEND_PRODUCT_ALL", parent="ROLE_BACKEND_PRODUCT_ALL", desc="Products all access", module="Product")
 */
class ProductController extends Controller
{


    /**
     * getCategoryRepository
     *
     * @return EntityRepository
     */
    protected function getRepository()
    {
        return $this->getDoctrine()->getManager()->getRepository('AppProductBundle:Product');
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
            throw $this->createNotFoundException('Product is not found');
        }
        return $entity;
    }

    public function getAccount() {

        $profiles = $this->getDoctrine()->getManager()->getRepository('AppAccountBundle:AccountProfile')->findAll();
        return $profiles;
    }


    /**
     * indexAction
     *
     * @Secure(roles="ROLE_BACKEND_PRODUCT_LIST")
     * @RoleInfo(role="ROLE_BACKEND_PRODUCT_LIST", parent="ROLE_BACKEND_PRODUCT_ALL", desc="List Products", module="Product")
     * @App\Route("/", name="backend_products")
     * @App\Method({"GET", "POST"})
     */
    public function indexAction()
    {
        /*
        $request = $this->getRequest();
        $params = $request->query->all();
        $params['limit'] = $this->container->getParameter("elastica.query.limit");
        $params['page'] = $this->container->getParameter("elastica.query.start");
        
        $searchManager = $this->container->get('app_product.search.manager');
        $entities = $searchManager->search($params);
        */
       
        /*
        $entity = new Product();
        $form = $this->createForm(new SearchFilterType($this->getDoctrine()->getManager()), $entity);
        if ($request->getMethod() == 'POST') {
            $form->bind($request);
        }else{
            $form = $this->createForm(new SearchFilterType());
        }
        */
//        $entities = $this->getRepository()->findAll();
        $form = $this->createForm(new SearchFilterType());

        return $this->render('AppProductBundle:Product:index.html.twig', array(
            'entities' => [],
            'form' => $form->createView(),
            'profiles' => $this->getAccount(),
        ));
        
    }
    
    /**
     * indexAction
     *
     * @Secure(roles="ROLE_BACKEND_PRODUCT_LIST")
     * @RoleInfo(role="ROLE_BACKEND_PRODUCT_LIST", parent="ROLE_BACKEND_PRODUCT_ALL", desc="List Products", module="Product")
     * @App\Route("/datatables", name="backend_product_datatables")
     * @App\Method("GET")
     */
    public function indexDatatablesAction(Request $request)
    {
        $filter = $this->get('app_product.filter.productfilter');

        if ($response = $filter->processRequest($request)) {
            return $response;
        }
    }

    /**
     * Create product
     *
     * @Secure(roles="ROLE_BACKEND_PRODUCT_CREATE")
     * @RoleInfo(role="ROLE_BACKEND_PRODUCT_CREATE", parent="ROLE_BACKEND_PRODUCT_ALL", desc="Create Product", module="Product")
     * @App\Route("/create", name="backend_products_create")
     * @App\Method({"GET", "POST"})
     */
    public function createAction()
    {
        $entity = new Product();
        $form = $this->createForm(new ProductType($this->getDoctrine()->getManager()), $entity);

        $request = $this->getRequest();
        if ($request->getMethod() == 'POST') {
            $form->bind($request);

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($entity);
                $em->flush();

                return $this->redirect($this->generateUrl('backend_products_show', array('id' => $entity->getId())));
            }
        }

        return $this->render('AppProductBundle:Product:create.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),
            'profiles' => $this->getAccount(),
        ));
    }

    /**
     * editAction
     *
     * @Secure(roles="ROLE_BACKEND_PRODUCT_EDIT")
     * @RoleInfo(role="ROLE_BACKEND_PRODUCT_EDIT", parent="ROLE_BACKEND_PRODUCT_ALL", desc="Edit Product", module="Product")
     * @App\Route("/edit/{id}", name="backend_products_edit")
     * @App\Method({"GET", "POST"})
     */
    public function editAction($id)
    {
        $entity = $this->getEntity($id);
        $form = $this->createForm(new ProductType($this->getDoctrine()->getManager()), $entity);
        $request = $this->getRequest();

        if ($request->getMethod() == 'POST') {
            $oldImages = $entity->getImages()->toArray();
            $form->bind($request);

            if ($form->isValid()) {
                $removeImages = array_udiff($oldImages, $entity->getImages()->toArray(),
                    function($img1, $img2) {
                        if ($img1->getId() == $img2->getId()) return 0;
                        return $img1->getId() > $img2->getId() ? 1 : -1;
                    }
                );

                $em = $this->getDoctrine()->getManager();

                foreach ($removeImages as $image) {
                    $entity->removeImage($image);
                    $em->remove($image);
                }

                $em->persist($entity);
                $em->flush();

                return $this->redirect($this->generateUrl('backend_products_show', array('id' => $id)));
            }
        }

        return $this->render('AppProductBundle:Product:create.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),
            'profiles' => $this->getAccount(),
        ));
    }

    /**
     * showAction
     *
     * @Secure(roles="ROLE_BACKEND_PRODUCT_SHOW")
     * @RoleInfo(role="ROLE_BACKEND_PRODUCT_SHOW", parent="ROLE_BACKEND_PRODUCT_ALL", desc="Show Product", module="Product")
     * @App\Route("/show/{id}", name="backend_products_show")
     * @App\Method("GET")
     */
    public function showAction($id)
    {
        return $this->render('AppProductBundle:Product:show.html.twig', array(
            'entity' => $this->getEntity($id),
            'profiles' => $this->getAccount(),
        ));
    }

    /**
     * deleteAction
     *
     * @Secure(roles="ROLE_BACKEND_PRODUCT_DELETE")
     * @RoleInfo(role="ROLE_BACKEND_PRODUCT_DELETE", parent="ROLE_BACKEND_PRODUCT_ALL", desc="Delete Product", module="Product")
     * @App\Route("/delete/{id}", name="backend_products_delete")
     * @App\Method("GET")
     */
    public function deleteAction($id)
    {
        $entity = $this->getEntity($id);

        $em = $this->getDoctrine()->getManager();
        $em->remove($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('backend_products'));
    }

    /**
     * @Secure(roles="ROLE_BACKEND_PRODUCT_ALL")
     * @App\Route("/ajax_get_produc_info/product_id={productId}", name="ajax_get_product_info")
     * @App\Method("GET")
     * @param $productId
     */
    public function getProductInfoAjax($productId) {

        $em = $this->getDoctrine()->getManager();

        $info = $em->getRepository('AppProductBundle:Product')->getProductInfo($productId);

        return new JsonResponse($info);

    }
}