<div class="profile-status-write">
    <div class="status-wrap">
        <div class="status-top-wrap">
            <div class="status-top">
                Create Post
            </div>
        </div>

        <div class="status-med">
            <div class="status-prof">
                <div class="top-pic"><img src="<?php echo $userData->profilePic;?>" alt=""></div>
            </div>
            <div class="status-prof-textarea" style="position:relative;">
                <textarea name="textStatus" id="statusEmoji" cols="5" rows="5" class="status align-middle" placeholder="What's going on your mind?"></textarea>
                <ul class="hash-men-holder" style="position:absolute;margin-top: 0;"></ul>
            </div>
        </div>
        <div class="status-bot">
            <div class="file-upload-remIm input-restore">
                <label for="multiple_files" class="file-upload-label">
                    <div class="status-bot-1">
                        <img src="assets/image/photo.JPG" alt="">
                        <div class="status-bot-text">Photo/Video</div>
                    </div>
                </label>
                <input type="file" name="post-file-upload" id="multiple_files" class="file-upload-input postImage" data-multiple-caption="{count} files selected" multiple="">
            </div>
            <div class="status-bot">
                <div class="status-bot-1">
                    <img src="assets/image/privacy.jpg" alt="">
                    <div class="status-bot-text" onclick="openLocationModal()">Localizacion</div>
                </div>
            </div>
            <div id="preview" class="status-preview" style="display: none;">
            <p id="previewLocation"></p>
        </div>
        </div>
        <ul id="sortable" style="position:relative;">

        </ul>
        <div id="error_multiple_files"></div>
        <div class="status-share-button-wrap">
            <div class="status-share-button">
                <div class="status-privacy-wrap">

                </div>
            </div>
            <div class="seemore-sharebutton">
                <div class="status-share-button align-middle">
                    Share
                </div>
            </div>
        </div>
    </div>
</div>

<style>
  #map {
    width: 400px; /* ancho del mapa */
    height: 300px; /* alto del mapa */
  }
  html, body {
    height: 100%;
    margin: 0;
    padding: 0;
  }
    /* Estilo para el modal */
.modal {
    display: none;
    position: fixed;
    z-index: 1;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0,0,0,0.4);
}

/* Contenido del modal */
.modal-content {
    background-color: #fefefe;
    margin: 15% auto;
    padding: 20px;
    border: 1px solid #888;
    width: 80%;
}

/* Botón para cerrar el modal */
.close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
}

.close:hover,
.close:focus {
    color: black;
    text-decoration: none;
    cursor: pointer;
}
input{
    width: 100%;
    height: 40px;
    border-radius: 0;
    border: none;
    font-size: 20px;
}
</style>
<!-- Modal para la ubicación -->
<div id="locationModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeLocationModal()">&times;</span>
        <input type="text" id="place_input" placeholder="Ingresa una ubicacion"/>
        <div id="map" style="width: 100%; height: 400px;"></div>
        <button onclick="saveLocation()">Guardar Ubicación</button>
    </div>
</div>

<!-- Código de Google Maps -->
<script async src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA_XA230BKT0TSYn81wB5wNzlli7j4lvTQ&libraries=places&callback=initMap"></script>

<script>
const argCoords = {lat: 4.610, lng: -74.082};
const mapDiv = document.getElementById("map");
const input = document.getElementById("place_input");
let map;
let marker;
let autocomplete;

// Función para abrir el modal de ubicación
function openLocationModal() {
    document.getElementById("locationModal").style.display = "block";
    initMap(); // Inicializa el mapa cuando se abre el modal
}

// Función para cerrar el modal de ubicación
function closeLocationModal() {
    document.getElementById("locationModal").style.display = "none";
}

// Función para inicializar el mapa
function initMap() {
    map = new google.maps.Map(mapDiv,{
        center: argCoords,
        zoom: 5,
    });
    marker = new google.maps.Marker({
    position: argCoords,
    map: map,
    });
    initAutocomplete();    
}

function initAutocomplete(){
    autocomplete = new google.maps.places.Autocomplete(input);
    autocomplete.addListener('place_changed', function(){
        const place = autocomplete.getPlace();
        if (!place.geometry) {
            console.error("El lugar seleccionado no tiene una geometría");
            return;
        }
        map.setCenter(place.geometry.location);
        map.setZoom(15);
        marker.setPosition(place.geometry.location);
    });
}

// Función para guardar la ubicación
// Función para guardar la ubicación
function saveLocation() {
    // Aquí puedes agregar la lógica para guardar la ubicación seleccionada
    const place = autocomplete.getPlace();
    if (!place.geometry) {
        console.error("El lugar seleccionado no tiene una geometría");
        return;
    }

    let locationName = place.name;
    document.getElementById("previewLocation").innerText = locationName;
    document.getElementById("preview").style.display = "block"; // Mostrar la vista previa
    
    // Actualizar las coordenadas en un campo oculto
    document.getElementById("locationCoordinates").value = place.geometry.location.lat().toFixed(6) + ", " + place.geometry.location.lng().toFixed(6);

    // Cerrar la modal del mapa
    closeLocationModal();
}

</script>

<div class="status-preview" id="preview" style="display: none;">
    <p id="previewLocation"></p>
</div>

<input type="hidden" name="locationCoordinates" id="locationCoordinates">
