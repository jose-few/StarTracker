
# Star Tracker

This is a mini project I started a while ago, that uses the free Astronomy API found at https://docs.astronomyapi.com/. The aim is to make using the API for planning astronomical observations easier by way of PHP cURL requests, as the API and the data it returns can be overwhelming and at times difficult to understand.

## Methods

This project is built around  src/StarTracker.php - which aims to make using more complicated parts of the AstronomyAPI more streamlined through using PHP to generate requests.

There are three main Methods:

- connect(string $url, string $method, array $body) - this method allows you to run manual API calls using specific URLs, different HTTP methods (default is GET), and the ability to provide a POST body if applicable. The result will be returned as a json_decode associative array.

- searchPositions(array $location) - this method allows you to provide your current location as an array of details (e.g. latitude, longitude, elevation) as well as observation dates and times, and then returns an associative array of planetary bodies in the solar system (Mars, Venus, etc) and their coordinates in the sky during those dates and times.

- starChart(array $observer, array $view) - this method returns a freshly generated image of a specified astronomical object based on the location of the observer in the first parameter, and the requested object/observable in the second array.

There is also a method called toScreen() - which will attempt to render data returned from connect() and searchPositions() as a nice set of tables.

Specific details about method parameters can be found in the class file, as well as example usages in the tests folder.



