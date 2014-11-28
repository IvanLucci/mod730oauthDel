<?php

class Delegation_Model_Delegation {
	
	/**
	 * Scope delimiter
	 * @var string
	 */
	public static $SCOPE_DELIMITER = ' ';
	
	/**
	 * The delegator
	 * @var string
	 */
	protected $_delegator;
	
	/**
	 * The delegate
	 * @var string
	 */
	protected $_delegate;
	
	/**
	 * The scopes
	 * @var string
	 */
	protected $_scopes;
	
	/**
	 * The expiration date
	 * @var date
	 */
	protected $_expDate;
	
	/**
	 * 
	 * @param string
	 * @return Oauth_Model_Delegation
	 */
	public function setDelegator($delegator){
		$this->_delegator = (string) $delegator;
		return $this;
	}
	
	/**
	 * 
	 * @param string $delegate
	 * @return Oauth_Model_Delegation
	 */
	public function setDelegate($delegate){
		$this->_delegate = (string) $delegate;
		return $this;
	}

	/**
	 * 
	 * @param string $scopes (comma separated)
	 * @return Oauth_Model_Delegation
	 */
	public function setScopes($scopes){
		$this->_scopes = (string) $scopes;
		return $this;
	}
	
	/**
	 * 
	 * @param date $expDate
	 * @return Oauth_Model_Delegation
	 */
	public function setExpDate($expDate){
		$this->_expDate = $expDate;
		return $this;
	}
	
	/**
	 * Getter of delegator
	 * @return string
	 */
	public function getDelegator(){
		return $this->_delegator;
	}
	
	/**
	 * Getter of scopes
	 * @return string
	 */
	public function getScopes(){
		return $this->_scopes;
	}
	
	/**
	 * Getter of delegate
	 * @return string
	 */
	public function getDelegate(){
		return $this->_delegate;
	}
	
	/**
	 * Getter of the expiration date
	 * @return date
	 */
	public function getExpDate(){
		return $this->_expDate;
	}
}
