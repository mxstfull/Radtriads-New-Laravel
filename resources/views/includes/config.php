<?php
date_default_timezone_set('Europe/London');

// Show or Hide the errors
define("DEBUG_MODE", false);

// Show or Hide the errors
define("TEST_MODE", false);

// MAIN URL
//define("URL", "https://www.radtriads.com"); /* replace with current temp url */
// define("URL", "127.0.0.1/RadTriads_4.13");
define("URL", "http://15.222.57.63:8000");

// DB Host
define("DB_HOST", "localhost");

// DB Username
define("DB_USERNAME", "user");

// DB Password
//define("DB_PASSWORD", "%NF6q6l)m2)a]BfyK.");
define("DB_PASSWORD", "Password123!");
// DB Name
//define("DB_NAME", "radtriads_main");
define("DB_NAME", "radtriads_main");
// FREE PLAN
define("FREE_PLAN", "4");

// PLATINUM PLAN
define("PLATINUM_PLAN", "3");

// GOLD PLAN
define("GOLD_PLAN", "2");

// SILVER PLAN
define("SILVER_PLAN", "1");

/*
TEST MODE	
*/
/*
// STRIPE KEY
define("STRIPE_KEY", "pk_test_EisGL3oCloa0wp8YNH0HjNWl00sZkgXQGI");

// STRIPE KEY
define("STRIPE_SECRET_KEY", "sk_test_rjkpKROAdoLYaBHBwEDEvizX00Gmzg9ScY");

// STRIPE WEBHOOK KEY
define("STRIPE_WEBHOOK_KEY", "whsec_H6XMBagJHzrQrk04sKR6UkYpcTF2ilaJ");

// PLAN 1 ID
define("STRIPE_PLAN_1", "price_1H2C6hI8XlJR7K1Gj2cxxmmY");

// PLAN 2 ID
define("STRIPE_PLAN_2", "price_1H2CB4I8XlJR7K1G8QaqzJ9D");

// PLAN 3 ID
define("STRIPE_PLAN_3", "price_1H2CF4I8XlJR7K1GWtyE5G4B");

// PLAN 4 ID
define("STRIPE_PLAN_4", "price_1H2C6hI8XlJR7K1Gllvot61P");

// PLAN 5 ID
define("STRIPE_PLAN_5", "price_1H2CB4I8XlJR7K1GkfSMAZtJ");

// PLAN 6 ID
define("STRIPE_PLAN_6", "price_1H2CF5I8XlJR7K1GqXssIIQb");
*/

/*
PROD MODE
*/
// STRIPE KEY
define("STRIPE_KEY", "pk_live_X73EY5xhMQWnpw5MeI1McBGS00Vy7XoZH6");

// STRIPE KEY
define("STRIPE_SECRET_KEY", "sk_live_5y2oZjBPcz0l3nM3vTRuiGAV00igGpe3EF");

// STRIPE WEBHOOK KEY
define("STRIPE_WEBHOOK_KEY", "whsec_QTV76aLi8p41VmXu2dcAUYwiib9c6q7Z");

// PLAN 1 ID
define("STRIPE_PLAN_1", "price_1HUA3RI8XlJR7K1GDRvS27AD");

// PLAN 2 ID
define("STRIPE_PLAN_2", "price_1HUA3MI8XlJR7K1GvriLPHnt");

// PLAN 3 ID
define("STRIPE_PLAN_3", "price_1HUA3CI8XlJR7K1GDJbkVcbd");

// PLAN 4 ID
define("STRIPE_PLAN_4", "price_1HUA3RI8XlJR7K1Go8doxDwo");

// PLAN 5 ID
define("STRIPE_PLAN_5", "price_1HUA3MI8XlJR7K1Gt81wnnry");

// PLAN 6 ID
define("STRIPE_PLAN_6", "price_1HUA3CI8XlJR7K1GtyaNRqEK");

// STACKPATH CDN
define("STACKPATH_URL", "https://radtriads.com");

// GOOGLE API KEY
define("GOOGLE_API_KEY", "AIzaSyDkGP4XbpCDaAB-qFIqnJqNIqStWWA1IOU");

if(DEBUG_MODE) {
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
}	
define("FRONTEND_URL", "http://localhost:4200");
?>
