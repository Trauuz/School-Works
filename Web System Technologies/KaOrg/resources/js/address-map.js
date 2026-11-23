import 'leaflet/dist/leaflet.css';

export function initializeAddress() {
    const root = document.querySelector('[data-address-form]');
    const latitude = root.querySelector('#address_latitude');
    const longitude = root.querySelector('#address_longitude');
    let map;
    let marker;
    let leaflet;
    let addressController;
    let addressTimer;
    let addressVersion = 0;
    let weatherController;
    let weatherTimer;
    let weatherVersion = 0;
    const weatherStatus = root.querySelector('[data-pin-weather]');
    const weatherCondition = root.querySelector('[data-pin-weather-condition]');
    const loadingDialog = document.querySelector('[data-address-loading]');
    loadingDialog.addEventListener('cancel', (event) => event.preventDefault());

    function setAddressLoading(isLoading) {
        root.setAttribute('aria-busy', String(isLoading));
        document.body.classList.toggle('address-is-loading', isLoading);
        if (isLoading && !loadingDialog.open) loadingDialog.showModal();
        if (!isLoading && loadingDialog.open) loadingDialog.close();
    }

    function cancelWeatherLookup() {
        weatherVersion += 1;
        clearTimeout(weatherTimer);
        weatherController?.abort();
    }

    function updateWeather() {
        cancelWeatherLookup();
        weatherCondition.hidden = true;
        weatherCondition.textContent = '';
        const lat = latitude.value;
        const lng = longitude.value;
        if (lat === '' || lng === '' || !Number.isFinite(Number(lat)) || !Number.isFinite(Number(lng)) || Math.abs(Number(lat)) > 90 || Math.abs(Number(lng)) > 180) {
            weatherStatus.textContent = 'Place an address pin to see local weather.';
            return;
        }
        const version = weatherVersion;
        weatherStatus.textContent = 'Checking weather at your pin…';
        weatherTimer = setTimeout(async () => {
            const controller = new AbortController();
            weatherController = controller;
            try {
                const url = new URL(root.dataset.weatherPreviewUrl);
                url.searchParams.set('latitude', lat);
                url.searchParams.set('longitude', lng);
                const response = await fetch(url, { headers: { Accept: 'application/json' }, signal: controller.signal });
                if (!response.ok) throw new Error();
                const weather = await response.json();
                if (version !== weatherVersion || latitude.value !== lat || longitude.value !== lng) return;
                weatherStatus.textContent = weather.message;
                weatherCondition.textContent = weather.condition || '';
                weatherCondition.hidden = !weather.condition;
            } catch (error) {
                if (error.name !== 'AbortError' && version === weatherVersion) {
                    weatherStatus.textContent = 'Weather is temporarily unavailable. You can still save your address.';
                }
            }
        }, 500);
    }

    const mapStatus = root.querySelector('[data-map-status]');

    function cancelAddressLookup() {
        addressVersion += 1;
        clearTimeout(addressTimer);
        addressController?.abort();
        setAddressLoading(false);
    }

    for (const id of ['address_line', 'city', 'region', 'country_code', 'postal_code']) {
        root.querySelector(`#${id}`).addEventListener('input', () => {
            cancelAddressLookup();
            mapStatus.textContent = 'Address edited manually. Move the pin to look up another address.';
        });
    }

    function lookupAddress() {
        cancelAddressLookup();
        const lat = latitude.value;
        const lng = longitude.value;
        if (lat === '' || lng === '' || !Number.isFinite(Number(lat)) || !Number.isFinite(Number(lng)) || Math.abs(Number(lat)) > 90 || Math.abs(Number(lng)) > 180) return;
        const version = addressVersion;
        setAddressLoading(true);
        mapStatus.textContent = 'Finding address details for this pin…';
        addressTimer = setTimeout(async () => {
            const controller = new AbortController();
            addressController = controller;
            const timeout = setTimeout(() => controller.abort(), 12000);
            try {
                const url = new URL(root.dataset.reverseUrl);
                url.searchParams.set('latitude', lat);
                url.searchParams.set('longitude', lng);
                const response = await fetch(url, { headers: { Accept: 'application/json' }, signal: controller.signal });
                if (!response.ok) throw new Error();
                const { address } = await response.json();
                if (version !== addressVersion || latitude.value !== lat || longitude.value !== lng) return;
                for (const id of ['address_line', 'city', 'region', 'country_code', 'postal_code']) {
                    root.querySelector(`#${id}`).value = address[id] || '';
                }
                mapStatus.textContent = 'Address details updated. Review them and fill in anything missing before saving.';
            } catch (error) {
                if (version === addressVersion) {
                    mapStatus.textContent = 'Address lookup is unavailable or no nearby address was found. Your pin is kept; enter the address manually.';
                }
            } finally {
                clearTimeout(timeout);
                if (version === addressVersion) setAddressLoading(false);
            }
        }, 500);
    }

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
        lookupAddress();
        updateWeather();
    }
    for (const input of [latitude, longitude]) {
        input.addEventListener('input', () => { cancelAddressLookup(); cancelWeatherLookup(); });
        input.addEventListener('change', () => { updateMarker(); lookupAddress(); updateWeather(); });
    }
    root.querySelector('[data-pin-clear]').addEventListener('click', () => {
        cancelAddressLookup();
        latitude.value = '';
        longitude.value = '';
        updateMarker();
        updateWeather();
        mapStatus.textContent = 'Pin removed. You can still edit and save your address.';
    });
    async function initializeMap() {
        try {
            leaflet = await import('leaflet');
            const hasPin = latitude.value !== '' && longitude.value !== '';
            const center = hasPin ? [Number(latitude.value), Number(longitude.value)] : [12.8797, 121.774];
            map = leaflet.map(root.querySelector('[data-address-map]')).setView(center, hasPin ? 15 : 5);
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
        }
    }
    initializeMap();
    updateWeather();
}
