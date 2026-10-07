<?php
function slugify(string $text): string {
    // Replace non letter or digits by -
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    // Transliterate
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    // Remove unwanted characters
    $text = preg_replace('~[^-\w]+~', '', $text);
    // Trim
    $text = trim($text, '-');
    // Remove duplicate -
    $text = preg_replace('~-+~', '-', $text);
    // Lowercase
    $text = strtolower($text);
    if (empty($text)) {
        return 'n-a';
    }
    return $text;
}

function generate_unique_slug(string $baseSlug, string $table = 'tools'): string {
    $db = Database::getInstance();
    $slug = $baseSlug;
    $counter = 1;
    while (true) {
        $exists = $db->fetchOne("SELECT id FROM $table WHERE slug = ?", [$slug]);
        if (!$exists) {
            return $slug;
        }
        $slug = $baseSlug . '-' . $counter;
        $counter++;
    }
}
