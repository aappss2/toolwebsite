<?php
class Seo {
    public static function generateTitle(string $toolName, string $category = ''): string {
        if ($category) {
            return "$toolName - Free Online $category Tool | " . APP_NAME;
        }
        return "$toolName - Free Online Tool | " . APP_NAME;
    }

    public static function generateDescription(string $toolName, string $category = '', string $custom = ''): string {
        if ($custom) return $custom;
        return "Use our free online $toolName. Fast, secure, and easy to use $category tool. No signup required for basic use. Try now at " . APP_NAME . ".";
    }

    public static function canonicalUrl(string $path): string {
        $path = '/' . ltrim($path, '/');
        return rtrim(APP_DOMAIN, '/') . $path;
    }

    public static function ogTags(string $title, string $description, string $url, string $image = ''): string {
        $image = $image ?: APP_DOMAIN . '/assets/images/og-default.png';
        return '
    <meta property="og:title" content="' . htmlspecialchars($title, ENT_QUOTES) . '">
    <meta property="og:description" content="' . htmlspecialchars($description, ENT_QUOTES) . '">
    <meta property="og:url" content="' . htmlspecialchars($url, ENT_QUOTES) . '">
    <meta property="og:type" content="website">
    <meta property="og:image" content="' . htmlspecialchars($image, ENT_QUOTES) . '">
    <meta property="og:site_name" content="' . APP_NAME . '">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="' . htmlspecialchars($title, ENT_QUOTES) . '">
    <meta name="twitter:description" content="' . htmlspecialchars($description, ENT_QUOTES) . '">
    <meta name="twitter:image" content="' . htmlspecialchars($image, ENT_QUOTES) . '">';
    }

    public static function breadcrumbSchema(array $breadcrumbs): string {
        $items = [];
        foreach ($breadcrumbs as $i => $crumb) {
            $items[] = [
                '@type' => 'ListItem',
                'position' => $i + 1,
                'name' => $crumb['name'],
                'item' => $crumb['url'] ?? null
            ];
        }
        $data = [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $items
        ];
        return '<script type="application/ld+json">' . json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>';
    }

    public static function softwareAppSchema(array $tool): string {
        $data = [
            '@context' => 'https://schema.org',
            '@type' => 'SoftwareApplication',
            'name' => $tool['name'],
            'description' => $tool['description'],
            'applicationCategory' => $tool['category'] ?? 'Utilities',
            'operatingSystem' => 'Web',
            'offers' => [
                '@type' => 'Offer',
                'price' => '0',
                'priceCurrency' => 'USD'
            ],
            'url' => $tool['canonical_url'] ?? APP_DOMAIN . '/tools/' . $tool['slug'] . '/'
        ];
        return '<script type="application/ld+json">' . json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>';
    }

    public static function faqSchema(array $faqs): string {
        $entities = [];
        foreach ($faqs as $faq) {
            $entities[] = [
                '@type' => 'Question',
                'name' => $faq['q'],
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => $faq['a']
                ]
            ];
        }
        $data = [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => $entities
        ];
        return '<script type="application/ld+json">' . json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>';
    }
}
