<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HeroSlide;
use App\Models\PromoBanner;
use Illuminate\Http\Request;

class AdminPromoBannerController extends Controller
{
    public function index()
    {
        $banners    = PromoBanner::orderBy('display_order')->orderBy('created_at')->get();
        $heroSlides = HeroSlide::orderBy('display_order')->orderBy('created_at')->get();
        return view('admin.marketing.banners', compact('banners', 'heroSlides'));
    }

    // ─── Hero Slides ──────────────────────────────────────────────────────────

    public function heroStore(Request $request)
    {
        $data = $request->validate([
            'title'         => 'nullable|string|max:100',
            'subtitle'      => 'nullable|string|max:200',
            'image_url'     => 'required|string|max:500',
            'button_text'   => 'nullable|string|max:50',
            'button_link'   => 'nullable|string|max:300',
            'display_order' => 'nullable|integer|min:0',
        ]);
        $data['is_active']     = $request->boolean('is_active');
        $data['display_order'] = $data['display_order'] ?? 0;
        HeroSlide::create($data);
        return back()->with('success', 'Slide creado correctamente.');
    }

    public function heroUpdate(Request $request, string $id)
    {
        $slide = HeroSlide::findOrFail($id);
        $data = $request->validate([
            'title'         => 'nullable|string|max:100',
            'subtitle'      => 'nullable|string|max:200',
            'image_url'     => 'required|string|max:500',
            'button_text'   => 'nullable|string|max:50',
            'button_link'   => 'nullable|string|max:300',
            'display_order' => 'nullable|integer|min:0',
        ]);
        $data['is_active']     = $request->boolean('is_active');
        $data['display_order'] = $data['display_order'] ?? 0;
        $slide->update($data);
        return back()->with('success', 'Slide actualizado correctamente.');
    }

    public function heroDestroy(string $id)
    {
        HeroSlide::findOrFail($id)->delete();
        return back()->with('success', 'Slide eliminado.');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'               => 'required|string|max:100',
            'subtitle'            => 'nullable|string|max:150',
            'description'         => 'nullable|string|max:500',
            'background_gradient' => 'nullable|string|max:300',
            'text_color'          => 'nullable|string|max:50',
            'button_text'         => 'nullable|string|max:50',
            'button_link'         => 'nullable|string|max:300',
            'button_text_color'   => 'nullable|string|max:50',
            'watermark_text'      => 'nullable|string|max:100',
            'display_order'       => 'nullable|integer|min:0',
        ]);

        $data['is_active']     = $request->boolean('is_active');
        $data['display_order'] = $data['display_order'] ?? 0;
        $data['text_color']    = $data['text_color'] ?: 'white';

        PromoBanner::create($data);
        return back()->with('success', 'Banner creado correctamente.');
    }

    public function update(Request $request, string $id)
    {
        $banner = PromoBanner::findOrFail($id);

        $data = $request->validate([
            'title'               => 'required|string|max:100',
            'subtitle'            => 'nullable|string|max:150',
            'description'         => 'nullable|string|max:500',
            'background_gradient' => 'nullable|string|max:300',
            'text_color'          => 'nullable|string|max:50',
            'button_text'         => 'nullable|string|max:50',
            'button_link'         => 'nullable|string|max:300',
            'button_text_color'   => 'nullable|string|max:50',
            'watermark_text'      => 'nullable|string|max:100',
            'display_order'       => 'nullable|integer|min:0',
        ]);

        $data['is_active']     = $request->boolean('is_active');
        $data['display_order'] = $data['display_order'] ?? 0;
        $data['text_color']    = $data['text_color'] ?: 'white';

        $banner->update($data);
        return back()->with('success', 'Banner actualizado correctamente.');
    }

    public function destroy(string $id)
    {
        PromoBanner::findOrFail($id)->delete();
        return back()->with('success', 'Banner eliminado.');
    }
}
