<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\Tag;
use Illuminate\Http\Request;

class AdminCampaignController extends Controller
{
    public function index()
    {
        $campaigns = Campaign::orderBy('display_order')->orderBy('name')->get();
        $tags      = Tag::orderBy('name')->pluck('slug', 'name');
        return view('admin.marketing.campaigns', compact('campaigns', 'tags'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'            => 'required|string|max:100',
            'tag'             => 'nullable|string|max:50',
            'description'     => 'nullable|string|max:500',
            'banner_image'    => 'nullable|max:500',
            'start_date'      => 'nullable|date',
            'end_date'        => 'nullable|date|after_or_equal:start_date',
            'display_order'   => 'nullable|integer|min:0',
        ]);

        $data['display_on_home'] = $request->boolean('display_on_home');
        $data['is_active']       = $request->boolean('is_active');
        $data['display_order']   = $data['display_order'] ?? 0;

        Campaign::create($data);

        return back()->with('success', 'Campaña creada correctamente.');
    }

    public function update(Request $request, string $id)
    {
        $campaign = Campaign::findOrFail($id);

        $data = $request->validate([
            'name'            => 'required|string|max:100',
            'tag'             => 'nullable|string|max:50',
            'description'     => 'nullable|string|max:500',
            'banner_image'    => 'nullable|max:500',
            'start_date'      => 'nullable|date',
            'end_date'        => 'nullable|date|after_or_equal:start_date',
            'display_order'   => 'nullable|integer|min:0',
        ]);

        $data['display_on_home'] = $request->boolean('display_on_home');
        $data['is_active']       = $request->boolean('is_active');
        $data['display_order']   = $data['display_order'] ?? 0;

        $campaign->update($data);

        return back()->with('success', 'Campaña actualizada correctamente.');
    }

    public function destroy(string $id)
    {
        Campaign::findOrFail($id)->delete();
        return back()->with('success', 'Campaña eliminada.');
    }
}
