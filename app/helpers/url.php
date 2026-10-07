<?php
function current_url(): string {
    $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
    return $protocol . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
}

function base_url(string $path = ''): string {
    return rtrim(APP_DOMAIN, '/') . '/' . ltrim($path, '/');
}

function redirect(string $url, int $code = 302): void {
    http_response_code($code);
    header("Location: $url");
    exit;
}

function get_old_to_new_map(): array {
    $map = [];
    $csvPath = ROOT_PATH . '/docs/MIGRATION-MAP.csv';
    if (file_exists($csvPath)) {
        $handle = fopen($csvPath, 'r');
        $header = fgetcsv($handle);
        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) >= 2) {
                $map[trim($row[0], '"')] = trim($row[1], '"');
            }
        }
        fclose($handle);
    }
    return $map;
}

function find_new_url_for_old(string $oldUrl): ?string {
    $map = get_old_to_new_map();
    return $map[$oldUrl] ?? null;
}
