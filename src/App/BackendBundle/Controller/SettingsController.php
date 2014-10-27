<?php
/**
 * Created by JetBrains PhpStorm.
 * User: nastya
 * Date: 5/29/13
 * Time: 4:42 PM
 * To change this template use File | Settings | File Templates.
 */

namespace App\BackendBundle\Controller;


use App\CoreBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use App\UserBundle\Annotation\RoleInfo;

/**
 * Class SettingsController
 * @package App\BackendBundle\Controller
 */
class SettingsController extends Controller
{

    /**
     * @Secure(roles="ROLE_BACKEND_ALL_SETTINGS")
     * @RoleInfo(role="ROLE_BACKEND_ALL_SETTINGS", parent="null", desc="Backend Settings all access",module="Backend")
     * @Route("/backend/settings", name="backend_settings")
     */
    public function allBackendSettingsAction(Request $request)
    {
        return $this->render('AppBackendBundle:Dashboard:settingsIndex.html.twig');
    }

    /**
     * @Template
     */
    public function statusesGroupsAction($wrapper = 'li')
    {
        $statuses_groups = $this->getRepository('AppStatusBundle:Group')->getAll();

        return [
            'statuses_groups' => $statuses_groups,
            'wrapper' => $wrapper
        ];
    }

}