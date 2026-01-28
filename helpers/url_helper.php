<?php

if (!function_exists('base_url')) {
    function base_url(string $path = ''): string {
        static $base = null;
        if ($base === null) {
            $config = include __DIR__ . '/../config/config.php';
            $base = rtrim($config['base_url'] ?? '', '/');
        }
        $path = ltrim($path, '/');
        if ($base === '') {
            return $path === '' ? '/' : '/' . $path;
        }
        return $path === '' ? $base : $base . '/' . $path;
    }
}

if (!function_exists('asset_url')) {
    function asset_url(string $path = ''): string {
        $path = trim($path);
        if ($path === '') {
            return base_url('');
        }
        $path = str_replace('\\', '/', $path);
        if (preg_match('#^(?:https?:)?//#', $path)) {
            return $path;
        }
        // Normalize legacy "public/" prefix when the web root is already /public
        if (str_starts_with($path, 'public/')) {
            $path = substr($path, strlen('public/'));
        } elseif (str_starts_with($path, '/public/')) {
            $path = substr($path, strlen('/public/'));
        }
        $legacyPrefix = '/Noithat_store';
        if (str_starts_with($path, $legacyPrefix)) {
            $path = substr($path, strlen($legacyPrefix));
        } elseif (str_starts_with($path, ltrim($legacyPrefix, '/'))) {
            $path = substr($path, strlen(ltrim($legacyPrefix, '/')));
        }
        $path = ltrim($path, '/');
        return base_url($path);
    }
}
