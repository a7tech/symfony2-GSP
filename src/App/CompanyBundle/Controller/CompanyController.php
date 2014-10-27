<?php

namespace App\CompanyBundle\Controller;

use App\CompanyBundle\Form\SearchFilterType;
use App\CoreBundle\Controller\Controller;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use App\CompanyBundle\Entity\Company;
use App\CompanyBundle\Form\CompanyType;
use App\UserBundle\Annotation\RoleInfo;

/**
 * Company controller.
 * @RoleInfo(role="ROLE_BACKEND_COMPANY_ALL", parent="ROLE_BACKEND_COMPANY_ALL", desc="Companies all access", module="Companies")
 * @Route("/backend/company")
 */
class CompanyController extends Controller
{
    /**
     * Lists all Company entities.
     * @Secure(roles="ROLE_BACKEND_COMPANY_LIST")
     * @RoleInfo(role="ROLE_BACKEND_COMPANY_LIST", parent="ROLE_BACKEND_COMPANY_ALL", desc="List Companies", module="Companies")
     * @Route("/", name="backend_company")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        
        $em = $this->getDoctrine()->getManager();
        /*
        $entities = $em->getRepository('AppCompanyBundle:Company')->findAll();
        */
        
        /*
        $request = $this->get('request');
        $params = $request->query->all();
        $params['limit'] = $this->container->getParameter("elastica.query.limit");
        $params['page'] = $this->container->getParameter("elastica.query.start");
        
        $searchManager = $this->container->get('app_company.search.manager');
        $entities = $searchManager->search($params);
        */
        /*
        $request = $this->get('request');
        $params = $request->query->all();
        $entities = $em->getRepository('AppCompanyBundle:Company')->getCompanyList($params);
        */
        $form = $this->createForm(new SearchFilterType($em));

        return array(
                'entities' => [],
                'form' => $form->createView()
        );
        

    }
    
    /**
     * indexAction
     *
     * @Secure(roles="ROLE_BACKEND_COMPANY_LIST")
     * @RoleInfo(role="ROLE_BACKEND_COMPANY_LIST", parent="ROLE_BACKEND_COMPANY_ALL", desc="List Companies", module="Companies")
     * @Route("/datatables", name="backend_company_datatables")
     * @Method("GET")
     */
    public function indexDatatablesAction(Request $request)
    {
        $filter = $this->get('app_company.filter.companyfilter');

        if ($response = $filter->processRequest($request)) {
            return $response;
        }
    }

    /**
     * Creates a new Company entity.
     * @Secure(roles="ROLE_BACKEND_COMPANY_CREATE")
     *
     * @Route("/create", name="backend_company_create")
     * @Method({"POST","GET"})
     * @Template("AppCompanyBundle:Company:edit.html.twig")
     */
    public function createAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $entity  = new Company();
        $form = $this->createForm(new CompanyType($em), $entity);

        if($request->getMethod() == 'POST'){
            $form->submit($request);

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($entity);
                $em->flush();

                return $this->redirect($this->generateUrl('backend_company_show', array('id' => $entity->getId())));
            }
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new Company entity.
     * @Secure(roles="ROLE_BACKEND_COMPANY_CREATE")
     * @RoleInfo(role="ROLE_BACKEND_COMPANY_CREATE", parent="ROLE_BACKEND_COMPANY_ALL", desc="Create Company", module="Companies")
     * @Route("/new", name="backend_company_new")
     * @Method({"PUT","GET"})
     * @Template("AppCompanyBundle:Company:edit.html.twig")
     */
    public function newAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entity = new Company();
        $form   = $this->createForm(new CompanyType($em), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Company entity.
     * @Secure(roles="ROLE_BACKEND_COMPANY_SHOW")
     * @RoleInfo(role="ROLE_BACKEND_COMPANY_SHOW", parent="ROLE_BACKEND_COMPANY_ALL", desc="Show Company", module="Companies")
     * @Route("/show/{id}", name="backend_company_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppCompanyBundle:Company')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Company entity.');
        }

        return array(
            'entity'      => $entity
        );
    }

    /**
     * Displays a form to edit an existing Company entity.
     * @Secure(roles="ROLE_BACKEND_COMPANY_EDIT")
     * @RoleInfo(role="ROLE_BACKEND_COMPANY_EDIT", parent="ROLE_BACKEND_COMPANY_ALL", desc="Edit Company", module="Companies")
     * @Route("/edit/{id}", name="backend_company_edit")
     * @Template()
     */
    public function editAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var Company $entity */
        $entity = $em->getRepository('AppCompanyBundle:Company')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Company entity.');
        }


        $sector = $entity->getSector();
        $editForm = $this->createForm(new CompanyType($em, $sector), $entity);



        if($request->getMethod() == 'POST'){
            $oldImages = $entity->getImages()->toArray();
            $oldEmployments = $entity->getEmployments()->toArray();

            $editForm->submit($request);

            if ($editForm->isValid()) {

                $removeImages = $this->getEntitiesToRemove($entity->getImages()->toArray(), $oldImages);

                foreach ($removeImages as $image) {
                    $entity->removeImage($image);
                    $em->remove($image);
                }

                $this->removeOldEntities($entity->getEmployments()->toArray(), $oldEmployments);

                $em->persist($entity);
                $em->flush();

                return $this->redirect($this->generateUrl('backend_company_show', array('id' => $id)));
            }
        }

        return array(
            'entity'      => $entity,
            'form'   => $editForm->createView()
        );
    }

    /**
     * Deletes a Company entity.
     * @Secure(roles="ROLE_BACKEND_COMPANY_DELETE")
     * @RoleInfo(role="ROLE_BACKEND_COMPANY_DELETE", parent="ROLE_BACKEND_COMPANY_ALL", desc="Delete Company", module="Companies")
     * @Route("/delete/{id}", name="backend_company_delete")
     * @Method({"GET","DELETE"})
     */
    public function deleteAction(Request $request, $id)
    {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('AppCompanyBundle:Company')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Company entity.');
            }

            $em->remove($entity);
            $em->flush();


        return $this->redirect($this->generateUrl('backend_company'));
    }

    /**
     * @Secure(roles="ROLE_BACKEND_COMPANY_ALL")
     * @param $companyId
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/ajax_company_shipping_address/companyId={companyId}", name="ajax_company_shipping_address")
     */
    public function getCompanyShippingAddressAjax($companyId) {

        $em = $this->getDoctrine()->getManager();

        $address = $em->getRepository('AppCompanyBundle:CommonCompany')->getCompanyShippingAddress($companyId);

        return new JsonResponse($address);
    }

    /**
     * @Secure(roles="ROLE_BACKEND_COMPANY_ALL")
     * @param $companyId
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/ajax_company_billing_address/companyId={companyId}", name="ajax_company_billing_address")
     */
    public function getCompanyBillingAddressAjax($companyId) {

        $em = $this->getDoctrine()->getManager();

        $address = $em->getRepository('AppCompanyBundle:CommonCompany')->getCompanyBillingAddress($companyId);

        return new JsonResponse($address);
    }
}
