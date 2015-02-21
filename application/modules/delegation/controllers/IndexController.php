<?php

class Delegation_IndexController extends Zend_Controller_Action {
		
	/**
	 * Delegation mapper
	 * @var Delegation_Mapper_Delegation
	 */
	protected $delMapper;
	
	/**
	 * Splitted Uri
	 * @var array of string (keys: 'base', 'scopes', 'state')
	 */
	protected $Uri;
	
	/**
	 * Array degli scope dell'uri
	 * @var array of string
	 */
	protected $scopeArray;
	
	/**
	 * Flag that tells if roles can be used or not
	 * @var bool
	 */
	protected $roles_active;
	
	/**
	 * The role of the user
	 * @var array of Delegation_Model_Role
	 */
	protected $role;
	
	/**
	 * The delegators of the user
	 * @var array of string
	 */
	protected $delegators;
	
	/**
	 * LastRequest session namespace
	 * @var session namespace
	 */
	protected $session_lastRequest;
	
	/**
	 * Delegation session namespace
	 * @var session namespace
	 */
	protected $session_delegation;
	
	/**
	 * Scope delimiter
	 * @var string
	 */
	protected $_DELIMITER = ' ';
	
    public function init() {
    	
    	$this->delMapper = new Delegation_Mapper_Delegation();
    	
    	$this->session_lastRequest = new Zend_Session_Namespace('lastRequest');
    	$this->session_delegation = new Zend_Session_Namespace('delegation');
    	$this->Uri = $this->splitUri($this->session_lastRequest->lastRequestUri);
    	$this->scopeArray = $this->toScopeArray($this->Uri['scopes']);
    	
    }
    
    
    public function indexAction() {
    	
        //Se l'utente e' loggato con un ruolo, mostra il form dei ruoli
		if(isset($this->session_delegation->role)) {
			$this->view->form = $this->getForm4Roles();
		}
		//Altrimenti il form delle deleghe
		else $this->view->form = $this->getForm();
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
    	
    	$selectedUser = $post['selectUser'];
    	$loggedUser = Zend_Auth::getInstance()->getIdentity();
    	
    	//if the delegator is the logged user: normal flow
    	if($selectedUser == $loggedUser){
    		$this->session_delegation->usesDelegation = false;
    		$this->_helper->redirector->goToUrl($this->view->serverUrl($this->session_lastRequest->lastRequestUri));
    	}
    	
    	//otherwise, use the delegation flow
    	$this->session_delegation->usesDelegation = true;	
    	
    	$delegationScopes = $this->delMapper->findScopes($selectedUser, $loggedUser);
    	$intersectedScopes = array_intersect($this->scopeArray, $delegationScopes);
    	$intersectedScopeStr = implode('+', $intersectedScopes);
		
    	//builds the new URI
    	$this->session_delegation->delegator = $selectedUser;
    	$newUri = $this->buildUri($this->Uri['base'], $intersectedScopeStr, $this->Uri['state']);
    	$this->_helper->redirector->goToUrl($this->view->serverUrl($newUri));
    	
    	
    }
    
    public function roleprocessAction(){
    	$request = $this->getRequest();
    	 
    	// Check if we have a POST request
    	if (!$request->isPost()) {
    		return $this->_helper->redirector('index');
    	}
    	 
    	$post = $request->getPost();
    	$form = $this->getForm4Roles();
    	 
    	if (!$form->isValid($post)) {
    		// Invalid entries
    		$this->view->form = $form;
    		return $this->render('index'); // re-render the login form
    		// TODO il render() non sortisce effetti, il form non appare.
    	}
    	 
    	$selectedUser = $post['selectUser'];
    	
    	$this->session_delegation->usesDelegation = true;
    	
    	$role = $this->delMapper->findRole($this->session_delegation->role);
    	$roleScopes = explode($this->_DELIMITER, $role->getRoleScopes());
    	$intersectedScopes = array_intersect($this->scopeArray, $roleScopes);
    	$intersectedScopeStr = implode('+', $intersectedScopes);
    	
    	//builds the new URI
    	$this->session_delegation->delegator = $selectedUser;
    	$newUri = $this->buildUri($this->Uri['base'], $intersectedScopeStr, $this->Uri['state']);
    	$this->_helper->redirector->goToUrl($this->view->serverUrl($newUri));
    }
    
    public function choseroleAction(){
    	$this->view->form = $this->getRoleChoiceForm();
    }
    
    public function authroleAction(){
    	
    	$request = $this->getRequest();
    	 
    	// Check if we have a POST request
    	if (!$request->isPost()) {
    		return $this->_helper->redirector('index');
    	}
    	 
    	$post = $request->getPost();
    	$form = $this->getRoleChoiceForm();
    	 
    	if (!$form->isValid($post)) {
    		// Invalid entries
    		$this->view->form = $form;
    		return $this->render('index'); // re-render the login form
    		// TODO il render() non sortisce effetti, il form non appare.
    	}
    	 
    	$selectedRole = $post['selectRole'];
    	
    	//Ask the role server if the role is legit
    	$role = $this->delMapper->findRole($selectedRole);
    	$url = $this->view->serverUrl($this->view->baseUrl()).'/delegation/index/verifyrole';
    	$this->_helper->redirector->gotoUrl($role->getRoleUri() . '?role=' .$selectedRole.'&url='.$url);
    	
    }
    
