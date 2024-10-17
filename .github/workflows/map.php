<?php
session_start();

// Vérifier si l'utilisateur est connecté et est un administrateur
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true ) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>CIEL 2023</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css"/>
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
  
</head>
<body>
    <br><br>
    <h1>Formulaire de saisie</h1>
    

</video>
<div class="box">
    <div>
    <form method="post">
        
        <label for="nom">Nom de la salle :</label>
        <br>
        <input type="text" id="salle" name="salle" size="25">
        <br><br>
        <label for="age">ID du capteur (1-100) :</label>
        <input type="number" id="id" name="id" min="1" max="100">
        <br>
        <h2>Localisation</h2>
        <label for="latitude">Latitude :</label>
        <br>
        <input type="number" id="latitude" name="latitude" step="0.00000001">
        <br><br>
        <label for="longitude">Longitude :</label>
        <br>
        <input type="number" id="longitude" name="longitude" step="0.000000001">
        <br><br>
        <button type="button" onclick="getLocation()">localisation auto</button>
        <br><br>
        <label for="val">Valeur de mesure :</label>
        <input type="number" id="val" name="val" min="-50" max="50" step="0.0001">
        <br><br>
        <input type="submit" id="envoyer" name="envoyer" value="Valider">
    </form>
    </div>

    <div id="mapid" style="width: 600px; height: 400px;"></div>
   
    </div>

    <?php
    
    $serveur = "mysql-nolannlev.alwaysdata.net";
    $dbname = "nolannlev_mesures";
    $user = "nolannlev_1";
    $pass = "tpphpmdp";

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

<script>
       let map = L.map('mapid').setView([48.852969, 2.349903], 13);
       let markers = [];

       L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
           attribution: '© OpenStreetMap contributors'
       }).addTo(map);

       let bounds = L.latLngBounds();

       for (var i = 0; i < mesures.length; i++) {
           let lat = mesures[i][0];
           let lon = mesures[i][1];
           let mesure = mesures[i][2];

           let marker = L.marker([lat, lon]).addTo(map);
           map.setView([lat, lon], 13);
           markers.push(marker);
           

           (function(marker, lat, lon) {
               fetch(`https://api.openweathermap.org/data/2.5/weather?lat=${lat}&lon=${lon}&appid=05e2c231fecfa13051d9e51caa9fff15`)
                   .then(response => response.json())
                   .then(data => {
                       const tempCelsius = (data.main.temp - 273.15).toFixed(2);
                       const description = data.weather[0].description; 
                       const humidity = data.main.humidity;
                       const winSpeed = data.wind.speed; 
                       const name = data.name;

                       marker.bindPopup('Ville: '+ name +'<br>Température actuelle: ' + tempCelsius + ' °C<br>Température mesurée: ' + mesure + ' °C<br>Description: ' + description + '<br>Humidité: ' + humidity + ' %<br>Vitesse du vent: ' + winSpeed + ' m/s').openPopup();
                   })
                   .catch(error => console.error('Erreur:', error));
           })(marker, lat, lon);

           bounds.extend([lat, lon]);
       }

       if (mesures.length > 0) {
           map.fitBounds(bounds);
       }

       function getLocation() {
           if (navigator.geolocation) {
               navigator.geolocation.getCurrentPosition(showPosition);
           } else {
               alert("La géolocalisation n'est pas supportée par ce navigateur.");
           }
       }

       function showPosition(position) {
           document.getElementById("latitude").value = position.coords.latitude;
           document.getElementById("longitude").value = position.coords.longitude;
       }
   </script>
    
<footer>
    <img src="https://www.st-joseph-lorient.org/wp-content/uploads/2020/10/logo-1.png" alt="logo">
    </footer>
</body>
</html>
