<?php

declare(strict_types=1);

namespace Agora\Controller\Component;

use Cake\Controller\Component;
use Cake\Core\Configure;
use Cake\Http\Client;
use Cake\Log\Log;
use Cake\Cache\Cache;
use Exception;

/**
 * Google Maps Component
 *
 * Componente abstracto para manejar todas las funcionalidades de Google Maps API
 * Incluye: Geocoding, Distance Matrix, Places API, y utilidades generales
 *
 * @property \Cake\Http\Client $httpClient
 */
class GoogleMapsComponent extends Component
{
    /**
     * @var string API Key de Google Maps
     */
    protected $apikey;

    /**
     * @var \Cake\Http\Client Cliente HTTP para realizar consultas
     */
    protected $httpClient;

    // ✅ Sintaxis moderna (CakePHP 5.2)
    protected array $_defaultConfig = [
        'apikey' => 'AIzaSyBXQUzPVhX9YEKLXjXJzJeaN2g6Mve44Wc',
        'cache' => true,
        'cache_duration' => '+1 day',
        'cache_prefix' => 'googlemaps_',
        'language' => 'es',
        'region' => 'AR',
        'timeout' => 10
    ];
    /**
     * URLs base de las APIs de Google
     */
    const GEOCODING_URL = 'https://maps.googleapis.com/maps/api/geocode/json';
    const DISTANCE_MATRIX_URL = 'https://maps.googleapis.com/maps/api/distancematrix/json';
    const PLACES_SEARCH_URL = 'https://maps.googleapis.com/maps/api/place/textsearch/json';
    const PLACES_DETAILS_URL = 'https://maps.googleapis.com/maps/api/place/details/json';
    const PLACES_AUTOCOMPLETE_URL = 'https://maps.googleapis.com/maps/api/place/autocomplete/json';

    /**
     * Inicialización del componente
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        // Obtener API key desde configuración o parámetro
        $this->apikey = $config['apikey'] ?? Configure::read('Google.ApiKeys.JuguemosRol');

        if (empty($this->apikey)) {
            throw new Exception('Google Maps API key no configurada');
        }

        $this->httpClient = new Client([
            'timeout' => $this->getConfig('timeout')
        ]);
    }

    // ================================================================================
    // GEOCODING - Convertir direcciones en coordenadas y viceversa
    // ================================================================================

    /**
     * Geocodificar una dirección (obtener coordenadas desde dirección)
     *
     * @param string $address Dirección a geocodificar
     * @param array $options Opciones adicionales
     * @return array Resultado de la geocodificación
     */
    public function geocodeAddress(string $address, array $options = []): array
    {
        if (empty($address)) {
            return $this->_formatError('Dirección vacía');
        }

        $cacheKey = $this->_getCacheKey('geocode', $address, $options);

        if ($this->getConfig('cache')) {
            $cached = Cache::read($cacheKey);
            if ($cached !== null) {
                return $cached;
            }
        }

        $params = array_merge([
            'address' => $address,
            'key' => $this->apikey,
            'language' => $this->getConfig('language'),
            'region' => $this->getConfig('region')
        ], $options);

        try {
            $response = $this->httpClient->get(self::GEOCODING_URL, $params);
            $result = $response->getJson();

            if ($result['status'] === 'OK' && !empty($result['results'])) {
                $formatted = $this->_formatGeocodeResult($result);

                if ($this->getConfig('cache')) {
                    Cache::write($cacheKey, $formatted, $this->getConfig('cache_duration'));
                }

                return $formatted;
            } else {
                return $this->_formatError($result['status'] ?? 'Error desconocido', $result);
            }
        } catch (Exception $e) {
            Log::error('Error en geocodificación: ' . $e->getMessage());
            return $this->_formatError('Error de comunicación: ' . $e->getMessage());
        }
    }

