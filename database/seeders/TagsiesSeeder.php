<?php

namespace Database\Seeders;

use App\Models\Bookmark;
use App\Models\Folder;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use PhpParser\Node\Stmt\For_;

class TagsiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::factory()->create();
        $tags = Tag::factory()->count(30)->create();

        $lists = Folder::factory()->count(10)->create()->each(function (Folder $folder) use ($tags) {
            if (random_int(0, 1)) {
                $folder->tags()->sync($tags->random(random_int(1, 4))->pluck('id'));
            }
        });

        // Generate links and attach tags to them
        Bookmark::factory()->count(50)->create()->each(function (Bookmark $link) use ($tags, $lists) {
            if (random_int(0, 1)) {
                // Attach a random number of tags to the link
                $link->tags()->sync($tags->random(random_int(1, 8))->pluck('id'));
            }

            if (random_int(0, 1)) {
                // Attach a random number of tags to the link
                $link->folders()->sync($lists->random(random_int(1, 2))->pluck('id'));
            }
        });
    }
}
