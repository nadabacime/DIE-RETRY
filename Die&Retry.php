<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">

	<title>DIE & RETRY</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
	<link rel="stylesheet" type="text/css" href="again.css">
</head>
<body>
	<!-- ------------------------------------------PHP SQL CONNECTION------------------------------------------- -->
	<?php
		try {
	//// On se connecte à MySQL
			$bdd = new PDO('mysql:host=localhost;dbname=videogames;charset=utf8', 'root', 'root');
			$bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}
	// En cas d'erreur, on affiche un message et on arrête tout
		catch (Exception $e) {
			die('Erreur : ' .$e->getMessage());
	} // Si tout va bien, on peut continuer
	// On récupère tout de la table videogames en la liant avec developers, plateform & publishers
	$Query = $bdd->query('SELECT videogames.id as "id", Title, ReleaseDate, developers.name as "devs", platform.name as "platf", publishers.name as "edits"
		FROM videogames
		INNER JOIN developers ON developers.id=videogames.idDeveloper
		INNER JOIN platform ON platform.id=videogames.idPlatform
		INNER JOIN publishers ON publishers.id=videogames.idPublisher
		ORDER BY videogames.id');
	?>
	<!-------------------------------DEBUT HTML --------------------------------- -->
	<header class="container-fluid">
		<div id="logocenter">
			<a href=""><img src="007.png"></a>
		</div>
	</header>
	<section class="container-fluid">
		<div class="container">
			<!---------------------------------- ADD GAME ---------------------------------- -->
			<form method="POST">
				<input id="btnAdd" type="submit" name="btnAdd" value="Ajouter un jeu"/>
			</form>
			<?php
			if(isset($_POST['btnAdd'])){
				?>
				<div class="popUpAdd">
					<h2 class="text-center">Ajouter un jeu: </h2 class="text-center">
						<form action="" method="POST">
							<h6>Titre :</h6>
							<input id="title" type="text" name="title" placeholder=" Titre">
							<h6>Date de sortie :</h6><p class="text-muted mb-0">Ex: 25 July 1997</p>
							<input id="date" type="text" name="date" placeholder=" 25 July 1997">
							<h6>Plateforme :</h6>
							<select name="SelectPlatform">
								<option value="">Selectionner</option>
								<?php
								$platformQuery = $bdd->query('SELECT * FROM platform');
								while ($GetPlatform = $platformQuery->fetch()) {
									echo "<option value='" .$GetPlatform['id'] ."'>" .$GetPlatform['name'] ." </option>";	
								}
								?>
								<input id="plat" type="text" name="Plat" placeholder=" Autre">
							</select>
							<h6>Développeur :</h6>
							<select name="SelectDev">
								<option value="">Selectionner</option>
								<?php
								$DevQuery = $bdd->query('SELECT * FROM developers');
								while ($GetDev = $DevQuery->fetch()) {
									echo "<option value='" .$GetDev['id'] ."'>" .$GetDev['name'] ." </option>";					
								}
								?>
								<input id="dev" type="text" name="dev" placeholder=" Autre">
							</select>
							<h6>Editeur :</h6>
							<select name="SelectPub">
								<option value="">Selectionner</option>
								<?php
								$PublisherQuery = $bdd->query('SELECT * FROM publishers');
								while ($GetPubl = $PublisherQuery->fetch()) {
									echo "<option value='" .$GetPubl['id'] ."'>" .$GetPubl['name'] ." </option>";					
								}
								?>
								<input id="edit" type="text" name="edit" placeholder=" Autre">
							</select>
							<input id="button" type="submit" name="addConfirm" value="Valider">
							<a href=""><input type="button" id="annuler" value="Annuler"></a>
						</form>
					</div>
					<?php
				};
				if(isset($_POST['addConfirm'])){
					//Récupération de tous les champs
					$AddTitle = htmlspecialchars($_POST['title']);
					$AddDate = htmlspecialchars($_POST['date']);
					$AddPlatform = htmlspecialchars($_POST['SelectPlatform']);
					$Adddev = htmlspecialchars($_POST['SelectDev']);
					$AddPublisher = htmlspecialchars($_POST['SelectPub']);
					//Condition to make sure every input is filled
					if ($AddTitle!= "" && $AddDate!= "" && $AddPlatform!= "" && $Adddev!= "" && $AddPublisher!= "") {
					//Insert new row in table
						try{
							$AddGame = "INSERT INTO videogames (Title, ReleaseDate, idPlatform, idDeveloper, idPublisher) VALUES ('$AddTitle', '$AddDate', '$AddPlatform', '$Adddev', '$AddPublisher')"; 
							$bdd->exec($AddGame);
					//Display alert to confirm the new row in table
							echo '<SCRIPT language="Javascript">alert("Le jeu " + \''.$AddTitle.'\' + " a bien été rajouté !");</SCRIPT>';
					//Display error if row wasn't added
						} catch(PDOException $e){
							die("ERROR: Could not able to execute $AddGame. " . $e->getMessage());
						}
					//Display alert if not all inputs are filled
					} else {
						echo '<SCRIPT language="Javascript">alert("Rempli bien tous les champs !");</SCRIPT>';
					} 
				};
					?>
				<!-------------------------------------------- END OF ADD GAME --------------------------------------------------- -->
				<!-- ------------ Searchbar ------------------ -->
				<form action="" method="GET">
					<input id="search" type="search" name="search" placeholder="Chercher un titre..." >
					<input id="button" type="submit" value="Rechercher">
				</form>
				<!-- ------------ Table (games) ------------------ -->
				<div id="height">
					<table class="table table-bordered table-hover table-striped table-responsive">
						<thead>
							<tr>
								<th scope="col">Id</th>
								<th scope="col">Titre</th>
								<th class="cols" scope="col">Date de sortie</th>
								<th class="cols" scope="col">Plateforme</th>
								<th class="cols" scope="col">Développeur</th>
								<th class="cols" scope="col">Publisher</th>
								<th id="modifyHead" scope="col">Modifier</th>
								<th id="deleteHead" scope="col">Supprimer</th>
							</tr>
						</thead>
						<tbody>
				<?php
// -----------------------------------BAR DE RECHERCHE------------------------------------------
							//récupération mot dans bar de recherche et comparaison avec BDD
							if (isset($_GET['search']) AND !empty($_GET['search'])) {
								$search = htmlspecialchars($_GET['search']);
								// $search = strip_tags($search);
								// $search = explode(" ", $search);
								$Query = $bdd->query('SELECT videogames.id as "id", Title, ReleaseDate, developers.name as "devs", platform.name as "platf", publishers.name as "edits"
									FROM videogames
									INNER JOIN developers ON videogames.idDeveloper=developers.id
									INNER JOIN platform ON videogames.idPlatform=platform.id
									INNER JOIN publishers ON videogames.idPublisher=publishers.id 
									WHERE Title LIKE "%'.$search.'%"
									ORDER BY videogames.id'); 
							};
							//condition rajoutée : s'il y a minimim 1 ligne de trouvée l'afficher sinon afficher 'aucun résultat' (voir plus bas)
							if ($Query -> rowCount() > 0) {
								// On affiche chaque entrée une à une
								while ($game = $Query->fetch()) {
									echo "<form method='POST'>";
									echo "<tr class='text-center'>";
									echo "<th scope='row'>" .$game['id'] ." </th>";
									echo "<td class='text-left'>" .$game['Title'] ." </td>";
									echo "<td class='cols'>" .$game['ReleaseDate'] ." </td>";
									echo "<td class='cols'>" .$game['platf'] ." </td>";
									echo "<td class='cols'>" .$game['devs'] ." </td>";
									echo "<td class='cols'>" .$game['edits'] ." </td>";
									echo "<td>" ."<input class='update' type='submit' name='update' value='✎'>" ."</td>";
									echo "<td>" ."<input class='delete' type='submit' name='delete' value='X'>" ."</td>";
									//next, add input with type=hidden to get clicked row's id with $_POST[hidden]
									echo "<input type='hidden' name='hidden' value=" .$game['id'] .">";
									echo "</tr>";
									echo "</form>";
								}
							} else {
								echo '<p id="noresult">' .'Aucun résultat pour: "' .$search .'"' .'</p>';
							};
	//----------------------------------------------- UPDATE GAME ------------------------------------------ -->
			$idSelected = isset($_POST['hidden']) ? $_POST['hidden'] :''; //to make sure the form is submitted before using POST values
			if(isset($_POST['update'])){
				echo '<div class="popUpAdd">';
				echo '<h2 class="text-center">Modifier le jeu: </h2>';
				echo "<form method='POST'>";
				$Display = $bdd->prepare('SELECT  videogames.id as "id", Title, ReleaseDate, developers.name as "devs", platform.name as "platf", publishers.name as "edits"
					FROM videogames
					INNER JOIN developers ON developers.id=videogames.idDeveloper
					INNER JOIN platform ON platform.id=videogames.idPlatform
					INNER JOIN publishers ON publishers.id=videogames.idPublisher
					WHERE videogames.id=:id');
				$Display->bindParam(":id", $idSelected);
				$Display->execute();

				while ($DisplayUpdate = $Display->fetch()) {
					?>
					<h3 class="mb-4">Id : <span class="text-danger"><?= $DisplayUpdate['id']; ?></span></h3>
					<!-- <input type="text" name="id" value="<?= $DisplayUpdate['id']; ?>"/> -->
					<h3>Titre :</h3>
					<input type="text" name="UpdateTitle" value="<?= $DisplayUpdate['Title']; ?>"/>
					<h3>Date de sortie :</h3>
					<input type="text" name="UpdateDate" value="<?= $DisplayUpdate['ReleaseDate']; ?>"/>
					<h3>Plateforme : <span class="text-muted h4"><?= $DisplayUpdate['platf']; ?></span></h3>
					<select class="mb-3" name='UpdatePlatform'>
						<option value=''>Sélectionner</option>
						<?php
						$platformModify = $bdd->query('SELECT * FROM platform');
						while ($NewPlatform = $platformModify->fetch()) {
							echo "<option value='" .$NewPlatform['id'] ."'>" .$NewPlatform['name'] ." </option>";	
						}
						?>
						<input type="text" name="OtherPlatform" placeholder="Autre plateforme"/>
						<h3>Développeur : <span class="text-muted h4"><?= $DisplayUpdate['devs']; ?></span></h3>
						<select class="mb-3" name="UpdateDev">
							<option value="">Selectionner</option>
							<?php
							$DevUpdate = $bdd->query('SELECT * FROM developers');
							while ($UpdateDev = $DevUpdate->fetch()) {
								echo "<option value='" .$UpdateDev['id'] ."'>" .$UpdateDev['name'] ." </option>";					
							}
							?>
							<input type="text" name="OtherDev" placeholder="Autre développeur"/>
							<h3>Editeur : <span class="text-muted h4"><?= $DisplayUpdate['edits']; ?></span></h3>
							<select class="mb-3" name="UpdatePub">
								<option value="">Selectionner</option>
								<?php
								$PublisherUpdate = $bdd->query('SELECT * FROM publishers');
								while ($UpdatePubl = $PublisherUpdate->fetch()) {
									echo "<option value='" .$UpdatePubl['id'] ."'>" .$UpdatePubl['name'] ." </option>";					
								}
								?>
								<input type="text" name="OtherPub" placeholder="Autre éditeur"/>

								<!-- <input type="text" name="OtherPub" value="<?= $DisplayUpdate['edits']; ?>"/> -->
								<?php
							};
							echo "<input type='hidden' name='hidden' value=" .$idSelected . " >";
							echo '<input id="button" type="submit" name="UpdateBtn" value="Modifier">';
							echo"<a href=''><input type='button' id='annuler' value='Annuler'></a>";
							echo "</form>";
							echo "</div>";
						};
						if (isset($_POST['UpdateBtn'])) {
							$UpdateTitle = htmlspecialchars($_POST['UpdateTitle']);
							$UpdateDate = htmlspecialchars($_POST['UpdateDate']);

							$Update = "UPDATE videogames SET Title='$UpdateTitle', ReleaseDate='$UpdateDate', idPlatform='$_POST[UpdatePlatform]', idDeveloper='$_POST[UpdateDev]', idPublisher='$_POST[UpdatePub]'
							WHERE id='$idSelected'";  
							$bdd->exec($Update);
							echo '<SCRIPT language="Javascript">alert("Le jeu numéro " + \''.$idSelected.'\' + " a bien été mis à jour en "+ \''.$_POST["UpdateTitle"].'\' + " ! ");</SCRIPT>';
						};
//---------------------------------- DELETE GAME ---------------------------------- -->
			if(isset($_POST['delete'])) {
				try{
					$Delete = "DELETE FROM videogames WHERE id='$idSelected'"; 
					$bdd->exec($Delete);
					echo '<SCRIPT language="Javascript">alert("Le jeu numéro " + \''.$idSelected.'\' + " a bien été supprimé ");</SCRIPT>';

				} catch(PDOException $e){
					die("ERROR: Could not able to execute $Delete. " . $e->getMessage());
				}
			};
				$Query->closeCursor(); //Termine le traitement de la requête
			?>
						</tbody>
					</table>
				</div>
		</div>
	</section>
	<footer>
		<p id="copyright">Copyright &copy; 2019 Nada Bacime. Tous droits réservés.</p>
	</footer>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script type="text/javascript" src="project.js"></script>
</body>
</html>