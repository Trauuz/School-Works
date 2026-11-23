<section class="weather-greeting" aria-label="Your local weather">
    <h1>Hello, {{ auth()->user()->name }}!</h1>
    <p data-weather-message data-weather-url="{{ route('weather.show') }}" role="status">{{ auth()->user()->weather_location_id ? 'Checking the weather near your city…' : 'Add your city to see local weather.' }}</p>
    <p class="weather-meta"><span data-weather-meta></span><a class="text-link" href="{{ route('account.profile') }}">Update your city</a> · <a class="text-link" href="https://open-meteo.com/" target="_blank" rel="noopener noreferrer">Weather by Open-Meteo</a></p>
</section>
