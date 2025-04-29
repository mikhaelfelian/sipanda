<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Serp extends BaseConfig
{
    /**
     * SERP API Key
     * 
     * @var string
     */
    public string $apiKey = 'aee5377a9c86f394461bab57bec5b83faa622275bc9b787e2da3c51b6eaee754';

    /**
     * Default search parameters
     * 
     * @var array
     */
    public array $defaultParams = [
            'engine' => 'google',
            'google_domain' => 'google.co.id',
            'gl' => 'id',
            'hl' => 'id'
    ];
} 