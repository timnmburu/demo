Our Location

<div id="map" class="map" style=" z-index: 1; border: 2px solid black; height: 400px; margin-bottom: 75px; width: calc(100vw - 75px);"></div>


<script>
    var map = L.map('map').setView([-1.143577815826183, 36.98822508702845], 18); // Example location coordinates and zoom level
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap contributors',
    maxZoom: 36
    }).addTo(map);
    var marker = L.marker([-1.143577815826183, 36.98822508702845]).addTo(map); // Example location coordinates
    marker.bindPopup("<b> Go to <a href='https://www.google.com/maps?q=-1.143577815826183, 36.98822508702845'>Excel Tech Essentials</a></b><br>Along Matangi Rd, Opp. Deliverance Church Theta ");
    L.Map.addInitHook('addHandler', 'touchGesture', L.Map.TouchGesture);
    map.touchGesture = new L.Map.TouchGesture(map);
    map.addControl(map.touchGesture);
</script>