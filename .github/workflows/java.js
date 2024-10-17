
       var map = L.map('mapid').setView([48.852969, 2.349903], 13);
       var markers = [];

       L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
           attribution: '© OpenStreetMap contributors'
       }).addTo(map);

       var bounds = L.latLngBounds();

       for (var i = 0; i < mesures.length; i++) {
           var lat = mesures[i][0];
           var lon = mesures[i][1];
           var mesure = mesures[i][2];

           var marker = L.marker([lat, lon]).addTo(map);
           markers.push(marker);

           (function(marker, lat, lon) {
               fetch(`https://api.openweathermap.org/data/2.5/weather?lat=${lat}&lon=${lon}&appid=05e2c231fecfa13051d9e51caa9fff15`)
                   .then(response => response.json())
                   .then(data => {
                       var tempCelsius = (data.main.temp - 273.15).toFixed(2);
                       var description = data.weather[0].description; 
                       var humidity = data.main.humidity;
                       var winSpeed = data.wind.speed; 
                       var name = data.name;

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