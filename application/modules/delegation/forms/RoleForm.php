<?php
class Delegation_Form_RoleForm extends Zend_Form {
	
	public function init(){
		
		//decorators
		$this->setDecorators(array(
				'FormElements',
				array('HtmlTag', array('tag' => 'dl', 'class' => 'zend_form')),
				array('Description', array('placement' => 'prepend')),
				'Form'
		));
	}
	
	public function buildForm($roles){
		$rolesArray = array();
		foreach($roles as $r){
			$rolesArray[$r->getRoleId()] = $r->getRoleName();
		}
	
		$select = $this->addElement('select', 'selectRole', array(
				'required' => false,
				'ignore' =>true,
				'label' => 'Roles',
				'description' => 'Select your role and then click Proceed',
				'multiOptions' => $rolesArray
		));
	
		$process = $this->addElement('submit', 'process', array(
				'required' => false,
				'ignore' => true,
				'label' => 'Proceed',
		));
	}
}