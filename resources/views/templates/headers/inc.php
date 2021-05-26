<?php
include __DIR__ . "/../../includes/config.php";
include __DIR__ . "/../../includes/functions.php";
include __DIR__ . "/../../includes/db_connect.php";
session_destroy();
session_start(); 
$global_sql = $dbh->prepare("SELECT user.* FROM session, user where session.user_id = user.id");
$global_sql->execute();
$user = $global_sql->fetch(PDO::FETCH_ASSOC);


$username = $user['username'];
$user_id = $user["id"];
$email = $user["email"];
$rank = $user["rank"]; 
$first_pay = $user["first_pay"];
$is_paying = $user["is_paying"];
$stripe_plan = $user["stripe_plan"];

// Create the sessions variables
$_SESSION["USER_ID"] = $user_id;
$_SESSION["EMAIL"] = $email;
$_SESSION["USERNAME"] = $username;
$_SESSION["RANK"] = $rank;
$_SESSION["FIRST_PAY"] = $first_pay;
$_SESSION["IS_PAYING"] = $is_paying;

if($stripe_plan == "") {
    $_SESSION["SUBSCRIPTION_PLAN"] = FREE_PLAN;
} else {
    $_SESSION["SUBSCRIPTION_PLAN"] = $stripe_plan;
}
// dd($_SESSION);
// require_once('vendor/autoload.php');
?>