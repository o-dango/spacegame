

<?php

/*directs user to requested page*/

    session_start();
    require_once("utils.php");

    siteHeader(); /*page header*/

    if ($_GET["p"] === "register") {
        require("register.php");
    }

    else if ($_GET["p"] === "login") {
        require("login.php");
    }

    else if ($_GET["p"] === "logout") {
        require("logout.php");
    }

    else if ($_GET["p"] === "game") {
        require("game.php");
    }

    else if($_GET["p"] === "frontpage") {
        require("frontpage.php");
    }
    
    else {
        require("default.php");
    }
    
    footer(); /*page footer*/

 ?>
