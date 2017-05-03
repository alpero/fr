<?php 
/* ---------------------------------------------------------------------------
 * filename    : fr_event_update.php
 * author      : George Corser, gcorser@gmail.com
 * description : This program updates an event (table: fr_events)
 * ---------------------------------------------------------------------------
 */
session_start();
if(!isset($_SESSION["fr_person_id"])){ // if "user" not set,
	session_destroy();
	exit;
}

if($_SESSION["fr_person_title"] != "Administrator") {
    // if "user" is not an administrator
    exit;
}

require 'database.php';

$id = $_GET['id'];
$response = array();
$response[valid] = false;

if (!empty($_POST)) { // if $_POST filled then process the form
    
	# initialize/validate (same as file: fr_event_create.php)

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
	
	if ($valid) { // if valid user input update the database
		$pdo = Database::connect();
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$sql = "UPDATE fr_events SET event_date = ?, event_time = ?, event_location = ?, event_description = ? WHERE id = ?";
		$q = $pdo->prepare($sql);
		$q->execute(array($date,$time,$location,$description,$id));
		Database::disconnect();
		$response[valid] = true;
	}
} else { // if $_POST NOT filled then pre-populate the form
	$pdo = Database::connect();
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$sql = "SELECT * FROM fr_events where id = ?";
	$q = $pdo->prepare($sql);
	$q->execute(array($id));
	$data = $q->fetch(PDO::FETCH_ASSOC);
	$response[event_date] = $data['event_date'];
	$response[event_time] = $data['event_time'];
	$response[event_location] = $data['event_location'];
	$response[event_description] = $data['event_description'];
	Database::disconnect();
}

// output response
echo json_encode($response);
?>