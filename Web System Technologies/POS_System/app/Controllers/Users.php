<?php

namespace App\Controllers;

class Users extends BaseController
{
    public function index(): string
    {
        $users = [
            [
                'username'  => 'rimuru',
                'full_name' => 'Rimuru Tempest',
                'role'      => 'Administrator',
            ],
            [
                'username'  => 'benimaru',
                'full_name' => 'Benimaru',
                'role'      => 'Manager',
            ],
            [
                'username'  => 'shuna',
                'full_name' => 'Shuna',
                'role'      => 'Cashier',
            ],
            [
                'username'  => 'shion',
                'full_name' => 'Shion',
                'role'      => 'Cashier',
            ],
            [
                'username'  => 'milim',
                'full_name' => 'Milim Nava',
                'role'      => 'Staff',
            ],
        ];

        return view('users/index', [
            'users' => $users,
        ]);
    }
}
