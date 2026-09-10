<?php

namespace App\Controllers;

class Customers extends BaseController
{
    public function index(): string
    {
        $customers = [
            [
                'full_name' => 'Rimuru Tempest',
                'email'     => 'rimuru.tempest@example.com',
                'phone'     => '0917-123-4567',
            ],
            [
                'full_name' => 'Benimaru',
                'email'     => 'benimaru@example.com',
                'phone'     => '0918-234-5678',
            ],
            [
                'full_name' => 'Shuna',
                'email'     => 'shuna@example.com',
                'phone'     => '0919-345-6789',
            ],
            [
                'full_name' => 'Shion',
                'email'     => 'shion@example.com',
                'phone'     => '0920-456-7890',
            ],
            [
                'full_name' => 'Milim Nava',
                'email'     => 'milim.nava@example.com',
                'phone'     => '0921-567-8901',
            ],
        ];

        return view('customers/index', [
            'customers' => $customers,
        ]);
    }
}