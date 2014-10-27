<?php
/**
 * Created by JetBrains PhpStorm.
 * User: nastya
 * Date: 5/22/13
 * Time: 7:07 PM
 * To change this template use File | Settings | File Templates.
 */

namespace App\UserBundle\Controller\Backend\Security;


use FOS\UserBundle\Controller\SecurityController;
use Symfony\Component\DependencyInjection\ContainerAware;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;

/**
 * Class LoginController
 * @package App\UserBundle\Controller\Security
 */

class UserSecurityController extends SecurityController {

    /**
     * @Route("/backend/login", name="backend_login")
     * @return \Symfony\Component\HttpFoundation\Response
     */

    public function loginAction(Request $request)
    {
        $request = $this->container->get('request');
        /* @var $request \Symfony\Component\HttpFoundation\Request */
        $session = $request->getSession();
        /* @var $session \Symfony\Component\HttpFoundation\Session */

        // get the error if any (works with forward and redirect -- see below)
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } elseif (null !== $session && $session->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = '';
        }

        if ($error) {
            // TODO: this is a potential security risk (see http://trac.symfony-project.org/ticket/9523)
            $error = $error->getMessage();
        }
        // last username entered by the user
        $lastUsername = (null === $session) ? '' : $session->get(SecurityContext::LAST_USERNAME);

        $csrfToken = $this->container->get('form.csrf_provider')->generateCsrfToken('authenticate');

        return $this->renderLogin(array(
            'last_username' => $lastUsername,
            'error'         => $error,
            'csrf_token' => $csrfToken,
        ));
    }

    protected function renderLogin(array $data)
    {
        $template = sprintf('AppUserBundle:Backend\Security:login.html.%s', $this->container->getParameter('fos_user.template.engine'));

        return $this->container->get('templating')->renderResponse($template, $data);
    }

    /**
     * @Route("/backend/login_check", name="backend_login_check")
     */
    public function checkAction()
    {
        parent::checkAction();
    }

    /**
     * @Route("/backend/logout", name="backend_logout")
     */
    public function logoutAction()
    {
        parent::logoutAction();
    }

}