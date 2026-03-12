<?php
date_default_timezone_set('Asia/Bangkok');

// *** Restrict Access To Page: Grant or deny access to this page
function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup)
{
	// For security, start by assuming the visitor is NOT authorized.
	$isValid = False;

	// When a visitor has logged into this site, the Session variable MM_Username set equal to their username.
	// Therefore, we know that a user is NOT logged in if that Session variable is blank.
	if (!empty($UserName)) {
		// Besides being logged in, you may restrict access to only certain users based on an ID established when they login.
		// Parse the strings into arrays.
		$arrUsers = Explode(",", $strUsers);
		$arrGroups = Explode(",", $strGroups);
		if (in_array($UserName, $arrUsers)) {
			$isValid = true;
		}
		// Or, you may restrict access to only certain users based on their username.
		if (in_array($UserGroup, $arrGroups)) {
			$isValid = true;
		}
		if (($strUsers == "") && true) {
			$isValid = true;
		}
	}
	return $isValid;
}

$RedirectPath = "login.php";
$adminLogin = $_SESSION['AdminLogin'] ?? null;
$adminId = (string)($adminLogin['AdminID'] ?? '');
$adminType = (string)($_SESSION['AdminLoginType']['Thaifa'] ?? $_SESSION['AdminLoginType']['SchoolHub'] ?? '');

if (!is_array($adminLogin) || isAuthorized("", $MM_authorizedUsers, $adminId, $adminType) == false) {
	$MM_qsChar = "?";
	$MM_referrer = $_SERVER['PHP_SELF'];
	if (strpos($RedirectPath, "?")) $MM_qsChar = "&";
	if (isset($_SERVER['QUERY_STRING']) && strlen($_SERVER['QUERY_STRING']) > 0)
		$MM_referrer .= "?" . $_SERVER['QUERY_STRING'];
	$RedirectPath = $RedirectPath . $MM_qsChar . "accesscheck=" . urlencode($MM_referrer);
	header("Location: " . $RedirectPath);
	exit;
}

if (!class_exists('AdminSecurityManagement')) {
	include_once(__DIR__ . '/../../backend/classes/AdminSecurityManagement.class.php');
}
if (class_exists('AdminSecurityManagement')) {
	$adminSecurityAudit = new AdminSecurityManagement();
	if (!$adminSecurityAudit->isCurrentSessionAllowed()) {
		session_destroy();
		header("Location: login.php?revoked=1");
		exit;
	}
	$adminSecurityAudit->touchCurrentAdminSession();
}
