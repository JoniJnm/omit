<?php

class User {
	const TYPE_ALUMNO = 0;
	const TYPE_PROFESOR = 1;
	const TYPE_ADMIN = 2;
	
	private static $USER_TYPES = array('alumno', 'profesor', 'admin');
	private static $PREFIX_KEY_ALIAS = "user_";
	private static $instances = array();
	
	private $username;
	private $name;
	private $id;
	private $loged;
	private $userType;
	
	function __construct($userType) {
		$userType = self::clearUserType($userType);
		$this->clear();
		$this->userType = $userType;
	}
	
	/**
	 * 
	 * @return User
	 */
	static function getInstance($userType) {
		$userType = self::clearUserType($userType);
		if (!isset(self::$instances[$userType])) {
			$id = Session::get(self::$PREFIX_KEY_ALIAS.$userType);
			$obj = new self($userType);
			if ($id) {
				$db = Database::getInstance();
				$r = $db->loadObject('SELECT username,name FROM #__usuarios WHERE id='.$db->scape($id).' AND type='.$db->scape($userType));
				if ($r) {
					$obj->id = $id;
					$obj->name = $r->name;
					$obj->username = $r->username;
					$obj->loged = true;
				}
				else {
					Session::clear(self::$PREFIX_KEY_ALIAS.$userType);
				}
			}
			self::$instances[$userType] = $obj;
		}
		return self::$instances[$userType];
	}
	static function getUserTypes() {
		return self::$USER_TYPES;
	}
	static function clearUserType($userType) {
		if (is_numeric($userType)) {
			if (!array_key_exists($userType, self::$USER_TYPES)) $userType = 0;
			return $userType;
		}
		else {
			$keys = array_keys(self::$USER_TYPES, $userType, true);
			return $keys ? $keys[0] : 0;
		}
	}
	
	function getUsername() {
		return $this->getUsername();
	}
	function getId() {
		return $this->id;
	}
	function login($username, $pass) {
		$db = Database::getInstance();
		$obj = $db->loadObject('SELECT id,name FROM #__usuarios WHERE username='.$db->scape($username).' AND pass='.$db->scape($pass));
		if ($obj) {
			$this->id = $obj->id;
			$this->name = $obj->name;
			$this->username = $username;
			$this->loged = true;
			Session::set(self::$PREFIX_KEY_ALIAS.$this->getUserType(), $this->id);
			return true;
		}
		else {
			die('SELECT id,name FROM #__usuarios WHERE username='.$db->scape($username).' AND pass='.$db->scape($pass));
		}
		return false;
	}
	function isLoged() {
		return $this->loged;
	}
	function getUserType() {
		return $this->userType;
	}
	function getUserTypeName() {
		return self::$USER_TYPES[$this->userType];
	}
	function toHomeIfLoged() {
		if ($this->isLoged())
			$this->toHome();
	}
	function toLoginIfNotLoged() {
		if (!$this->isLoged())
			$this->toLogin();
	}
	function toHome() {
		Redirect::_($this->getUserTypeName().'.php');
	}
	function toLogin() {
		Redirect::_('login.php?userType='.$this->getUserTypeName());
	}
	function logout() {
		$this->clear();
		Session::clear(self::$PREFIX_KEY_ALIAS.$this->getUserType());
	}
	
	private function clear() {
		$this->name = "";
		$this->username = "";
		$this->id = 0;
		$this->loged = false;
	}
}