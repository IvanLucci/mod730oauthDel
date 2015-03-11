<?php
class Delegation_Mapper_Delegation {

	/**
	 * The delimiter that divides the scopes in the db table
	 * @var string
	 */
	protected $_DELIMITER = ' ';
	
	/**
	 * The delegation table
	 * @var Delegation_Model_DbTable_Delegation
	 */
	protected $delegation_table;
	
	/**
	 * The userRole table
	 * @var Delegation_Model_DbTable_UserRole
	 */
	protected $userRole_table;
	
	/**
	 * The role table
	 * @var Delegation_Model_DbTable_Role
	 */
	protected $role_table;
	
	/**
	 * The roleScope table
	 * @var Delegation_Model_DbTable_RoleScope
	 */
	protected $roleScope_table;
	
	
	/**
	 * This object constructor
	 *
	 */
	public function __construct() {
		
		$this->delegation_table = new Delegation_Model_DbTable_Delegation();
		$this->role_table = new Delegation_Model_DbTable_Role();
		$this->roleScope_table = new Delegation_Model_DbTable_RoleScope(); 
		
		if (!$this->delegation_table instanceof Zend_Db_Table_Abstract or 
			!$this->role_table instanceof Zend_Db_Table_Abstract or
			!$this->roleScope_table instanceof Zend_Db_Table_Abstract	) {
			throw new Exception('Invalid table data gateway provided');
		}
	}

	
	/**
	 * Finds the delegations of a delegator
	 * @param string $id
	 * @return array of Delegation_Model_Delegation
	 */
	public function findDelegationsOfDelegator($id){
		$table = $this->delegation_table;
		$select = $table->select();
		$select->where('delegator = ?', $id)
			   ->where('state = 1');
		$rows = $table->fetchAll($select);
		$result = array();
		foreach($rows as $row){
			$d = new Delegation_Model_Delegation();
			$d->setDelegator($row->delegator)
			->setDelegate($row->delegate)
			->setScopes($row->scopes)
			->setExpDate($row->expiration_date)
			->setState($row->state)
			->setCode($row->code);
			$result[] = $d;
		}
		return $result;
	}
	
	/**
	 * Finds the delegations of a delegate
	 * @param string $id
	 * @return array of Delegation_Model_Delegation
	 */
	public function findDelegationsOfDelegate($id){
		$table = $this->delegation_table;
		$select = $table->select();
		$select->where('delegate = ?', $id)
			   ->where('state = 1');
		$rows = $table->fetchAll($select);
		$result = array();
		foreach($rows as $row){
			$d = new Delegation_Model_Delegation();
			$d->setDelegator($row->delegator)
			->setDelegate($row->delegate)
			->setScopes($row->scopes)
			->setExpDate($row->expiration_date)
			->setState($row->state)
			->setCode($row->code);
			$result[] = $d;
		}
		return $result;
	}
	
	/**
	 * Finds the pending delegations sent by a delegator
	 * @param string $id
	 * @return array of Delegation_Model_Delegation
	 */
	public function findPendingDelegationsSent($id){
		$table = $this->delegation_table;
		$select = $table->select();
		$select->where('delegator = ?', $id)
			   ->where('state = 0');
		$rows = $table->fetchAll($select);
		$result = array();
		foreach($rows as $row){
			$d = new Delegation_Model_Delegation();
			$d->setDelegator($row->delegator)
			->setDelegate($row->delegate)
			->setScopes($row->scopes)
			->setExpDate($row->expiration_date)
			->setState($row->state)
			->setCode($row->code);
			$result[] = $d;
		}
		return $result;
	}
	
	/**
	 * Finds the pending delegations received by a delegate
	 * @param string $id
	 * @return array of Delegation_Model_Delegation
	 */
	public function findPendingDelegationsReceived($id){
		$table = $this->delegation_table;
		$select = $table->select();
		$select->where('delegate = ?', $id)
		->where('state = 0');
		$rows = $table->fetchAll($select);
		$result = array();
		foreach($rows as $row){
			$d = new Delegation_Model_Delegation();
			$d->setDelegator($row->delegator)
			->setDelegate($row->delegate)
			->setScopes($row->scopes)
			->setExpDate($row->expiration_date)
			->setState($row->state)
			->setCode($row->code);
			$result[] = $d;
		}
		return $result;
	}
	
	/**
	 * Finds all the delegations of a delegator, pending ones included
	 * @param string $id
	 * @return array of Delegation_Model_Delegation
	 */
	public function findAllDelegationsOfDelegator($id){
		$table = $this->delegation_table;
		$select = $table->select();
		$select->where('delegator = ?', $id);
		$rows = $table->fetchAll($select);
		$result = array();
		foreach($rows as $row){
			$d = new Delegation_Model_Delegation();
			$d->setDelegator($row->delegator)
			->setDelegate($row->delegate)
			->setScopes($row->scopes)
			->setExpDate($row->expiration_date)
			->setState($row->state)
			->setCode($row->code);
			$result[] = $d;
		}
		return $result;
	}
	
