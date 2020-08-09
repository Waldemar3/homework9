<?php
require_once("../vendor/autoload.php");
require_once("../config/eloquent.php");

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    public function posts(){
        return $this->hasMany(Post::class);
    }

    public static function addUser($email, $password){
        $user = new self();
        $user->email = $email;
        $user->password = $password;
        $user->save();
    }

    public static function addPost($user_id, $author, $title, $text){
        $user = self::find($user_id);

        $post = new Post;
        $post->author = $author;
        $post->title = $title;
        $post->text = $text;

        $user->posts()->save($post);
    }
}
class Post extends Model{
    public function tg(){
        return $this->hasOne(Post::class);
    }
    public function tags(){
        return $this->belongsToMany(Tag::class);
    }
    public static function addRandomTags($post_id){
        $id = [];

        foreach (self::all() as $post){
            $id[] = $post->id;
        }

        $posts = Post::find($post_id);
        $posts->tags()->attach([$id[array_rand($id)], $id[array_rand($id)], $id[array_rand($id)]]);
    }
    public static function addTg($post_tg, $name){
        $post = Post::find($post_tg);

        $tg = new Telegram;
        $tg->name = $name;

        $post->tg()->save($tg);
    }
}
class Tag extends Model{
    public function posts(){
        return $this->belongsToMany(Post::class);
    }
    public static function addTag($tagName){
        $name = new self();
        $name->tag = $tagName;
        $name->save();
    }
}
class Telegram extends Model{}

$faker = Faker\Factory::create();

function maxLen($maxLen){
    return function($string) use ($maxLen){
        return strlen($string) <= $maxLen;
    };
}

for ($i = 0; $i < 10; $i++) {
    Tag::addTag($faker->valid(maxLen(100))->sentence);
}

for ($i = 0; $i < 3; $i++) {
    User::addUser($faker->valid(maxLen(50))->email, $faker->valid(maxLen(20))->password);
}

for ($i = 0; $i < 5; $i++){
    foreach(User::all() as $user){
        User::addPost($user->id, $faker->valid(maxLen(16))->name, $faker->valid(maxLen(100))->title, $faker->text);
    }
}
foreach(Post::all() as $post){
    Post::addRandomTags($post->id);
    Post::addTg($post->id, $faker->valid(maxLen(80))->uuid);
}






