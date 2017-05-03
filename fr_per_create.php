<?php 
/* ---------------------------------------------------------------------------
 * filename    : fr_per_create.php
 * author      : George Corser, gcorser@gmail.com
 * description : This program adds/inserts a new volunteer (table: fr_persons)
 * ---------------------------------------------------------------------------
 */
require 'database.php';

if (!empty($_POST)) { // if not first time through
    $response = array();
    $response[valid] = false;
    
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
    
	// insert data
	if ($valid) 
	{
		$pdo = Database::connect();
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$sql = "INSERT INTO fr_persons (fname,lname,email,mobile,password,title,
		filename,filesize,filetype,filecontent) values(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
		$q = $pdo->prepare($sql);
		$q->execute(array($fname,$lname,$email,$mobile,$password,$title,
		$fileName,$fileSize,$fileType,$content));        
		Database::disconnect();
		$response[valid] = true;
	}
    
    echo json_encode($response);
}
?>