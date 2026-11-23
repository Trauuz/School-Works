<?php

return [
    'entitlements' => [
        'basic' => [
            'course_catalog' => 'starter',
            'quizzes' => true,
            'progress_tracking' => true,
            'community_discussions' => true,
            'digital_badges' => true,
            'support_response_hours' => 72,
        ],
        'silver' => [
            'course_catalog' => 'core',
            'downloadable_materials' => true,
            'verifiable_certificates' => true,
            'live_webinars_per_cycle' => 1,
            'support_response_hours' => 48,
        ],
        'gold' => [
            'course_catalog' => 'advanced',
            'offline_access' => true,
            'live_workshops_per_cycle' => 4,
            'group_mentoring_per_cycle' => 1,
            'support_response_hours' => 24,
        ],
        'platinum' => [
            'course_catalog' => 'premium',
            'premium_specializations' => true,
            'capstone_courses' => true,
            'all_scheduled_workshops' => true,
            'private_mentoring_minutes_per_cycle' => 30,
            'portfolio_career_reviews_per_cycle' => 1,
            'early_course_access' => true,
            'support_response_hours' => 4,
        ],
    ],
];
