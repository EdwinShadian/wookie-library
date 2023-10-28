<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (User::count() > 0) {
            return;
        }

        $wookie = User::factory()->create([
            'name' => 'Brief Lohgarra',
            'author_pseudonym' => 'Wookie23',
        ]);

        $wookie->roles()->attach(Role::where('name', Role::ROLE_ADMIN)->first()->id);

        $yoda = User::factory()->create([
            'name' => 'Yoda',
            'author_pseudonym' => 'The One With Force I Am',
        ]);

        $yoda->roles()->attach(Role::where('name', Role::ROLE_AUTHOR)->first()->id);
        $yoda->roles()->attach(Role::where('name', Role::ROLE_PUBLISHER)->first()->id);

        $vader = User::factory()->create([
            'name' => 'Darth Vader',
            'author_pseudonym' => 'The Dark Lord',
        ]);

        $vader->roles()->attach(Role::where('name', Role::ROLE_AUTHOR)->first()->id);
    }
}
