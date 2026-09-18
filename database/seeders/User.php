<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class User extends Seeder
{
    public function run(): void
    {
        $users = [
            ["name" => "Administrator",   "username" => "admin",          "password" => Hash::make("admin"),    "role" => "super admin", "status_active" => "active"],
            ["name" => "Super Admin",     "username" => "superadmin",     "password" => Hash::make("password"), "role" => "super admin", "status_active" => "active"],
            ["name" => "Admin Brankas",   "username" => "admin_brankas",  "password" => Hash::make("password"), "role" => "admin",       "status_active" => "active"],
            ["name" => "Petugas Admin",   "username" => "petugas",        "password" => Hash::make("petugas123"), "role" => "admin",     "status_active" => "active"],
            ["name" => "Viewer Readonly", "username" => "viewer",         "password" => Hash::make("password"), "role" => "viewer",      "status_active" => "active"],
            ["name" => "Aproval Keuangan", "username" => "aproval",       "password" => Hash::make("password"), "role" => "aproval",     "status_active" => "active"],
            ["name" => "Operator Data",   "username" => "7020001",        "password" => Hash::make("7020001"),  "role" => "operator",    "status_active" => "active"],
        ];
        foreach ($users as $user) {
            \App\Models\User::firstOrCreate(["username" => $user["username"]], $user);
        }
    }
}
