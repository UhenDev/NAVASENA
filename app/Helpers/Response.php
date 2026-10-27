<?php
/**
 * Response Helper - Handles redirects, JSON, and file exports
 * NAVASENA MANAGEMENT
 */

namespace App\Helpers;

class Response
{
    public static function redirect(string $path): void
    {
        $config = require dirname(__DIR__, 2) . '/config/config.php';
        $baseUrl = $config['base_url'];

        // If path does not start with http/https, prepend baseUrl
        if (!preg_match('/^https?:\/\//i', $path)) {
            $path = '/' . ltrim($path, '/');
            $fullUrl = $baseUrl . $path;
        } else {
            $fullUrl = $path;
        }

        header("Location: " . $fullUrl);
        exit;
    }

    public static function json(mixed $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    public static function downloadCsv(string $filename, array $headers, array $rows): void
    {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $output = fopen('php://output', 'w');
        // Add UTF-8 BOM so Microsoft Excel opens it with correct UTF-8 encoding
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        if (!empty($headers)) {
            fputcsv($output, $headers);
        }

        foreach ($rows as $row) {
            fputcsv($output, $row);
        }

        fclose($output);
        exit;
    }
}
