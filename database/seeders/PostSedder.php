<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
class PostSedder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        for($i =0; 1 <1000; $i++){
            DB::table('posts')->insert([
                'title' => $faker->title(),
                'desc' => $faker->text(200),
                'slug' => $faker->slug()
            ]);
        }
    }
}
