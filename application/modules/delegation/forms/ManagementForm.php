<?php
class Delegation_Form_ManagementForm extends Zend_Form {
	
	public function init(){

		//decorators
		$this->setDecorators(array(
				'FormElements',
				array('HtmlTag', array('tag' => 'dl', 'class' => 'zend_form')),
				array('Description', array('placement' => 'prepend')),
				'Form'
		));
	}
	
	public function buildForm($users, $scopes){
		$usersArray = array();
		foreach($users as $u) $usersArray[$u] = $u;
		
		$select = $this->addElement('select', 'selectDelegate', array(
				'required' => false,
				'ignore' =>true,
				'label' => 'Delegate',
				'description' => 'Chose the user you want to delegate',
				'multiOptions' => $usersArray
		));
		
		/*$multiselect = $this->addElement('multiselect', 'selectScopes', array(
				'required' => false,
				'ignore' =>true,
				'label' => 'Scopes',
				'description' => 'Chose the scopes of the delegation',
				'multiOptions' => $scopes
		));*/
		
		$multiCheckbox = $this->addElement('multiCheckbox', 'selectScopes', array(
				'required' => false,
				'ignore' =>true,
				'label' => 'Scopes',
				'description' => 'Chose the scopes of the delegation',
				'multiOptions' => $scopes
		));
		
		
		$add = $this->addElement('submit', 'add', array(
				'required' => false,
				'ignore' => true,
				'label' => 'Add',
		));
	}
}