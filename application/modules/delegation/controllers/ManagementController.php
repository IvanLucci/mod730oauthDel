<?php

class Delegation_ManagementController extends Zend_Controller_Action{

	/**
	 * Scope delimiter
	 * @var string
	 */
	protected $_DELIMITER = ' ';
	
	/**
	 * Delegation mapper
	 * @var Delegation_Mapper_Delegation
	 */
	protected $delMapper;
	
	/**
	 * The logged user's id
	 * @var string
	 */
	protected $loggedUser;
	
	
	public function init(){
		
		$this->view->addHelperPath('ZendX/JQuery/View/Helper/', 'ZendX_JQuery_View_Helper');
		$this->view->jQuery()->setCdnSsl(true);
		
		$this->delMapper = new Delegation_Mapper_Delegation();
		$this->loggedUser = Zend_Auth::getInstance()->getIdentity();
	}
	
	public function indexAction(){
		
		$this->view->form = $this->getForm();
		$this->view->delegationList = $this->getDelegationList();
		$this->view->pendingDelegations = $this->getPendingDelegations();
		$this->view->receivedDelegations = $this->getReceivedDelegations();
		$this->view->deleteUrl = 'management/delete';
		$this->view->editUrl = 'management/edit';
		$s = new Zend_Session_Namespace('delegation');
		if(isset($s->role))
			$this->view->loggedRole = true;
	}
	
	public function processAction(){
		$request = $this->getRequest();
    	
    	// Check if we have a POST request
    	if (!$request->isPost()) {
    		return $this->_helper->redirector('index');
    	}
    	
    	$post = $request->getPost();
    	$form = $this->getForm();
    	
    	if (!$form->isValid($post)) {
    		// Invalid entries
    		$this->view->form = $form;
    		return $this->render('index'); // re-render the login form
    		// TODO il render() non sortisce effetti, il form non appare.
    	}
    	
    	$selectedDelegate = $post['selectDelegate'];
    	$selectedScopes = $post['selectScopes'];
    	
    	//Scadenza di default della delega: 3 mesi
    	$date = new Zend_Date();
    	$date->add(3, Zend_Date::MONTH);
    	
    	$delegation = new Delegation_Model_Delegation();
    	$delegation->setDelegator($this->loggedUser)
    			   ->setDelegate($selectedDelegate)
    			   ->setExpDate($date->toString("yyyy-MM-dd"));
    	
    	if(!empty($selectedScopes))
    		$delegation->setScopes(implode($this->_DELIMITER, $selectedScopes));
    	
    	//Sends a notification via mail to the delegate. The delegate can confirm the
    	//delegation by clicking on the verification link in the email received.
    	$url = $this->view->serverUrl('/oauth/public/delegation/management/verifydelegation');
    	$this->delMapper->delegationCreationMail($this->loggedUser, $selectedDelegate, $selectedScopes, $delegation, $url);
    
    	return $this->_helper->redirector('index');
	}
	
	public function verifydelegationAction(){

		$seed = $this->_request->getParam('seed');
		mt_srand($seed);
		$code = mt_rand();
		
		$e = $this->delMapper->addDelegationWithCode($code);
		if(!$e) $this->view->msg = "Couldn't verify the delegation";
		else $this->view->msg = "The delegation has been verified."; 
	}
	
