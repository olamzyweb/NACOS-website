<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\Setting;
use App\Models\VoteTransaction;

class CandidateController extends Controller
{
    /**
     * Display a candidate's campaign page with full details.
     */
    public function show(Candidate $candidate)
    {
        $candidate->load('category');

        // Track page view (fire and forget)
        $candidate->incrementPageViews();

        $votePrice = Setting::getVotePriceNaira();
        $votingEnabled = Setting::isVotingEnabled();
        $eventDate = Setting::getEventDate();
        $siteTitle = Setting::getSiteTitle();
        $paymentProvider = Setting::getPaymentProvider();
        $paymentProviderLabel = 'Korapay';

        // Get rank in category
        $rank = $candidate->rank_in_category;

        // Total candidates in category
        $totalInCategory = $candidate->total_candidates_in_category;

        // Get other candidates in the same category
        $relatedCandidates = $candidate->category
            ->candidates()
            ->active()
            ->where('id', '!=', $candidate->id)
            ->orderByDesc('vote_count')
            ->limit(4)
            ->get();

        // Recent supporters (last 10 successful votes)
        $recentSupporters = VoteTransaction::where('candidate_id', $candidate->id)
            ->where('payment_status', 'success')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get(['voter_name', 'votes', 'created_at']);

        // Vote packages for quick selection
        $votePackages = [
            ['votes' => 5, 'label' => 'Starter', 'icon' => '⭐'],
            ['votes' => 10, 'label' => 'Supporter', 'icon' => '🔥'],
            ['votes' => 20, 'label' => 'Champion', 'icon' => '🏆'],
            ['votes' => 50, 'label' => 'Legend', 'icon' => '👑'],
            ['votes' => 100, 'label' => 'Ultimate', 'icon' => '💎'],
        ];

        // Build social sharing text
        $shareText = "Vote for {$candidate->name} for {$candidate->category->name}! 🗳️ Every vote counts — show your support!";
        $shareUrl = $candidate->campaign_url;

        return view('candidates.show', compact(
            'candidate',
            'votePrice',
            'votingEnabled',
            'eventDate',
            'siteTitle',
            'paymentProvider',
            'paymentProviderLabel',
            'rank',
            'totalInCategory',
            'relatedCandidates',
            'recentSupporters',
            'votePackages',
            'shareText',
            'shareUrl'
        ));
    }

    /**
     * Track a share action for a candidate.
     */
    public function trackShare(Candidate $candidate)
    {
        $candidate->incrementShares();

        return response()->json(['success' => true]);
    }
}
