<?php
/**
 * Created by JetBrains PhpStorm.
 * User: nastya
 * Date: 5/22/13
 * Time: 8:02 PM
 * To change this template use File | Settings | File Templates.
 */

namespace App\BackendBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class BackendController extends Controller{

    /**
     * @Route("/backend", name="backend")
     *
     * @return mixed
     */
    public function backendAction() {
    	
        return $this->redirect($this->generateUrl('backend_login'));
    }
}