<?php 
/* ---------------------------------------------------------------------------
 * filename    : fr_event_create.php
 * author      : George Corser, gcorser@gmail.com
 * description : This program adds/inserts a new event (table: fr_events)
 * ---------------------------------------------------------------------------
 */
session_start();
if(!isset($_SESSION["fr_person_id"])){ // if "user" not set,
	session_destroy();
	exit;
}

require 'database.php';

if($_SESSION["fr_person_title"] != "Administrator") {
    // if "user" is not an administrator
    exit;
}

if (!empty($_POST)) { // if not first time through
    $response = array();
    $response[valid] = false;

	// initialize user input validation variables
	$dateError = null;
	$timeError = null;
	$locationError = null;
	$descriptionError = null;
	
	// initialize $_POST variables
	$date = $_POST['event_date'];
	$time = $_POST['event_time'];
	$location = $_POST['event_location'];
	$description = $_POST['event_description'];		
	
	// validate user input
	$valid = true;
	if (empty($date) || empty($time) || empty($location) || empty($description)) {
		$valid = false;
	}

	// insert data
	if ($valid) {
		$pdo = Database::connect();
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$sql = "INSERT INTO fr_events (event_date, event_time, event_location, event_description) values(?, ?, ?, ?)";
		$q = $pdo->prepare($sql);
		$q->execute(array($date,$time,$location,$description));
		Database::disconnect();
        $response[valid] = true;
	}
    
    echo json_encode($response);
}
?>