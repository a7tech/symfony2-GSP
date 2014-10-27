<?php

namespace App\PlaceBundle\Controller;

use App\CoreBundle\Controller\Controller;
use App\PlaceBundle\Entity\Place;
use App\PlaceBundle\Entity\PlaceRepository;
use App\PlaceBundle\Form\PlaceType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as App;
use Symfony\Bundle\FrameworkBundle\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Doctrine\ORM\EntityRepository;
use App\UserBundle\Annotation\RoleInfo;

/**
 * @App\Route("/backend/settings/places")
 * RoleInfo(role="ROLE_BACKEND_PLACES_ALL", parent="ROLE_SUPER_ADMIN", desc="Projects all access", module="Project")
 */
class PlaceController extends Controller
{

    /**
     * getEntityRepository
     *
     * @return PlaceRepository
     */
    protected function getEntityRepository()
    {
        return $this->getRepository('AppPlaceBundle:Place');
    }

    /**
     * getEntity
     *
     * @param int $id
     * @return Place
     * @throws NotFoundHttpException
     */
    protected function getEntity($id)
    {
        $entity = $this->getEntityRepository()->getById($id);
        if (!$entity) {
            throw $this->createNotFoundException('Place is not found');
        }
        return $entity;
    }

    /**
     * indexAction
     *
     * Secure(roles="ROLE_BACKEND_PLACES_LIST")
     * RoleInfo(role="ROLE_BACKEND_PLACES_LIST", parent="ROLE_BACKEND_PLACES_ALL", desc="List Places", module="Place")
     * @App\Route("/", name="backend_place")
     * @App\Method("GET")
     * @Template
     */
    public function indexAction()
    {
//        $entities = $this->getEntityRepository()->getAll();

        return [
            'entities' => [],
        ];

    }
    
    /**
     * indexAction
     *
     * Secure(roles="ROLE_BACKEND_PLACES_LIST")
     * RoleInfo(role="ROLE_BACKEND_PLACES_LIST", parent="ROLE_BACKEND_PLACES_ALL", desc="List Places", module="Place")
     * @App\Route("/datatables", name="backend_place_datatables")
     * @App\Method("GET")
     */
    public function indexDatatablesAction(Request $request)
    {
        $filter = $this->get('app_place.filter.placefilter');

        if ($response = $filter->processRequest($request)) {
            return $response;
        }
    }

    /**
     * createAction
     *
     * Secure(roles="ROLE_BACKEND_PLACES_CREATE")
     * RoleInfo(role="ROLE_BACKEND_PLACES_CREATE", parent="ROLE_BACKEND_PLACES_ALL", desc="Create Place", module="Place")
     * @App\Route("/create", name="backend_place_create")
     * @App\Method({"GET", "POST"})
     * @Template
     */
    public function createAction()
    {
        $entity = new Place();
        $em = $this->getEntityManager();

        $form = $this->createForm(new PlaceType(), $entity);
        $request = $this->getRequest();

        if($request->getMethod() == 'POST'){
            $form->submit($request);

            if ($form->isValid()) {
                $em->persist($entity);
                $em->flush();

                return $this->redirect($this->generateUrl('backend_place_show', array('id' => $entity->getId())));
            }
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView()
        );

    }

    /**
     * editAction
     *
     * Secure(roles="ROLE_BACKEND_PROJECT_EDIT")
     * RoleInfo(role="ROLE_BACKEND_PROJECT_EDIT", parent="ROLE_BACKEND_PROJECT_ALL", desc="Edit Project", module="Project")
     * @App\Route("/edit/{id}", name="backend_place_edit")
     * @App\Method({"GET", "POST"})
     * @Template("AppPlaceBundle:Place:create.html.twig")
     */
    public function editAction($id)
    {
        $entity = $this->getEntity($id);

        $form = $this->createForm(new PlaceType(), $entity);
        $request = $this->getRequest();

        if ($request->getMethod() == 'POST') {
            $form->submit($request);

            if ($form->isValid()) {
                $em = $this->getEntityManager();
                $em->persist($entity);
                $em->flush();

                return $this->redirect($this->generateUrl('backend_place_show', array('id' => $id)));
            }
        }

        return  array(
            'entity' => $entity,
            'id' => $id,
            'form' => $form->createView()
        );
    }

    /**
     * showAction
     *
     * Secure(roles="ROLE_BACKEND_PROJECT_SHOW")
     * RoleInfo(role="ROLE_BACKEND_PROJECT_SHOW", parent="ROLE_BACKEND_PROJECT_ALL", desc="Show Project", module="Project")
     * @App\Route("/show/{id}", name="backend_place_show")
     * @App\Method("GET")
     * @Template
     */
    public function showAction($id)
    {
        $entity = $this->getEntity($id);

        return array(
            'entity' => $entity,
            'id' => $id
        );
    }

    /**
     * deleteAction
     *
     * Secure(roles="ROLE_BACKEND_PROJECT_DELETE")
     * RoleInfo(role="ROLE_BACKEND_PROJECT_DELETE", parent="ROLE_BACKEND_PROJECT_ALL", desc="Delete Project", module="Project")
     * @App\Route("/delete/{id}", name="backend_place_delete")
     * @App\Method("GET")
     */
    public function deleteAction($id)
    {
        $entity = $this->getEntity($id);

        $em = $this->getDoctrine()->getManager();
        try {
            $em->remove($entity);
            $em->flush();

        } catch(\Exception $e){
            $this->addAdminMessage('Cannot remove place. It\'s used', 'error');
        }

        return $this->redirect($this->generateUrl('backend_project'));
    }
}
