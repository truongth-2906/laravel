<?php

namespace Database\Seeders;

use Database\Seeders\Traits\DisableForeignKeys;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TimezoneSeeder extends Seeder
{
    use DisableForeignKeys;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->disableForeignKeys();
        DB::table('timezones')->truncate();
        $timestamp = time();

        $cities = [
            'London' => '+00:00',
            'Paris' => '+01:00',
            'Cairo' => '+02:00',
            'Moscow' => '+03:00',
            'Tehran' => '+03:30',
            'Abu Dhabi' => '+04:00',
            'Kabul' => '+04:30',
            'Islamabad' => '+05:00',
            'New Delhi' => '+05:30',
            'Kathmandu' => '+05:45',
            'Astana' => '+06:00',
            'Yangon' => '+06:30',
            'Bangkok' => '+07:00',
            'Beijing' => '+08:00',
            'Eucla' => '+08:45',
            'Tokyo' => '+09:00',
            'Adelaide' => '+09:30',
            'Sydney' => '+10:00',
            'Adelaide ' => '+10:30',
            'Solomon Islands' => '+11:00',
            'Wellington' => '+12:00',
            "Nuku'alofa" => '+13:00',
            'Chatham Islands' => '+13:45',
            'Kiritimati' => '+14:00',
            'Azores' => '-01:00',
            'Mid-Atlantic' => '-02:00',
            'Brasilia' => '-03:00',
            'Newfoundland and Labrador' => '-03:30',
            'Santiago' => '-04:00',
            'New York' => '-05:00',
            'Mexico City' => '-06:00',
            'Arizona' => '-07:00',
            'Los Angeles' => '-08:00',
            'Alaska' => '-09:00',
            'Polynesia' => '-09:30',
            'Hawaii' =>  '-10:00',
            'Samoa' =>  '-11:00',
        ];

        $zones = [];
        foreach (timezone_identifiers_list() as $zone) {
            date_default_timezone_set($zone);
            $offset = date('P', $timestamp);
            $city = array_search($offset, $cities);

            $zones[$offset]['offset'] = $offset;
            $zones[$offset]['diff_from_gtm'] = 'GMT' . $offset;
            $zones[$offset]['city'] = $city !== false ? $city : "Updating";
            $zones[$offset]['created_at'] = now();
            $zones[$offset]['updated_at'] = now();
        }
        uasort($zones, function ($a, $b) {
            return strtotime($b['offset']) - strtotime($a['offset']);
        });
        DB::table('timezones')->insert($zones);

        $this->enableForeignKeys();
    }
}
