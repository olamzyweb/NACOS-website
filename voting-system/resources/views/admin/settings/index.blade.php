@extends('layouts.admin')
@section('title', 'Settings')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white dark:bg-surface-800 rounded-2xl border border-surface-200 dark:border-surface-700 shadow-sm p-6">
        <h3 class="text-lg font-bold mb-6">Site Settings</h3>

        <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data" class="space-y-5">
            @csrf @method('PUT')

            <div>
                <label class="block text-sm font-medium mb-2">Site Title</label>
                <input type="text" name="site_title" value="{{ $settings['site_title'] }}" class="w-full px-4 py-3 rounded-xl border border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-700 focus:ring-2 focus:ring-primary-500 transition">
            </div>

            <div>
                <label class="block text-sm font-medium mb-2">Vote Price (₦)</label>
                <input type="number" name="vote_price" value="{{ $settings['vote_price'] }}" min="1" step="1" class="w-full px-4 py-3 rounded-xl border border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-700 focus:ring-2 focus:ring-primary-500 transition">
                <p class="text-xs text-surface-400 mt-1">Price per single vote in Naira</p>
            </div>

            <div class="flex items-center gap-3">
                <input type="checkbox" name="voting_enabled" value="1" {{ $settings['voting_enabled'] ? 'checked' : '' }} class="rounded text-primary-600 focus:ring-primary-500">
                <label class="text-sm font-medium">Voting Enabled</label>
            </div>

            <div>
                <label class="block text-sm font-medium mb-2">Online Payment Provider</label>
                <select name="payment_provider" class="w-full px-4 py-3 rounded-xl border border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-700 focus:ring-2 focus:ring-primary-500 transition">
                    <option value="korapay" {{ $settings['payment_provider'] === 'korapay' ? 'selected' : '' }}>Korapay</option>
                </select>
                <p class="text-xs text-surface-400 mt-1">Used for online card/USSD payments</p>
            </div>

            <div>
                <label class="block text-sm font-medium mb-2">Event Date</label>
                <input type="datetime-local" name="event_date" value="{{ $settings['event_date'] ? date('Y-m-d\TH:i', strtotime($settings['event_date'])) : '' }}" class="w-full px-4 py-3 rounded-xl border border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-700 focus:ring-2 focus:ring-primary-500 transition">
            </div>

            <div>
                <label class="block text-sm font-medium mb-2">Event Banner</label>
                <input type="file" name="event_banner" accept="image/*" class="w-full px-4 py-3 rounded-xl border border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-700 file:mr-4 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-primary-100 file:text-primary-700 transition">
            </div>

            <div class="border-t border-surface-200 dark:border-surface-700 pt-5 mt-5">
                <h4 class="font-semibold mb-4">Bank Transfer Details</h4>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-2">Bank Name</label>
                        <input type="text" name="bank_name" value="{{ $settings['bank_name'] }}" class="w-full px-4 py-3 rounded-xl border border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-700 focus:ring-2 focus:ring-primary-500 transition">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">Account Number</label>
                        <input type="text" name="account_number" value="{{ $settings['account_number'] }}" class="w-full px-4 py-3 rounded-xl border border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-700 focus:ring-2 focus:ring-primary-500 transition">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">Account Name</label>
                        <input type="text" name="account_name" value="{{ $settings['account_name'] }}" class="w-full px-4 py-3 rounded-xl border border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-700 focus:ring-2 focus:ring-primary-500 transition">
                    </div>
                </div>
            </div>

            <button type="submit" class="btn-primary">Save Settings</button>
        </form>
    </div>
</div>
@endsection
