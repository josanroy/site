<?php
/*
Listing avec des requêtes Composées

Dans ce code on utilise un requete avec jointure .

*/

$connexion = new PDO("mysql:dbname=catalogue;host=localhost;", "root" , "");// à adapter selon le nom de la base crée
$statement = $connexion->query("SELECT * FROM categorie ORDER BY niveau ASC;");
$categories = $statement->fetchAll();


echo "<table>";
echo "<thead>
      <tr>
      <th>Nom</th>
      <th>Réference</th>
      <th>Prix HT</th>
       <th>Prix TTC</th>
       <th>Catégorie</th> ";
echo "</tr>
      </thead>";
echo "<tbody>";
$statement = $connexion->query("SELECT * FROM produit ;");
$produits = $statement->fetchAll();

     foreach($produits as $produit){
         echo "<tr>
          <td>".$produit["nom"]."</td> 
          <td>".$produit["reference"]."</td> 
          <td>".$produit["prix"]."</td> 
          <td>".$produit["prixht"]."</td> 
          <td>".$produit["id_categorie"]."</td> 
         </tr>";
     }
echo "</tbody>
      </table>";

