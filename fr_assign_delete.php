<?php 
/* ---------------------------------------------------------------------------
 * filename    : fr_assign_delete.php
 * author      : George Corser, gcorser@gmail.com
 * description : This program deletes one assignment's details (table: fr_assignments)
 * ---------------------------------------------------------------------------
 */
 
session_start();
if(!isset($_SESSION["fr_person_id"])){ // if "user" not set,
	session_destroy();
	exit;
}

require 'database.php';
require 'functions.php';

if (!empty($_POST)) { // if user clicks "yes" (sure to delete), delete record

	// delete data
	$pdo = Database::connect();
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$sql = "DELETE FROM fr_assignments WHERE assign_per_id = ? AND assign_event_id = ?";
	$q = $pdo->prepare($sql);
	$q->execute(array($_POST['pid'],$_POST['eid']));
	Database::disconnect();
    print_r($_POST);
} 
else { // otherwise, pre-populate fields to show data to be deleted

	$pdo = Database::connect();
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
    # get volunteer details
    $sql = "SELECT * FROM fr_persons where id = ?";
    $q = $pdo->prepare($sql);
    $q->execute(array($_GET['pid']));
    $perdata = $q->fetch(PDO::FETCH_ASSOC);
	
	# get event details
	$sql = "SELECT * FROM fr_events where id = ?";
	$q = $pdo->prepare($sql);
	$q->execute(array($_GET['eid']));
	$eventdata = $q->fetch(PDO::FETCH_ASSOC);
	
	Database::disconnect();
}
?>			

			<!-- Display same information as in file: fr_assign_read.php -->
			
			<div class="form-horizontal" >
			
				<div class="control-group">
					<label class="control-label">Volunteer</label>
					<div class="controls">
						<label class="checkbox">
							<?php echo $perdata['lname'] . ', ' . $perdata['fname'] ;?>
						</label>
					</div>
				</div>
				
				<div class="control-group">
					<label class="control-label">Event</label>
					<div class="controls">
						<label class="checkbox">
							<?php echo trim($eventdata['event_description']) . " (" . trim($eventdata['event_location']) . ") ";?>
						</label>
					</div>
				</div>
				
				<div class="control-group">
					<label class="control-label">Date, Time</label>
					<div class="controls">
						<label class="checkbox">
							<?php echo Functions::dayMonthDate($eventdata['event_date']) . ", " . Functions::timeAmPm($eventdata['event_time']);?>
						</label>
					</div>
				</div>
			
			</div> <!-- end div: class="form-horizontal" -->