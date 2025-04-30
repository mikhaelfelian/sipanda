<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class GoogleMaps extends BaseConfig
{
    /**
     * Google Maps API Key
     * 
     * @var string
     */
    public string $apiKey = 'AIzaSyBAyMH-A99yD5fHQPz7uzqk8glNJYGEquA';

    /**
     * Default map options
     * 
     * @var array
     */
    public array $defaultOptions = [
        'zoom' => 13,
        'mapTypeId' => 'roadmap'
    ];
} 