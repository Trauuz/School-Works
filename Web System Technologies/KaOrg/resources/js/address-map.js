import 'leaflet/dist/leaflet.css';

export function initializeAddress() {
    const root = document.querySelector('[data-address-form]');
    const cityInput = root.querySelector('#city');
    const countryInput = root.querySelector('#country_code');
    const locationInput = root.querySelector('#weather_location_id');
    const status = root.querySelector('[data-city-status]');
    const results = root.querySelector('[data-city-results]');
    const search = root.querySelector('[data-city-search]');
    const latitude = root.querySelector('#address_latitude');
    const longitude = root.querySelector('#address_longitude');
    const details = root.querySelector('.map-details');
    let map;
    let marker;
    let leaflet;
    let initializing = false;
    let searchController;

    function clearCity() {
        locationInput.value = '';
        results.replaceChildren();
        status.textContent = 'No weather city selected. You can still save your address.';
    }
    cityInput.addEventListener('input', clearCity);
    countryInput.addEventListener('input', clearCity);
    root.querySelector('[data-city-clear]').addEventListener('click', clearCity);

    search.addEventListener('click', async () => {
        const query = cityInput.value.trim();
        const country = countryInput.value.trim().toUpperCase();
        if (query.length < 2 || !/^[A-Z]{2}$/.test(country)) {
            status.textContent = 'Enter a city of at least two letters and a two-letter country code.';
            return;
        }
        searchController?.abort();
        searchController = new AbortController();
        search.disabled = true;
        status.textContent = 'Finding matching cities…';
        results.replaceChildren();
        try {
            const url = new URL(root.dataset.searchUrl);
            url.searchParams.set('q', query);
            url.searchParams.set('country', country);
            const response = await fetch(url, { headers: { Accept: 'application/json' }, signal: searchController.signal });
            if (!response.ok) throw new Error();
            const data = await response.json();
            if (cityInput.value.trim() !== query || countryInput.value.trim().toUpperCase() !== country) return;
            status.textContent = data.results.length ? 'Choose your city below.' : 'No matches found. Try another spelling or continue with your manual address.';
            for (const city of data.results) {
                const item = document.createElement('li');
                const button = document.createElement('button');
                button.type = 'button';
                button.className = 'city-result';
                button.textContent = [city.name, city.admin1, city.country_code].filter(Boolean).join(', ');
                button.addEventListener('click', () => {
                    cityInput.value = city.name;
                    countryInput.value = city.country_code;
                    root.querySelector('#region').value = city.admin1 || root.querySelector('#region').value;
                    locationInput.value = city.id;
                    root.dataset.cityLatitude = city.latitude;
                    root.dataset.cityLongitude = city.longitude;
                    status.textContent = `Weather city selected: ${button.textContent}.`;
                    results.replaceChildren();
                    if (map && !marker) map.setView([city.latitude, city.longitude], 12);
                });
                item.append(button);
                results.append(item);
            }
        } catch (error) {
            if (error.name !== 'AbortError') status.textContent = 'City lookup is unavailable. You can enter your address manually and add a weather city later.';
        } finally {
            search.disabled = false;
        }
    });

    function updateMarker() {
        if (!map) return;
        if (latitude.value === '' || longitude.value === '') {
            if (marker) { marker.remove(); marker = null; }
            return;
        }
        const lat = Number(latitude.value);
        const lng = Number(longitude.value);
        if (!Number.isFinite(lat) || !Number.isFinite(lng) || Math.abs(lat) > 90 || Math.abs(lng) > 180) return;
        if (marker) marker.setLatLng([lat, lng]);
        else {
            marker = leaflet.marker([lat, lng], {
                draggable: true,
                title: 'Address pin; use the coordinate fields to position precisely',
                icon: leaflet.divIcon({ className: 'address-pin', iconSize: [20, 20], iconAnchor: [10, 10] }),
            }).addTo(map);
            marker.on('dragend', () => setCoordinates(marker.getLatLng()));
        }
    }
    function setCoordinates(position) {
        latitude.value = position.lat.toFixed(7);
        longitude.value = position.lng.toFixed(7);
        updateMarker();
    }
    for (const input of [latitude, longitude]) input.addEventListener('change', updateMarker);
    root.querySelector('[data-pin-clear]').addEventListener('click', () => {
        latitude.value = '';
        longitude.value = '';
        updateMarker();
    });
    details.addEventListener('toggle', async () => {
        if (!details.open) return;
        if (map) { map.invalidateSize(); return; }
        if (initializing) return;
        initializing = true;
        try {
            leaflet = await import('leaflet');
            const hasPin = latitude.value !== '' && longitude.value !== '';
            const hasCity = root.dataset.cityLatitude !== '' && root.dataset.cityLongitude !== '';
            const center = hasPin ? [Number(latitude.value), Number(longitude.value)] : hasCity ? [Number(root.dataset.cityLatitude), Number(root.dataset.cityLongitude)] : [12.8797, 121.774];
            map = leaflet.map(root.querySelector('[data-address-map]')).setView(center, hasPin ? 15 : hasCity ? 12 : 5);
            const tiles = leaflet.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            }).addTo(map);
            tiles.on('tileerror', () => {
                root.querySelector('[data-map-status]').textContent = 'Some map tiles could not load. Enter coordinates directly or leave the pin blank.';
            });
            map.on('click', (event) => setCoordinates(event.latlng));
            updateMarker();
        } catch {
            root.querySelector('[data-map-status]').textContent = 'The map could not load. Enter coordinates directly or leave the pin blank.';
        } finally {
            initializing = false;
        }
    });
}