	public function processeditAction(){
		
		//I parametri che identificano la delega che deve essere modificata
		$delegator = $this->_request->getParam('delegator');
		$delegate = $this->_request->getParam('delegate');
		
		$request = $this->getRequest();
		 
		// Check if we have a POST request
		if (!$request->isPost()) {
			return $this->_helper->redirector('index');
		}
		 
		
		$post = $request->getPost();
		$form = $this->getEditForm($delegator, $delegate);
		 
		if (!$form->isValid($post)) {
			// Invalid entries
			$this->view->form = $form;
			return $this->render('index'); // re-render the login form
			// TODO il render() non sortisce effetti, il form non appare.
		}
		 
		$selectedDelegate = $delegate;
		$selectedScopes = $post['selectScopes'];
		 
		$delegation = new Delegation_Model_Delegation();
		$delegation->setDelegator($this->loggedUser)
				   ->setDelegate($selectedDelegate);
				
		if(!empty($selectedScopes))
    		$delegation->setScopes(implode($this->_DELIMITER, $selectedScopes));
		else $delegation->setScopes(null);
		 
		$this->delMapper->editDelegation($delegation, $delegator, $delegate);
		$this->delMapper->delegationEditMail($delegator, $delegate, $selectedScopes); 
		
		return $this->_helper->redirector('index');
	}
	
	public function deleteAction(){
		
		$request = $this->getRequest();
		
		if (!$request->isGet()) {
			return $this->_helper->redirector('index');
		}
		
		$delegator = $request->getParam('delegator');
		$delegate = $request->getParam('delegate');
		if($delegator == $this->loggedUser){
			$this->delMapper->removeDelegation($delegator, $delegate);
			$this->delMapper->delegationDeletionMail($delegator, $delegate);
		}
		else if($delegate == $this->loggedUser){
			$this->delMapper->removeDelegation($delegator, $delegate);
			$this->delMapper->delegationDeletionMail($delegate, $delegator);
		}
		
		$this->_helper->redirector('index');
	}
	
	public function editAction(){
		$request = $this->getRequest();
		
		if (!$request->isGet()) {
			return $this->_helper->redirector('index');
		}
		
		$delegator = $request->getParam('delegator');
		$delegate = $request->getParam('delegate');
		if($delegator == $this->loggedUser){
			$this->view->form = $this->getEditForm($delegator, $delegate);
		}
		else $this->_helper->redirector('index');
	}
	
	protected function getForm(){
		
		$action = $this->view->url(array('module' => 'delegation',
				'controller' => 'management',
				'action'     => 'process'), 'default');
		
		$form = new Delegation_Form_ManagementForm(array(
				'action' => $action,
				'method' => 'post',
		));
		
		$allUsers = $this->delMapper->findAllUsers($this->loggedUser);
		$allScopes = $this->delMapper->findAllScopes();
		$form->buildForm($allUsers, $allScopes);
		return $form;
	}
	
	protected function getEditForm($delegator, $delegate){
	
		$params = '?delegator='.$delegator.'&delegate='.$delegate;
		$form = new Delegation_Form_ManagementEditForm(array(
				'action' => 'processedit'.$params,
				'method' => 'post',
		));
	
		$allUsers = $this->delMapper->findAllUsers($this->loggedUser);
		$allScopes = $this->delMapper->findAllScopes();
		$defaultScopes = $this->delMapper->findScopes($delegator, $delegate);
		$form->buildForm($allUsers, $allScopes, $delegator, $delegate, $defaultScopes);
		return $form;
	}
	
	protected function getDelegationList(){
		$delegations = $this->delMapper->findDelegationsOfDelegator($this->loggedUser);
		return $delegations;
	}
	
	protected function getPendingDelegations(){
		$pending = $this->delMapper->findPendingDelegations($this->loggedUser);
		return $pending;
	}
	
	protected function getReceivedDelegations(){
		$received = $this->delMapper->findDelegationsOfDelegate($this->loggedUser);
		return $received;
	}
	
	/**
	 * Ensures the user is logged in using Zend_Auth, if not, prompt the login
	 *
	 */
	public function preDispatch() {
		if (!Zend_Auth::getInstance()->hasIdentity()) {
			$requestUri = Zend_Controller_Front::getInstance()->getRequest()->getRequestUri();
			$sessionLast = new Zend_Session_Namespace('lastRequest');
			$sessionLast->lastRequestUri = $requestUri;
			
			$this->_helper->redirector('index', 'index', 'login');
		}
	}
}