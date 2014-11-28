<?php
class Delegation_Model_DbTable_RoleScope extends Zend_Db_Table_Abstract {

	/**
	 * Table name
	 * @var string
	 */
	protected $_name = 'role_scopes';

	/**
	 * The primary key
	 * @var string
	 */
	protected $_primary = 'role_id';

	/**
	 * Getter for the table name
	 */
	public function getName(){
		return $this->_name;
	}
}