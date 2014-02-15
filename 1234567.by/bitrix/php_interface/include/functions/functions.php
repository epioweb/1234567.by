<?
// ѕроверка строки, €вл€етс€ ли строка email адресом
	function isEmail($email) {
		return preg_match('|^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]{2,})+$|i', $email);
	};
?>