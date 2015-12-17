<?php
//Authetification
	//Auth 1 - HTTP Basic Auth with 1 user attempt per browsing session (only 1 if he uses a regular browser)
if (!isset($_SERVER['PHP_AUTH_USER'])) {
// send headers for the browser to show an authentication prompt box
header('WWW-Authenticate: Basic realm="Codes requis"');
header('HTTP/1.0 401 Unauthorized');
//logic to send if the users cancels authentication
echo "Entrez vos dientfiants";
exit;
} else {
//we hardcode users and passwords in an array
//we can get them from a database instead
$users = array("admin" => "superadmin", "valocal" => "valocal");
if (!array_key_exists($_SERVER["PHP_AUTH_USER"], $users) || $users[$_SERVER["PHP_AUTH_USER"]] !== $_SERVER['PHP_AUTH_PW']) {
//stop scripts from processing any further - best to place at the top of your scripts logic
//instead, you can set a variable and do not show some sections of the webpage or show something else
//finally you can reask for credentials instead of preventing further code execution
header('WWW-Authenticate: Basic realm="Codes requis"');
header('HTTP/1.0 401 Unauthorized');
echo "Idientifiants incorrects";
exit;
}
}
?>