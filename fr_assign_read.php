<?php 
/* ---------------------------------------------------------------------------
 * filename    : fr_assign_read.php
 * author      : George Corser, gcorser@gmail.com
 * description : This program displays one assignment's details (table: fr_assignments)
 * ---------------------------------------------------------------------------
 */
session_start();
if(!isset($_SESSION["fr_person_id"])){ // if "user" not set,
	session_destroy();
	exit;
}

require 'database.php';
require 'functions.php';

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
?>

    <div class="container">
    
		<div class="span10 offset1">
		
			<div class="row">
				<h3>Assignment Details</h3>
			</div>
			
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
				
				<div class="form-actions">
					<a class="btn" href="fr_assignments.html">Back</a>
				</div>
			
			</div> <!-- end div: class="form-horizontal" -->
			
		</div> <!-- end div: class="span10 offset1" -->
				
    </div> <!-- end div: class="container" -->