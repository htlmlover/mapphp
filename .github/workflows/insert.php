<!DOCTYPE html>
<html>
<?php
    $serveur = "...";
    $dbname = "...";
    $user = "...";
    $pass = "...";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Récupération des données du formulaire
        $salle = $_POST["salle"];
        $id = $_POST["id"];
        $latitude = $_POST["latitude"];
        $longitude = $_POST["longitude"];
        $val = $_POST["val"];

        try {
            // Connexion à la base de données
            $dbco = new PDO("mysql:host=$serveur;dbname=$dbname", $user, $pass);
            $dbco->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Insertion des données dans la base
            $sth = $dbco->prepare("
                INSERT INTO mesures(salle, capteur, latitude, longitude, val)
                VALUES(:salle, :id, :latitude, :longitude, :val)");
            $sth->bindParam(':salle', $salle);
            $sth->bindParam(':id', $id);
            $sth->bindParam(':latitude', $latitude);
            $sth->bindParam(':longitude', $longitude);
            $sth->bindParam(':val', $val);
            $sth->execute();

            
        } catch (PDOException $e) {
            echo 'Erreur : ' . $e->getMessage();
        }
    }

    try {
        // Récupération de toutes les mesures stockées
        $dbco = new PDO("mysql:host=$serveur;dbname=$dbname", $user, $pass);
        $dbco->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $reponse = $dbco->query('SELECT latitude, longitude, val FROM mesures');

        // Création d'un tableau JavaScript pour stocker les mesures
        echo '<script>';
        echo 'var mesures = [];';
        while ($donnees = $reponse->fetch()) {
            echo 'mesures.push([' . $donnees['latitude'] . ', ' . $donnees['longitude'] . ', ' . $donnees['val'] . ']);';
        }
        echo '</script>';
    } catch (PDOException $e) {
        echo 'Erreur : ' . $e->getMessage();
    }
    ?>
</html>