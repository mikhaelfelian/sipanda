<?php

use CodeIgniter\Router\RouteCollection;
$routes->setAutoRoute(true);

/**
 * @var RouteCollection $routes
 */
$routes->get('/', to: 'Auth::index');

// Auth routes
$routes->group('auth', function ($routes) {
    $routes->get('/', 'Auth::index');
    $routes->match(['get', 'post'], 'login', 'Auth::login');
    $routes->post('cek_login', 'Auth::cek_login');
    $routes->get('logout', 'Auth::logout');
    $routes->match(['get', 'post'], 'forgot_password', 'Auth::forgot_password');
});

$routes->get('/dashboard', 'Dashboard::index', ['namespace' => 'App\Controllers', 'filter' => 'auth']);

// Protected routes (require authentication)
$routes->group('', ['filter' => 'auth'], function ($routes) {
    // ChatGPT routes
    $routes->post('chatgpt/send', 'ChatGPT::send', ['filter' => 'cors']);

});


// SERP routes
$routes->group('serp', function ($routes) {
    $routes->get('google', 'Serp::index');
    $routes->match(['get', 'post'], 'search', 'Serp::search');
    $routes->get('result', 'Serp::result');
    $routes->get('searchGoogle', 'Serp::searchGoogle');
    $routes->get('searchYoutube', 'Serp::searchYoutube');
    $routes->get('searchAmazon', 'Serp::searchAmazon');
    $routes->get('customSearch', 'Serp::customSearch');
    $routes->post('analyzeNews', 'Serp::analyzeNews');
    $routes->post('exportSearchResultPdf', 'Serp::exportSearchResultPdf');
    $routes->post('exportAllResultsPdf', 'Serp::exportAllResultsPdf');
    $routes->post('exportToText', 'Serp::exportToText');
    
    // Maps search routes
    $routes->get('maps', 'SerpMaps::index');
    $routes->match(['get', 'post'], 'maps/search', 'SerpMaps::search');
    
    // Instagram search routes
    $routes->get('instagram', 'SerpInstagram::index');
    $routes->post('instagram/profiles', 'SerpInstagram::searchProfiles');
    $routes->post('instagram/hashtags', 'SerpInstagram::searchHashtags');
    $routes->get('instagram/profile/(:segment)', 'SerpInstagram::viewProfile/$1');
    $routes->get('instagram/post/(:segment)', 'SerpInstagram::viewPost/$1');
    
    // Sentiment Analysis routes
    $routes->get('sentiment', 'SentimentAnalysis::index');
    $routes->post('sentiment/analyze', 'SentimentAnalysis::analyze');
    $routes->post('sentiment/export-pdf', 'SentimentAnalysis::exportPdf');
});

// Words routes (using GET method)
$routes->group('words', function ($routes) {
    $routes->get('/', 'Words::index');
    $routes->get('add', 'Words::add');
    $routes->get('edit/(:num)', 'Words::edit/$1');
    $routes->get('delete/(:num)', 'Words::delete/$1');
    $routes->get('positive', 'Words::viewPositive');
    $routes->get('negative', 'Words::viewNegative');
    $routes->get('category', 'Words::viewByCategory');
    $routes->get('analyze', 'Words::analyze');
});

// PHPInsights Demo routes
$routes->group('insights', function ($routes) {
    $routes->get('/', 'InsightsDemo::index');
    $routes->get('api/analyze', 'InsightsDemo::apiAnalyze');
    $routes->get('api/compare', 'InsightsDemo::apiCompare');
});