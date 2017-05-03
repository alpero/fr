<?php
    session_start();
    if ($_SESSION['fr_person_id'] !== NULL)
        print "active";
    else
        print "expired";
?>