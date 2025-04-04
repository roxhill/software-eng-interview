<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Comment;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Create a special user with a fixed username for N+1 testing.
        $specialUser = User::factory()->create([
            'username' => 'bigbob',
        ]);

        // Create 20 additional users.
        $otherUsers = User::factory()->count(250)->create();

        // Merge the special user into a single collection of users.
        $users = $otherUsers->push($specialUser);

        // For the special user, use syncWithoutDetaching to attach all other users as followers.
        $specialUser->followers()->syncWithoutDetaching($otherUsers->pluck('id')->toArray());

        // For each of the other users, attach a random set of followers.
        foreach ($otherUsers as $user) {
            $randomFollowers = $users->random(rand(0, 5));
            // Ensure we have an array of IDs whether one or many are returned.
            $followerIds = is_iterable($randomFollowers)
                ? $randomFollowers->pluck('id')->toArray()
                : [$randomFollowers->id];
            // Use syncWithoutDetaching to prevent duplicate pivot entries.
            $user->followers()->syncWithoutDetaching($followerIds);
        }

        // Create 50 articles for the special user to magnify the N+1 issue.
        $specialArticles = Article::factory()
            ->count(500)
            ->create(['author_id' => $specialUser->id]);

        // Create 100 articles for random other users.
        $otherArticles = Article::factory()
            ->count(300)
            ->state(new Sequence(fn() => [
                'author_id' => $otherUsers->random()->id,
            ]))
            ->create();

        // Merge all articles.
        $articles = $specialArticles->merge($otherArticles);

        // Create 20 tags.
        $tags = Tag::factory()->count(20)->create();

        // Attach random tags and favored users to each article.
        foreach ($articles as $article) {
            $randomTags = $tags->random(rand(0, 6));
            $tagIds = is_iterable($randomTags)
                ? $randomTags->pluck('id')->toArray()
                : [$randomTags->id];
            $article->tags()->attach($tagIds);

            $randomFavored = $users->random(rand(0, 8));
            $favoredIds = is_iterable($randomFavored)
                ? $randomFavored->pluck('id')->toArray()
                : [$randomFavored->id];
            $article->favoredUsers()->attach($favoredIds);
        }

        // Create 360 comments across articles.
        Comment::factory()
            ->count(320)
            ->state(new Sequence(fn() => [
                'article_id' => $articles->random()->id,
                'author_id' => $users->random()->id,
            ]))
            ->create();
    }
}
