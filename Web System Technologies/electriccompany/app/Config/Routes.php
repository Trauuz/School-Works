<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// route for navbar
$routes->get('/', 'Home::home');
$routes->get('/about', 'About::index');
$routes->get('/services', 'Services::index');
$routes->match(['get', 'post'], '/contact', 'Contact::index');
$routes->get('/register', 'Register::index');
$routes->post('/register', 'Register::create');


// route for login page
$routes->get('/login', 'Home::login');
$routes->post('/login', 'Home::login');

// authenticated dashboard and customer account details
$routes->get('/dashboard', 'Home::index');
$routes->get('account/create', 'Home::createAccount');
$routes->post('account', 'Home::storeAccount');
$routes->get('account/(:num)/edit', 'Home::editAccount/$1');
$routes->post('account/(:num)', 'Home::updateAccount/$1');
$routes->post('account/(:num)/delete', 'Home::deleteAccount/$1');
$routes->get('account/(:num)', 'Home::viewAccount/$1');

// route for logout
$routes->post('/logout', 'Home::logout');
