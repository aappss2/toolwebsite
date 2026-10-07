<?php
class Security {
    public static function setSecurityHeaders(): void {
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: SAMEORIGIN');
        header('Referrer-Policy: strict-origin-when-cross-origin');
        header('X-XSS-Protection: 1; mode=block');
        // CSP - relatively permissive for tool functionality but safe
        $csp = "default-src 'self'; " .
               "script-src 'self' 'unsafe-inline' https://checkout.razorpay.com https://www.googletagmanager.com https://www.google-analytics.com https://pagead2.googlesyndication.com https://analytics.ahrefs.com; " .
               "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; " .
               "font-src 'self' https://fonts.gstatic.com; " .
               "img-src 'self' data: https:; " .
               "connect-src 'self' https://api.razorpay.com https://www.google-analytics.com; " .
               "frame-src 'self' https://api.razorpay.com https://checkout.razorpay.com https://www.googletagmanager.com; " .
               "object-src 'none'; " .
               "base-uri 'self';";
        header("Content-Security-Policy: $csp");
        
        // HSTS only if HTTPS confirmed - check if we are on https
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
            header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
        }
    }

    public static function generateRandomToken(int $length = 64): string {
        return bin2hex(random_bytes($length / 2));
    }

    public static function sanitizeFilename(string $filename): string {
        $filename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);
        $filename = preg_replace('/_{2,}/', '_', $filename);
        return substr($filename, 0, 255);
    }

    public static function getClientIp(): string {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            return trim($ips[0]);
        } else {
            return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        }
    }

    public static function getUserAgent(): string {
        return $_SERVER['HTTP_USER_AGENT'] ?? '';
    }
}
