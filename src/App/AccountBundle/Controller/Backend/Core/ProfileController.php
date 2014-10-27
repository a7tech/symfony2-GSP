<?php

namespace App\AccountBundle\Controller\Backend\Core;

use App\AccountBundle\Entity\AccountProfile;
use App\AccountBundle\Form\AccountProfileType;
use App\CompanyBundle\Entity\Company;
use App\CompanyBundle\Form\CompanyType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\Response;
use App\UserBundle\Annotation\RoleInfo;

/**
 * Profile controller.
 *
 * @RoleInfo(role="ROLE_BACKEND_PROFILE_ALL", parent="ROLE_BACKEND_PROFILE_ALL", desc="Account Profiles all access", module="Account Profile")
 */
class ProfileController extends Controller
{
    /**
     * Lists all Company entities for this user.
     * @Secure(roles="ROLE_BACKEND_PROFILE_LIST")
     * @RoleInfo(role="ROLE_BACKEND_PROFILE_LIST", parent="ROLE_BACKEND_PROFILE_ALL", desc="List Account Profiles", module="Account Profile")
     * @Route("/backend/settings/profile/", name="backend_settings_account_profile")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
//        $em = $this->getDoctrine()->getManager();

//        $entities = $em->getRepository('AppAccountBundle:AccountProfile')->findAll();

        return array(
            'entities' => [],
        );
    }
    
    /**
     * indexAction
     *
     * @Secure(roles="ROLE_BACKEND_PROFILE_LIST")
     * @RoleInfo(role="ROLE_BACKEND_PROFILE_LIST", parent="ROLE_BACKEND_PROFILE_ALL", desc="List Account Profiles", module="Account Profile")
     * @Route("/backend/settings/profile/datatables", name="backend_account_profile_datatables")
     * @Method("GET")
     */
    public function indexDatatablesAction(Request $request)
    {
        $filter = $this->get('app_account.filter.accountprofilefilter');

        if ($response = $filter->processRequest($request)) {
            return $response;
        }
    }

    /**
     * Creates a new Profile entity.
     * @Secure(roles="ROLE_BACKEND_PROFILE_CREATE")
     *
     * @Route("/backend/settings/profile/create", name="backend_settings_account_profile_create")
     * @Method("POST")
     * @Template("AppAccountBundle:Backend\Core\Profile:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $entity  = new AccountProfile();
        $form = $this->createForm(new AccountProfileType($em), $entity);
        $form->bind($request);

        if ($form->isValid()) {

            $employments = $entity->getEmployments();
            foreach($employments as $employment) {
                $employment->setCompany($entity);
                $em->persist($employment);
            }

            $currencies = $entity->getCurrencies();
            foreach($currencies as $curr) {
                $curr->setAccount($entity);
                $em->persist($curr);
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();


            return $this->redirect($this->generateUrl('backend_settings_account_profile_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new Profile entity.
     * @Secure(roles="ROLE_BACKEND_PROFILE_CREATE")
     * @RoleInfo(role="ROLE_BACKEND_PROFILE_CREATE", parent="ROLE_BACKEND_PROFILE_ALL", desc="Create Account Profile", module="Account Profile")
     * @Route("/backend/settings/profile/new", name="backend_settings_account_profile_new")
     * @Method({"PUT","GET"})
     * @Template()
     */
    public function newAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entity = new AccountProfile($em);
        $form   = $this->createForm(new AccountProfileType($em), $entity);
        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Profile entity.
     * @Secure(roles="ROLE_BACKEND_PROFILE_SHOW")
     * @RoleInfo(role="ROLE_BACKEND_PROFILE_SHOW", parent="ROLE_BACKEND_PROFILE_ALL", desc="Show Account Profile", module="Account Profile")
     * @Route("/backend/settings/profile/show/{id}", name="backend_settings_account_profile_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppAccountBundle:AccountProfile')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Company entity.');
        }


        return array(
            'entity'      => $entity,
        );
    }

    /**
     * Displays a form to edit an existing Profile entity.
     * @Secure(roles="ROLE_BACKEND_PROFILE_EDIT")
     * @RoleInfo(role="ROLE_BACKEND_PROFILE_EDIT", parent="ROLE_BACKEND_PROFILE_ALL", desc="Edit Account Profile", module="Account Profile")
     * @Route("/backend/settings/profile/edit/{id}", name="backend_settings_account_profile_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppAccountBundle:AccountProfile')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Company entity.');
        }

        $sector = $entity->getSector();
        $editForm = $this->createForm(new AccountProfileType($em, $sector), $entity);

        return array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        );
    }

    /**
     * Edits an existing Profile entity.
     * @Secure(roles="ROLE_BACKEND_PROFILE_EDIT")
     * @Route("/backend/settings/profile/update/{id}", name="backend_settings_account_profile_update")
     * @Method({"POST", "PUT"})
     * @Template("AppAccountBundle:Backend/Core/Profile:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppAccountBundle:AccountProfile')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Company entity.');
        }

        $editForm = $this->createForm(new AccountProfileType($em), $entity);
        $oldImages = $entity->getImages()->toArray();
        $oldCurr = $entity->getCurrencies()->toArray();
        $editForm->submit($request);

        if ($editForm->isValid()) {

            $removeImages = array_udiff($oldImages, $entity->getImages()->toArray(),
                function($img1, $img2) {
                    if ($img1->getId() == $img2->getId()) return 0;
                    return $img1->getId() > $img2->getId() ? 1 : -1;
                }
            );

            foreach ($removeImages as $image) {
                $entity->removeImage($image);
                $em->remove($image);
            }
            $employments = $entity->getEmployments();
            foreach($employments as $employment) {
                $employment->setCompany($entity);
                $em->persist($employment);
            }

            $currencies = $entity->getCurrencies();
            foreach($currencies as $curr) {
                $curr->setAccount($entity);
                $em->persist($curr);
            }

            $removeCurrencies = array_udiff($oldCurr, $entity->getCurrencies()->toArray(),
                function($img1, $img2) {
                    if ($img1->getId() == $img2->getId()) return 0;
                    return $img1->getId() > $img2->getId() ? 1 : -1;
                }
            );

            foreach ($removeCurrencies as $image) {
                $entity->removeCurrency($image);
                $em->remove($image);
            }

            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('backend_settings_account_profile_show', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        );
    }

    /**
     * Deletes a Profile entity.
     * @Secure(roles="ROLE_BACKEND_PROFILE_DELETE")
     * @RoleInfo(role="ROLE_BACKEND_PROFILE_DELETE", parent="ROLE_BACKEND_PROFILE_ALL", desc="Delete Account Profile", module="Account Profile")
     * @Route("/backend/settings/profile/delete/{id}", name="backend_settings_account_profile_delete")
     * @Method({"GET","DELETE"})
     */
    public function deleteAction(Request $request, $id)
    {

            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('AppAccountBundle:AccountProfile')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Company entity.');
            }

            $em->remove($entity);
            $em->flush();


        return $this->redirect($this->generateUrl('backend_settings_account_profile'));
    }
}
