class Map
{
    constructor()
    {
        this.map = L.map('map').setView([47.081012, 2.398782], 6);
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
        /*
        this.url = 'http://localhost/Games&Friends/index.php?action=search';
        this.searchBtn = document.getElementById('submitButton');
        this.searchBtn.addEventListener('click', (e) => {
            e.preventDefault();
            this.gameId = document.getElementById('gameList').value;
            console.log(this.postData());
        });*/

        this.createMap();
    }

// Map creation

    createMap()
    {
        L.tileLayer('https://tiles-eu.stadiamaps.com/tiles/stamen_toner/{z}/{x}/{y}{r}.png', {
            minZoom: 0,
            maxZoom: 18,    
            attribution: '&copy; <a href="https://stadiamaps.com/" target="_blank">Stadia Maps</a> &copy; <a href="https://stamen.com/" target="_blank">Stamen Design</a> &copy; <a href="https://openmaptiles.org/" target="_blank">OpenMapTiles</a> &copy; <a href="https://www.openstreetmap.org/about" target="_blank">OpenStreetMap</a> contributors', 
        }).addTo(this.map);
    }

    /*postData() {
        const postDataAsJson = async () => {
            const data = new FormData();
            data.append("gameId", this.gameId);        
            const fetchOptions = {
                method: "POST",    
                body: data,
            };        
            const response = await fetch(this.url, fetchOptions);        
            if (!response.ok) {
                const errorMessage = await response.text();
                throw new Error(errorMessage);
            }        
            return await response.json();
        }
        return postDataAsJson();
    }*/

    /*getUserData() {
        const getUserDataJson = async () => {
            this.response = await fetch(this.url);
            this.userTab = await this.response.json();

            this.userTab.forEach(user => {
                this.userName = user.name;
                this.userLat = user.userLat;
                this.userLong = user.userLog

                this.marker = new L.marker([this.userLat, this.userLong], {
                    icon: this.iconIdle
                }).addTo(this.map);

                this.marker.addEventListener('click', () => {
                    this.userData.style.display = 'block';
                })
            });
        }
        getUserDataJson();
        console.log(this.userTab);
    }*/
}

window.onload = new Map(); // Init map