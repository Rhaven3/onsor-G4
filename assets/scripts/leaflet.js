// Code de LeafLet -- Normalement n'a pas besoin d'être touché.

var map = L.map('map').setView([48.038905, -1.692377], 17);

L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
}).addTo(map);


var marker = L.marker();

// -- Fin code Leaflet

let latitudeInput = document.getElementById('trip_newAddress_latitude');
let longitudeInput = document.getElementById('trip_newAddress_longitude');
let addressSelected = document.getElementById('trip_address');

// -- Événements --

map.on('click', onMapClick);
latitudeInput.addEventListener('blur', (event) => markerWithInput(latitudeInput.value, longitudeInput.value));
longitudeInput.addEventListener('blur', (event) => markerWithInput(latitudeInput.value, longitudeInput.value));
addressSelected.addEventListener('change', (event) => {
    const option = addressSelected.options[addressSelected.selectedIndex];

    const lat = option.dataset.lat;
    const long = option.dataset.lng;

    markerWithInput(lat, long);
});

// -- Fonctions --

/**
 * Methode permettant de vérifier si lat et long sont remplis et si c'est le cas, déplacer le marqueur et la vision vers ce dernier
 * @param lat latitude de l'input
 * @param long longitude de l'input
 */
function markerWithInput(lat, long) {
    lat = parseFloat(lat);
    long = parseFloat(long);

    console.log(lat, long);
    console.log("Type de la variable latitude : " + typeof lat);
    console.log("Type de la variable longitude : " + typeof long);
    if ((lat !== null && typeof lat === "number") && (long !== null && typeof long === "number")) {
        replaceMarker(lat, long);
        map.flyTo([lat, long]);
    }
}

/**
 * Methode qui place le marqueur sur la map et rempli les inputs du formulaire.
 * @param e
 */
function onMapClick(e) {
    let latLng = clearlatLngString(e.latlng.toString());

    let latitude = parseFloat(latLng[0]);
    let longitude = parseFloat(latLng[1]);

    replaceMarker(latitude, longitude);

    latitudeInput.value = latitude;
    longitudeInput.value = longitude;
}

/**
 * Methode permettant de supprimer le marker précédemment apposé sur la carte et de le remplacer par les nouvelles coordonnées lat et lng
 * @param lat latitude
 * @param lng longitude
 */
function replaceMarker(lat, lng) {
    marker.remove();
    marker = L.marker([lat, lng]);
    marker.addTo(map);
}


/**
 * Methode permettant de changer le string renvoyé de base par e.latlng.toString() => "LatLng(latitude, longitude)" en tableau contenant [latitude, longitude]
 * @param latLngString fonction toString() de latlng
 * @returns un tableau [latitude, longitude]. Exemple : [-41.054212, 5.54548]
 */
function clearlatLngString(latLngString) {
    latLngString = latLngString.replace("LatLng(", "");
    latLngString = latLngString.replace(")", "");
    let latLng = latLngString.split(",");
    latLng[1] = latLng[1].trim();
    return latLng;
}



