<?php
/*
Ce formulaire permet d'enregister un éléve dans la table éléve et sa note correspondant à un choix de matière 
dans la table note , vérifie aussi si le nom prenom de l'éléve existe déja et si c'est le cas vérifie s'il a déjà 
été noté , sinon insère une nouvelle note dans la table note .
(on admet pour cet exemple qu'on donne des notes uniques par matière )

Dans ce code on utilise la méthode  prepare() aux lignes suivantes : 20 , 30 , 40 , 50 , 60
pour envoyer des requêtes dites préparées afin d'éviter des attaques de type injection de code SQL .
En effet , la PDO introduite entre PHP 4.9 et PHP 5 améliorée a permis de simplifier 
la tache des développeurs pour sécuriser l'envoie , la récupération , la modification des données .

Pour approfondir le sujet : 
injection sql définition anciennes façons de prévenir PHP 4 : https://openclassrooms.com/courses/eviter-les-injections-sql
injection sql et la pdo nouvelles façons d'envoyer des requête PHP 5: http://jbvigneron.fr/php-pdo-et-les-injections-sql/

*/






$connexion = new PDO("mysql:dbname=base1;host=localhost", "root" , "");// à adapter selon le nom de la base crée
$statement = $connexion->query("SELECT * FROM matiere;");
$matieres = $statement->fetchAll();

if (isset($_POST["submit"])){
	// Post validation des données 
	if(!empty($_POST["nom"]) && !empty($_POST["prenom"]) && !empty($_POST["note"]) && $_POST["note"] <= 20){
		// 1 ére requete : verifie si le nom et le prenom exite déja dans la table eleve
		$query = "SELECT * FROM eleve where nom=LOWER(:nom) and prenom=LOWER(:prenom);";// lower fonction sql qui met les caractére en miniscule
		$statement = $connexion->prepare($query);
		$statement->bindParam(":nom",utf8_decode($_POST["nom"])); // Liaison param :nom , et $_POST["nom"]
		$statement->bindParam(":prenom",utf8_decode($_POST["prenom"])); // Liaison param :prenom , et $_POST["prenom"]
        $statement->execute(); // Execution de la requête préparée 
        $result = $statement->fetch(); // Methode fetch de l'objet statement 

        // Si le nom , prenom n'existe pas on insère un nouvel éléve
        if (empty($result)){ 
        	
			$query = "INSERT INTO eleve (id_eleve,nom,prenom) values('',LOWER(:nom),LOWER(:prenom));";
			$statement = $connexion->prepare($query);
			$statement->bindParam(":nom",utf8_decode($_POST["nom"]));
			$statement->bindParam(":prenom",utf8_decode($_POST["prenom"]));
        	$statement->execute();
        	$last_id = $connexion->lastInsertId();

            /* On insére la note de l'éléve -récemment ajouté à la table éléve ,
            $last_id est sont id correspondante- dans la matière correspondante */

       	    $query = "INSERT INTO note (id_note,id_eleve,id_matiere,note) values('',:id_eleve,:id_matiere,:note);";
			$statement = $connexion->prepare($query);
			$statement->bindParam(":id_eleve",$last_id);
			$statement->bindParam(":id_matiere",$_POST["id_matiere"]);
			$statement->bindParam(":note",floatval($_POST["note"]));
       		$statement->execute();
       		echo "L'éléve ".$_POST["nom"].",".$_POST["prenom"]." a été ajouté et noté";
        }else{
        	// on récuppere l'id de l'éléve existant dans la table eleve resultat de la requête à la ligne 9
        	$id = $result["id_eleve"];
        	// on vérifie s'il a déjà été noté sur la matiere choisie
            $query = "SELECT * from note where id_eleve=:id_eleve and id_matiere=:id_matiere;";
            $statement = $connexion->prepare($query);
            $statement->bindParam(":id_eleve",$id);
			$statement->bindParam(":id_matiere",$_POST["id_matiere"]);
        	$statement->execute();
            $result1 = $statement->fetch();

            if(empty($result1)){
            	// Si le resultat de la requête à la ligne 39 est vide on insère une nouvelle note de l'éléve existant
	        	$query = "INSERT INTO note (id_note,id_eleve,id_matiere,note) values('',:id_eleve,:id_matiere,:note);";
				$statement = $connexion->prepare($query);
				$statement->bindParam(":id_eleve",$id);
				$statement->bindParam(":id_matiere",$_POST["id_matiere"]);
				$statement->bindParam(":note",floatval($_POST["note"]));
        		$statement->execute();
        		echo "L'éléve ".utf8_encode($result["nom"]).",".utf8_encode($result["prenom"])."  a été noté";
            }else{
            	echo "L'éléve ".utf8_encode($result["nom"]).",".utf8_encode($result["prenom"])."  a déjà été noté";
            }
        } 
	}else{
		echo "le formulaire comporte des erreurs";
	}
}
?>
<form action="formulaire.php" method="POST">
	<label>Nom de l'éléve :</label>
	<input type="text" name="nom">
	<br/>
	<label>Prénom de l'éléve :</label>
	<input type="text" name="prenom">
	<br/>
	<label> Matière :</label>
	<select name="id_matiere">
		<?php 
		// Réccuperation des matiéres dans la tables matiéres
         foreach($matieres as $matiere){
         	echo "<option value='".$matiere["id_matiere"]."'>".utf8_encode($matiere["sujet"])."</option>";
         }
		?>
	</select>
	<br/>
	<label>Note :</label>
    <input type="text" name="note">
    <br/>
    <input type="submit" name="submit" value="ok">
</form>

<ul>
<li><a href="listing1.php">Listing des eleves matieres et notes version de code 1 :</a></li>
<li><a href="listing2.php">Listing des eleves matieres et notes version de code 2 :</a></li>
</ul>
<?php
// Unique manière pour détruire l'objet de connexion PDO
$connexion = null;
 ?>