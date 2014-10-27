<?php

namespace App\PersonBundle\Controller;

use App\PersonBundle\Form\PersonType;
use App\PersonBundle\Form\SearchFilterType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use App\PersonBundle\Entity\Person;
use JMS\SecurityExtraBundle\Annotation\Secure;
use App\UserBundle\Annotation\RoleInfo;

/**
 * Person controller.
 * @RoleInfo(role="ROLE_BACKEND_PERSON_ALL", parent="ROLE_BACKEND_PERSON_ALL", desc="Contacts all access", module="Contact")
 * @Route("/backend/person")
 */
class PersonController extends Controller
{
    /**
     * Lists all Person entities.
     * @Secure(roles="ROLE_BACKEND_PERSON_LIST")
     * @RoleInfo(role="ROLE_BACKEND_PERSON_LIST", parent="ROLE_BACKEND_PERSON_ALL", desc="List Contacts", module="Contact")
     * @Route("/", name="backend_person")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        
        /*
        $entities = $em->getRepository('AppPersonBundle:Person')->findAll();
        $request = $this->get('request');
        $params = $request->query->all();
        $params['limit'] = $this->container->getParameter("elastica.query.limit");
        $params['page'] = $this->container->getParameter("elastica.query.start");
        
        $searchManager = $this->container->get('app_person.search.manager');
        $entities = $searchManager->search($params);
        */
        $form = $this->createForm(new SearchFilterType($em));
        
        //return array('entities' => $entities);
        
