<?php

namespace App\Http\Controllers;

use App\Models\SeoSetting;
use App\Models\SiteContent;

class PageController extends Controller
{
    public function aboutUs()
    {
        $content = SiteContent::getByKey('about_us');
        $seo     = SeoSetting::forPage('about_us');
        return view('pages.about-us', compact('content', 'seo'));
    }

    public function faq()
    {
        $content = SiteContent::getByKey('faq');
        $seo     = SeoSetting::forPage('faq');
        return view('pages.faq', compact('content', 'seo'));
    }

    public function terms()
    {
        $content = SiteContent::getByKey('terms');
        $seo     = SeoSetting::forPage('terms');
        return view('pages.terms', compact('content', 'seo'));
    }

    public function privacy()
    {
        $content = SiteContent::getByKey('privacy_policy');
        $seo     = SeoSetting::forPage('privacy_policy');
        return view('pages.privacy', compact('content', 'seo'));
    }

    public function giftCards()
    {
        $seo = SeoSetting::forPage('gift_cards');
        return view('pages.gift-cards', compact('seo'));
    }
}
