<?php
/**
 * Language controller for adding and editing language in the system
 *
 * @author: Arend-Jan
 */

namespace App\LanguageBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;
use App\UserBundle\Annotation\RoleInfo;

use App\LanguageBundle\Entity\Language;
use App\LanguageBundle\Form\LanguageType;

/**
 * Class SettingsController
 * @RoleInfo(role="ROLE_BACKEND_LANGUAGE_ALL", parent="ROLE_BACKEND_LANGUAGE_ALL", desc="Languages all access", module="Language")
 */
class LanguageController extends Controller 
{

    /**
     * @Secure(roles="ROLE_BACKEND_LANGUAGE_LIST")
     * @RoleInfo(role="ROLE_BACKEND_LANGUAGE_LIST", parent="ROLE_BACKEND_LANGUAGE_ALL", desc="List Languages", module="Language")
     * @Route("/backend/settings/language", name="backend_settings_language")
     * @Method("GET")
     * @Template()
     */
    public function languageAction() 
    {
        $em = $this->getDoctrine()->getManager();

//        $entities = $em->getRepository('AppLanguageBundle:Language')->findAll();

        return array(
            'entities' => [],
        );        
    }
    
    /**
     * indexAction
     *
     * @Secure(roles="ROLE_BACKEND_LANGUAGE_LIST")
     * @RoleInfo(role="ROLE_BACKEND_LANGUAGE_LIST", parent="ROLE_BACKEND_LANGUAGE_ALL", desc="List Languages", module="Language")
     * @Route("/backend/settings/language/datatables", name="backend_language_datatables")
     * @Method("GET")
     */
    public function indexLanguageDatatablesAction(Request $request)
    {
        $filter = $this->get('lexik_translation.filter.languagefilter');

        if ($response = $filter->processRequest($request)) {
            return $response;
        }
    }

    /**
     * @Secure(roles="ROLE_BACKEND_LANGUAGE_CREATE")
     * @RoleInfo(role="ROLE_BACKEND_LANGUAGE_CREATE", parent="ROLE_BACKEND_LANGUAGE_ALL", desc="Create Language", module="Language")
     * @Route("/backend/settings/language/add", name="backend_settings_language_add")
     * @Template()
     */
    public function addAction(Request $request) 
    {
        $language = new Language();
        $form = $this->createForm(new LanguageType(), $language);

        if ($request->isMethod('POST')) {
            $form->bind($request);

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($language);
                $em->flush();

                $this->get('session')->getFlashBag()->add('notice', 'Language succesfully added!');

                return $this->redirect($this->generateUrl('backend_settings_language'));
            }
        }

        return array('form' => $form->createView());
    }

     /**
     * @Secure(roles="ROLE_BACKEND_LANGUAGE_LIST")
     * @Route("/backend/settings/language/activate/{locale}/{type}", name="backend_settings_language_activate")
     * @Template()
     */
    public function activateAction(Request $request, $locale, $type) 
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('AppLanguageBundle:Language')->findOneBy(array('iso' => $locale));
        if($type == 'frontend'){
            $entity->setFrontend(true);    
        }else{
            $entity->setBackend(true);    
        }

        $redirect =  $this->redirect($this->generateUrl('backend_settings_language'));

        //clear url matcher cache
        $cache_dir = $this->container->getParameter("kernel.cache_dir");
        $finder = new Finder();
        $finder->files()
            ->in($cache_dir)
            ->name('*Url*');

        foreach($finder as $file){
            /** @var SplFileInfo $file */
            unlink($file->getPathname());
        }

        $em->persist($entity);
        $em->flush();

        return $redirect;
    }

    /**
     * @Secure(roles="ROLE_BACKEND_LANGUAGE_LIST")
     * @Route("/backend/settings/language/deactivate/{locale}/{type}", name="backend_settings_language_deactivate")
     * @Template()
     */
    public function deactivateAction(Request $request, $locale, $type) 
    {
        if($locale == 'en'){
            // We can't disable english, so just redirect. 
            return $this->redirect($this->generateUrl('backend_settings_language'));
        }

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('AppLanguageBundle:Language')->findOneBy(array('iso' => $locale));
        if($type == 'frontend'){
            $entity->setFrontend(false);    
        }else{
            // If we disable the backend, we also need to disable the frontend
            $entity->setFrontend(false);    
            $entity->setBackend(false);    
        }
        $em->flush();

        return $this->redirect($this->generateUrl('backend_settings_language'));
    }

}