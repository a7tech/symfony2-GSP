<?php
/**
 * Created by JetBrains PhpStorm.
 * User: nastya
 * Date: 5/28/13
 * Time: 12:03 AM
 * To change this template use File | Settings | File Templates.
 */

namespace App\BackendBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\SecurityExtraBundle\Annotation\Secure;
use App\UserBundle\Annotation\RoleInfo;
/**
 * @RoleInfo(role="ROLE_BACKEND_ALL_DASHBOARD", parent="ROLE_BACKEND_ALL_DASHBOARD", desc="Backend Dashboard access all",module="Dashboard")
 */
class DashboardController extends Controller {

    /**
     * @Secure(roles="ROLE_BACKEND_DASHBOARD")
     * @RoleInfo(role="ROLE_BACKEND_DASHBOARD", parent="ROLE_BACKEND_ALL_DASHBOARD", desc="Backend Dashboard access",module="Dashboard")
     * @Route("/backend/dashboard", name="backend_dashboard")
     */
    public function dashboardAction() {
        

        return $this->render(
            'AppBackendBundle:Dashboard:home.html.twig',
            array()
        );

    }

}