<?php

/*Gets player information from session variable and returns it*/

    require_once('utils.php');
    session_start();
    if (isset($_GET['requested'])) {
        /*return requested value*/
        if(isset($_SESSION["username"])) { /*if user has logged in*/
            print $_SESSION[$_GET['requested']];
        }
        
        else {
        /*return default name*/
            print "Anonymous";
    }
    } 
    
?>