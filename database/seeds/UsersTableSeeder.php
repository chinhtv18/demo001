<?php

use Illuminate\Database\Seeder;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::query()->truncate();
        User::create([
            'email' => 'admin@vti.test',
            'password' => Hash::make('secret'),
            'first_name' => 'admin',
            'last_name' => '',
            'sex' => 1,
            'address' => 'Cầu Giấy - Hà Nội',
            'birthday' => '1993-05-31',
            'is_active' => 1,
        ]);
        User::create([
            'email' => 'test001@vti.test',
            'password' => Hash::make('123'),
            'first_name' => 'Test',
            'last_name' => '001',
            'sex' => 1,
            'address' => 'Hoàn Kiếm - Hà Nội',
            'birthday' => '1992-04-30',
            'is_active' => 1,
        ]);

        User::create([
            'email' => 'test002@vti.test',
            'password' => Hash::make('456'),
            'first_name' => 'Test',
            'last_name' => '001',
            'sex' => 1,
            'address' => 'Hoàn Kiếm - Hà Nội',
            'birthday' => '1992-04-30',
            'is_active' => 0,
        ]);



    }
}
