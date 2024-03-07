<?php

namespace Database\Seeders;

use App\Domains\Emoji\Models\Emoji;
use Database\Seeders\Traits\DisableForeignKeys;
use Database\Seeders\Traits\TruncateTable;
use DB;
use Illuminate\Database\Seeder;

class EmojiSeeder extends Seeder
{
    use DisableForeignKeys, TruncateTable;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->disableForeignKeys();
        DB::table('emojis')->delete();

        $emojis = [
            ':heart:',
            ':heart_eyes:',
            ':smile:',
            ':smiling_face_with_tear:',
            ':fearful:',
            ':rage:',
            ':+1:',
            ':-1:',
            ':ok_hand:',
        ];

        foreach ($emojis as $emoji) {
            Emoji::create([
                'content' => $emoji
            ]);
        }

        $this->enableForeignKeys();
    }
}
