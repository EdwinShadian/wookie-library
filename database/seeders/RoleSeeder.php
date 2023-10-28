<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = json_decode(file_get_contents(__DIR__.'/data/roles.json'), true);

        foreach ($roles as $role) {
            Role::firstOrCreate([
                'name' => $role['name'],
                'description' => $role['description'],
            ]);
        }
    }
}
