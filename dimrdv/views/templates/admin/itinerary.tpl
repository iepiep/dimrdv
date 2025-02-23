{if isset($errors) && $errors|@count > 0}
    <div class="alert alert-danger">
        {foreach from=$errors item=error}
            <p>{$error}</p>
        {/foreach}
    </div>
{else}
    <h2>{$itinOptimise|default:"Itinéraire optimisé"}</h2>

    <!-- Carte Google Maps -->
    <div id="map" style="width: 100%; height: 400px;"></div>

    <!-- Récapitulatif de l'itinéraire -->
    <h3>{$resumeItineraire|default:"Résumé de l'itinéraire"}</h3>
    <ul>
        {foreach from=$itinerary_schedule|default:[] item=appointment}
            <li>
                {$appointment.time} - {$appointment.firstname} {$appointment.lastname} - {$appointment.address}
            </li>
        {/foreach}
    </ul>

    <!-- Inclusion de l'API Google Maps et affichage de l'itinéraire -->
    <script src="https://maps.googleapis.com/maps/api/js?key={$google_maps_api_key}&callback=initMap" async defer></script>
    <script>
        function initMap() {
            var directionsService = new google.maps.DirectionsService();
            var directionsRenderer = new google.maps.DirectionsRenderer();
            var mapOptions = {
                zoom: 10,
                center: {ldelim}lat: 48.0, lng: -0.1{rdelim} // Centre approximatif ; à ajuster en fonction des adresses réelles
            };
            var map = new google.maps.Map(document.getElementById('map'), mapOptions);
            directionsRenderer.setMap(map);

            // Construction du tableau des adresses depuis optimized_route
            var addresses = [];
            {foreach from=$optimized_route item=stop}
                addresses.push("{$stop.full_address|escape:'javascript'}");
            {/foreach}

            if (addresses.length < 2)
                return;

            var origin = addresses[0];
            var destination = addresses[addresses.length - 1];
            var waypts = [];
            // Les arrêts intermédiaires (entre le départ et le retour à la base)
            for (var i = 1; i < addresses.length - 1; i++) {
                waypts.push({
                    location: addresses[i],
                    stopover: true
                });
            }

            var request = {
                origin: origin,
                destination: destination,
                waypoints: waypts,
                travelMode: google.maps.TravelMode.DRIVING
            };

            directionsService.route(request, function (result, status) {
                if (status == google.maps.DirectionsStatus.OK) {
                    directionsRenderer.setDirections(result);
                } else {
                    console.error("{$errorMessage|escape:'javascript'}: " + status);
                }
            });
        }
    </script>
{/if}