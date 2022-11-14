<?php

namespace Database\Seeders;

use App\Models\Bookmark;
use App\Models\Folder;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TagsiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Generate users, categories and tags
        // User::factory()->create();
        $tags = Tag::factory()->count(15)->create();
        $folders = Folder::factory()->count(8)->create()->each(function (Folder $folder) use ($tags) {
            if (random_int(0, 1)) {
                // Attach a random number of tags to the folder
                $folder->tags()->sync($tags->random(random_int(1, 8)));
            }
        });
        // Generate bookmarks and attach tags to them
        Bookmark::factory()->count(30)->create()->each(function (Bookmark $bookmark) use ($tags, $folders) {
            if (random_int(0, 1)) {
                // Attach a random number of tags to the link
                $bookmark->tags()->sync($tags->random(random_int(1, 8)));
            }
            if (random_int(0, 1)) {
                // Attach a random number of folders to the link
                $bookmark->folders()->attach($folders->random(random_int(1, 8)));
            }
        });
    }
}
