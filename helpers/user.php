<?php

class User {
	function getUserTypes() {
		return array('alumno', 'profesor', 'admin');
	}
	function isLoged($userType) {
		$userType = self::clearUserType($userType);
		$types = self::getUserTypes();
		foreach ($types as $type) {
			if ($userType == $type && Session::get('is'.ucfirst($type)))
				return true;
		}
		return false;
	}
	function toHome($userType) {
		$userType = self::clearUserType($userType);
		Redirect::_($userType.'.php');
	}
	function toLogin($userType) {
		$userType = self::clearUserType($userType);
		Redirect::_('login.php?userType='.$userType);
	}
	function clearUserType($userType) {
		if (!in_array($userType, self::getUserTypes())) $userType = 'alumno';
		return $userType;
	}
	function salir() {
		$types = self::getUserTypes();
		foreach ($types as $type) {
			Session::clear('is'.ucfirst($type));
		}
	}
}