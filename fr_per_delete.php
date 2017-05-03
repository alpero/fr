<?php 
/* ---------------------------------------------------------------------------
 * filename    : fr_per_delete.php
 * author      : George Corser, gcorser@gmail.com
 * description : This program deletes one volunteer's details (table: fr_persons)
 * ---------------------------------------------------------------------------
 */
session_start();
if(!isset($_SESSION["fr_person_id"])){ // if "user" not set,
	session_destroy();
	exit;
}

require 'database.php';
require 'functions.php';

$id = $_GET['id'];

if($_SESSION["fr_person_id"] != $id && $_SESSION["fr_person_title"] != "Administrator") {
    // if "user" is not an administrator or profile owner
    exit;
}

if (!empty($_POST)) { // if user clicks "yes" (sure to delete), delete record
	$id = $_POST['id'];	
	$pdo = Database::connect();
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$sql = "DELETE FROM fr_persons  WHERE id = ?";
	$q = $pdo->prepare($sql);
	$q->execute(array($id));
	Database::disconnect();
} 
else { // otherwise, pre-populate fields to show data to be deleted
	$pdo = Database::connect();
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$sql = "SELECT * FROM fr_persons where id = ?";
	$q = $pdo->prepare($sql);
	$q->execute(array($id));
	$data = $q->fetch(PDO::FETCH_ASSOC);
	Database::disconnect();
}
?>

    <!-- Display same information as in file: fr_per_read.php -->
    
    <div class="form-horizontal" >
            
        <div class="control-group col-md-6">
        
            <label class="control-label">First Name</label>
            <div class="controls ">
                <label class="checkbox">
                    <?php echo $data['fname'];?> 
                </label>
            </div>
            
            <label class="control-label">Last Name</label>
            <div class="controls ">
                <label class="checkbox">
                    <?php echo $data['lname'];?> 
                </label>
            </div>
            
            <label class="control-label">Email</label>
            <div class="controls">
                <label class="checkbox">
                    <?php echo $data['email'];?>
                </label>
            </div>
            
            <label class="control-label">Mobile</label>
            <div class="controls">
                <label class="checkbox">
                    <?php echo $data['mobile'];?>
                </label>
            </div>     
            
            <label class="control-label">Title</label>
            <div class="controls">
                <label class="checkbox">
                    <?php echo $data['title'];?>
                </label>
            </div>   
            
            <!-- password omitted on Read/View -->
            
        </div>
        
        <!-- Display photo, if any --> 

        <div class='control-group col-md-6'>
            <div class="controls ">
            <?php 
            if ($data['filesize'] > 0) 
                echo '<img  height=5%; width=15%; src="data:image/jpeg;base64,' . 
                    base64_encode( $data['filecontent'] ) . '" />'; 
            else 
                echo 'No photo on file.';
            ?><!-- converts to base 64 due to the need to read the binary files code and display img -->
            </div>
        </div>
        
            <div class="row">
                <h4>Events for which this Volunteer has been assigned</h4>
            </div>
            
            <?php
                $pdo = Database::connect();
                $sql = "SELECT * FROM fr_assignments, fr_events WHERE assign_event_id = fr_events.id AND assign_per_id = " . $id . " ORDER BY event_date ASC, event_time ASC";
                foreach ($pdo->query($sql) as $row) {
                    echo Functions::dayMonthDate($row['event_date']) . ': ' . Functions::timeAmPm($row['event_time']) . ' - ' . $row['event_location'] . ' - ' . $row['event_description'] . '<br />';
                }
            ?>
            
    </div>  <!-- end div: class="form-horizontal" -->