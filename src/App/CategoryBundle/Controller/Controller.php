<?php

namespace App\CategoryBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;
use App\CategoryBundle\Entity\Category;

abstract class Controller extends BaseController
{

    /**
     * 
     * @param \App\ProjectBundle\Entity\Category $entity
     * @param Category $entity
     * @param Category $oldEntity
     * @return void
     */
    protected function moveNodeToPosition(Category $entity, Category $oldEntity)
    {
        if ($entity->getLevel() !== $oldEntity->getLevel()) {
            $positions = $entity->getPosition() - 1;
        } else {
            $positions = \abs($oldEntity->getPosition() - $entity->getPosition());
        }

        $repository = $this->getRepository();
        $repository->decreaseNextSiblingsPositions($oldEntity);

        if ($entity->getLevel() !== $oldEntity->getLevel()) {
            if ($entity->getLevel() !== 0) {
                if ($positions !== 0) {
                    $repository->moveDown($entity, $positions);
                }
            } else {
                $positions = \abs($repository->countRootNodes() - $entity->getPosition());
                $repository->moveUp($entity, $positions);
            }
        } else {
            if ($oldEntity->getPosition() < $entity->getPosition()) {
                if ($positions !== 0) {
                    $repository->moveDown($entity, $positions);
                }
            } else {
                $repository->moveUp($entity, $positions);
            }
        }

        $this->fixErrors();
        $repository->increaseNextSiblingsPositions($entity);

        return;
    }

    public function moveNodeDirection(Category $entity, Category $oldEntity, $direction)
    {
        $repository = $this->getRepository();
        $repository->decreaseNextSiblingsPositions($oldEntity);
        
        if ($direction === 'left') {
            $position = $oldEntity->getParent()->getPosition() + 1;
            $entity->setPosition($position);
        } else {
            $position = 1;
            $entity->setPosition($position);
        }
        
        
        $this->fixErrors();
        $repository->increaseNextSiblingsPositions($entity);
    }

    abstract protected function getRepository();

    /**
     * EntityManager
     *
     * @return \Doctrine\ORM\EntityManager;
     */
    protected function getEntityManager()
    {
        return $this->getDoctrine()->getManager();
    }

    protected function addBackendMessage($message, $type = 'success')
    {
        $this->get('session')->getFlashBag()->add('backend_message', [
            'type'    => $type,
            'message' => $message
        ]);
    }

    /**
     * getEntity
     *
     * @param int $id
     * @return Category
     * @throws NotFoundHttpException
     */
    protected function getEntity($id)
    {
        $entity = $this->getRepository()->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Category is not found');
        }
        return $entity;
    }

    /**
     * Fixes errors on the node if there are such
     * 
     * @return void
     */
    protected function fixErrors()
    {
        $errors = $this->getRepository()->verify();
        $iteration = 0;

        while ($errors !== true && $iteration < 10) {
            $this->getRepository()->recover();
            $em = $this->getEntityManager();
            $em->flush();
            $em->clear();
            $errors = $this->getRepository()->verify();
            $iteration++;
        }
    }

}