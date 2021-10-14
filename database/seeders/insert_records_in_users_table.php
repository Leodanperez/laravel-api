<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class insert_records_in_users_table extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = [
            [
                'name' => 'Leodan Perez',
                'email' => 'admin@admin.com',
                'password' => bcrypt('123456')
            ],
            [
                'name' => 'Jhon',
                'email' => 'jhon@admin.com',
                'password' => bcrypt('123456')
            ]
        ];
        User::insert($users);
    }
}
