<?php

namespace Database\Seeders;

use Carbon\Carbon;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use App\Enum\RolesEnum;
use App\Enum\GenericActionEnum;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Enum\UserSpecificActionEnum;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        /**
         * Generate roles & permissions
         */
        $superAdminRole = Role::create(['name' => RolesEnum::SuperAdmin->value]);
        $adminRole = Role::create(['name' => RolesEnum::Admin->value]);
        $userRole = Role::create(['name' => RolesEnum::User->value]);

        $models = ['user'];

        $permissions = [];

        foreach ($models as $model) {
            foreach (GenericActionEnum::cases() as $action) {
                $permissions[] = "{$action->value}_{$model}";
            }
        }

        foreach (UserSpecificActionEnum::cases() as $action) {
            $permissions[] = $action->value;
        }

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        $superAdminRole->syncPermissions($permissions);
        $adminRole->syncPermissions($permissions);
        $readPermissions = array_filter($permissions, fn($p) => str_starts_with($p, 'view_'));
        $userRole->syncPermissions($readPermissions);

        $superadmin = User::create([
            'username' => 'superadmin',
            'name' => 'Super Admin',
            'email' => 'superadmin@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => Carbon::now()
        ])->assignRole(RolesEnum::SuperAdmin);

        $admin = User::create([
            'username' => 'admin',
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => Carbon::now()
        ])->assignRole(RolesEnum::Admin);

        $user = User::create([
            'username' => 'User',
            'name' => 'user',
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => Carbon::now()
        ])->assignRole(RolesEnum::User);

        $users = [
            ['operator', 'Operator', '$2y$13$h/MfR82WKgLp/8v3xNSmBOrZmxL2PMXD53yKwYjvB4UqJFZ7hH0ZC'],
            ['operator1', 'Operator PTR', '$2y$13$F/sHMytF4EHPiQJ91/SkhuZUe4vMb2JYInP.XxySuSwj6C6keDWuC'],
            ['operator2', 'Operator PTR', '$2y$13$GNAVShSUHH1eknuTyhTqVuJsDUOE4MKynVqvDOh6N479xVOwzu0Mu'],
            ['op_bantul', 'Operator Bantul', '$2y$13$ovDGrGb.K.sd5qYTp.DdQ.yDHfwtOlfKB7ePMcE7V/JnLYwO.4G/S'],
            ['op_gunungkidul', 'Operator Gunungkidul', '$2y$13$3FRvkSFlEHJgyCLFrcGnlu7kRPBSAF01zgeBFAX7MIzNgHJNML/AG'],
            ['op_sleman', 'Operator Sleman', '$2y$13$jzxm/EZuGIi2ubiDo.UdxeXKMmlyVBRaj2tuAsb2KE73CRfqyTvf2'],
            ['op_yogyakarta', 'Operator Yogyakarta', '$2y$13$DcEJ9MuJCfbxlgk8IoC4Ne4crwzZVZEGNLSoE2oLgEUfip3CVjv.m'],
            ['op_kulonprogo', 'Operator Kulonprogo', '$2y$13$O3tyHWPOiLQG2W5Yzgs4deaEl.RuZOxh0jHBEPMxbBH/9ROG4AJ1S'],
            ['op_kadipaten', 'Operator Kadipaten', '$2y$13$UKVwrpmoov3GpeVlFZEL/eXdOxv0XRuKVCe/p0kmgFLwy3mTEji8W'],
            ['paniradya', 'Paniradya', '$2y$13$fJjxAgQLSeXDpCWwkh52iODz2seneH7zdYNenR6lPuG0b3Aks/yMC'],
            ['pengelolaan', 'Pengelolaan', '$2y$13$yrzfGUmMZCc/nLkRM.K7EORQ2cZn0/pfd0r5fWg7JH4/gNFBw1JiS'],
            ['commandcenter', 'Command Center', '$2y$13$14/2.P7WyFMrJJ/umKR/lOdDLWb5vJ7sKFE/ZGXmAG/c0X5U0oN5O'],
        ];

        foreach ($users as [$username, $name, $password]) {
            User::create([
                'username' => $username,
                'name' => $name,
                'email' => $username . '@example.com',
                'password' => Hash::make('password'),
                'email_verified_at' => Carbon::now()
            ])->assignRole(RolesEnum::User);
        }
    }
}
