<?php
$routes = [
    '/' => 'HomeController@index',
    '/login' => 'AuthController@login',
    '/logout' => 'AuthController@logout',
    '/register' => 'AuthController@register',
    '/client/dashboard' => 'ClientController@dashboard',
    '/products' => 'ProductController@index',
    '/purchase' => 'ProductController@purchase'
];
?>