<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Bootstrap
 *
 * @author andou
 */
class Login_Bootstrap extends Zend_Application_Module_Bootstrap {

    public function _initLogin() {
        
        $route = new Zend_Controller_Router_Route('login/:action',
                        array('module'=>'login','controller' => 'index'));

        $ctrl = Zend_Controller_Front::getInstance();
        $router = $ctrl->getRouter();
        $router->addRoute('loginModuleRoute', $route);
    }

}

