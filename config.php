<?php

if(session_status() == PHP_SESSION_NONE){
    session_start();
}


//connexion l database
$db_host = 'localhost';  //server
$db_name = 'gestion_budget'; //database name
$db_username = 'root'; // username f xampp
$db_password = '';

try{

    $pdo = new PDO("mysql:localhost=$db_host;dbname=$db_name",$db_username,$db_password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

}catch(PDOException $ex){
    die("Database Connection Probleme : " . $ex->getMessage());
}
?>