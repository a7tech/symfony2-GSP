<?php

namespace App\AccountProductBundle\Controller;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use App\AccountProductBundle\Entity\AccountProduct;
use App\AccountProductBundle\Form\AccountProductType;

/**
 * AccountProduct controller.
 *
 * @Route("/backend/account_product")
 */
class AccountProductController extends Controller
{

    public function getEntityManager() {

        $em = $this->getDoctrine()->getManager();
        return $em;
    }

    public function getAccount($profileId) {

        $account = $this->getEntityManager()->getRepository('AppAccountBundle:AccountProfile')->findOneById($profileId);
        return $account;
    }

    public function getAccounts() {

        $profiles = $this->getDoctrine()->getManager()->getRepository('AppAccountBundle:AccountProfile')->findAll();
        return $profiles;
    }

    /**
     * Lists all AccountProduct entities.
     *
     * @Route("/profile/{profileId}/", name="backend_account_product")
     * @Method("GET")
     * @Template()
     */
    public function indexAction($profileId)
    {
        $em = $this->getEntityManager();

//        $entities = $em->getRepository('AppAccountProductBundle:AccountProduct')->findByAccount($this->getAccount($profileId));

        return array(
            'entities' => [],
            'profile_id' => $profileId,
            'account' => $this->getAccount($profileId)->getName(),
            'profiles' => $this->getAccounts(),
        );
    }
    
    /**
     * indexAction
     *
     * @Route("/datatables", name="backend_account_product_datatables")
     * @Method("GET")
     */
    public function indexDatatablesAction(Request $request)
    {
        $filter = $this->get('app_account_product.filter.accountproductfilter');

        if ($response = $filter->processRequest($request)) {
            return $response;
        }
    }
    
    /**
     * Creates a new AccountProduct entity.
     *
     * @Route("/profile/{profileId}/create", name="backend_account_product_create")
     * @Method({"GET", "POST"})
     * @Template("AppAccountProductBundle:AccountProduct:form.html.twig")
     */
    public function createAction(Request $request, $profileId)
    {
        $entity = new AccountProduct();
        $entity->setAccount($this->getAccount($profileId));
        $form = $this->createForm(new AccountProductType($this->getEntityManager(), $profileId), $entity);

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($entity);
                $em->flush();

                return $this->redirect($this->generateUrl('backend_account_product_show', array('profileId'=>$profileId,'id' => $entity->getId())));
            }
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'profile_id' => $profileId,
            'account' => $this->getAccount($profileId)->getName(),
            'profiles' => $this->getAccounts(),

        );
    }

    /**
     * Finds and displays a AccountProduct entity.
     *
     * @Route("/profile/{profileId}/show/{id}", name="backend_account_product_show")
     * @Method("GET")
     * @Template("AppAccountProductBundle:AccountProduct:show.html.twig")
     */
    public function showAction($profileId, $id)
    {
        $em = $this->getEntityManager();

        $entity = $em->getRepository('AppAccountProductBundle:AccountProduct')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AccountProduct entity.');
        }

        return array(
            'entity'      => $entity,
            'profile_id' => $profileId,
            'account' => $this->getAccount($profileId)->getName(),
            'profiles' => $this->getAccounts(),
        );
    }

    /**
     * Displays a form to edit an existing AccountProduct entity.
     *
     * @Route("/profile/{profileId}/edit/{id}", name="backend_account_product_edit")
     * @Method({"GET", "POST"})
     * @Template("AppAccountProductBundle:AccountProduct:form.html.twig")
     */
    public function editAction(Request $request,$profileId, $id)
    {
        $em = $this->getEntityManager();

        $entity = $em->getRepository('AppAccountProductBundle:AccountProduct')->find($id);
        $editForm = $this->createForm(new AccountProductType($this->getEntityManager(), $profileId), $entity);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AccountProduct entity.');
        }
        if ($request->getMethod() == 'POST') {
            $editForm->handleRequest($request);
            if ($editForm->isValid()) {
                $em->flush();

                return $this->redirect($this->generateUrl('backend_account_product_show', array('profileId'=>$profileId,'id' => $entity->getId())));
            }
        }



        return array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
            'profile_id' => $profileId,
            'account' => $this->getAccount($profileId)->getName(),
            'profiles' => $this->getAccounts(),
        );
    }

    /**
     * Deletes a AccountProduct entity.
     *
     * @Route("/profile/{profileId}/delete/{id}", name="backend_account_product_delete")
     * @Method({"DELETE", "GET"})
     */
    public function deleteAction(Request $request, $id)
    {
        $em = $this->getEntityManager();
        $entity = $this->getEntityManager()->getRepository('AppAccountProductBundle:AccountProduct')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AccountProduct entity.');
        }

        $em->remove($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('backend_account_product'));
    }
}