    /**
     * Geocodificación inversa (obtener dirección desde coordenadas)
     *
     * @param float $lat Latitud
     * @param float $lng Longitud
     * @param array $options Opciones adicionales
     * @return array Resultado de la geocodificación inversa
     */
    public function reverseGeocode(float $lat, float $lng, array $options = []): array
    {
        if (!$this->validateCoordinates($lat, $lng)) {
            return $this->_formatError('Coordenadas inválidas');
        }

        $cacheKey = $this->_getCacheKey('reverse', $lat . ',' . $lng, $options);

        if ($this->getConfig('cache')) {
            $cached = Cache::read($cacheKey);
            if ($cached !== null) {
                return $cached;
            }
        }

        $params = array_merge([
            'latlng' => $lat . ',' . $lng,
            'key' => $this->apikey,
            'language' => $this->getConfig('language')
        ], $options);

        try {
            $response = $this->httpClient->get(self::GEOCODING_URL, $params);
            $result = $response->getJson();

            if ($result['status'] === 'OK' && !empty($result['results'])) {
                $formatted = $this->_formatGeocodeResult($result);

                if ($this->getConfig('cache')) {
                    Cache::write($cacheKey, $formatted, $this->getConfig('cache_duration'));
                }

                return $formatted;
            } else {
                return $this->_formatError($result['status'] ?? 'Error desconocido', $result);
            }
        } catch (Exception $e) {
            Log::error('Error en geocodificación inversa: ' . $e->getMessage());
            return $this->_formatError('Error de comunicación: ' . $e->getMessage());
        }
    }

    // ================================================================================
    // DISTANCE MATRIX - Calcular distancias entre puntos
    // ================================================================================

    /**
     * Calcular distancias entre múltiples orígenes y destinos
     *
     * @param array $origins Array de orígenes (direcciones o coordenadas)
     * @param array $destinations Array de destinos (direcciones o coordenadas)
     * @param array $options Opciones adicionales (modo de viaje, unidades, etc.)
     * @return array Matriz de distancias
     */
    public function calculateDistances(array $origins, array $destinations, array $options = []): array
    {
        if (empty($origins) || empty($destinations)) {
            return $this->_formatError('Orígenes o destinos vacíos');
        }

        $params = array_merge([
            'origins' => implode('|', $origins),
            'destinations' => implode('|', $destinations),
            'key' => $this->apikey,
            'mode' => 'driving',
            'units' => 'metric',
            'language' => $this->getConfig('language')
        ], $options);

        try {
            $response = $this->httpClient->get(self::DISTANCE_MATRIX_URL, $params);
            $result = $response->getJson();

            if ($result['status'] === 'OK') {
                return $this->_formatDistanceMatrixResult($result, $origins, $destinations);
            } else {
                return $this->_formatError($result['status'] ?? 'Error desconocido', $result);
            }
        } catch (Exception $e) {
            Log::error('Error en Distance Matrix: ' . $e->getMessage());
            return $this->_formatError('Error de comunicación: ' . $e->getMessage());
        }
    }

    /**
     * Encontrar el punto más cercano a un origen
     *
     * @param string|array $origin Origen (dirección o coordenadas)
     * @param array $destinations Array de destinos posibles
     * @param array $options Opciones adicionales
     * @return array Resultado con el destino más cercano
     */
    public function findNearestPoint($origin, array $destinations, array $options = []): array
    {
        $distances = $this->calculateDistances([$origin], $destinations, $options);

        if (!$distances['success']) {
            return $distances;
        }

        $nearestIndex = null;
        $nearestDistance = PHP_INT_MAX;
        $nearestData = null;

        foreach ($distances['data']['rows'][0]['elements'] as $index => $element) {
            if ($element['status'] === 'OK' && $element['distance']['value'] < $nearestDistance) {
                $nearestDistance = $element['distance']['value'];
                $nearestIndex = $index;
                $nearestData = $element;
            }
        }

        if ($nearestIndex !== null) {
            return [
                'success' => true,
                'data' => [
                    'nearest_index' => $nearestIndex,
                    'nearest_destination' => $destinations[$nearestIndex],
                    'distance' => $nearestData['distance'],
                    'duration' => $nearestData['duration'],
                    'destination_address' => $distances['data']['destination_addresses'][$nearestIndex]
                ]
            ];
        } else {
            return $this->_formatError('No se encontraron destinos válidos');
        }
    }

    // ================================================================================
    // PLACES API - Búsqueda y detalles de lugares
    // ================================================================================

