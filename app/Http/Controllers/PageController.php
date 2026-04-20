<?php

namespace App\Http\Controllers;

use App\Models\SiteContent;

class PageController extends Controller
{
    public function aboutUs()
    {
        $content = SiteContent::getByKey('about_us');
        return view('pages.about-us', compact('content') + ['seoPage' => 'about_us']);
    }

    public function faq()
    {
        $content = SiteContent::getByKey('faq');
        return view('pages.faq', compact('content') + ['seoPage' => 'faq']);
    }

    public function terms()
    {
        $content = SiteContent::getByKey('terms');
        return view('pages.terms', compact('content') + ['seoPage' => 'terms']);
    }

    public function privacy()
    {
        $content = SiteContent::getByKey('privacy_policy');
        return view('pages.privacy', compact('content') + ['seoPage' => 'privacy_policy']);
    }

    public function giftCards()
    {
        return view('pages.gift-cards', ['seoPage' => 'gift_cards']);
    }
}