	/**
	 * Retrieves the delegators of a user that has at least 1 scope in the array $scopes
	 * @param string $id
	 * @param array of string $scopes
	 * @return array of string
	 */
	public function findDelegators($id, $scopes=null){
		$table = $this->delegation_table;
		$select = $table->select();
		$select->where('delegate = ?', $id)
			   ->where('expiration_date >= current_date')
			   ->where('state = 1');
		$rows = $table->fetchAll($select);
		$result = array();
		foreach($rows as $row){
			if($scopes!=null){
				$rowScopes = explode($this->_DELIMITER, $row->scopes);
				foreach($rowScopes as $rs){
					if(in_array($rs, $scopes)){
						$result[] = $row->delegator;
						continue;
					}
				}
			}
			else 
				$result[] = $row->delegator;
		}
		return $result;
	}
	
	/**
	 * Gets the string of scopes from a delegation
	 * @param string $delegator
	 * @param string $delegate
	 * @return array of string
	 */
	public function findScopes($delegator, $delegate){
		$table = $this->delegation_table;
		$select = $table->select();
		$select->where('delegator = ?', $delegator)
			   ->where('delegate = ?', $delegate)
			   ->where('state = 1');
		$row = $table->fetchRow($select);
		if(!$row) return array();
		return explode($this->_DELIMITER, $row->scopes);
	}
	
	
	/**
	 * Finds all users except the one specified
	 * @param string $userId
	 * @return array of string
	 */
	public function findAllUsers($except=null){
		$table = new Oauth_Model_DbTable_ResourceOwner();
		$select = $table->select();
		if($except != null) $select->where('user_id <> ?', $except);
		$rows = $table->fetchAll($select);
		$result = array();
		foreach($rows as $row){
			$result[] = $row->user_id;
		}
		return $result;
	}
	
	/**
	 * Returns all scopes 
	 * @return array of string (scope_id => scope_description)
	 */
	public function findAllScopes(){
		$table = new Oauth_Model_DbTable_Scope();
		$select = $table->select();
		$rows = $table->fetchAll($select);
		$result = array();
		foreach($rows as $row){
			$result[$row->scope_id] = $row->scope_description;
		}
		return $result;
	}
	
	/**
	 * Finds the scopes permitted for a role
	 * @param integer $roleId
	 * @return array of string | boolean
	 */
	public function findRoleScopes($roleId){
		$table = $this->roleScope_table;
		$select = $table->select();
		$select->where('role_id = ?', $roleId);
		$row = $table->fetchRow($select);
		if(!$row) return false;
		return explode($this->_DELIMITER, $row->scopes);
	}
	
	/**
	 * Check if at least 1 scope of the role is included in the scopes specified
	 * @param array of Delegation_Model_Role $roles
	 * @param array of string $scopes
	 * @return boolean
	 */
	public function CanRoleSeeUsers($role, $scopes){
		$result = false;
		$roleScopes = explode($this->_DELIMITER, $role->getRoleScopes());
		foreach($roleScopes as $rs){
			if(in_array($rs, $scopes)){
				$result = true;
				break;
			}
		}
		return $result;
	}
	

	/**
	 * Finds a role
	 * @param int $id
	 * @return boolean|Delegation_Model_Role
	 */
	public function findRole($id){
		$tableRole = $this->role_table;
		$tableRoleScope = $this->roleScope_table; 
		
		$select = $tableRole->select();
		$select->setIntegrityCheck(false)
				->from($tableRole->getName())
				->joinNatural($tableRoleScope->getName())
				->where('role_id = ?', $id);
		
		$row = $tableRole->fetchRow($select);
		if(!$row) return false;
		$role = new Delegation_Model_Role();
		$role->setRoleId($row->role_id)
		     ->setRoleName($row->role_name)
		     ->setRoleUri($row->role_uri)
		     ->setRoleScopes($row->scopes);
		return $role;
	}
	/**
	 * Finds all the available roles
	 * @return array of Delegation_Model_Role
	 */
	public function findAllRoles(){
		$table = $this->role_table;
		$select = $table->select();
		$rows = $table->fetchAll($select);
		$result = array();
		foreach($rows as $row){
			$r = new Delegation_Model_Role();
			$r->setRoleId($row->role_id)
			  ->setRoleName($row->role_name)
			  ->setRoleUri($row->role_uri);
			$result[] = $r;
		}
		return $result;
	}
	
	/**
	 * Insert a delegation into the database
	 * @param Delegation_Model_Delegation $delegation
	 */
	public function addDelegation($delegation){
		
		$record = array('delegator' => $delegation->getDelegator(),
						'delegate' => $delegation->getDelegate(),
						'scopes' => $delegation->getScopes(),
						'expiration_date' => $delegation->getExpDate(),
						'state' => $delegation->getState(),
						'code' => $delegation->getCode()
		);
		$this->delegation_table->insert($record);
	}
	
