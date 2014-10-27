<?php
/**
 * BarcodeTypeController
 *
 * @author Andrey Zakharov <fuse5@yandex.ru>
 * @since 05.08.13 1:48
 */

namespace App\BarcodeBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use App\BarcodeBundle\Entity\BarcodeType;
use App\BarcodeBundle\Form\BarcodeTypeType;
use Doctrine\ORM\EntityRepository;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as App;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\UserBundle\Annotation\RoleInfo;

/**
 * @App\Route("/backend/settings/custom/barcode_type")
 * @RoleInfo(role="ROLE_BACKEND_BARCODETYPE_ALL", parent="ROLE_BACKEND_BARCODETYPE_ALL", desc="Barcode types all access", module="Barcode type")
 */
class BarcodeTypeController extends Controller
{
    /**
     * getCategoryRepository
     *
     * @return EntityRepository
     */
    protected function getRepository()
    {
        return $this->getDoctrine()->getManager()->getRepository('AppBarcodeBundle:BarcodeType');
    }

    /**
     * getEntity
     *
     * @param int $id
     * @return BarcodeType
     * @throws NotFoundHttpException
     */
    protected function getEntity($id)
    {
        $entity = $this->getRepository()->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Barcode type is not found');
        }
        return $entity;
    }

    /**
     * indexAction
     *
     * @Secure(roles="ROLE_BACKEND_ALL_SETTINGS")
     * @RoleInfo(role="ROLE_BACKEND_BARCODETYPE_LIST", parent="ROLE_BACKEND_BARCODETYPE_ALL", desc="List barcodes types", module="Barcode type")
     * @App\Route("/", name="backend_settings_custom_barcode_type")
     * @App\Method("GET")
     */
    public function indexAction()
    {
        return $this->render('AppBarcodeBundle:BarcodeType:index.html.twig', array(
            'entities' => [],
        ));
    }
    
    /**
     * indexAction
     *
     * @Secure(roles="ROLE_BACKEND_ALL_SETTINGS")
     * @RoleInfo(role="ROLE_BACKEND_BARCODETYPE_LIST", parent="ROLE_BACKEND_BARCODETYPE_ALL", desc="List barcodes types", module="Barcode type")
     * @App\Route("/datatables", name="backend_barcode_type_datatables")
     * @App\Method("GET")
     */
    public function indexDatatablesAction(Request $request)
    {
        $filter = $this->get('app_barcode.filter.barcodetypefilter');

        if ($response = $filter->processRequest($request)) {
            return $response;
        }
    }

    /**
     * createAction
     *
     * @Secure(roles="ROLE_BACKEND_ALL_SETTINGS")
     * @RoleInfo(role="ROLE_BACKEND_BARCODETYPE_CREATE", parent="ROLE_BACKEND_BARCODETYPE_ALL", desc="Create barcode type", module="Barcode type")
     * @App\Route("/create", name="backend_settings_custom_barcode_type_create")
     * @App\Method({"GET", "POST"})
     */
    public function createAction()
    {
        $entity  = new BarcodeType();
        $form = $this->createForm(new BarcodeTypeType(), $entity);

        if ($this->getRequest()->getMethod() == 'POST') {
            $form->bind($this->getRequest());

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($entity);
                $em->flush();

                return $this->redirect($this->generateUrl('backend_settings_custom_barcode_type'));
            }
        }

        return $this->render('AppBarcodeBundle:BarcodeType:create.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView()
        ));
    }

    /**
     * editAction
     *
     * @Secure(roles="ROLE_BACKEND_ALL_SETTINGS")
     * @RoleInfo(role="ROLE_BACKEND_BARCODETYPE_EDIT", parent="ROLE_BACKEND_BARCODETYPE_ALL", desc="Edit barcode type", module="Barcode type")
     * @App\Route("/edit/{id}", name="backend_settings_custom_barcode_type_edit")
     * @App\Method({"GET", "POST"})
     */
    public function editAction($id)
    {
        $entity = $this->getEntity($id);
        $form = $this->createForm(new BarcodeTypeType(), $entity);

        if ($this->getRequest()->getMethod() == 'POST') {
            $form->bind($this->getRequest());

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($entity);
                $em->flush();

                return $this->redirect($this->generateUrl('backend_settings_custom_barcode_type_show', array('id' => $id)));
            }
        }

        return $this->render('AppBarcodeBundle:BarcodeType:create.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView()
        ));
    }

    /**
     * showAction
     *
     * @Secure(roles="ROLE_BACKEND_ALL_SETTINGS")
     * @RoleInfo(role="ROLE_BACKEND_BARCODETYPE_SHOW", parent="ROLE_BACKEND_BARCODETYPE_ALL", desc="Show barcode type", module="Barcode type")
     * @App\Route("/show/{id}", name="backend_settings_custom_barcode_type_show")
     * @App\Method("GET")
     */
    public function showAction($id)
    {
        return $this->render('AppBarcodeBundle:BarcodeType:show.html.twig', array(
            'entity' => $this->getEntity($id)
        ));
    }

    /**
     * deleteAction
     *
     * @Secure(roles="ROLE_BACKEND_ALL_SETTINGS")
     * @RoleInfo(role="ROLE_BACKEND_BARCODETYPE_DELETE", parent="ROLE_BACKEND_BARCODETYPE_ALL", desc="Delete barcode type", module="Barcode type")
     * @App\Route("/delete/{id}", name="backend_settings_custom_barcode_type_delete")
     * @App\Method("GET")
     */
    public function deleteAction($id)
    {
        $entity = $this->getEntity($id);

        $em = $this->getDoctrine()->getManager();
        $em->remove($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('backend_settings_custom_barcode_type'));
    }
}