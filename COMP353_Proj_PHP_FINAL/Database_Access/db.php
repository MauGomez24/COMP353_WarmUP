<?php
$host = 'ftc353.encs.concordia.ca';
$db = 'ftc353_1';
$user = 'ftc353_1';
$pass = 'F4nTastK';

try {
  $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  die("Database connection failed: " . $e->getMessage());
}
?>
