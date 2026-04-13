<?php

namespace App\Services;

use App\Models\AnalyticsSetting;

class AnalyticsService
{
    public static function getSettings(): ?AnalyticsSetting
    {
        return AnalyticsSetting::first();
    }

    public static function getScripts(): string
    {
        $settings = static::getSettings();
        if (!$settings) return '';

        $scripts = '';

        // Google Tag Manager
        if ($settings->gtm_enabled && $settings->gtm_container_id) {
            $id = e($settings->gtm_container_id);
            $scripts .= <<<HTML
<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','{$id}');</script>
<!-- End Google Tag Manager -->
HTML;
        }

        // Google Analytics 4
        if ($settings->ga4_enabled && $settings->ga4_measurement_id) {
            $id = e($settings->ga4_measurement_id);
            $scripts .= <<<HTML
<!-- Google Analytics 4 -->
<script async src="https://www.googletagmanager.com/gtag/js?id={$id}"></script>
<script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','{$id}');</script>
<!-- End Google Analytics 4 -->
HTML;
        }

        // Meta Pixel
        if ($settings->meta_pixel_enabled && $settings->meta_pixel_id) {
            $id = e($settings->meta_pixel_id);
            $scripts .= <<<HTML
<!-- Meta Pixel -->
<script>!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,document,'script','https://connect.facebook.net/en_US/fbevents.js');fbq('init','{$id}');fbq('track','PageView');</script>
<!-- End Meta Pixel -->
HTML;
        }

        return $scripts;
    }
}
