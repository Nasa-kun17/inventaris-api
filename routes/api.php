<?php
use App\Core\Router;
use App\Controllers\ProductController;
use App\Controllers\CategoryController;

return function (
    Router $router,
    ProductController $products,
    CategoryController $categories
): void {
    // PRODUCT CRUD
    $router->add('GET', '/api/v1/products', [$products, 'index']);
    $router->add('GET', '/api/v1/products/{id}', [$products, 'show']);
    $router->add('POST', '/api/v1/products', [$products, 'store']);
    $router->add('PUT', '/api/v1/products/{id}', [$products, 'update']);
    $router->add('DELETE', '/api/v1/products/{id}', [$products, 'destroy']);
    
    // CATEGORY CRUD
    $router->add('GET', '/api/v1/categories', [$categories, 'index']);
    $router->add('GET', '/api/v1/categories/{id}', [$categories, 'show']);
    $router->add('POST', '/api/v1/categories', [$categories, 'store']);
    $router->add('PUT', '/api/v1/categories/{id}', [$categories, 'update']);
    $router->add('DELETE', '/api/v1/categories/{id}', [$categories, 'destroy']);
};