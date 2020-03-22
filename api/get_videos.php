<?php
// include database and object files
include_once './config/database.php';
header("Access-Control-Allow-Origin: *");
// get database connection
$database = new Database();
$db = $database->getConnection();
$query = "select * from songs";
$statement = $db->prepare($query);
$statement->execute();

// make it json format
print_r(json_encode($statement->fetchAll()));
?>