    public function verifyroleAction(){
    	
    	require_once realpath(APPLICATION_PATH . '/../library/Saml/settings.php');
    	require_once realpath(APPLICATION_PATH . '/../library/Saml/lib/onelogin/saml/settings.php');
    	require_once realpath(APPLICATION_PATH . '/../library/Saml/lib/onelogin/saml/response.php');
    	
    	$post = $this->_request->getPost();
    	
    	//verificare saml assertion
    	$samlresponse = new SamlResponse(saml_get_settings(), $post['assertion']);
    	if(!$samlresponse->is_valid()){
    		throw new Exception("SAML Response is not valid");
    	}
    	
    	$assertion = base64_decode($post['assertion']);  
    	
    	
    	$saml = new DOMDocument();
    	$saml->loadXML($assertion);
    	$xpath = new DOMXPath($saml);
    	$xpath->registerNamespace("samlp","urn:oasis:names:tc:SAML:2.0:protocol");
    	$xpath->registerNamespace("saml","urn:oasis:names:tc:SAML:2.0:assertion");
    	$xpath->registerNamespace("ds", "http://www.w3.org/2000/09/xmldsig#");
    	
    	
    	$status = $xpath->query("//Status/StatusCode/@Value")->item(0)->nodeValue;
    	$role = (int)$xpath->query("//Attribute[@Name = 'role_id']/AttributeValue")->item(0)->nodeValue;
    	$id = trim($xpath->query("//Attribute[@Name = 'subject_id']/AttributeValue")->item(0)->nodeValue);   	
    	
    	
    	$this->session_delegation->role = $role;
    	
    	//Create a Zend_Auth using the role name and person id
    	$roleName = $this->delMapper->findRole($role)->getRoleName();
    	$storage = Zend_Auth::getInstance()->getStorage();
    	$storage->write($roleName . ' '. $id); 
    	
    	$s = new Zend_Session_Namespace('lastRequest');
    	$this->_helper->redirector->gotoUrl($this->view->serverUrl($s->lastRequestUri));
    }
    
    protected function getForm() {
    	
    	$action = $this->view->url(array('module' => 'delegation',
    			'controller' => 'index',
    			'action'     => 'process'), 'default');
    	
    	$form = new Delegation_Form_DelegationForm(array(
    			'action' => $action,
    			'method' => 'post',
    	));
    		
    	$loggedUser = Zend_Auth::getInstance()->getIdentity();
    	$users = array();
    	
    	//Setta i deleganti
    	$this->delegators = $this->delMapper->findDelegators($loggedUser, $this->scopeArray);
    	$users = $this->delegators;
    	array_unshift($users, $loggedUser);
    	//Popola il form con i deleganti
    	$form->buildForm($users);
    	return $form;
    }
    
    protected function getForm4Roles(){
    	$action = $this->view->url(array('module' => 'delegation',
    			'controller' => 'index',
    			'action'     => 'roleprocess'), 'default');
    	
    	$form = new Delegation_Form_DelegationForm4Roles(array(
    			'action' => $action,
    			'method' => 'post',
    	));
    	 
    	$users = array();
    	
    	$roleId = $this->session_delegation->role;
    	$role = $this->delMapper->findRole($roleId);
    	
    	//Se almeno uno scope del ruolo e' incluso negli scope dell'url
    	//allora il ruolo e' attivo e si puo' usare per accedere ai dati
    	//di tutti gli utenti
    	if($this->delMapper->canRoleSeeUsers($role, $this->scopeArray)){
    		$users = $this->delMapper->findAllUsers();
    		$form->buildForm($users);
    	}
    	else $form = "Role can't use any specified scope";
    	return $form;
    }
    
    protected function getRoleChoiceForm(){
    	
    	$action = $this->view->url(array('module' => 'delegation',
    			'controller' => 'index',
    			'action'     => 'authRole'), 'default');
    	
    	$form = new Delegation_Form_RoleForm(array(
    			'action' => $action,
    			'method' => 'post',
    	));
    	
    	$roles = $this->delMapper->findAllRoles();
    	$form->buildForm($roles);
    	return $form;
    }
    
    private function splitUri($uri){
    	$parts = explode("&scope=", $uri);
    	if(count($parts)<2) return array('base' => $parts[0],
    									 'scopes' => '',
    									 'state' => ''
    	);
    	$parts2 = explode("&state=", $parts[1]);
    	return array('base' => $parts[0], 
    				 'scopes' => $parts2[0],
    				 'state' => $parts2[1]
    	);
    }
    
    private function toScopeArray($scopeStr){
    	return explode("+", $scopeStr);
    }
    
    private function buildUri($base, $scopes, $state){
    	return $base . "&scope=" . $scopes . "&state=" . $state;
    }
}

