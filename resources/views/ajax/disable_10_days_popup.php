<?php
include("../includes/config.php");
include("../includes/db_connect.php");
include("../includes/upload.class.php");
include("../includes/functions.php");

$result = array();

if($_SESSION) {
	
	$user_id = $_SESSION["USER_ID"];
	
	$user_infos = $dbh->prepare("UPDATE user SET has_seen_10_days_left_popup = 1 WHERE id = :user_id");
	$user_infos->bindParam(":user_id", $user_id);
	$user_infos->execute();
	
	$result["success"] = 1;
	
}

echo json_encode($result);
?>