    /**
     * Buscar lugares mediante texto
     *
     * @param string $query Consulta de búsqueda
     * @param array $options Opciones adicionales (tipo, ubicación, radio, etc.)
     * @return array Resultados de la búsqueda
     */
    public function searchPlaces(string $query, array $options = []): array
    {
        if (empty($query)) {
            return $this->_formatError('Consulta vacía');
        }

        $params = array_merge([
            'query' => $query,
            'key' => $this->apikey,
            'language' => $this->getConfig('language')
        ], $options);

        try {
            $response = $this->httpClient->get(self::PLACES_SEARCH_URL, $params);
            $result = $response->getJson();

            if ($result['status'] === 'OK') {
                return $this->_formatPlacesResult($result);
            } else {
                return $this->_formatError($result['status'] ?? 'Error desconocido', $result);
            }
        } catch (Exception $e) {
            Log::error('Error en búsqueda de lugares: ' . $e->getMessage());
            return $this->_formatError('Error de comunicación: ' . $e->getMessage());
        }
    }

    /**
     * Autocompletar direcciones/lugares
     *
     * @param string $input Texto de entrada
     * @param array $options Opciones adicionales
     * @return array Sugerencias de autocompletado
     */
    public function autocompletePlace(string $input, array $options = []): array
    {
        if (empty($input)) {
            return $this->_formatError('Entrada vacía');
        }

        $params = array_merge([
            'input' => $input,
            'key' => $this->apikey,
            'language' => $this->getConfig('language')
        ], $options);

        try {
            $response = $this->httpClient->get(self::PLACES_AUTOCOMPLETE_URL, $params);
            $result = $response->getJson();

            if ($result['status'] === 'OK') {
                return $this->_formatAutocompleteResult($result);
            } else {
                return $this->_formatError($result['status'] ?? 'Error desconocido', $result);
            }
        } catch (Exception $e) {
            Log::error('Error en autocompletado: ' . $e->getMessage());
            return $this->_formatError('Error de comunicación: ' . $e->getMessage());
        }
    }

    /**
     * Obtener detalles de un lugar específico
     *
     * @param string $placeId ID del lugar
     * @param array $options Opciones adicionales (campos a incluir)
     * @return array Detalles del lugar
     */
    public function getPlaceDetails(string $placeId, array $options = []): array
    {
        if (empty($placeId)) {
            return $this->_formatError('Place ID vacío');
        }

        $params = array_merge([
            'place_id' => $placeId,
            'key' => $this->apikey,
            'language' => $this->getConfig('language'),
            'fields' => 'place_id,name,formatted_address,geometry,types,rating,reviews'
        ], $options);

        try {
            $response = $this->httpClient->get(self::PLACES_DETAILS_URL, $params);
            $result = $response->getJson();

            if ($result['status'] === 'OK') {
                return [
                    'success' => true,
                    'data' => $result['result']
                ];
            } else {
                return $this->_formatError($result['status'] ?? 'Error desconocido', $result);
            }
        } catch (Exception $e) {
            Log::error('Error en detalles de lugar: ' . $e->getMessage());
            return $this->_formatError('Error de comunicación: ' . $e->getMessage());
        }
    }

    // ================================================================================
    // UTILIDADES Y HELPERS
    // ================================================================================

    /**
     * Validar coordenadas
     *
     * @param float $lat Latitud
     * @param float $lng Longitud
     * @return bool
     */
    public function validateCoordinates(float $lat, float $lng): bool
    {
        return ($lat >= -90 && $lat <= 90) && ($lng >= -180 && $lng <= 180);
    }

    /**
     * Formatear componentes de dirección
     *
     * @param array $addressComponents Componentes de dirección de Google
     * @return array Dirección formateada
     */
    public function formatAddress(array $addressComponents): array
    {
        $formatted = [
            'street_number' => '',
            'route' => '',
            'locality' => '',
            'administrative_area_level_1' => '',
            'administrative_area_level_2' => '',
            'country' => '',
            'postal_code' => ''
        ];

        foreach ($addressComponents as $component) {
            foreach ($component['types'] as $type) {
                if (array_key_exists($type, $formatted)) {
                    $formatted[$type] = $component['long_name'];
                }
            }
        }

        return $formatted;
    }

    /**
     * Generar URL de Google Maps
     *
     * @param float $lat Latitud
     * @param float $lng Longitud
     * @param array $options Opciones adicionales (zoom, tipo de mapa, etc.)
     * @return string URL de Google Maps
     */
    public function generateMapUrl(float $lat, float $lng, array $options = []): string
    {
        $defaults = [
            'zoom' => 15,
            'maptype' => 'roadmap'
        ];

        $params = array_merge($defaults, $options);
        $coords = $lat . ',' . $lng;

        return "https://www.google.com/maps/@{$coords},{$params['zoom']}z/{$params['maptype']}";
    }

