<?php 
/* ---------------------------------------------------------------------------
 * filename    : fr_per_update.php
 * author      : George Corser, gcorser@gmail.com
 * description : This program updates one volunteer's details (table: fr_persons)
 * ---------------------------------------------------------------------------
 */
session_start();
if(!isset($_SESSION["fr_person_id"])){ // if "user" not set,
	session_destroy();
	exit;
}
	
require 'database.php';

$id = $_GET['id'];
$response = array();

if($_SESSION["fr_person_id"] != $id && $_SESSION["fr_person_title"] != "Administrator") {
    // if "user" is not an administrator or profile owner
    exit;
}

if (!empty($_POST)) { // if $_POST filled then process the form
    $response[valid] = false;

	# initialize/validate (same as file: fr_per_create.php)

	// initialize user input validation variables
	$fnameError = null;
	$lnameError = null;
	$emailError = null;
	$mobileError = null;
	$passwordError = null;
	$titleError = null;
	$pictureError = null; // not used
	
	// initialize $_POST variables
	$fname = $_POST['fname'];
	$lname = $_POST['lname'];
	$email = $_POST['email'];
	$mobile = $_POST['mobile'];
	$password = $_POST['password'];
	$title =  $_POST['title'];
	$picture = $_POST['picture']; // not used
	
	// initialize $_FILES variables
	$fileName = $_FILES['userfile']['name'];
	$tmpName  = $_FILES['userfile']['tmp_name'];
	$fileSize = $_FILES['userfile']['size'];
	$fileType = $_FILES['userfile']['type'];
	$content = file_get_contents($tmpName);

    // validate user input
	$valid = true;
	if (empty($fname) || empty($lname) || empty($email) || empty($mobile) || 
        empty($password) || empty($title)) {
		$valid = false;
	}
	
    if ($valid) {
        // validate email
        if ( !filter_var($email,FILTER_VALIDATE_EMAIL) ) {
            $response[emailErr] = true;
            $valid = false;
        }
        
        // do not allow 2 records with same email address!
        $pdo = Database::connect();
        $sql = "SELECT * FROM fr_persons";
        foreach($pdo->query($sql) as $row) {
            if($email == $row['email']) {
                $response[duplicateEmail] = true;
                $valid = false;
            }
        }
        Database::disconnect();
        
        // validate mobile number
        if(!preg_match("/^[0-9]{3}-[0-9]{3}-[0-9]{4}$/", $mobile)) {
            $response[mobileError] = true;
            $valid = false;
        }
    }
	
    // if valid user input update the database
	if ($valid) {
		if($fileSize > 0) { // if file was updated, update all fields
			$pdo = Database::connect();
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$sql = "UPDATE fr_persons  set fname = ?, lname = ?, email = ?, mobile = ?, password = ?, title = ?, filename = ?,filesize = ?,filetype = ?,filecontent = ? WHERE id = ?";
			$q = $pdo->prepare($sql);
			$q->execute(array($fname, $lname, $email, $mobile, $password, $title, $fileName,$fileSize,$fileType,$content, $id));
			Database::disconnect();
			$response[valid] = true;
		}
		else { // otherwise, update all fields EXCEPT file fields
			$pdo = Database::connect();
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$sql = "UPDATE fr_persons  set fname = ?, lname = ?, email = ?, mobile = ?, password = ?, title = ? WHERE id = ?";
			$q = $pdo->prepare($sql);
			$q->execute(array($fname, $lname, $email, $mobile, $password, $title,  $id));
			Database::disconnect();
			$response[valid] = true;
		}
	}
} 

// if $_POST NOT filled then pre-populate the form
else { 
	$pdo = Database::connect();
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$sql = "SELECT * FROM fr_persons where id = ?";
	$q = $pdo->prepare($sql);
	$q->execute(array($id));
	$data = $q->fetch(PDO::FETCH_ASSOC);
	$response[fname] = $data['fname'];
	$response[lname] = $data['lname'];
	$response[email] = $data['email'];
	$response[mobile] = $data['mobile'];
	$response[password] = $data['password'];
	$response[title] = $data['title'];
	Database::disconnect();
                            
    // editor is a volunteer only allow volunteer option
    if (0==strcmp($_SESSION['fr_person_title'],'Volunteer')) 
        $response[select] = '<option selected value="Volunteer" >Volunteer</option>';
    else if($title==Volunteer) 
        $response[select] = '<option selected value="Volunteer" >Volunteer</option><option value="Administrator" >Administrator</option>';
    else 
        $response[select] = '<option value="Volunteer">Volunteer</option><option selected value="Administrator" >Administrator</option>';
        
    // converts to base 64 due to the need to read the binary files code and display img
    if ($data['filesize'] > 0) {
        $response[img] = '<img  height=5%; width=15%; src="data:image/jpeg;base64,' . 
            base64_encode( $data['filecontent'] ) . '" />'; 
    } else {
        $response[img] = 'No photo on file.';
    }
}

// output response
echo json_encode($response);
?>