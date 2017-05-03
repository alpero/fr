<?php
/* ---------------------------------------------------------------------------
 * filename    : login.php
 * author      : George Corser, gcorser@gmail.com
 * description : This program logs the user in by setting $_SESSION variables
 * ---------------------------------------------------------------------------
 */

// Start or resume session, and create: $_SESSION[] array
session_start(); 

require 'database.php';

if (!empty($_POST)) { // if $_POST filled then process the form
    $response = array();

	// initialize $_POST variables
	$username = $_POST['username']; // username is email address
	$password = $_POST['password'];
		
	// verify the username/password
	$pdo = Database::connect();
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$sql = "SELECT * FROM fr_persons WHERE email = ? AND password = ? LIMIT 1";
	$q = $pdo->prepare($sql);
	$q->execute(array($username,$password));
	$data = $q->fetch(PDO::FETCH_ASSOC);
	
	if($data) { // if successful login set session variables
		$_SESSION['fr_person_id'] = $data['id'];
		$_SESSION['fr_person_title'] = $data['title'];
        $response['valid'] = true;
        $response['sid'] = $data['id'];
	}
	else { // otherwise go to login error page
        $response['valid'] = false;
	}
    
    Database::disconnect();
    echo json_encode($response);
}
?>