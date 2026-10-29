<?php
/**
 * View Helper - View template rendering, URL generation, and formatting
 * NAVASENA MANAGEMENT
 */

namespace App\Helpers;

class View
{
    public static function render(string $viewPath, array $data = [], string $layout = 'main'): void
    {
        $config = require dirname(__DIR__, 2) . '/config/config.php';
        $baseUrl = $config['base_url'];

        // Extract variables to view scope
        extract($data);

        $viewsBase = dirname(__DIR__, 2) . '/views/';
        $viewFile = $viewsBase . ltrim($viewPath, '/') . '.php';

        if (!file_exists($viewFile)) {
            die("View template tidak ditemukan: " . htmlspecialchars($viewPath));
        }

        if ($layout === 'none') {
            require $viewFile;
            return;
        }

        // Buffer content of the view
        ob_start();
        require $viewFile;
        $content = ob_get_clean();

        // Determine layout file
        $layoutHeader = $viewsBase . 'layouts/' . ($layout === 'student' ? 'student_header.php' : 'header.php');
        $layoutFooter = $viewsBase . 'layouts/' . ($layout === 'student' ? 'student_footer.php' : 'footer.php');

        if (file_exists($layoutHeader)) {
            require $layoutHeader;
        }

        echo $content;

        if (file_exists($layoutFooter)) {
            require $layoutFooter;
        }
    }

    public static function url(string $path = ''): string
    {
        $config = require dirname(__DIR__, 2) . '/config/config.php';
        $baseDir = $config['base_dir'] ?? '';
        $cleanPath = '/' . ltrim($path, '/');
        if (empty($baseDir) || $baseDir === '/') {
            return $cleanPath;
        }
        return rtrim($baseDir, '/') . $cleanPath;
    }

    public static function e(?string $string): string
    {
        return htmlspecialchars((string)$string, ENT_QUOTES, 'UTF-8');
    }

    public static function formatRupiah(float|int|string|null $amount): string
    {
        $val = (float)($amount ?? 0);
        return 'Rp' . number_format($val, 0, ',', '.');
    }

    public static function formatDate(?string $date, bool $withTime = false): string
    {
        if (empty($date) || $date === '0000-00-00' || $date === '0000-00-00 00:00:00') {
            return '-';
        }

        $timestamp = strtotime($date);
        if (!$timestamp) {
            return '-';
        }

        $months = [
            1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];

        $day = date('j', $timestamp);
        $month = (int)date('n', $timestamp);
        $year = date('Y', $timestamp);

        $result = $day . ' ' . ($months[$month] ?? '') . ' ' . $year;

        if ($withTime) {
            $result .= ', ' . date('H:i', $timestamp) . ' WIB';
        }

        return $result;
    }

    public static function statusBadge(string $status): string
    {
        $status = strtolower($status);
        return match ($status) {
            'terverifikasi', 'lunas', 'aktif' => 
                '<span class="badge badge-success"><span class="badge-dot"></span>' . ucfirst($status) . '</span>',
            'menunggu_verifikasi', 'sebagian' => 
                '<span class="badge badge-warning"><span class="badge-dot"></span>' . ($status === 'menunggu_verifikasi' ? 'Menunggu' : 'Sebagian') . '</span>',
            'ditolak', 'belum_bayar', 'nonaktif' => 
                '<span class="badge badge-danger"><span class="badge-dot"></span>' . ($status === 'belum_bayar' ? 'Belum Bayar' : ucfirst($status)) . '</span>',
            'dibatalkan' => 
                '<span class="badge badge-secondary"><span class="badge-dot"></span>Dibatalkan</span>',
            default => 
                '<span class="badge badge-neutral">' . htmlspecialchars(ucfirst($status)) . '</span>'
        };
    }

    public static function methodBadge(string $method): string
    {
        $method = strtolower($method);
        return match ($method) {
            'tunai'    => '<span class="badge badge-method badge-tunai">Tunai</span>',
            'transfer' => '<span class="badge badge-method badge-transfer">Transfer</span>',
            default    => '<span class="badge badge-neutral">' . htmlspecialchars(ucfirst($method)) . '</span>'
        };
    }
}
