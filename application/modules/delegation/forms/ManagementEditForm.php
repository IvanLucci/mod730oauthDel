<?php
class Delegation_Form_ManagementEditForm extends Zend_Form {
	
	public function init(){

		//decorators
		$this->setDecorators(array(
				'FormElements',
				array('HtmlTag', array('tag' => 'dl', 'class' => 'zend_form')),
				array('Description', array('placement' => 'prepend')),
				'Form'
		));
	}
	
	public function buildForm($users, $scopes, $delegator, $delegate, $DefaultScopes){
		$usersArray = array();
		foreach($users as $u) $usersArray[$u] = $u;
		unset($usersArray[$delegate]);
		$temp = array($delegate => $delegate);
		$usersArray = $temp + $usersArray;
		
		
		$multiCheckbox = $this->addElement('multiCheckbox', 'selectScopes', array(
				'required' => false,
				'ignore' =>true,
				'label' => 'Scopes',
				'description' => 'Chose the scopes of the delegation',
				'value'=> $DefaultScopes,
				'multiOptions' => $scopes
		));
		
		
		$add = $this->addElement('submit', 'edit', array(
				'required' => false,
				'ignore' => true,
				'label' => 'Edit',
		));
	}
}