<?php
require_once('utils.php');
session_start();
if (empty($_POST["player"])) { /*check if comment field has stuff*/
    echo "prkl";
}

else {
    try {
        /*connect to database*/
        $db = new PDO("mysql:host=127.0.0.1;dbname=c9;charset=utf8;port:3306", "odango", ""); 
        $username = $_POST["player"];
        if(empty($_SESSION)) {
            $usernane = $username . " (Visitor)";
        }
        $score = $_POST["score"];
        $date = date("Y-m-d H:i:s");
        /*prepare statement*/
        $stmt = $db->prepare("INSERT INTO highscores(username, score, date) VALUES(:f1, :f2, :f3)");
        $stmt->execute(array(":f1" => $username, ":f2" => $score, ":f3" => $date));
    }
    catch(Exception $e) {
        echo $e;
        return false;
    }
}