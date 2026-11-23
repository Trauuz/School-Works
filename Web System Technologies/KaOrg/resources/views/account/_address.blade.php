<div data-address-form data-search-url="{{ route('locations.search') }}" data-city-latitude="{{ $user->weather_latitude }}" data-city-longitude="{{ $user->weather_longitude }}">
    <h2 class="section-title">Your address</h2>
    <p class="section-description">Enter your address manually. City lookup and a map pin are optional.</p>
    <div class="form-grid">
        <div class="field field-wide"><label for="address_line">House, street, and barangay</label><input id="address_line" name="address_line" autocomplete="street-address" maxlength="255" value="{{ old('address_line', $user->address_line) }}" required></div>
        <div class="field"><label for="city">City / municipality</label><input id="city" name="city" autocomplete="address-level2" value="{{ old('city', $user->city) }}" maxlength="100" required></div>
        <div class="field"><label for="region">Province / region</label><input id="region" name="region" autocomplete="address-level1" value="{{ old('region', $user->region) }}" maxlength="100" required></div>
        <div class="field"><label for="country_code">Country code</label><input id="country_code" name="country_code" autocomplete="country" value="{{ old('country_code', $user->country_code ?? 'PH') }}" pattern="[A-Za-z]{2}" maxlength="2" required><p class="field-help">Two-letter code, such as PH for the Philippines.</p></div>
        <div class="field"><label for="postal_code">Postal code <span>Optional</span></label><input id="postal_code" name="postal_code" autocomplete="postal-code" maxlength="20" value="{{ old('postal_code', $user->postal_code) }}"></div>
    </div>
    <div class="city-tools">
        <button class="secondary-action" type="button" data-city-search>Find city for weather</button>
        <button class="text-link" type="button" data-city-clear>Clear weather city</button>
    </div>
    <input type="hidden" id="weather_location_id" name="weather_location_id" value="{{ old('weather_location_id', $user->weather_location_id) }}">
    <p class="field-help" data-city-status role="status">{{ old('weather_location_id', $user->weather_location_id) ? 'Weather city selected. Search again if you change your city or country.' : 'No weather city selected. You can still save your address.' }}</p>
    <ul class="city-results" data-city-results aria-label="Matching cities"></ul>
    <p class="field-help">City search by <a class="text-link" href="https://open-meteo.com/" target="_blank" rel="noopener noreferrer">Open-Meteo</a>, using GeoNames data.</p>
    <details class="map-details">
        <summary>Add an address pin <span>Optional</span></summary>
        <p class="field-help">Click the map or drag the marker. A pin helps locate your address; it does not validate it. Weather uses your selected city.</p>
        <div class="address-map" data-address-map aria-label="Address location map"></div>
        <div class="form-grid pin-fields">
            <div class="field"><label for="address_latitude">Pin latitude</label><input id="address_latitude" name="address_latitude" type="number" step="any" min="-90" max="90" value="{{ old('address_latitude', $user->address_latitude) }}"></div>
            <div class="field"><label for="address_longitude">Pin longitude</label><input id="address_longitude" name="address_longitude" type="number" step="any" min="-180" max="180" value="{{ old('address_longitude', $user->address_longitude) }}"></div>
        </div>
        <button class="text-link" type="button" data-pin-clear>Remove pin</button>
        <p class="field-help" data-map-status role="status">You can also enter coordinates directly or leave both blank.</p>
    </details>
</div>
