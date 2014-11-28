<?php

class Delegation_Model_Role {
	
	/**
	 * Id of the role
	 * @var integer
	 */
	protected $roleId;

	/**
	 * Name of the role
	 * @var string
	 */
	protected $roleName;
	
	/**
	 * URI of the server that checks the role
	 * @var string
	 */
	protected $roleUri;
	
	/**
	 * Set of scopes available for this role
	 * @var array of string
	 */
	protected $roleScopes;
	
	/**
	 * Setter for roleId
	 * @param integer $id
	 * @return Delegation_Model_Role
	 */
	public function setRoleId($id){
		$this->roleId = $id;
		return $this;
	}
	
	/**
	 * Setter for roleName
	 * @param string $name
	 * @return Delegation_Model_Role
	 */
	public function setRoleName($name){
		$this->roleName = (string) $name;
		return $this;
	}
	
	/**
	 * Setter for the role URI
	 * @param string $uri
	 * @return Delegation_Model_Role
	 */
	public function setRoleUri($uri){
		$this->roleUri = (string) $uri;
		return $this;
	}
	
	/**
	 * Setter for roleScopes
	 * @param string $scopes
	 * @return Delegation_Model_Role
	 */
	public function setRoleScopes($scopes){
		$this->roleScopes = $scopes;
		return $this;
	}
	
	/**
	 * Getter for roleId
	 */
	public function getRoleId(){ return $this->roleId; }
	
	/**
	 * Getter for roleName
	 */
	public function getRoleName(){ return $this->roleName; }
	
	/**
	 * Getter for roleUri
	 */
	public function getRoleUri(){ return $this->roleUri; }
	
	/**
	 * Getter for roleScopes
	 */
	public function getRoleScopes(){ return $this->roleScopes; }
}