<?php

$serveur = "172.187.184.132";
$utilisateur = "admin";
$motDePasse = "PWD";
$nomBaseDeDonnees = "Football";


try {
    $base = new PDO("mysql:host=$serveur;dbname=$nomBaseDeDonnees", $utilisateur, $motDePasse);
} catch (PDOException $e) {
    echo "Erreur de connexion à la base de données : " . $e->getMessage();
}


$connexion = new mysqli($serveur, $utilisateur, $motDePasse, $nomBaseDeDonnees);

if ($connexion->connect_error) {
    die("Erreur de connexion MySQLi : " . $connexion->connect_error);
} else {
}

?>
