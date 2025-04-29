<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Recaptcha extends BaseConfig
{
    public $siteKey = '';
    public $secretKey = '';
    public $score = 0.5;

    public function __construct()
    {
        parent::__construct();
        
        $this->siteKey = env('recaptcha.sitekey') ?? $this->siteKey;
        $this->secretKey = env('recaptcha.secretkey') ?? $this->secretKey;
        $this->score = (float)(env('recaptcha.score') ?? $this->score);
    }
} 