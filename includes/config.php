<?php
$servername = "sql208.freehosting.com.ar";
$username = "sifhi_41571160";
$password = "sTULrZFG9Qf4";
$dbname = "sifhi_41571160_membershiphp";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error . ".<br> Please create a database and import the SQL file");
}

// Start Session
session_start();
