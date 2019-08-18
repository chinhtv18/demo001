<?php

use Illuminate\Database\Seeder;
use App\Models\Staff;

class StaffsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Staff::query()->truncate();
        Staff::create([
            'email' => 'test001@vti.test',
            'first_name' => 'test',
            'last_name' => '001',
            'sex' => 1,
            'address' => 'Hải Phòng',
            'birthday' => '2019-04-23'
        ]);
        Staff::create([
            'email' => 'test002@vti.test',
            'first_name' => 'test',
            'last_name' => '002',
            'sex' => 1,
            'address' => 'Thai Binh',
            'birthday' => '2019-06-28'
        ]);
        Staff::create([
            'email' => 'test003@vti.test',
            'first_name' => 'test',
            'last_name' => '003',
            'sex' => 1,
            'address' => 'Hà Nội',
            'birthday' => '2019-06-28'
        ]);
    }
}
