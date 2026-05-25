@extends('layouts.app')
@section('title', 'Bank Transfer')

@section('content')
<div class="max-w-lg mx-auto px-4 py-16">
    <div class="bg-white dark:bg-surface-800 rounded-2xl shadow-sm border border-surface-200 dark:border-surface-700 p-8">
        <div class="text-center mb-6">
            <div class="w-16 h-16 mx-auto mb-4 bg-primary-100 dark:bg-primary-900/30 rounded-full flex items-center justify-center">
                <svg class="w-8 h-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
            </div>
            <h1 class="text-2xl font-bold mb-2">Bank Transfer Payment</h1>
            <p class="text-surface-500 dark:text-surface-400">Transfer the exact amount to complete your vote</p>
        </div>

        {{-- Transaction Details --}}
        <div class="bg-surface-50 dark:bg-surface-700 rounded-xl p-4 mb-6 space-y-2">
            <div class="flex justify-between">
                <span class="text-sm text-surface-500">Reference</span>
                <span class="text-sm font-mono font-medium">{{ $transaction->reference }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-sm text-surface-500">Candidate</span>
                <span class="text-sm font-medium">{{ $transaction->candidate->name }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-sm text-surface-500">Votes</span>
                <span class="text-sm font-bold">{{ $transaction->votes }}</span>
            </div>
            <div class="flex justify-between border-t border-surface-200 dark:border-surface-600 pt-2 mt-2">
                <span class="text-sm font-semibold">Amount to Transfer</span>
                <span class="text-lg font-bold text-primary-600 dark:text-primary-400">{{ $transaction->formatted_amount }}</span>
            </div>
        </div>

        {{-- Bank Details --}}
        <div class="bg-gradient-to-r from-primary-50 to-primary-100 dark:from-primary-900/20 dark:to-primary-800/20 rounded-xl p-5 mb-6 border border-primary-200 dark:border-primary-800">
            <h3 class="font-bold mb-3 flex items-center gap-2">
                <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                Bank Details
            </h3>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-surface-500">Bank</span>
                    <span class="font-medium">{{ \App\Models\Setting::get('bank_name', 'First Bank') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-surface-500">Account Number</span>
                    <span class="font-mono font-bold text-lg">{{ \App\Models\Setting::get('account_number', '0123456789') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-surface-500">Account Name</span>
                    <span class="font-medium">{{ \App\Models\Setting::get('account_name', 'NACOS Day Awards') }}</span>
                </div>
            </div>
        </div>

        <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl p-4 mb-6 text-sm text-amber-700 dark:text-amber-300">
            <p class="font-semibold mb-1 inline-flex items-center gap-2">
                <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z" />
                    <path d="M12 9v4" />
                    <path d="M12 17h.01" />
                </svg>
                Important:
            </p>
            <ul class="list-disc list-inside space-y-1 text-xs">
                <li>Use <strong>{{ $transaction->reference }}</strong> as payment narration</li>
                <li>Transfer the exact amount shown above</li>
                <li>Upload your payment receipt below after transfer</li>
            </ul>
        </div>

        {{-- Receipt Upload --}}
        <form method="POST" action="{{ route('vote.upload-receipt', $transaction->reference) }}" enctype="multipart/form-data">
            @csrf
            <label class="block text-sm font-medium mb-2">Upload Payment Receipt</label>
            <input type="file" name="receipt" accept="image/*" required class="w-full px-4 py-3 rounded-xl border border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-700 file:mr-4 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-primary-100 file:text-primary-700 hover:file:bg-primary-200 transition">
            @error('receipt')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror

            <button type="submit" class="w-full mt-4 py-3 bg-primary-600 hover:bg-primary-700 text-white font-bold rounded-xl hover:shadow-lg transition-all">
                Upload Receipt & Submit
            </button>
        </form>
    </div>
</div>
@endsection
