<?php 
    require 'database.php';

    $personsTable = "
    CREATE TABLE IF NOT EXISTS `fr_persons` ( 
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `fname` varchar(20) NOT NULL,
    `lname` varchar(20) NOT NULL,
    `email` varchar(20) NOT NULL,
    `mobile` varchar(20) NOT NULL,
    `password` varchar(20) NOT NULL,
    `title` varchar(20) NOT NULL,
    `filename` varchar(20),
    `filesize` int(20),
    `filetype` varchar(20),
    `filecontent` varchar(20),
    PRIMARY KEY (`id`))";

    $eventsTable = "
    CREATE TABLE IF NOT EXISTS `fr_events` ( 
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `event_date` DATE,
    `event_time` TIME, 
    `event_location` varchar(255),
    `event_description` varchar(255),
    PRIMARY KEY (`id`))";

    $assignmentsTable = "
    CREATE TABLE IF NOT EXISTS `fr_assignments` ( 
    `assign_per_id` int(11) NOT NULL,
    `assign_event_id` int(11) NOT NULL,
    PRIMARY KEY (assign_per_id, assign_event_id),
    FOREIGN KEY (assign_per_id) REFERENCES fr_persons(id),
    FOREIGN KEY (assign_event_id) REFERENCES fr_events(id))";

    $constraint1 = "
    ALTER TABLE `fr_assignments` 
    ADD CONSTRAINT `fk_assignments_per`
    FOREIGN KEY (`assign_per_id`)
    REFERENCES `fr_persons` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE";

    $constraint2 = "
    ALTER TABLE `fr_assignments` 
    ADD CONSTRAINT `fk_assignments_evn`
    FOREIGN KEY (`assign_event_id`)
    REFERENCES `fr_events` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE";

    $pdo = Database::connect();
    $pdo->query($personsTable);
    $pdo->query($eventsTable);
    $pdo->query($assignmentsTable);
    $pdo->query($constraint1);
    $pdo->query($constraint2);
    Database::disconnect();
    
    echo "done";
?>