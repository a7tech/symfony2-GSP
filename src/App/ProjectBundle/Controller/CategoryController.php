<?php

/**
 * Created by Sublime Text 3
 * Author: ricardo <ricardo@technologias.com>
 * Date:   Mon Dec 2 01:45:11 2013
 */

namespace App\ProjectBundle\Controller;

use App\CategoryBundle\Entity\CategoryRepository;
use App\CategoryBundle\NestedSet\TreeWrapper;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as App;
use App\ProjectBundle\Entity\Category;
use App\CategoryBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use JMS\SecurityExtraBundle\Annotation\Secure;
use App\UserBundle\Annotation\RoleInfo;

/**
 * @App\Route("/backend/settings/category/project")
 * @RoleInfo(role="ROLE_BACKEND_PROJECTCATEGORY_ALL", parent="ROLE_BACKEND_PROJECTCATEGORY_ALL", desc="Project categories all access", module="Project category")
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
        return $this->getEntityManager()->getRepository('AppProjectBundle:Category');
    }

    /**
     * indexAction
     *
     * @Secure(roles="ROLE_BACKEND_PROJECTCATEGORY_LIST")
     * @RoleInfo(role="ROLE_BACKEND_PROJECTCATEGORY_LIST", parent="ROLE_BACKEND_PROJECTCATEGORY_ALL", desc="List Project categories",module="Project category")
     * @App\Route("/", name="backend_settings_category_project")
     * @App\Method("GET")
     */
    public function indexAction()
    {
        $this->fixErrors();
        $categoryList = $this->getRepository()->getList();

        $treeWrapper = new TreeWrapper($categoryList);

        return $this->render('AppProjectBundle:Category:index.html.twig', array(
                    'category_list' => $categoryList,
                    'tree_wrapper'  => $treeWrapper,
        ));
    }

    /**
     * createAction
     *
     * @Secure(roles="ROLE_BACKEND_PROJECTCATEGORY_CREATE")
     * @RoleInfo(role="ROLE_BACKEND_PROJECTCATEGORY_CREATE", parent="ROLE_BACKEND_PROJECTCATEGORY_ALL", desc="Create Project category", module="Project category")
     * @App\Route("/create", name="backend_settings_category_project_create")
     * @App\Method({"GET", "POST"})
     */
    public function createAction()
    {
        $entity = new Category();
        $form   = $this->createForm('app_categorybundle_categorytype', $entity);
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
//                $this->getRepository()->reorder(null/* reorder starting from parent */, 'position');
                $em->clear();

                return $this->redirect($this->generateUrl('backend_settings_category_project'));
            }
        }

        return $this->render('AppProjectBundle:Category:create.html.twig', array(
                    'entity' => $entity,
                    'form'   => $form->createView()
        ));
    }

    /**
     * editAction
     *
     * @Secure(roles="ROLE_BACKEND_PROJECTCATEGORY_EDIT")
     * @RoleInfo(role="ROLE_BACKEND_PROJECTCATEGORY_EDIT", parent="ROLE_BACKEND_PROJECTCATEGORY_ALL", desc="Edit Project category", module="Project category")
     * @App\Route("/edit/{id}", name="backend_settings_category_project_edit")
     * @App\Method({"GET", "POST"})
     */
    public function editAction($id)
    {
        $entity    = $this->getEntity($id);
        $form      = $this->createForm('app_categorybundle_categorytype', $entity);
        $oldEntity = clone $entity;

        if ($this->getRequest()->getMethod() == 'POST') {
            $form->bind($this->getRequest());

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($entity);
                $em->flush();

                if (($entity->getPosition() !== $oldEntity->getPosition() && $entity->getLevel() !== $oldEntity->getLevel()) || ($entity->getLevel() !== $oldEntity->getLevel()) || ($entity->getPosition() !== $oldEntity->getPosition())) {
                    $this->moveNodeToPosition($entity, $oldEntity);
                }

                return $this->redirect($this->generateUrl('backend_settings_category_project_show', array('id' => $id)));
            }
        }

        return $this->render('AppProjectBundle:Category:create.html.twig', array(
                    'entity' => $entity,
                    'form'   => $form->createView()
        ));
    }

    /**
     * showAction
     *
     * @Secure(roles="ROLE_BACKEND_PROJECTCATEGORY_SHOW")
     * @RoleInfo(role="ROLE_BACKEND_PROJECTCATEGORY_SHOW", parent="ROLE_BACKEND_PROJECTCATEGORY_ALL", desc="Show Project category", module="Project category")
     * @App\Route("/show/{id}", name="backend_settings_category_project_show")
     * @App\Method("GET")
     */
    public function showAction($id)
    {
        return $this->render('AppProjectBundle:Category:show.html.twig', array(
                    'entity' => $this->getEntity($id)
        ));
    }

    /**
     * deleteAction
     *
     * @Secure(roles="ROLE_BACKEND_PROJECTCATEGORY_DELETE")
     * @RoleInfo(role="ROLE_BACKEND_PROJECTCATEGORY_DELETE", parent="ROLE_BACKEND_PROJECTCATEGORY_ALL", desc="Delete Project category", module="Project category")
     * @App\Route("/delete/{id}", name="backend_settings_category_project_delete")
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

        return $this->redirect($this->generateUrl('backend_settings_category_project'));
    }

    /**
     * moveupAction
     *
     * @Secure(roles="ROLE_BACKEND_PROJECTCATEGORY_ALL")
     * @App\Route("/moveup/{id}", name="backend_settings_category_project_moveup")
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
        return $this->redirect($this->generateUrl('backend_settings_category_project'));
    }

    /**
     * movedownAction
     *
     * @Secure(roles="ROLE_BACKEND_PROJECTCATEGORY_ALL")
     * @App\Route("/movedown/{id}", name="backend_settings_category_project_movedown")
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
        return $this->redirect($this->generateUrl('backend_settings_category_project'));
    }

    /**
     * moveleftAction
     *
     * @Secure(roles="ROLE_BACKEND_PROJECTCATEGORY_ALL")
     * @App\Route("/moveleft/{id}", name="backend_settings_category_project_moveleft")
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
        return $this->redirect($this->generateUrl('backend_settings_category_project'));
    }

    /**
     * moverightAction
     *
     * @Secure(roles="ROLE_BACKEND_PROJECTCATEGORY_ALL")
     * @App\Route("/moveright/{id}", name="backend_settings_category_project_moveright")
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
        return $this->redirect($this->generateUrl('backend_settings_category_project'));
    }

    /**
     * @App\Route("/fix")
     * @return Response
     */
    public function fixAction()
    {
        $errors = $this->getRepository()->verify();

        if ($errors !== true) {
            print_r($errors);
            $this->getRepository()->recover();
            $this->get('doctrine.orm.entity_manager')->flush();
        }


        return new Response('Done');
    }

    /**
     * @App\Route("/fix-contracted")
     * @return Response
     */
    public function fixContractedAction()
    {
        $repository = $this->getEntityManager()->getRepository('AppProjectBundle:ContractCategory');
        $errors     = $repository->verify();

        if ($errors !== true) {
            print_r($errors);
            $repository->recover();
            $this->getEntityManager()->flush();
        }

        return new Response('Done');
    }

    /**
     * set order
     * @Secure(roles="ROLE_BACKEND_PROJECTCATEGORY_ALL")
     * @App\Route("/set-order", name="backend_settings_category_project_set_order")
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