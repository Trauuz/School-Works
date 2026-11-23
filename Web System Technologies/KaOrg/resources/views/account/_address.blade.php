<div data-address-form data-reverse-url="{{ route('locations.reverse') }}" data-weather-preview-url="{{ route('weather.show') }}">
    <h2 class="section-title">Your address</h2>
    <p class="section-description">Enter your address or place a map pin to fill in available address details.</p>
    <div class="form-grid">
        <div class="field field-wide"><label for="address_line">House, street, and barangay</label><input id="address_line" name="address_line" autocomplete="street-address" maxlength="255" value="{{ old('address_line', $user->address_line) }}" required></div>
        <div class="field"><label for="city">City / municipality</label><input id="city" name="city" autocomplete="address-level2" value="{{ old('city', $user->city) }}" maxlength="100" required></div>
        <div class="field"><label for="region">Province / region</label><input id="region" name="region" autocomplete="address-level1" value="{{ old('region', $user->region) }}" maxlength="100" required></div>
        <div class="field"><label for="country_code">Country code</label><input id="country_code" name="country_code" autocomplete="country" value="{{ old('country_code', $user->country_code ?? 'PH') }}" pattern="[A-Za-z]{2}" maxlength="2" required><p class="field-help">Two-letter code, such as PH for the Philippines.</p></div>
        <div class="field"><label for="postal_code">Postal code <span>Optional</span></label><input id="postal_code" name="postal_code" autocomplete="postal-code" maxlength="20" value="{{ old('postal_code', $user->postal_code) }}"></div>
    </div>
    <section class="map-details" aria-labelledby="address-pin-heading">
        <h2 class="section-title" id="address-pin-heading">Address pin</h2>
        <p class="field-help">Click the map or drag the marker to update your address details. Review the result and add any missing house or street information. Weather updates automatically from your pin.</p>
        <div class="address-map" data-address-map aria-label="Address location map"></div>
        <div class="form-grid pin-fields">
            <div class="field"><label for="address_latitude">Pin latitude</label><input id="address_latitude" name="address_latitude" type="number" step="any" min="-90" max="90" value="{{ old('address_latitude', $user->address_latitude) }}"></div>
            <div class="field"><label for="address_longitude">Pin longitude</label><input id="address_longitude" name="address_longitude" type="number" step="any" min="-180" max="180" value="{{ old('address_longitude', $user->address_longitude) }}"></div>
        </div>
        <button class="text-link" type="button" data-pin-clear>Remove pin</button>
        <p class="field-help" data-map-status role="status">You can also enter coordinates directly or leave both blank.</p>
    </section>
    <section aria-labelledby="pin-weather-heading">
        <h2 class="section-title" id="pin-weather-heading">Weather at your address</h2>
        <p class="weather-condition" data-pin-weather-condition hidden></p>
        <p class="field-help" data-pin-weather role="status">Place an address pin to see local weather.</p>
    </section>
</div>
@push('dialogs')
    <dialog class="address-loading" data-address-loading aria-labelledby="address-loading-title" aria-describedby="address-loading-description" aria-busy="true" closedby="none" tabindex="-1">
        <span class="address-loading-spinner" aria-hidden="true"></span>
        <h2 id="address-loading-title">Updating your address</h2>
        <p id="address-loading-description" role="status">Please wait while we find the details for your pin.</p>
    </dialog>
@endpush
