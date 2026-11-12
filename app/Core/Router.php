<?php
namespace App\Core;
use App\Core\Response;

class Router
{
    private array $routes = [];

    public function add(string $method, string $pattern, callable $handler): void 
    {
        $this->routes[] = [
            'method' => strtoupper($method),
            'pattern' => $pattern,
            'handler' => $handler
        ];
    }

    public function dispatch(string $method, string $uri): void 
    {
        $path = parse_url($uri, PHP_URL_PATH);
        foreach ($this->routes as $route) {
            $regex = preg_replace('/\{id\}/', '([0-9]+)', $route['pattern']);
            if ($route['method'] === strtoupper($method) && preg_match('#^' . $regex . '/?$#', $path, $matches)) {
                $params = array_slice($matches, 1);
                call_user_func_array($route['handler'], array_map('intval', $params));
                return;
            }
        }
        Response::error('Endpoint tidak ditemukan', 404);
    }
}