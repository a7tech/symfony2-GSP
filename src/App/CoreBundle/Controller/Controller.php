<?php
/**
 * Created by PhpStorm.
 * User: Maciej
 * Date: 16.01.14
 * Time: 11:09
 */

namespace App\CoreBundle\Controller;

use App\UserBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;

class Controller extends BaseController
{
    /**
     * @return EntityManager
     */
    public function getEntityManager()
    {
        return $this->getDoctrine()->getManager();
    }

    public function getRepository($class)
    {
        return $this->getEntityManager()->getRepository($class);
    }

    public function redirectToRoute($route, $parameters = [], $absolutePath = false)
    {
        return $this->redirect($this->generateUrl($route, $parameters, $absolutePath));
    }

    public function addAdminMessage($message, $type = 'success')
    {
        $this->get('session')->getFlashBag()->add('backend_message', [
            'type' => $type,
            'message' => $message
        ]);
    }

    /**
     * @return User
     */
    public function getCurrentUser()
    {
        return $this->get('security.context')->getToken()->getUser();
    }

    public function getEntitiesToRemove(array $new, array $old)
    {
        return array_udiff($old, $new,
            function($entity1, $entity2) {
                if ($entity1->getId() == $entity2->getId()) return 0;
                return $entity1->getId() > $entity2->getId() ? 1 : -1;
            }
        );
    }

    public function removeOldEntities(array $new, array $old)
    {
        $toRemove = $this->getEntitiesToRemove($new, $old);

        $entityManager = $this->getEntityManager();
        foreach($toRemove as $entity){
            $entityManager->remove($entity);
        }
    }
} 