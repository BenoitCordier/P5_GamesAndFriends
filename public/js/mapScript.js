class Map
{
    constructor()
    {
        this.map = L.map('map').setView([47.081012, 2.398782], 6);
        this.myAPIKey = "6c5dd3e7389140018457150f3a20be4b";
        this.isRetina = L.Browser.retina;
        this.baseUrl = "https://maps.geoapify.com/v1/tile/toner/{z}/{x}/{y}.png?apiKey=6c5dd3e7389140018457150f3a20be4b";
        this.retinaUrl = "https://maps.geoapify.com/v1/tile/toner/{z}/{x}/{y}@2x.png?apiKey=6c5dd3e7389140018457150f3a20be4b";
        
        this.LeafIcon = L.Icon.extend({
            options: {
                iconSize: [35, 45],
                shadowSize: [41, 41],
                iconAnchor: [13, 41],
                shadowAnchor: [0, 41],
                popupAnchor: [0, -40]
            }
        });
        this.iconIdle = new this.LeafIcon({
            iconUrl: 'img/iconIdle.png'
        });
        this.iconActive = new this.LeafIcon({
            iconUrl: 'img/iconActive.png'
        });
        
        this.searchMapForm = document.getElementById('searchMapForm');
        this.searchMapForm.addEventListener('submit', this.search, false);

        this.createMap();
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

    search(event)
    {
        event.preventDefault();

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
        postDataAsJson().then(data => {
        // Isolate location
            let userLocation = [];
            for (let i = 0; i < data.length; i++) {
                userLocation.push(data[i].userLocation);
            }

        // Start batch geocoder job
            const batchData = userLocation;
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
                if (result.status !== 202) {
                    return Promise.reject(result)
                } else {
                    const queryResult = await getAsyncResult(`${url}&id=${result.body.id}`, 1 * 1000 /* 60 seconds */, 100);
                    console.log(queryResult);
                    for (let j = 0; j < queryResult.length; j++) {
                        var lat = queryResult.map(function(object) {
                            return object.lat;
                        });
                    }
                    for (let k = 0; k < queryResult.length; k++) {
                        var lon = queryResult.map(function(object) {
                            return object.lon;
                        });
                    }
                    console.log(lat);
                    console.log(lon);
                    return queryResult;
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
                console.log("Attempt: " + attempt);
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