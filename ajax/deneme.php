<?php
//error_reporting(E_ERROR);
//error_reporting(E_ALL);
//ini_set('report_errors','on');

require_once("../ajax/dbfuncs.php");

$db = new dbfuncs();

if (!isset($_SESSION) || !is_array($_SESSION)) session_start();
$ownerID = isset($_SESSION['ownerID']) ? $_SESSION['ownerID'] : "";
$username = isset($_SESSION['username']) ? $_SESSION['username'] : "";
$google_id = isset($_SESSION['google_id']) ? $_SESSION['google_id'] : "";

$id = isset($_REQUEST["id"]) ? $_REQUEST["id"] : "";
$p = isset($_REQUEST["p"]) ? $_REQUEST["p"] : "";
if (isset($_REQUEST['id'])) {
    $id = $_REQUEST['id'];
}

if ($p=="getProjects"){
    $data = $db -> getProjects($id,$ownerID);
}
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Content-type: application/json');
echo $data;
exit;
?>