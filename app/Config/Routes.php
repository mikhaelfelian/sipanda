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
    $routes->post('chatgpt/ask', 'ChatGPT::ask', ['filter' => 'cors']);
    $routes->post('chatgpt/ask-with-system', 'ChatGPT::askWithSystem', ['filter' => 'cors']);
    $routes->post('chatgpt/conversation', 'ChatGPT::conversation', ['filter' => 'cors']);

});


// SERP routes
$routes->group('serp', ['filter' => 'auth'], function ($routes) {
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
    
    // X.com OSINT routes
    $routes->get('xosint', 'XOsint::index');
    $routes->post('xosint/profile', 'XOsint::profile');
    $routes->post('xosint/search', 'XOsint::search');
    $routes->post('xosint/trends', 'XOsint::trends');
    $routes->post('xosint/export-profile-pdf', 'XOsint::exportProfilePdf');
});

// Words routes (using GET method)
$routes->group('words', ['filter' => 'auth'], function ($routes) {
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
$routes->group('insights', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'InsightsDemo::index');
    $routes->get('api/analyze', 'InsightsDemo::apiAnalyze');
    $routes->get('api/compare', 'InsightsDemo::apiCompare');
});

// Pengaturan routes with auth filter
$routes->group('pengaturan', ['filter' => 'auth'], function ($routes) {
    $routes->get('app', 'Pengaturan::index');
    $routes->post('app/update', 'Pengaturan::update');
    
    // API Tokens management routes
    $routes->get('api-tokens', 'ApiTokens::index');
    $routes->get('api-tokens/add', 'ApiTokens::add');
    $routes->post('api-tokens/add', 'ApiTokens::add');
    $routes->get('api-tokens/edit/(:num)', 'ApiTokens::edit/$1');
    $routes->post('api-tokens/edit/(:num)', 'ApiTokens::edit/$1');
    $routes->get('api-tokens/delete/(:num)', 'ApiTokens::delete/$1');
    $routes->get('api-tokens/toggle/(:num)', 'ApiTokens::toggle/$1');
});

/*
 * OSINT Routes
 */
$routes->group('osint', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'Osint::index');
    $routes->get('x', 'Osint::x');
    $routes->post('x/profile', 'Osint::xProfile');
    $routes->get('x/export-profile/(:segment)', 'Osint::xExportProfile/$1');
});