    /**
     * Calcular distancia entre dos puntos usando fórmula Haversine
     *
     * @param float $lat1 Latitud punto 1
     * @param float $lng1 Longitud punto 1
     * @param float $lat2 Latitud punto 2
     * @param float $lng2 Longitud punto 2
     * @param string $unit Unidad (km, mi, nm)
     * @return float Distancia calculada
     */
    public function calculateHaversineDistance(float $lat1, float $lng1, float $lat2, float $lng2, string $unit = 'km'): float
    {
        $earthRadius = [
            'km' => 6371,
            'mi' => 3959,
            'nm' => 3440
        ];

        $radius = $earthRadius[$unit] ?? $earthRadius['km'];

        $lat1Rad = deg2rad($lat1);
        $lng1Rad = deg2rad($lng1);
        $lat2Rad = deg2rad($lat2);
        $lng2Rad = deg2rad($lng2);

        $deltaLat = $lat2Rad - $lat1Rad;
        $deltaLng = $lng2Rad - $lng1Rad;

        $a = sin($deltaLat / 2) * sin($deltaLat / 2) +
            cos($lat1Rad) * cos($lat2Rad) *
            sin($deltaLng / 2) * sin($deltaLng / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $radius * $c;
    }

    // ================================================================================
    // MÉTODOS PRIVADOS - Formateo y utilidades internas
    // ================================================================================

    /**
     * Formatear resultado de geocodificación
     */
    private function _formatGeocodeResult(array $result): array
    {
        $firstResult = $result['results'][0];

        return [
            'success' => true,
            'data' => [
                'formatted_address' => $firstResult['formatted_address'],
                'latitude' => $firstResult['geometry']['location']['lat'],
                'longitude' => $firstResult['geometry']['location']['lng'],
                'location_type' => $firstResult['geometry']['location_type'],
                'address_components' => $this->formatAddress($firstResult['address_components']),
                'place_id' => $firstResult['place_id'] ?? null,
                'types' => $firstResult['types'] ?? [],
                'raw' => $firstResult
            ]
        ];
    }

    /**
     * Formatear resultado de Distance Matrix
     */
    private function _formatDistanceMatrixResult(array $result, array $origins, array $destinations): array
    {
        return [
            'success' => true,
            'data' => [
                'origin_addresses' => $result['origin_addresses'],
                'destination_addresses' => $result['destination_addresses'],
                'rows' => $result['rows'],
                'origins_input' => $origins,
                'destinations_input' => $destinations
            ]
        ];
    }

    /**
     * Formatear resultado de búsqueda de lugares
     */
    private function _formatPlacesResult(array $result): array
    {
        $places = [];
        foreach ($result['results'] as $place) {
            $places[] = [
                'place_id' => $place['place_id'],
                'name' => $place['name'],
                'formatted_address' => $place['formatted_address'],
                'latitude' => $place['geometry']['location']['lat'],
                'longitude' => $place['geometry']['location']['lng'],
                'rating' => $place['rating'] ?? null,
                'types' => $place['types'] ?? [],
                'price_level' => $place['price_level'] ?? null
            ];
        }

        return [
            'success' => true,
            'data' => [
                'places' => $places,
                'next_page_token' => $result['next_page_token'] ?? null
            ]
        ];
    }

    /**
     * Formatear resultado de autocompletado
     */
    private function _formatAutocompleteResult(array $result): array
    {
        $predictions = [];
        foreach ($result['predictions'] as $prediction) {
            $predictions[] = [
                'place_id' => $prediction['place_id'],
                'description' => $prediction['description'],
                'main_text' => $prediction['structured_formatting']['main_text'] ?? '',
                'secondary_text' => $prediction['structured_formatting']['secondary_text'] ?? '',
                'types' => $prediction['types'] ?? []
            ];
        }

        return [
            'success' => true,
            'data' => [
                'predictions' => $predictions
            ]
        ];
    }

    /**
     * Formatear error
     */
    private function _formatError(string $message, array $rawData = []): array
    {
        return [
            'success' => false,
            'error' => $message,
            'raw' => $rawData
        ];
    }

    /**
     * Generar clave de cache
     */
    private function _getCacheKey(string $operation, string $input, array $options): string
    {
        $prefix = $this->getConfig('cache_prefix');
        $hash = md5($operation . '_' . $input . '_' . serialize($options));
        return $prefix . $hash;
    }
}
