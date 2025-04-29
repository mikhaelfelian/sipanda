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
    $routes->get('/', 'Serp::index');
    $routes->match(['get', 'post'], 'search', 'Serp::search');
    $routes->get('searchGoogle', 'Serp::searchGoogle');
    $routes->get('searchYoutube', 'Serp::searchYoutube');
    $routes->get('searchAmazon', 'Serp::searchAmazon');
    $routes->get('customSearch', 'Serp::customSearch');
    $routes->post('analyzeNews', 'Serp::analyzeNews');
    
    // Maps search routes
    $routes->get('maps', 'SerpMaps::index');
    $routes->match(['get', 'post'], 'maps/search', 'SerpMaps::search');
    
    // Instagram search routes
    $routes->get('instagram', 'SerpInstagram::index');
    $routes->post('instagram/profiles', 'SerpInstagram::searchProfiles');
    $routes->post('instagram/hashtags', 'SerpInstagram::searchHashtags');
    $routes->get('instagram/profile/(:segment)', 'SerpInstagram::viewProfile/$1');
    $routes->get('instagram/post/(:segment)', 'SerpInstagram::viewPost/$1');
});