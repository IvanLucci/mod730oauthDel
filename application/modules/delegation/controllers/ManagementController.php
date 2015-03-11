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
		
		$this->delMapper = new Delegation_Mapper_Delegation();
		$this->loggedUser = Zend_Auth::getInstance()->getIdentity();
	}
	
	public function indexAction(){
		
		$this->view->form = $this->getForm();
		$this->view->delegationList = $this->getDelegationList();
		$this->view->pendingDelegationsSent = $this->getPendingDelegationsSent();
		$this->view->pendingDelegationsReceived = $this->getPendingDelegationsReceived();
		$this->view->receivedDelegations = $this->getReceivedDelegations();
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
    	
    	//Se esiste gia' una delega con quello stesso delegato e delegante, solleva eccezione.
    	$dels = $this->delMapper->findAllDelegationsOfDelegator($this->loggedUser);
    	foreach($dels as $d){
    		if($d->getDelegate() == $selectedDelegate){
    			throw new Exception("There is already a delegation with delegator ". $this->loggedUser. " and delegate ". $selectedDelegate);
    		}
    	}
    	
    	
    	//Scadenza di default della delega: 3 mesi
    	$date = new Zend_Date();
    	$date->add(3, Zend_Date::MONTH);
    	
    	$delegation = new Delegation_Model_Delegation();
    	$delegation->setDelegator($this->loggedUser)
    			   ->setDelegate($selectedDelegate)
    			   ->setExpDate($date->toString("yyyy-MM-dd"))
    			   ->setState(0)
    			   ->setCode(mt_rand());
    			 
    	
    	if(!empty($selectedScopes))
    		$delegation->setScopes(implode($this->_DELIMITER, $selectedScopes));
    	
    	//Adds the pending delegation
    	$this->delMapper->addDelegation($delegation);
    	
    	//Sends a notification via mail to the delegate. The delegate can confirm the
    	//delegation by clicking on the verification link in the email received.
    	$url = $this->view->serverUrl('/oauth/public/delegation/management/verifydelegation');
    	try {
    		$this->delMapper->delegationCreationMail($this->loggedUser, $selectedDelegate, $selectedScopes, $delegation, $url);
    	} catch (Exception $e){}
    		
    	return $this->_helper->redirector('index');
	}
	
	public function verifydelegationAction(){

		$code = $this->_request->getParam('code');
		
		$e = $this->delMapper->verifyDelegation($code);
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
		try {
			$this->delMapper->delegationEditMail($delegator, $delegate, $selectedScopes); 
		} catch(Exception $e){}
		
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
			try {
				$this->delMapper->delegationDeletionMail($delegate, $delegator);
			} catch(Exception $e){}
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
	
	public function revokeAction(){
	
		$request = $this->getRequest();
	
		if (!$request->isGet()) {
			return $this->_helper->redirector('index');
		}
	
		$delegator = $request->getParam('delegator');
		$delegate = $request->getParam('delegate');
		if($delegator == $this->loggedUser || $delegate == $this->loggedUser){
			$this->delMapper->revokeDelegation($delegator, $delegate);
		}
	
		$this->_helper->redirector('index');
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
	
	protected function getPendingDelegationsSent(){
		$pending = $this->delMapper->findPendingDelegationsSent($this->loggedUser);
		return $pending;
	}
	
	protected function getPendingDelegationsReceived(){
		$pending = $this->delMapper->findPendingDelegationsReceived($this->loggedUser);
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