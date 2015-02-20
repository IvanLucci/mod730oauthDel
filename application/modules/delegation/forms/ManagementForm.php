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
		
		
		$acElem = new ZendX_JQuery_Form_Element_AutoComplete('selectDelegate');
		$acElem->setLabel('Delegate');
		$acElem->setDescription('Select the user you want to delegate');
		$acElem->setJQueryParam('data', $users);
		$this->addElement($acElem);
		
		
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