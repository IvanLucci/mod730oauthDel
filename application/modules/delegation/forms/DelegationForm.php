<?php
class Delegation_Form_DelegationForm extends Zend_Form {
		
	protected $users;
	
	public function init(){
		
		//decorators
		$this->setDecorators(array(
				'FormElements',
				array('HtmlTag', array('tag' => 'dl', 'class' => 'zend_form')),
				array('Description', array('placement' => 'prepend')),
				'Form'
		));
	}
	
	public function buildForm($users){
		$usersArray = array();
		foreach($users as $u){
			$usersArray[$u] = $u;
		}
		
		$select = $this->addElement('select', 'selectUser', array(
				'required' => false,
				'ignore' =>true,
				'label' => 'Available users',
				'description' => 'Select the user whose data you want to access and click Proceed',
				'multiOptions' => $usersArray
		));
     	
     	$process = $this->addElement('submit', 'process', array(
     			'required' => false,
     			'ignore' => true,
     			'label' => 'Proceed',
     	));
	}
}