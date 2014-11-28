<?php

class Delegation_Bootstrap extends Zend_Application_Module_Bootstrap {
	public function _initDelegation() {
	
		$route = new Zend_Controller_Router_Route('delegation/index/:action',
				array('module'=>'delegation','controller' => 'index'));
	
		$route2 = new Zend_Controller_Router_Route('delegation/management/:action',
				array('module'=>'delegation', 'controller' => 'management'));
		
		$ctrl = Zend_Controller_Front::getInstance();
		$router = $ctrl->getRouter();
		
		$router->addRoute('delegationModuleRoute', $route);
		$router->addRoute('delegationModuleRoute2', $route2);
	}
	
	protected function _initResourceLoader() {
		$this->_resourceLoader->addResourceType('mapper', 'models/Mapper', 'Mapper');
	}
}

