<?php
require_once('utils.php');
session_start();
if (empty($_POST["comment"])) { /*check if comment field has stuff*/
    echo "prkl";
}

else {
    try {
        $db = new PDO("mysql:host=127.0.0.1;dbname=c9;charset=utf8;port:3306", "odango", ""); /*connect to database*/
        $comment = $_POST["comment"];
        $date = date("Y-m-d H:i:s");
        if(isset($_SESSION["username"])) {
            $username = $_SESSION["username"]; /*if user has logged in*/
        }
        else {
            $username = "Anonymous"; /*if user has not logged in*/
        }
        $stmt = $db->prepare("INSERT INTO comments(username, comment, date) VALUES(:f1, :f2, :f3)");
        $stmt->execute(array(":f1" => $username, ":f2" => $comment, ":f3" => $date));
    }
    
    catch(Exception $e) {
        echo $e;
        return false;
    }
    
}