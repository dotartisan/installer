<?php

namespace Dotartisan\Installer\Install;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AdminAccount
{
    public function setup($data)
    {
        $admin = User::find(1);
        $admin->name = $data['name'];
        $admin->email = $data['email'];
        $admin->password = bcrypt($data['password']);
        $admin->save();

        Auth::login($admin);
    }
}
