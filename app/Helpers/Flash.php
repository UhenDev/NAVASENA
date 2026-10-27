<?php
/**
 * Flash Message Helper
 * NAVASENA MANAGEMENT
 */

namespace App\Helpers;

class Flash
{
    public static function setSuccess(string $message): void
    {
        $_SESSION['flash_success'] = $message;
    }

    public static function setError(string $message): void
    {
        $_SESSION['flash_error'] = $message;
    }

    public static function setInfo(string $message): void
    {
        $_SESSION['flash_info'] = $message;
    }

    public static function getSuccess(): ?string
    {
        if (isset($_SESSION['flash_success'])) {
            $msg = $_SESSION['flash_success'];
            unset($_SESSION['flash_success']);
            return $msg;
        }
        return null;
    }

    public static function getError(): ?string
    {
        if (isset($_SESSION['flash_error'])) {
            $msg = $_SESSION['flash_error'];
            unset($_SESSION['flash_error']);
            return $msg;
        }
        return null;
    }

    public static function getInfo(): ?string
    {
        if (isset($_SESSION['flash_info'])) {
            $msg = $_SESSION['flash_info'];
            unset($_SESSION['flash_info']);
            return $msg;
        }
        return null;
    }

    public static function render(): string
    {
        $html = '';
        if ($success = self::getSuccess()) {
            $html .= '<div class="alert alert-success" role="alert"><svg class="alert-icon" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg><div>' . htmlspecialchars($success, ENT_QUOTES, 'UTF-8') . '</div></div>';
        }
        if ($error = self::getError()) {
            $html .= '<div class="alert alert-danger" role="alert"><svg class="alert-icon" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg><div>' . htmlspecialchars($error, ENT_QUOTES, 'UTF-8') . '</div></div>';
        }
        if ($info = self::getInfo()) {
            $html .= '<div class="alert alert-info" role="alert"><svg class="alert-icon" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg><div>' . htmlspecialchars($info, ENT_QUOTES, 'UTF-8') . '</div></div>';
        }
        return $html;
    }
}
