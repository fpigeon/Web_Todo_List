<?php
//variables 
$heading = ['ID', 'name', 'location', 'date established', 'area in acres', 'description' ];
$isValid = false; //form validation
$error_msg=''; //initailize variable to hold error messages

// Get new instance of PDO object
$dbc = new PDO('mysql:host=127.0.0.1;dbname=codeup_todo_db', 'frank', 'password');

// Tell PDO to throw exceptions on error
$dbc->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

echo $dbc->getAttribute(PDO::ATTR_CONNECTION_STATUS) . "\n";

// Create the query and assign to var
$query = 'CREATE TABLE todos (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    task VARCHAR(50) NOT NULL,    
    PRIMARY KEY (id)
)';
// Run query, if there are errors they will be thrown as PDOExceptions
$dbc->exec($query);