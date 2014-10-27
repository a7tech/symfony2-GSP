<?php
/**
 * Created by JetBrains PhpStorm.
 * User: nastya
 * Date: 9/13/13
 * Time: 11:32 AM
 * To change this template use File | Settings | File Templates.
 */

namespace App\EmailBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as App;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\JsonResponse;

class EmailController extends Controller {

    /**
     * @Secure(roles="ROLE_BACKEND_ALL_SETTINGS")
     * @App\Route("/backend/ajax_email_person/personId={personId}", name="ajax_email_person")
     * @App\Method("GET")
     */
    public function emailsByPersonAjaxAction($personId) {

        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('AppEmailBundle:Email')->getEmailsByPerson($personId);

        $choices = $this->formatChoices($entities);

        return new JsonResponse(array('choices' => $choices));
    }

    /**
     * formatChoices
     *
     * @param array $entities
     * @return array
     */
    protected function formatChoices(array $entities)
    {
        $choices = array();

        foreach ($entities as $entity) {
            $choices[$entity->getId()] = array('label' => (string)$entity->getEmail(), 'value' => (string)$entity->getId());
        }

        return $choices;
    }

}