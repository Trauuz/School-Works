<section class="weather-greeting" aria-label="Your local weather">
    <h1>Hello, {{ auth()->user()->username ?? auth()->user()->name }}!</h1>
    <p data-weather-message data-weather-url="{{ route('weather.show') }}" role="status">{{ (auth()->user()->address_latitude !== null || auth()->user()->weather_location_id) ? 'Checking your local weather…' : 'Place an address pin to see local weather.' }}</p>
    <p class="weather-meta"><span data-weather-meta></span><a class="text-link" href="{{ route('account.profile') }}">Update your address</a></p>
</section>
