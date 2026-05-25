<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = [
            'site_title' => Setting::get('site_title', 'NACOS Day Awards'),
            'vote_price' => Setting::getVotePriceKobo() / 100, // Display in Naira
            'voting_enabled' => Setting::get('voting_enabled', '1'),
            'event_date' => Setting::get('event_date', ''),
            'event_banner' => Setting::get('event_banner', ''),
            'bank_name' => Setting::get('bank_name', ''),
            'account_number' => Setting::get('account_number', ''),
            'account_name' => Setting::get('account_name', ''),
            'payment_provider' => Setting::getPaymentProvider(),
        ];

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'site_title' => 'required|string|max:255',
            'vote_price' => 'required|numeric|min:1',
            'event_date' => 'nullable|date',
            'event_banner' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'bank_name' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:20',
            'account_name' => 'nullable|string|max:255',
            'payment_provider' => 'required|in:korapay',
        ]);

        Setting::set('site_title', $request->site_title);
        Setting::set('vote_price', $request->vote_price * 100); // Store in kobo
        Setting::set('voting_enabled', $request->boolean('voting_enabled') ? '1' : '0');
        Setting::set('event_date', $request->event_date);
        Setting::set('bank_name', $request->bank_name);
        Setting::set('account_number', $request->account_number);
        Setting::set('account_name', $request->account_name);
        Setting::set('payment_provider', $request->payment_provider);

        if ($request->hasFile('event_banner')) {
            $oldBanner = Setting::get('event_banner');
            if ($oldBanner) {
                Storage::disk('public')->delete($oldBanner);
            }
            $path = $request->file('event_banner')->store('banners', 'public');
            Setting::set('event_banner', $path);
        }

        ActivityLog::log('settings_updated', 'Updated site settings');

        return back()->with('success', 'Settings updated successfully.');
    }
}
