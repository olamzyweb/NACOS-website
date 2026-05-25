@extends('layouts.admin')
@section('title', 'Transactions')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h2 class="text-xl font-bold">Transactions</h2>
    <a href="{{ route('admin.transactions.export') }}" class="px-5 py-2.5 bg-primary-600 text-white font-semibold rounded-xl hover:bg-primary-700 transition text-sm inline-flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
            <path d="M7 10l5 5 5-5" />
            <path d="M12 15V3" />
        </svg>
        Export CSV
    </a>
</div>

{{-- Filters --}}
<div class="bg-white dark:bg-surface-800 rounded-2xl border border-surface-200 dark:border-surface-700 shadow-sm p-4 mb-6">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-xs font-medium mb-1">Search</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Name, email, reference..." class="w-full px-3 py-2 rounded-lg border border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-700 text-sm focus:ring-2 focus:ring-primary-500 transition">
        </div>
        <div>
            <label class="block text-xs font-medium mb-1">Status</label>
            <select name="status" class="px-3 py-2 rounded-lg border border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-700 text-sm focus:ring-2 focus:ring-primary-500">
                <option value="">All</option>
                <option value="success" {{ request('status') === 'success' ? 'selected' : '' }}>Success</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium mb-1">Method</label>
            <select name="method" class="px-3 py-2 rounded-lg border border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-700 text-sm focus:ring-2 focus:ring-primary-500">
                <option value="">All</option>
                <option value="korapay" {{ request('method') === 'korapay' ? 'selected' : '' }}>Korapay</option>
                <option value="bank_transfer" {{ request('method') === 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium mb-1">Category</label>
            <select name="category" class="px-3 py-2 rounded-lg border border-surface-300 dark:border-surface-600 bg-surface-50 dark:bg-surface-700 text-sm focus:ring-2 focus:ring-primary-500">
                <option value="">All</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->slug }}" {{ request('category') === $cat->slug ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-lg text-sm font-medium hover:bg-primary-700 transition">Filter</button>
        <a href="{{ route('admin.transactions.index') }}" class="px-4 py-2 bg-surface-200 dark:bg-surface-700 rounded-lg text-sm font-medium hover:bg-surface-300 transition">Clear</a>
    </form>
</div>

{{-- Table --}}
<div class="bg-white dark:bg-surface-800 rounded-2xl border border-surface-200 dark:border-surface-700 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-surface-50 dark:bg-surface-900 text-sm text-surface-500">
                    <th class="px-4 py-3 text-left">Reference</th>
                    <th class="px-4 py-3 text-left">Voter</th>
                    <th class="px-4 py-3 text-left">Candidate</th>
                    <th class="px-4 py-3 text-center">Votes</th>
                    <th class="px-4 py-3 text-right">Amount</th>
                    <th class="px-4 py-3 text-center">Method</th>
                    <th class="px-4 py-3 text-center">Status</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $txn)
                <tr class="border-t border-surface-100 dark:border-surface-700 hover:bg-surface-50 dark:hover:bg-surface-700/50 transition">
                    <td class="px-4 py-3 text-xs font-mono">{{ Str::limit($txn->reference, 16) }}</td>
                    <td class="px-4 py-3">
                        <p class="text-sm font-medium">{{ $txn->voter_name }}</p>
                        <p class="text-xs text-surface-400">{{ $txn->voter_email }}</p>
                    </td>
                    <td class="px-4 py-3 text-sm">{{ $txn->candidate->name ?? 'N/A' }}</td>
                    <td class="px-4 py-3 text-center font-bold text-sm">{{ $txn->votes }}</td>
                    <td class="px-4 py-3 text-right text-sm font-medium">{{ $txn->formatted_amount }}</td>
                    <td class="px-4 py-3 text-center">
                        <span class="text-xs font-medium {{ $txn->payment_method === 'korapay' ? 'text-primary-600' : 'text-surface-600 dark:text-surface-300' }} inline-flex items-center gap-1">
                            @if($txn->payment_method === 'korapay')
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <rect x="2" y="5" width="20" height="14" rx="2" />
                                    <path d="M2 10h20" />
                                </svg>
                                {{ $txn->payment_method_label }}
                            @else
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path d="M3 21h18" />
                                    <path d="M5 21V9" />
                                    <path d="M9 21V9" />
                                    <path d="M15 21V9" />
                                    <path d="M19 21V9" />
                                    <path d="M2 9l10-6 10 6" />
                                </svg>
                                {{ $txn->payment_method_label }}
                            @endif
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="inline-block px-2.5 py-1 rounded-full text-xs font-semibold
                            {{ $txn->payment_status === 'success' ? 'bg-primary-100 text-primary-800 dark:bg-primary-900/30 dark:text-primary-300' : ($txn->payment_status === 'pending' ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400' : 'bg-red-100 text-red-700') }}">
                            {{ ucfirst($txn->payment_status) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <a href="{{ route('admin.transactions.show', $txn) }}" class="px-3 py-1.5 bg-surface-100 dark:bg-surface-700 rounded-lg text-xs font-medium hover:bg-surface-200 transition">View</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-12 text-center text-surface-400">No transactions found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="px-6 py-4 border-t border-surface-200 dark:border-surface-700">
        {{ $transactions->links() }}
    </div>
</div>
@endsection
