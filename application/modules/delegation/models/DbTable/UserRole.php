<?php
class Delegation_Model_DbTable_UserRole extends Zend_Db_Table_Abstract {

	/**
	 * Table name
	 * @var string
	 */
	protected $_name = 'user_roles';

	/**
	 * The primary key
	 * @var array of strings
	 */
	protected $_primary = array('user_id', 'role_id');
	
	/**
	 * Getter for the table name
	 */
	public function getName(){ return $this->_name; }
}