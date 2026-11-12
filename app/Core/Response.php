<?php
namespace App\Core;

class Response
{
    public static function json(mixed $data, int $status = 200): void 
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    public static function success(mixed $data, string $message = 'Berhasil', int $status = 200): void 
    {
        self::json(['success' => true, 'message' => $message, 'data' => $data], $status);
    }

    public static function error(string $message, int $status): void 
    {
        self::json(['success' => false, 'message' => $message], $status);
    }
}