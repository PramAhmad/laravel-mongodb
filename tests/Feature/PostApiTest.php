<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PostApiTest extends TestCase{
    use WithFaker;
            /**
             * A basic feature test example.
             */
        /**
         * @test
         */
        public function get_post_test(): void
        {
            $response = $this->get('/api/posts');
            $response->assertStatus(200);
        }

        /**
         * @test
         */
        public function get_invalid_page_test(): void
        {
            $response = $this->get('/api/posts?page=1000');
            $response->assertStatus(404);
        }

        /**
         * @test
         */
        public function create_post_test(): void
        {
            $response = $this->post('/api/posts', [
                'title' => $this->faker->sentence(),
                'desc' => $this->faker->paragraph(),
                'slug' => $this->faker->slug()
            ]);
            
            $response->assertStatus(201);
           
            
        }

        /**
         * @test
         */
        public function create_post_test_with_invalid_data(): void
        {
            $response = $this->post('/api/posts', [
                'title' => $this->faker->sentence, 
                'desc' => '', 
                'slug' => '' // Data yang tidak valid
            ]);
            $response->assertStatus(422)
                     ->assertJsonValidationErrors(['desc', 'slug']);
        }

        /**
         * @test
         */
        public function create_post_test_with_duplicate_title(): void
        {
            DB::beginTransaction();
            $response1 = $this->post('/api/posts', [
                'title' => 'Title yang sama',
                'desc' => $this->faker->paragraph(),
                'slug' => $this->faker->slug()
            ]);
            $response1->assertStatus(201);
            $response2 = $this->post('/api/posts', [
                'title' => 'Title yang sama',
                'desc' => $this->faker->paragraph(),
                'slug' => $this->faker->slug()
            ]);
            $response2->assertStatus(422)
                      ->assertJsonValidationErrors(['title']);
            DB::rollBack();


        }

}
