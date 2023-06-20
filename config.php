<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ecfc6";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Connexion à la base de données réussie";
} catch (PDOException $e) {
    // echo "Erreur de connexion à la base de données : " . $e->getMessage();
}