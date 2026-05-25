<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Candidate;
|use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            // TECH & DIGITAL AWARDS
            [
                'name' => 'Best Programmer of the Year',
                'description' => 'TECH & DIGITAL AWARDS: Recognizing exceptional coding skills and technical problem-solving.',
                'sort_order' => 10,
                'candidates' => [['name' => 'Nominee A'], ['name' => 'Nominee B'], ['name' => 'Nominee C']],
            ],
            [
                'name' => 'Most Innovative Student',
                'description' => 'TECH & DIGITAL AWARDS: For the student with the most creative and forward-thinking tech solutions.',
                'sort_order' => 11,
                'candidates' => [['name' => 'Nominee A'], ['name' => 'Nominee B'], ['name' => 'Nominee C']],
            ],
            [
                'name' => 'Tech Influencer of the Year',
                'description' => 'TECH & DIGITAL AWARDS: Celebrating those who inspire and lead the tech community.',
                'sort_order' => 12,
                'candidates' => [['name' => 'Nominee A'], ['name' => 'Nominee B'], ['name' => 'Nominee C']],
            ],
            [
                'name' => 'Best Tech Content Creator',
                'description' => 'TECH & DIGITAL AWARDS: For outstanding contributions in tech education and content sharing.',
                'sort_order' => 13,
                'candidates' => [['name' => 'Nominee A'], ['name' => 'Nominee B'], ['name' => 'Nominee C']],
            ],
            [
                'name' => 'AI/Tech Enthusiast Award',
                'description' => 'TECH & DIGITAL AWARDS: Honoring passion and dedication to emerging technologies.',
                'sort_order' => 14,
                'candidates' => [['name' => 'Nominee A'], ['name' => 'Nominee B'], ['name' => 'Nominee C']],
            ],
            [
                'name' => 'Most Creative Developer',
                'description' => 'TECH & DIGITAL AWARDS: For unique and artistic approaches to software development.',
                'sort_order' => 15,
                'candidates' => [['name' => 'Nominee A'], ['name' => 'Nominee B'], ['name' => 'Nominee C']],
            ],
            [
                'name' => 'Best Creative Designer',
                'description' => 'TECH & DIGITAL AWARDS: Recognizing excellence in UI/UX and visual design.',
                'sort_order' => 16,
                'candidates' => [['name' => 'Nominee A'], ['name' => 'Nominee B'], ['name' => 'Nominee C']],
            ],

            // LEADERSHIP & IMPACT AWARDS
            [
                'name' => 'HOC of the Year',
                'description' => 'LEADERSHIP & IMPACT AWARDS: Recognizing the best Head of Class for outstanding leadership.',
                'sort_order' => 20,
                'candidates' => [['name' => 'Nominee A'], ['name' => 'Nominee B']],
            ],
            [
                'name' => 'Assistant HOC of the Year',
                'description' => 'LEADERSHIP & IMPACT AWARDS: Honoring the supporting leadership in class management.',
                'sort_order' => 21,
                'candidates' => [['name' => 'Nominee A'], ['name' => 'Nominee B']],
            ],
            [
                'name' => 'Most Outstanding Leader',
                'description' => 'LEADERSHIP & IMPACT AWARDS: Celebrating exceptional leadership across the department.',
                'sort_order' => 22,
                'candidates' => [['name' => 'Nominee A'], ['name' => 'Nominee B']],
            ],
            [
                'name' => 'Best Executive of the Year',
                'description' => 'LEADERSHIP & IMPACT AWARDS: For the most impactful member of the NACOS executive team.',
                'sort_order' => 23,
                'candidates' => [['name' => 'Nominee A'], ['name' => 'Nominee B']],
            ],
            [
                'name' => 'Best Team Player',
                'description' => 'LEADERSHIP & IMPACT AWARDS: Recognizing those who excel in collaboration and support.',
                'sort_order' => 24,
                'candidates' => [['name' => 'Nominee A'], ['name' => 'Nominee B']],
            ],

            // SOCIAL & PERSONALITY AWARDS
            [
                'name' => 'Social Influencer of the Year',
                'description' => 'SOCIAL & PERSONALITY AWARDS: For students with the most social impact and reach.',
                'sort_order' => 30,
                'candidates' => [['name' => 'Nominee A'], ['name' => 'Nominee B']],
            ],
            [
                'name' => 'Social Personality of the Year',
                'description' => 'SOCIAL & PERSONALITY AWARDS: Celebrating vibrant and engaging personalities.',
                'sort_order' => 31,
                'candidates' => [['name' => 'Nominee A'], ['name' => 'Nominee B']],
            ],
            [
                'name' => 'Most Popular Student',
                'description' => 'SOCIAL & PERSONALITY AWARDS: The most well-known student in the department.',
                'sort_order' => 32,
                'candidates' => [['name' => 'Nominee A'], ['name' => 'Nominee B']],
            ],
            [
                'name' => 'Mr. Money of the Year',
                'description' => 'SOCIAL & PERSONALITY AWARDS: Celebrating entrepreneurial success and prosperity.',
                'sort_order' => 33,
                'candidates' => [['name' => 'Nominee A'], ['name' => 'Nominee B']],
            ],
            [
                'name' => 'Fashion Icon of the Department',
                'description' => 'SOCIAL & PERSONALITY AWARDS: For the student with the most consistent and unique style.',
                'sort_order' => 34,
                'candidates' => [['name' => 'Nominee A'], ['name' => 'Nominee B']],
            ],

            // CREATIVE & BRANDS AWARDS
            [
                'name' => 'Artist of the Year',
                'description' => 'CREATIVE & BRANDS AWARDS: Recognizing outstanding musical or visual artistic talent.',
                'sort_order' => 40,
                'candidates' => [['name' => 'Nominee A'], ['name' => 'Nominee B']],
            ],
            [
                'name' => 'Content Creator of the Year',
                'description' => 'CREATIVE & BRANDS AWARDS: For excellence in digital storytelling and creation.',
                'sort_order' => 41,
                'candidates' => [['name' => 'Nominee A'], ['name' => 'Nominee B']],
            ],
            [
                'name' => 'CEO of the Year',
                'description' => 'CREATIVE & BRANDS AWARDS: Celebrating student entrepreneurs leading their own brands.',
                'sort_order' => 42,
                'candidates' => [['name' => 'Nominee A'], ['name' => 'Nominee B']],
            ],
            [
                'name' => 'Tech Entrepreneur/Student Founder',
                'description' => 'CREATIVE & BRANDS AWARDS: For founders of tech-driven startups and initiatives.',
                'sort_order' => 43,
                'candidates' => [['name' => 'Nominee A'], ['name' => 'Nominee B']],
            ],
            [
                'name' => 'Best Brand of the Year',
                'description' => 'CREATIVE & BRANDS AWARDS: Recognizing the most successful student-led brand.',
                'sort_order' => 44,
                'candidates' => [['name' => 'Nominee A'], ['name' => 'Nominee B']],
            ],

            // SPORTS AWARDS
            [
                'name' => 'Best Male Footballer of the Year',
                'description' => 'SPORTS AWARDS: Honoring the most outstanding male player on the pitch.',
                'sort_order' => 50,
                'candidates' => [['name' => 'Nominee A'], ['name' => 'Nominee B']],
            ],
            [
                'name' => 'Best Female Footballer of the Year',
                'description' => 'SPORTS AWARDS: Honoring the most outstanding female player on the pitch.',
                'sort_order' => 51,
                'candidates' => [['name' => 'Nominee A'], ['name' => 'Nominee B']],
            ],
            [
                'name' => 'Best Football Team of the Year',
                'description' => 'SPORTS AWARDS: Recognizing the most successful team in the department.',
                'sort_order' => 52,
                'candidates' => [['name' => 'Nominee A'], ['name' => 'Nominee B']],
            ],

            // SPECIAL RECOGNITION AWARDS
            [
                'name' => 'FX Trader of the Year',
                'description' => 'SPECIAL RECOGNITION: For excellence in financial markets and trading.',
                'sort_order' => 60,
                'candidates' => [['name' => 'Nominee A'], ['name' => 'Nominee B']],
            ],
            [
                'name' => 'Best Lecturer of the Year',
                'description' => 'SPECIAL RECOGNITION: Voted by students for the most impactful and supportive lecturer.',
                'sort_order' => 61,
                'candidates' => [['name' => 'Nominee A'], ['name' => 'Nominee B']],
            ],
            [
                'name' => 'Best Bootcamp Attendee',
                'description' => 'SPECIAL RECOGNITION: Recognizing the most dedicated and improved student from the recent tech bootcamp.',
                'sort_order' => 62,
                'candidates' => [['name' => 'Nominee A'], ['name' => 'Nominee B']],
            ],
        ];

        // Clear existing data to avoid duplicates if re-seeded
        Candidate::truncate();
        Category::truncate();

        foreach ($categories as $catData) {
            $candidates = $catData['candidates'];
            unset($catData['candidates']);

            $category = Category::create($catData);

            foreach ($candidates as $candData) {
                $candData['category_id'] = $category->id;
                $candData['vote_count'] = 0;
                $candData['status'] = true;
                Candidate::create($candData);
            }
        }
    }
}
