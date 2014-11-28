<?php

class Login_IndexController extends Zend_Controller_Action {

    protected $redirect_after_login = NULL;

    public function init() {
        //$this->form_action = '/login/process'; BAD
        $this->form_action = $this->view->url(array('module' => 'login',
							'controller' => 'index',
							'action'     => 'process'), 'default'); 
        
        if ($this->getRequest()->getParam('destination')) {
            $this->redirect_after_login = $this->getRequest()->getParam('destination');
        }
    }

    protected function getAuthAdapter(array $params) {
        $dbAdapter = Zend_Db_Table::getDefaultAdapter();
        $authAdapter = new Zend_Auth_Adapter_DbTable($dbAdapter);        

        $authAdapter->setTableName('user')

            ->setIdentityColumn('user_id')

            ->setCredentialColumn('user_password')

            ->setCredentialTreatment('MD5(?)');


        $authAdapter->setIdentity($params['username']); 
        
        $authAdapter->setCredential($params['password']);
                   

        return $authAdapter;     
        
    }

    protected function afterSuccessfulLogin() {
        $session = new Zend_Session_Namespace('lastRequest');
        if (isset($session->lastRequestUri)) {
            $redirect_uri=$session->lastRequestUri;
            unset($session->lastRequestUri);
                        
            //echo $redirect_uri;
            //echo $this->view->serverUrl($redirect_uri);
            $this->_helper->redirector->gotoUrl($this->view->serverUrl($redirect_uri));
            //$this->_redirect($this->getRequest()->getHttpHost().$redirect_uri);
            return;
        }

        $this->_helper->redirector('index', 'index', 'default');
    }

    protected function loginAfterLogin() {
        $this->_helper->redirector('index', 'index', 'default');
    }
    
    
    
    public function indexAction() {
        $form = $this->getForm();
        $form->setDescription('Sign in the authorization service');
        $this->view->form = $form;
        
        //Add link to the role form
        $this->view->toRoleForm = "delegation/index/choserole";
        
    }

    
    public function logoutAction() {
        Zend_Auth::getInstance()->clearIdentity();
        
        //Resets role (delegation module)
        $d = new Zend_Session_Namespace('delegation');
        unset($d->role);
        
        $this->_helper->redirector('index'); // back to login page
    }

    public function processAction() {
        $request = $this->getRequest();
        
        // Check if we have a POST request
        if (!$request->isPost()) {
            return $this->_helper->redirector('index');
        }
        // Get our form and validate it
        $form = $this->getForm();
        if (!$form->isValid($request->getPost())) {
            // Invalid entries
            $this->view->form = $form;
            return $this->render('index'); // re-render the login form
// TODO il render() non sortisce effetti, il form non appare.
        }

        // Get our authentication adapter and check credentials
        $adapter = $this->getAuthAdapter($form->getValues());
        $auth = Zend_Auth::getInstance();
        $result = $auth->authenticate($adapter);
        if (!$result->isValid()) {
            // Invalid credentials
            $form->setDescription('Invalid credentials provided');
            $this->view->form = $form;
            return $this->render('index'); // re-render the login form
// TODO il render() non sortisce effetti, il form non appare.
// Con quella sotto invece si', ma ovviamente senza la frase
// "Invalid credential"
//            return $this->_helper->redirector('index');
        }

        // We're authenticated!
        $this->afterSuccessfulLogin();
    }

    public function preDispatch() {

        if (Zend_Auth::getInstance()->hasIdentity()) {
            // If the user is logged in, we don't want to show the login form;
            // however, the logout action should still be available
            if ('logout' != $this->getRequest()->getActionName()) {
                $this->loginAfterLogin();
            }
        } else {
            // If they aren't, they can't logout, so that action should 
            // redirect to the login form
            if ('logout' == $this->getRequest()->getActionName()) {
                $this->_helper->redirector('index');
            }
        }
    }

    protected function getForm() {
        $form = new Login_Form_LoginForm(array(
                    'action' => $this->form_action,
                    'method' => 'post',
                ));
        
        $form->injectRequestValues($this->getRequest()->getParams());
        
        return $form;
    }

}

