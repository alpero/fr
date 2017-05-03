<?php 
/* ---------------------------------------------------------------------------
 * filename    : fr_event_delete.php
 * author      : George Corser, gcorser@gmail.com
 * description : This program deletes one event's details (table: fr_events)
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

if($_SESSION["fr_person_title"] != "Administrator") {
    // if "user" is not an administrator
    exit;
}

if (!empty($_POST)) { // if user clicks "yes" (sure to delete), delete record

	$id = $_POST['id'];
	
	// delete data
	$pdo = Database::connect();
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$sql = "DELETE FROM fr_events WHERE id = ?";
	$q = $pdo->prepare($sql);
	$q->execute(array($id));
	Database::disconnect();
	
} 
else { // otherwise, pre-populate fields to show data to be deleted
	$pdo = Database::connect();
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$sql = "SELECT * FROM fr_events WHERE id = ?";
	$q = $pdo->prepare($sql);
	$q->execute(array($id));
	$data = $q->fetch(PDO::FETCH_ASSOC);
	Database::disconnect();
}
?>

			<!-- Display same information as in file: fr_event_read.php -->
			
			<div class="form-horizontal" >
			
				<div class="control-group">
					<label class="control-label">Date</label>
					<div class="controls">
						<label class="checkbox">
							<?php echo Functions::dayMonthDate($data['event_date']);?>
						</label>
					</div>
				</div>
				
				<div class="control-group">
					<label class="control-label">Time</label>
					<div class="controls">
						<label class="checkbox">
							<?php echo Functions::timeAmPm($data['event_time']);?>
						</label>
					</div>
				</div>
				
				<div class="control-group">
					<label class="control-label">Location</label>
					<div class="controls">
						<label class="checkbox">
							<?php echo $data['event_location'];?>
						</label>
					</div>
				</div>
				
				<div class="control-group">
					<label class="control-label">Description</label>
					<div class="controls">
						<label class="checkbox">
							<?php echo $data['event_description'];?>
						</label>
					</div>
				</div>
				
			<div class="row">
				<h4>Volunteers Assigned to This Shift</h4>
			</div>
			
			<?php
				$pdo = Database::connect();
				$sql = "SELECT * FROM fr_assignments, fr_persons WHERE assign_per_id = fr_persons.id AND assign_event_id = " . $data['id'] . ' ORDER BY lname ASC, fname ASC';
				$countrows = 0;
				foreach ($pdo->query($sql) as $row) {
					echo $row['lname'] . ', ' . $row['fname'] . ' - ' . $row['mobile'] . '<br />';
					$countrows++;
				}
				if ($countrows == 0) echo 'none.';
			?>
			
			</div> <!-- end div: class="form-horizontal" -->