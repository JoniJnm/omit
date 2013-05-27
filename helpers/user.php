<?php

class User {
	const TYPE_ALUMNO = 0;
	const TYPE_PROFESOR = 1;
	const TYPE_ADMIN = 2;
	
	private static $USER_TYPES = array('alumno', 'profesor', 'admin');
	private static $PREFIX_KEY_ALIAS = "user_";
	private static $instances = array();
	
	private $id;
	private $email;
	private $nombre;
	private $loged;
	private $userType;
	
	function __construct($userType) {
		$userType = self::clearUserType($userType);
		$this->clear();
		$this->userType = $userType;
	}
	
	/**
	 * Dado un tipo de usuario, comprueba si está logeado como tal y devuelve una instancia
	 * de tipo usuario
	 * @return User instancia de tipo usuario
	 */
	static function getInstance($userType) {
		$userType = self::clearUserType($userType);
		if (!isset(self::$instances[$userType])) {
			$id = Session::get(self::$PREFIX_KEY_ALIAS.$userType);
			$obj = new self($userType);
			if ($id) {
				$db = Database::getInstance();
				$r = $db->loadObject('SELECT email,nombre FROM #__usuarios WHERE id='.$db->scape($id).' AND type='.$db->scape($userType));
				if ($r) {
					$obj->id = $id;
					$obj->email = $r->email;
					$obj->nombre = $r->nombre;
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
	
	/**
	 * Devuelve un array con índice asociado al nombre del tipo de usuario
	 * @return string[]
	 */
	static function getUserTypes() {
		return self::$USER_TYPES;
	}
	
	/**
	 * Devuelve un tipo de usuario. La función se utiliza para asegurar
	 * que el "tipo de usuario" existe. Si no existe se devuelve por defecto TIPO_ALUMNO
	 * @param int|string $userType 
	 * @param boolean $string la salida puede ser el número asociado al unserType o el string
	 * @return int|string dependiendo de la variable $string se devuelve el userType de una forma u otra
	 */
	static function clearUserType($userType, $string=false) {
		if (is_numeric($userType)) {
			if (!array_key_exists($userType, self::$USER_TYPES))
				$userType = 0;
		}
		else {
			$keys = array_keys(self::$USER_TYPES, $userType, true);
			$userType = $keys ? $keys[0] : self::TYPE_ALUMNO;
		}
		return $string ? self::$USER_TYPES[$userType] : $userType;
	}
	
	function getUsername() {
		return $this->getUsername();
	}
	function getId() {
		return $this->id;
	}
	
	/**
	 * Aplica acceso al usuario
	 * @param string $email el email del usuario
	 * @param string $pass la contraseña del usuario (debe estar en md5)
	 * @return boolean true si todo fue bien, false en caso contrario
	 */
	function login($email, $pass) {
		$db = Database::getInstance();
		$obj = $db->loadObject('SELECT id,nombre FROM #__usuarios WHERE email='.$db->scape($email).' AND pass='.$db->scape($pass));
		if ($obj) {
			$this->id = $obj->id;
			$this->nombre = $obj->nombre;
			$this->email = $email;
			$this->loged = true;
			Session::set(self::$PREFIX_KEY_ALIAS.$this->getUserType(), $this->id);
			return true;
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
	
	/**
	 * Saca al usuario de todas las sesiones en las que esté logeado
	 * @param int $userType para redirigir al página de login con ese userType
	 */
	static function logout($userType) {
		$loged = false;
		$types = User::getUserTypes();
		foreach ($types as $type) {
			$loged = User::getInstance($type)->isLoged();
			if ($loged) break;
		}
		Session::clear(self::$PREFIX_KEY_ALIAS.self::TYPE_ALUMNO);
		Session::clear(self::$PREFIX_KEY_ALIAS.self::TYPE_PROFESOR);
		Session::clear(self::$PREFIX_KEY_ALIAS.self::TYPE_ADMIN);
		if ($loged) Mensajes::addInfo("Te has desconectado correctamente");
		self::getInstance($userType)->toLogin();
	}
	
	private function clear() {
		$this->nombre = "";
		$this->email = "";
		$this->id = 0;
		$this->loged = false;
	}
}