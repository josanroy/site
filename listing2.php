<?php
/*
Listing avec des requêtes simples


*/

$connexion = new PDO("mysql:dbname=base1;host=localhost", "root" , "");// à adapter selon le nom de la base crée
$statement = $connexion->query("SELECT * FROM matiere;");
$matieres = $statement->fetchAll();
