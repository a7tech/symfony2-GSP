<?php

/**
 * SkillCategoryController
 *
 * @author Andrey Zakharov <fuse5@yandex.ru>
 * @since 16.08.13 17:15
 */

namespace App\SkillBundle\Controller;

use App\CategoryBundle\Entity\CategoryRepository;
use App\CategoryBundle\NestedSet\TreeWrapper;
use App\SkillBundle\Entity\Category;
use App\SkillBundle\Form\CategoryType;
use App\CategoryBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as App;
use JMS\SecurityExtraBundle\Annotation\Secure;
use App\UserBundle\Annotation\RoleInfo;

/**
 * @App\Route("/backend/settings/category/skill")
 * @RoleInfo(role="ROLE_BACKEND_SKILL_CATEGORY_ALL", parent="ROLE_BACKEND_SKILL_CATEGORY_ALL", desc="Skill categoties all access", module="Skill category")
 */
class CategoryController extends Controller
{

    /**
     * getCategoryRepository
     *
     * @return CategoryRepository
     */
    protected function getRepository()
    {
        return $this->getEntityManager()->getRepository('AppSkillBundle:Category');
    }

    /**
     * indexAction
     *
     * @Secure(roles="ROLE_BACKEND_SKILL_CATEGORY_LIST")
     * @RoleInfo(role="ROLE_BACKEND_SKILL_CATEGORY_LIST", parent="ROLE_BACKEND_SKILL_CATEGORY_ALL", desc="List Skill categoties", module="Skill category")
     * @App\Route("/", name="backend_settings_category_skill")
     * @App\Method("GET")
     */
    public function indexAction()
    {
        $categoryList = $this->getRepository()->getList();
        $treeWrapper  = new TreeWrapper($categoryList);

        return $this->render('AppSkillBundle:Category:index.html.twig', array(
                    'category_list' => $categoryList,
                    'tree_wrapper'  => $treeWrapper,
        ));
    }

    /**
     * createAction
     *
     * @Secure(roles="ROLE_BACKEND_SKILL_CATEGORY_CREATE")
     * @RoleInfo(role="ROLE_BACKEND_SKILL_CATEGORY_CREATE", parent="ROLE_BACKEND_SKILL_CATEGORY_ALL", desc="Create Skill category", module="Skill category")
     * @App\Route("/create", name="backend_settings_category_skill_create")
     * @App\Method({"GET", "POST"})
     */
    public function createAction()
    {
        $entity = new Category();
        $form   = $this->createForm('app_skillbundle_categorytype', $entity);
        $this->fixErrors();

        if ($this->getRequest()->getMethod() == 'POST') {
            $form->bind($this->getRequest());

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($entity);
                $em->flush();

                $repository = $this->getRepository();
                if ($entity->getLevel() !== 0) {
                    $repository->moveUp($entity, $repository->countChildrenForNode($entity->getParent()) - $entity->getPosition());
                } else {
                    $positions = \abs($repository->countRootNodes() - $entity->getPosition());
                    $repository->moveUp($entity, $positions);
                }

                $repository->increaseNextSiblingsPositions($entity);
                $em->clear();

                return $this->redirect($this->generateUrl('backend_settings_category_skill'));
            }
        }