	/**
	 * Edits a delegation
	 * @param Delegation_Model_Delegation $delegation
	 */
	public function editDelegation($delegation, $delegator, $delegate){

		if($delegation->getScopes() == "")
			throw new Exception("Scopes are empty. Chose at least 1 scope");
		
		$data = array('delegate' => $delegation->getDelegate(),
					  'scopes' => $delegation->getScopes()
		);
		
		$table = $this->delegation_table;
		$table->update($data, "delegator = '".$delegator."' and delegate = '".$delegate."'");
	}
	
	/**
	 * Removes a delegation from the database (with notification)
	 * @param string $delegator
	 * @param string $delegate
	 */
	public function removeDelegation($delegator, $delegate){
		$this->delegation_table->delete('delegator = "'.$delegator.'" and delegate = "'.$delegate.'" and state = 1');
	}
	
	/**
	 * Revokes a pending delegation (without notification)
	 * @param string $delegator
	 * @param string $delegate
	 */
	public function revokeDelegation($delegator, $delegate){
		$this->delegation_table->delete('delegator = "'.$delegator.'" and delegate = "'.$delegate.'" and state = 0');
	}
	
	
	/**
	 * Sends an email to the address receiverMail, telling that
	 * a delegation has been made
	 * @param string $senderMail
	 * @param string $receiverMail
	 * @param array of string $scopes
	 */
	public function delegationCreationMail($senderMail, $receiverMail, $scopes, $delegation, $url){
		
		$link = $url . '?code=' . $delegation->getCode();
		
		$mail = new Zend_Mail();
		$mail->setBodyHtml("User <b>".$senderMail."</b> has delegated you for
				the following scopes: <br/><br/><b>".implode('<br/>', $scopes)."</b>.<br/><br/> To confirm
				the delegation click on the following link:<br/>
				<a href='".$link."'>Confirm delegation</a>");
		$mail->setFrom('oauth2del@gmail.com');
		//label for gmail. For tests and demo
		$exploded = explode('@', $receiverMail);
		$label = '_' . $exploded[0];
		$mail->addTo('oauth2del+'.$label.'@gmail.com', $label);
		//For a real usage: sends to the real email address
		//$mail->addTo($receiverMail);
		$mail->setSubject('[AS] New Delegation from '. $senderMail);
		$mail->send();
		
	}
	
	/**
	 * Sends an email to the address receiverMail, telling that
	 * a delegation has been deleted
	 * @param string $senderMail
	 * @param string $receiverMail
	 */
	public function delegationDeletionMail($senderMail, $receiverMail){
		$mail = new Zend_Mail();
		$mail->setBodyHtml("User <b>".$senderMail."</b> has deleted his delegation");
		$mail->setFrom('oauth2del@gmail.com');
		//label for gmail. For tests and demo
		$exploded = explode('@', $receiverMail);
		$label = '_' . $exploded[0];
		$mail->addTo('oauth2del+'.$label.'@gmail.com', $label);
		//For a real usage: sends to the real email address
		//$mail->addTo($receiverMail);
		$mail->setSubject('[AS] Delegation deleted by '. $senderMail);
		$mail->send();
	}
	
	/**
	 * Sends an email to the address receiverMail, telling that
	 * a delegation has been edited
	 * @param string $senderMail
	 * @param string $receiverMail
	 * @param array of string $scopes
	 */
	public function delegationEditMail($senderMail, $receiverMail, $scopes){
		$mail = new Zend_Mail();
		$mail->setBodyHtml("User <b>".$senderMail."</b> has edited his delegation.
					       Now the scopes you can access to are: <br/><br/>". 
						   implode('<br/>', $scopes));
		$mail->setFrom('oauth2del@gmail.com');
		//label for gmail. For tests and demo
		$exploded = explode('@', $receiverMail);
		$label = '_' . $exploded[0];
		$mail->addTo('oauth2del+'.$label.'@gmail.com', $label);
		//For a real usage: sends to the real email address
		//$mail->addTo($receiverMail);
		$mail->setSubject('[AS] Delegation edited by '. $senderMail);
		$mail->send();
	}
	
	/**
	 * Sends an email to the address receiverMail, telling that
	 * a delegation has been used
	 * @param string $senderMail
	 * @param string $receiverMail
	 */
	public function delegationUsedMail($senderMail, $receiverMail){
		$mail = new Zend_Mail();
		$mail->setBodyHtml("User <b>".$senderMail."</b> has	accessed to some of your scopes.");
		$mail->setFrom('oauth2del@gmail.com');
		//label for gmail. For tests and demo
		$exploded = explode('@', $receiverMail);
		$label = '_' . $exploded[0];
		$mail->addTo('oauth2del+'.$label.'@gmail.com', $label);
		//For a real usage: sends to the real email address
		//$mail->addTo($receiverMail);
		$mail->setSubject('[AS] Delegation used by '. $senderMail);
		$mail->send();
	}
	
	
	public function verifyDelegation($code){
		$table = $this->delegation_table;
		$select = $table->select();
		$select->where('code = ?', $code);
		$row = $table->fetchRow($select);
		if(!$row) return false;
		
		//set the delegation as verified (state = 1)
		$data = array( 'state' => 1 );
		$table->update($data, "code = ".$code);
		
		return true;
	}
}