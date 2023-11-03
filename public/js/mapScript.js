class Map
{
    constructor()
    {
        this.map = L.map('map').setView([47.081012, 2.398782], 6);
        this.markerClusterGroup = new L.MarkerClusterGroup({
            maxClusterRadius: 30,
            spiderfyOnMaxZoom: true,
            showCoverageOnHover: false,
            zoomToBoundsOnClick: true,
            removeOutsideVisibleBounds: true
        });
        this.myAPIKey = "6c5dd3e7389140018457150f3a20be4b";
        this.isRetina = L.Browser.retina;
        this.baseUrl = "https://maps.geoapify.com/v1/tile/toner/{z}/{x}/{y}.png?apiKey=6c5dd3e7389140018457150f3a20be4b";
        this.retinaUrl = "https://maps.geoapify.com/v1/tile/toner/{z}/{x}/{y}@2x.png?apiKey=6c5dd3e7389140018457150f3a20be4b";
        this.icon = L.divIcon({
                className: 'custom-div-icon',
                html: "<div class='marker-pin'></div><i class='fa-solid fa-dice-six awesome'>",
                iconSize: [30, 42],
                iconAnchor: [15, 42],
                popupAnchor: [5, -35]
        });
        this.createMap();
        this.searchMapForm = document.getElementById('searchMapForm');
        this.searchMapForm.addEventListener('submit', (event) => {event.preventDefault(); document.getElementById('modalSearch').style.display = 'block'; document.getElementById('modalError').style.display = 'none'; return this.search()}, false);
        this.closeModalErrorBtn = document.getElementById('closeBtn');
        this.closeModalErrorBtn.addEventListener('click', (event) => {event.preventDefault(); document.getElementById('modalError').style.display = 'none';})
    }

    // Map creation
    createMap()
    {
        L.tileLayer(this.isRetina ? this.retinaUrl : this.baseUrl, {
            attribution: 'Powered by <a href="https://www.geoapify.com/" target="_blank">Geoapify</a> | <a href="https://openmaptiles.org/" rel="nofollow" target="_blank">© OpenMapTiles</a> <a href="https://www.openstreetmap.org/copyright" rel="nofollow" target="_blank">© OpenStreetMap</a> contributors',
            apiKey: this.myAPIKey,
            minZoom: 0,
            maxZoom: 18,
            id: 'toner',
        }).addTo(this.map);
    }

    // Search fonction
    search()
    {
        this.markerArray = [];
        // Setting the storage array for the markers
        this.searchGameId = document.getElementById('searchGame').value;
        this.searchStyle = document.getElementById('searchType').value;
        // Get data from db
        const postDataAsJson = async () => {
            const data = new FormData();
            data.append("gameId", this.searchGameId);
            const fetchOptions = {
                method: "POST",    
                body: data,
            };
            // Call the right route of research
            if (this.searchStyle === 'player') {
                var response = await fetch(Routing.generate('search.player', { id : this.searchGameId }, true), fetchOptions ); // If player is searched
            }
            if (this.searchStyle === 'event') {
                var response = await fetch(Routing.generate('search.event', { id : this.searchGameId }, true), fetchOptions); // If event is searched
            }
            
            if (!response.ok) {
                const errorMessage = await response.text();
                throw new Error(errorMessage);
            }
            return await response.json();
        }
        // Batch geocoding
        postDataAsJson().then(data => {
            // Isolate location
            let location = [];
            for (let i = 0; i < data.length; i++) {
                location.push(data[i].location);
            }
            // Start batch geocoder
            const batchData = location;
            const url = `https://api.geoapify.com/v1/batch/geocode/search?apiKey=6c5dd3e7389140018457150f3a20be4b`;
            fetch(url, {
                method: 'post',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(batchData)
            })
            .then(getBodyAndStatus)
            .then(async (result) => {
                if (result.status === 400) {
                    document.getElementById('modalSearch').style.display = 'none';
                    document.getElementById('modalError').style.display = 'block';
                }
                if (result.status !== 202) {
                    document.getElementById('modalSearch').style.display = 'none';
                    document.getElementById('modalError').style.display = 'block';
                    return Promise.reject(result)
                } else {
                    // Get longitude/latitude
                    const queryResult = await getAsyncResult(`${url}&id=${result.body.id}`, 1 * 1000 /* 1 second */, 100);
                    let coordinates = queryResult.map( ({lat, lon}) => ({lat, lon}) );
                    coordinates.forEach(function(e){
                        if (typeof e === "object" ){
                            e["name"] = "";
                        }
                    });
                    // Rebind name and longitude/latitude
                    for (let j = 0; j < data.length; j++) {
                        coordinates[j].name = data[j].name;
                        coordinates[j].id = data[j].id;
                    }
                    // Generate marker
                    // Remove previous markers
                    this.markerClusterGroup.clearLayers();
                    // Add each markers to the cluster
                    if (this.searchStyle === 'player') {
                        for (let i = 0; i < coordinates.length; i++) {
                            if (/^-?[0-9]{1,3}(?:\.[0-9]{1,10})?$/.test(coordinates[i].lat) && /^-?[0-9]{1,3}(?:\.[0-9]{1,10})?$/.test(coordinates[i].lon)) {
                                this.markerClusterGroup.addLayer(L.marker([coordinates[i].lat, coordinates[i].lon], {
                                    icon: this.icon
                                })
                                .bindPopup('<h6 class="mt-4" style="text-align: center">' + coordinates[i].name + '</h6> <div style="display:flex; justify-content:center; align-items:center"><a class="btn btn-outline-primary btn-sm mt-2" target="_blank" style="color: black" onmouseover="this.style.color=\'white\';" onmouseout="this.style.color=\'black\';" href="user/viewProfile/' + coordinates[i].id + '" role="button">Voir le profil</a></div>')
                                .openPopup());
                            }
                        }
                    }
                    if (this.searchStyle === 'event') {
                        for (let i = 0; i < coordinates.length; i++) {
                            if (/^-?[0-9]{1,3}(?:\.[0-9]{1,10})?$/.test(coordinates[i].lat) && /^-?[0-9]{1,3}(?:\.[0-9]{1,10})?$/.test(coordinates[i].lon)) {
                                this.markerClusterGroup.addLayer(L.marker([coordinates[i].lat, coordinates[i].lon], {
                                    icon: this.icon
                                })
                                .bindPopup('<h6 class="mt-4" style="text-align: center">' + coordinates[i].name + '</h6> <div style="display:flex; justify-content:center; align-items:center"><a class="btn btn-outline-primary btn-sm mt-2" target="_blank" style="color: black" onmouseover="this.style.color=\'white\';" onmouseout="this.style.color=\'black\';" href="event/viewEvent/' + coordinates[i].id + '" role="button">Détails de l&#39;évènement</a></div>')
                                .openPopup());
                            } else {
                                // do nothing
                            }
                        }
                    }
                    this.map.addLayer(this.markerClusterGroup);
                    document.getElementById('modalSearch').style.display = 'none';
                }
            })
            .catch(err => console.log(err));
        });

        // Get job results - addresses, their geographical coordinates, address components
        function getBodyAndStatus(response) {
            return response.json().then(responceBody => {
                return {
                    status: response.status,
                    body: responceBody
                }
            });
        }

        function getAsyncResult(url, timeout /*timeout between attempts*/, maxAttempt /*maximal amount of attempts*/) {
            return new Promise((resolve, reject) => {
                setTimeout(() => {
                    repeatUntilSuccess(resolve, reject, 0)
                }, timeout);
            });

            function repeatUntilSuccess(resolve, reject, attempt) {
                fetch(url)
                .then(getBodyAndStatus)
                .then(result => {
                    if (result.status === 200) {
                        resolve(result.body);
                    } else if (attempt >= maxAttempt) {
                        reject("Max amount of attempt achived");
                    } else if (result.status === 202) {
                        // Check again after timeout
                        setTimeout(() => {
                            repeatUntilSuccess(resolve, reject, attempt + 1)
                        }, timeout);
                    } else {
                        // Something went wrong
                        reject(result.body)
                    }
                })
                .catch(err => reject(err));
            };
        }
    }
}
window.onload = new Map(); // Init map