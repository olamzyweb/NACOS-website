<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Public voting, no auth required
    }

    public function rules(): array
    {
        return [
            'candidate_id' => 'required|exists:candidates,id',
            'voter_name' => 'required|string|max:255',
            'voter_email' => 'required|email|max:255',
            'votes' => 'required|integer|min:1|max:1000',
            'payment_method' => 'required|in:korapay,bank_transfer',
        ];
    }

    public function messages(): array
    {
        return [
            'votes.min' => 'You must vote at least 1 time.',
            'votes.max' => 'Maximum 1000 votes per transaction.',
        ];
    }
}
