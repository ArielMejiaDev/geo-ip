# Serialization

`IpAddress` implements `Arrayable`, `Jsonable`, `JsonSerializable`, and `Stringable`, giving you multiple ways to output the data.

## toArray

Returns an associative array with snake_case keys:

```php
use ArielMejiaDev\GeoIp\Facades\GeoIp;

GeoIp::of('8.8.8.8')->toArray();
```

```php
[
    'ip' => '8.8.8.8',
    'country_code' => 'US',
    'country' => 'United States',
    'region' => 'California',
    'city' => 'Mountain View',
    'latitude' => 37.386,
    'longitude' => -122.084,
    'timezone' => 'America/Los_Angeles',
    'isp' => 'Google LLC',
    'postal_code' => '94035',
]
```

## toJson

Returns a JSON string:

```php
GeoIp::of('8.8.8.8')->toJson();
GeoIp::of('8.8.8.8')->toJson(JSON_PRETTY_PRINT);
```

## toString

Returns a human-readable location string (city, region, country — skipping null values):

```php
GeoIp::of('8.8.8.8')->toString();
// 'Mountain View, California, United States'
```

## String Casting

`IpAddress` implements `Stringable`, so you can cast it directly:

```php
$location = (string) GeoIp::of('8.8.8.8');
// 'Mountain View, California, United States'

echo GeoIp::of('8.8.8.8');
// Mountain View, California, United States
```

## JSON Serialization

`IpAddress` implements `JsonSerializable`, so it works with `json_encode`:

```php
json_encode(GeoIp::of('8.8.8.8'));
```

## Returning from Controllers

Since `IpAddress` implements `Jsonable`, Laravel can serialize it automatically:

```php
Route::get('/location', function () {
    return GeoIp::fromRequest();
});
```

This returns a JSON response with the geolocation data.
