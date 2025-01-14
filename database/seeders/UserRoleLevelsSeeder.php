<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserRoleLevelsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $arr1['key'] = 'root';
        $arr1['value'] = 'Super Admin';
        DB::table('user_role_levels')->insert($arr1);

        $arr2['key'] = 'view:approve:add:edit:delete';
        $arr2['value'] = 'Admin';
        DB::table('user_role_levels')->insert($arr2);

        $arr3['key'] = 'view:approve';
        $arr3['value'] = 'Approver';
        DB::table('user_role_levels')->insert($arr3);

        $arr4['key'] = 'view:add:edit';
        $arr4['value'] = 'User Manager';
        DB::table('user_role_levels')->insert($arr4);

        $arr5['key'] = 'view';
        $arr5['value'] = 'User';
        DB::table('user_role_levels')->insert($arr5);
    }
}
