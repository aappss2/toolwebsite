<?php
class AdService {
    public static function isAdsEnabled(): bool {
        $setting = get_site_setting('ads_enabled', '1');
        return $setting === '1';
    }

    public static function isAdFreeUser(?array $user): bool {
        if (!$user) return false;
        // If user has active subscription and setting ad_free_for_pro is enabled
        $adFreeForPro = get_site_setting('ad_free_for_pro', '0');
        if ($adFreeForPro === '1' && is_active_subscription($user)) {
            return true;
        }
        return false;
    }

    public static function adTop(?array $user = null): string {
        if (!self::isAdsEnabled() || self::isAdFreeUser($user)) return '';
        $client = SITE_ADSENSE_CLIENT;
        return '
        <div class="ad-container ad-top" style="margin:18px 0; text-align:center;">
            <ins class="adsbygoogle"
                 style="display:block"
                 data-ad-client="' . e($client) . '"
                 data-ad-slot="1415325571"
                 data-ad-format="auto"
                 data-full-width-responsive="true"></ins>
            <script>(adsbygoogle = window.adsbygoogle || []).push({});</script>
        </div>';
    }

    public static function adInContent(?array $user = null): string {
        if (!self::isAdsEnabled() || self::isAdFreeUser($user)) return '';
        $client = SITE_ADSENSE_CLIENT;
        return '
        <div class="ad-container ad-in-content" style="margin:24px 0; text-align:center;">
            <ins class="adsbygoogle"
                 style="display:block; text-align:center;"
                 data-ad-layout="in-article"
                 data-ad-format="fluid"
                 data-ad-client="' . e($client) . '"
                 data-ad-slot="1234567890"></ins>
            <script>(adsbygoogle = window.adsbygoogle || []).push({});</script>
        </div>';
    }

    public static function adBottom(?array $user = null): string {
        if (!self::isAdsEnabled() || self::isAdFreeUser($user)) return '';
        $client = SITE_ADSENSE_CLIENT;
        return '
        <div class="ad-container ad-bottom" style="margin:22px 0; text-align:center;">
            <ins class="adsbygoogle"
                 style="display:block"
                 data-ad-format="autorelaxed"
                 data-ad-client="' . e($client) . '"
                 data-ad-slot="2636907907"></ins>
            <script>(adsbygoogle = window.adsbygoogle || []).push({});</script>
        </div>';
    }

    public static function getAdScripts(): string {
        if (!self::isAdsEnabled()) return '';
        $client = SITE_ADSENSE_CLIENT;
        return '<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=' . e($client) . '" crossorigin="anonymous"></script>';
    }
}