        return array(
                'entities' => [],
                'form' => $form->createView()
        );
        
    }
    
    /**
     * indexAction
     *
     * @Secure(roles="ROLE_BACKEND_PERSON_LIST")
     * @RoleInfo(role="ROLE_BACKEND_PERSON_LIST", parent="ROLE_BACKEND_PERSON_ALL", desc="List Contacts", module="Contact")
     * @Route("/datatables", name="backend_person_datatables")
     * @Method("GET")
     */
    public function indexDatatablesAction(Request $request)
    {
        $filter = $this->get('app_person.filter.personfilter');

        if ($response = $filter->processRequest($request)) {
            return $response;
        }
    }

    /**
     * Creates a new Person entity.
     * @Secure(roles="ROLE_BACKEND_PERSON_CREATE")
     * @RoleInfo(role="ROLE_BACKEND_PERSON_CREATE", parent="ROLE_BACKEND_PERSON_ALL", desc="Create Contact", module="Contact")
     * @Route("/create", name="backend_person_create")
     * @Method({"GET","POST"})
     * @Template("AppPersonBundle:Person:form.html.twig")
     */
    public function createAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $entity  = new Person();

        $form = $this->createForm(new PersonType($em, array()), $entity);

        if ($request->getMethod() == 'POST') {
            $form->bind($request);

            if ($form->isValid()) {
                $employments = $entity->getEmployments();
                foreach($employments as $employment) {
                    $employment->setPerson($entity);
                    $em->persist($employment);
                }
                $emails = $entity->getEmails();
                foreach($emails as $email) {
                    $email->setPerson($entity);
                    $em->persist($email);
                }
                $em->persist($entity);
                $em->flush();

                return $this->redirect($this->generateUrl('backend_person_show', array('id' => $entity->getId())));
            }
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Person entity.
     * @Secure(roles="ROLE_BACKEND_PERSON_SHOW")
     * @RoleInfo(role="ROLE_BACKEND_PERSON_SHOW", parent="ROLE_BACKEND_PERSON_ALL", desc="Show Contact", module="Contact")
     * @Route("/show/{id}", name="backend_person_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('AppPersonBundle:Person')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Person entity.');
        }

        return array('entity' => $entity);
    }

    /**
     * Edits an existing Person entity.
     * @Secure(roles="ROLE_BACKEND_PERSON_EDIT")
     * @RoleInfo(role="ROLE_BACKEND_PERSON_EDIT", parent="ROLE_BACKEND_PERSON_ALL", desc="Edit Contact", module="Contact")
     * @Route("/edit/{id}", name="backend_person_edit")
     * @Method({"GET","POST"})
     * @Template("AppPersonBundle:Person:form.html.twig")
     */
    public function editAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var Person $entity */
        $entity = $em->getRepository('AppPersonBundle:Person')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Person entity.');
        }

        $editForm = $this->createForm(new PersonType($em, array()), $entity);
        $oldEmployments = $entity->getEmployments()->toArray();
        $oldSkills = $entity->getSkills()->toArray();
        $oldEmails = $entity->getEmails()->toArray();
        if ($request->getMethod() == 'POST') {
            $editForm->bind($request);

            if ($editForm->isValid()) {

                $removeEmployments = array_udiff($oldEmployments, $entity->getEmployments()->toArray(),
                    function($img1, $img2) {
                        if ($img1->getId() == $img2->getId()) return 0;
                        return $img1->getId() > $img2->getId() ? 1 : -1;
                    }
                );

                foreach ($removeEmployments as $emp) {
                    $entity->removeEmployment($emp);
                    $em->remove($emp);
                }

                $removeSkills = array_udiff($oldSkills, $entity->getSkills()->toArray(),
                    function($img1, $img2) {
                        if ($img1->getId() == $img2->getId()) return 0;
                        return $img1->getId() > $img2->getId() ? 1 : -1;
                    }
                );

                foreach ($removeSkills as $skill) {
                    $entity->removeSkill($skill);
                    $em->remove($skill);
                }

                $employments = $entity->getEmployments();
                foreach($employments as $employment) {
                    $employment->setPerson($entity);
                    $em->persist($employment);
                }

                $emails = $entity->getEmails();
                foreach($emails as $email) {
                    $email->setPerson($entity);
                    $em->persist($email);
                }
                $removeEmails = array_udiff($oldEmails, $entity->getEmails()->toArray(),
                    function($img1, $img2) {
                        if ($img1->getId() == $img2->getId()) return 0;
                        return $img1->getId() > $img2->getId() ? 1 : -1;
                    }
                );

                foreach ($removeEmails as $skill) {
                    $entity->removeEmail($skill);
                    $em->remove($skill);
                }

                $em->persist($entity);
                $em->flush();

                return $this->redirect($this->generateUrl('backend_person_show', array('id' => $id)));
            }
        }

        return array(
            'entity' => $entity,
            'form'   => $editForm->createView(),
        );
    }

    /**
     * Deletes a Person entity.
     * @Secure(roles="ROLE_BACKEND_PERSON_DELETE")
     * @RoleInfo(role="ROLE_BACKEND_PERSON_DELETE", parent="ROLE_BACKEND_PERSON_ALL", desc="Delete Contact", module="Contact")
     * @Route("/delete/{id}", name="backend_person_delete")
     * @Method({"GET","DELETE"})
     */
    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('AppPersonBundle:Person')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Person entity.');
        }

        $em->remove($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('backend_person'));
    }

    /**
     * @param $companyId
     * @Secure(roles="ROLE_BACKEND_PERSON_ALL")
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/ajax_customer_shipping_address/customerId={customerId}", name="ajax_customer_shipping_address")
     */
    public function getCustomerShippingAddressAjax($customerId) {

        $em = $this->getDoctrine()->getManager();

        $address = $em->getRepository('AppPersonBundle:Person')->getCustomerShippingAddress($customerId);

        return new JsonResponse($address);
    }

    /**
     * @param $companyId
     * @Secure(roles="ROLE_BACKEND_PERSON_ALL")
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/ajax_customer_billing_address/customerId={customerId}", name="ajax_customer_billing_address")
     */
    public function getCustomerBillingAddressAjax($customerId) {

        $em = $this->getDoctrine()->getManager();

        $address = $em->getRepository('AppPersonBundle:Person')->getCustomerBillingAddress($customerId);

        return new JsonResponse($address);
    }

    /**
     * @param $companyId
     * @Secure(roles="ROLE_BACKEND_PERSON_ALL")
     * @Route("/ajax_customer_by_company/companyId={companyId}", name="ajax_customer_by_company")
     */
    public function getCustomerByCompany($companyId) {

        $em=$this->getDoctrine()->getManager();

        $persons = $em->getRepository('AppPersonBundle:Person')->getCustomerByCompany($companyId);

        $choices = $this->formatChoices($persons);

        return new JsonResponse(array('choices' => $choices));
    }

    protected function formatChoices(array $entities)
    {
        $choices = array();

        foreach ($entities as $entity) {
            $choices[$entity->getId()] = array('value' => $entity->getId(), 'label' => (string)$entity);
        }

        return $choices;
    }
}