        return $this->render('AppSkillBundle:Category:create.html.twig', array(
                    'entity' => $entity,
                    'form'   => $form->createView()
        ));
    }

    /**
     * editAction
     *
     * @Secure(roles="ROLE_BACKEND_SKILL_CATEGORY_EDIT")
     * @RoleInfo(role="ROLE_BACKEND_SKILL_CATEGORY_EDIT", parent="ROLE_BACKEND_SKILL_CATEGORY_ALL", desc="Edit Skill category", module="Skill category")
     * @App\Route("/edit/{id}", name="backend_settings_category_skill_edit")
     * @App\Method({"GET", "POST"})
     */
    public function editAction($id)
    {
        $entity    = $this->getEntity($id);
        $em        = $this->getEntityManager();
        $form      = $this->createForm(new CategoryType($em, $entity->getSector()), $entity);
        $oldEntity = clone $entity;

        if ($this->getRequest()->getMethod() == 'POST') {
            $form->bind($this->getRequest());

            if ($form->isValid()) {
                $em->persist($entity);
                $em->flush();
                
                if (($entity->getPosition() !== $oldEntity->getPosition() && $entity->getLevel() !== $oldEntity->getLevel()) 
                        || ($entity->getLevel() !== $oldEntity->getLevel()) || ($entity->getPosition() !== $oldEntity->getPosition())) {
                    $this->moveNodeToPosition($entity, $oldEntity);
                }

                return $this->redirect($this->generateUrl('backend_settings_category_skill_show', array('id' => $id)));
            }
        }

        return $this->render('AppSkillBundle:Category:create.html.twig', array(
                    'entity' => $entity,
                    'form'   => $form->createView()
        ));
    }

    /**
     * showAction
     *
     * @Secure(roles="ROLE_BACKEND_SKILL_CATEGORY_SHOW")
     * @RoleInfo(role="ROLE_BACKEND_SKILL_CATEGORY_SHOW", parent="ROLE_BACKEND_SKILL_CATEGORY_ALL", desc="Show Skill category", module="Skill category")
     * @App\Route("/show/{id}", name="backend_settings_category_skill_show")
     * @App\Method("GET")
     */
    public function showAction($id)
    {
        return $this->render('AppSkillBundle:Category:show.html.twig', array(
                    'entity' => $this->getEntity($id)
        ));
    }

    /**
     * deleteAction
     *
     * @Secure(roles="ROLE_BACKEND_SKILL_CATEGORY_DELETE")
     * @RoleInfo(role="ROLE_BACKEND_SKILL_CATEGORY_DELETE", parent="ROLE_BACKEND_SKILL_CATEGORY_ALL", desc="Delete Skill category", module="Skill category")
     * @App\Route("/delete/{id}", name="backend_settings_category_skill_delete")
     * @App\Method("GET")
     */
    public function deleteAction($id)
    {
        $entity = $this->getEntity($id);

        $em         = $this->getEntityManager();
        $repository = $this->getRepository();
        $repository->decreaseNextSiblingsPositions($entity);
        $repository->removeFromTree($entity);
        $em->clear();
        $repository->recover();
        $em->flush();

        return $this->redirect($this->generateUrl('backend_settings_category_skill'));
    }

    /**
     * moveupAction
     *
     * @Secure(roles="ROLE_BACKEND_SKILL_CATEGORY_LIST")
     * @App\Route("/moveup/{id}", name="backend_settings_category_skill_moveup")
     * @App\Method("GET")
     */
    public function moveupAction($id)
    {
        $this->fixErrors();
        $entity     = $this->getEntity($id);
        $em         = $this->getEntityManager();
        $repository = $this->getRepository();
        $position   = $entity->getPosition();
        $repository->moveUp($entity);
        $entity->setPosition($position - 1);
        $em->flush();
        $repository->increaseNextSiblingsPositions($entity, $position);
        $this->getEntityManager()->clear();
        return $this->redirect($this->generateUrl('backend_settings_category_skill'));
    }

    /**
     * movedownAction
     *
     * @Secure(roles="ROLE_BACKEND_SKILL_CATEGORY_LIST")
     * @App\Route("/movedown/{id}", name="backend_settings_category_skill_movedown")
     * @App\Method("GET")
     */
    public function movedownAction($id)
    {
        $this->fixErrors();
        $entity     = $this->getEntity($id);
        $em         = $this->getEntityManager();
        $repository = $this->getRepository();
        $position   = $entity->getPosition();
        $repository->decreaseNextSiblingsPositions($entity, $position);
        $repository->moveDown($entity);
        $entity->setPosition($position + 1);
        $em->flush();
        $this->getEntityManager()->clear();
        return $this->redirect($this->generateUrl('backend_settings_category_skill'));
    }

    /**
     * moveleftAction
     *
     * @Secure(roles="ROLE_BACKEND_SKILL_CATEGORY_LIST")
     * @App\Route("/moveleft/{id}", name="backend_settings_category_skill_moveleft")
     * @App\Method("GET")
     */
    public function moveleftAction($id)
    {
        $this->fixErrors();
        $entity    = $this->getEntity($id);
        $em        = $this->getEntityManager();
        $oldEntity = clone $entity;
        $this->getRepository()->moveLeft($entity);
        $this->moveNodeDirection($entity, $oldEntity, 'left');
        $em->flush();
        $this->getEntityManager()->clear();
        return $this->redirect($this->generateUrl('backend_settings_category_skill'));
    }

    /**
     * moverightAction
     *
     * @Secure(roles="ROLE_BACKEND_SKILL_CATEGORY_LIST")
     * @App\Route("/moveright/{id}", name="backend_settings_category_skill_moveright")
     * @App\Method("GET")
     */
    public function moverightAction($id)
    {
        $this->fixErrors();
        $entity    = $this->getEntity($id);
        $em        = $this->getEntityManager();
        $oldEntity = clone $entity;
        $this->getRepository()->moveRight($entity);
        $this->moveNodeDirection($entity, $oldEntity, 'right');
        $em->flush();
        $this->getEntityManager()->clear();
        return $this->redirect($this->generateUrl('backend_settings_category_skill'));
    }

    /**
     * @Secure(roles="ROLE_BACKEND_SKILL_CATEGORY_LIST")
     * @App\Route("/ajax_skill_category/specialityId={specialityId}", name="ajax_skill_category")
     * @App\Method("GET")
     */
    public function categoriesBySpecialityAjaxAction($specialityId)
    {
        $em       = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('AppSkillBundle:Category')->getListBySpecialityId($specialityId);
        $choices  = $this->formatChoices($entities);

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
            $choices[$entity->getId()] = array('value' => $entity->getId(), 'label' => (string) $entity);
        }

        return $choices;
    }
    
    /**
     * set order
     * @Secure(roles="ROLE_BACKEND_SKILL_CATEGORY_ALL")
     * @App\Route("/set-order", name="backend_settings_category_skill_set_order")
     * @App\Method("POST")
     */
    public function setOrderAction(Request $request)
    {
        $id          = $request->get('id');
        $category    = $this->getEntity($id);
        $oldCategory = clone $category;
        $order       = ($request->request->getInt('order') == 0) ? 1 : $request->request->getInt('order');
        $children    = $this->getRepository()->countChildrenForNode($category->getParent(), $id) + 1;
        $em          = $this->getEntityManager();
        $position    = ($order - $children > 1) ? $children : $order;
        $category->setPosition($position);
        $em->persist($category);
        $em->flush();

        $this->moveNodeToPosition($category, $oldCategory);

        return new Response(\json_encode(['success' => true]));
    }

}