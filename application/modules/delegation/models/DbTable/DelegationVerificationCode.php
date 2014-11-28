<?php
class Delegation_Model_DbTable_DelegationVerificationCode extends Zend_Db_Table_Abstract {

	/**
	 * Table name
	 * @var string
	 */
	protected $_name = 'delegation_verification_code';

	/**
	 * The primary key
	 * @var array of strings
	 */
	protected $_primary = array('delegator', 'delegate');

	/**
	 * Getter for the table name
	*/
	public function getName(){ return $this->_name; }
}