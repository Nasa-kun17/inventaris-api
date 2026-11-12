<?php
declare(strict_types=1);

use App\Config\Database;
use App\Core\Router;
use App\Repositories\ProductRepository;
use App\Repositories\CategoryRepository;
use App\Services\ProductService;
use App\Services\CategoryService;
use App\Controllers\ProductController;
use App\Controllers\CategoryController;
use App\Middleware\ErrorHandler;

require dirname(__DIR__) . '/vendor/autoload.php';

use Dotenv\Dotenv;
Dotenv::createImmutable(dirname(__DIR__))->load();

ErrorHandler::register();
header('Content-Type: application/json; charset=utf-8');

try {
    $db = Database::connection();
    
    // Repository
    $productRepository = new ProductRepository($db);
    $categoryRepository = new CategoryRepository($db);
    
    // Service
    $productService = new ProductService($productRepository);
    $categoryService = new CategoryService($categoryRepository);
    
    // Controller
    $productController = new ProductController($productService);
    $categoryController = new CategoryController($categoryService);
    
    // Router
    $router = new Router();
    $registerRoutes = require dirname(__DIR__) . '/routes/api.php';
    $registerRoutes($router, $productController, $categoryController);
    
    $router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
} catch (Throwable $e) {
    // Lempar ulang error-nya agar ditangkap dengan benar oleh ErrorHandler.php
    throw $e;
}