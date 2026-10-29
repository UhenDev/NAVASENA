<?php
/**
 * Flash Message Helper - Executive Glass Alert Component
 * GUARDIAN OF NAVASENA 27 — SMKN 1 PANYINGKIRAN
 */

namespace App\Helpers;

class Flash
{
    public static function setSuccess(string $message, ?string $title = null): void
    {
        $_SESSION['flash_success'] = [
            'message' => $message,
            'title'   => $title
        ];
    }

    public static function setError(string $message, ?string $title = null): void
    {
        $_SESSION['flash_error'] = [
            'message' => $message,
            'title'   => $title
        ];
    }

    public static function setWarning(string $message, ?string $title = null): void
    {
        $_SESSION['flash_warning'] = [
            'message' => $message,
            'title'   => $title
        ];
    }

    public static function setInfo(string $message, ?string $title = null): void
    {
        $_SESSION['flash_info'] = [
            'message' => $message,
            'title'   => $title
        ];
    }

    public static function getSuccess(): ?array
    {
        return self::extractFlash('flash_success');
    }

    public static function getError(): ?array
    {
        return self::extractFlash('flash_error');
    }

    public static function getWarning(): ?array
    {
        return self::extractFlash('flash_warning');
    }

    public static function getInfo(): ?array
    {
        return self::extractFlash('flash_info');
    }

    public static function hasAny(): bool
    {
        return !empty($_SESSION['flash_success']) ||
               !empty($_SESSION['flash_error']) ||
               !empty($_SESSION['flash_warning']) ||
               !empty($_SESSION['flash_info']);
    }

    private static function extractFlash(string $key): ?array
    {
        if (isset($_SESSION[$key])) {
            $val = $_SESSION[$key];
            unset($_SESSION[$key]);

            if (is_array($val)) {
                return [
                    'message' => (string)($val['message'] ?? ''),
                    'title'   => !empty($val['title']) ? (string)$val['title'] : null
                ];
            }

            return [
                'message' => (string)$val,
                'title'   => null
            ];
        }

        return null;
    }

    public static function render(): string
    {
        $html = '';

        if ($success = self::getSuccess()) {
            $html .= self::renderItem('success', $success);
        }

        if ($error = self::getError()) {
            $html .= self::renderItem('danger', $error);
        }

        if ($warning = self::getWarning()) {
            $html .= self::renderItem('warning', $warning);
        }

        if ($info = self::getInfo()) {
            $html .= self::renderItem('info', $info);
        }

        return $html;
    }

    private static function renderItem(string $type, array $data): string
    {
        $message = $data['message'];
        $title = $data['title'];

        $config = match ($type) {
            'success' => [
                'badge'        => 'BERHASIL',
                'defaultTitle' => 'Aksi Berhasil Dilakukan',
                'autoDismiss'  => 6000,
                'icon'         => '<svg class="alert-icon" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>'
            ],
            'danger' => [
                'badge'        => 'PERHATIAN',
                'defaultTitle' => 'Terjadi Kesalahan',
                'autoDismiss'  => 9000,
                'icon'         => '<svg class="alert-icon" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>'
            ],
            'warning' => [
                'badge'        => 'PERINGATAN',
                'defaultTitle' => 'Pemberitahuan Penting',
                'autoDismiss'  => 8000,
                'icon'         => '<svg class="alert-icon" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>'
            ],
            default => [
                'badge'        => 'INFORMASI',
                'defaultTitle' => 'Informasi Sistem',
                'autoDismiss'  => 6000,
                'icon'         => '<svg class="alert-icon" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>'
            ]
        };

        $finalTitle = htmlspecialchars($title ?: $config['defaultTitle'], ENT_QUOTES, 'UTF-8');
        $finalMessage = nl2br(htmlspecialchars($message, ENT_QUOTES, 'UTF-8'));
        $autoDismissAttr = $config['autoDismiss'] ? ' data-auto-dismiss="' . (int)$config['autoDismiss'] . '"' : '';

        return '
        <div class="alert alert-' . $type . ' alert-dismissible" role="alert"' . $autoDismissAttr . '>
            <div class="alert-icon-wrapper">
                ' . $config['icon'] . '
            </div>
            <div class="alert-content">
                <div class="alert-header">
                    <span class="alert-badge">' . $config['badge'] . '</span>
                    <strong class="alert-title">' . $finalTitle . '</strong>
                </div>
                <div class="alert-message">' . $finalMessage . '</div>
            </div>
            <button type="button" class="alert-close" aria-label="Tutup notifikasi" onclick="dismissAlert(this.closest(\'.alert\'))">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
            <div class="alert-progress-bar"><div class="alert-progress-fill"></div></div>
        </div>';
    }
}
