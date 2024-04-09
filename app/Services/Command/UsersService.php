<?php

namespace App\Services\Command;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UsersService
{
    public function createUser(string $name, string $email, string $password): User
    {
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
        ]);

        return $user;
    }
}
