<?php

namespace Tests\Feature;

use App\Models\Place;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Testing\Fluent\AssertableJson;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class PlaceTest extends TestCase
{
    use DatabaseMigrations;
    use WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function testIndexShouldReturnElements()
    {
        $response = $this->get('/api/places');

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson(
            fn(AssertableJson $json) =>
            $json->where('total', 50)
                ->where('per_page', 15)
                ->where('current_page', 1)
                ->etc()
        );
    }

    public function testIndexShouldFilter()
    {
        $this->artisan('migrate:fresh');
        Place::create([
            'name' => 'Place',
            'city' => 'City',
            'state' => 'State',
            'slug' => 'place-city-state'
        ]);
        Place::create([
            'name' => 'TestName',
            'city' => 'TestCity',
            'state' => 'TestState',
            'slug' => 'test-place-city-state'
        ]);
        $queryParams = '?name=test';

        $response = $this->get('/api/places' . $queryParams);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson(
            fn(AssertableJson $json) =>
            $json->where('total', 1)
                ->where('per_page', 15)
                ->where('current_page', 1)
                ->etc()
        );
    }

    public function testIndexShouldPaginate()
    {
        $queryParams = '?page=2';

        $response = $this->get('/api/places' . $queryParams);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson(
            fn(AssertableJson $json) =>
            $json->where('total', 50)
                ->where('per_page', 15)
                ->where('current_page', 2)
                ->etc()
        );
    }

    public function testShowWithoutDataReturnNotFound()
    {
        $this->artisan('migrate:fresh');

        $response = $this->get("/api/places/1");

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testShowShouldReturnElement()
    {
        $place = Place::factory()->create();

        $response = $this->get("/api/places/{$place->id}");

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson([
            'success' => true,
            'data' => $place->toArray(),
        ]);
    }

    public function testCreateShouldCreateElement()
    {
        $requestData = [
            'name' => $this->faker->name,
            'city' => $this->faker->city,
            'state' => $this->faker->name,
        ];

        $response = $this->post('/api/places', $requestData);

        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJson([
            'success' => true,
            'data' => $requestData,
        ]);
    }

    public function testCreateIncorrectParamsMustThrowError()
    {
        $requestData = [
            'name' => $this->faker->name,
            'city' => $this->faker->city,
        ];
        $this->expectException(ValidationException::class);
        $response = $this->withoutExceptionHandling()->post('/api/places', $requestData);
    }

    public function testCreateWithDuplicateRecordException()
    {
        $requestData = [
            'name' => 'Place',
            'city' => 'City',
            'state' => 'State',
        ];

        Place::create([
            'name' => 'Place',
            'city' => 'City',
            'state' => 'State',
            'slug' => 'place-city-state'
        ]);
        $response = $this->post('/api/places', $requestData);

        $response->assertStatus(Response::HTTP_CONFLICT);
        $response->assertJson([
            'success' => false,
            'message' => "A record with the same slug already exists."
        ]);
    }

    public function testUpdateShouldUpdateElement()
    {
        $place = Place::factory()->create();
        $updatedData = [
            'name' => $this->faker->name,
            'city' => $this->faker->city,
            'state' => $this->faker->name,
        ];

        $response = $this->put("/api/places/{$place->id}", $updatedData);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson([
            'success' => true,
            'data' => $updatedData,
        ]);
    }

    public function testUpdateIncorrectParamsMustThrowError()
    {
        $place = Place::factory()->create();
        $updatedData = [
            'name' => $this->faker->name,
            'city' => $this->faker->city,
        ];

        $this->expectException(ValidationException::class);

        $response = $this->withoutExceptionHandling()->put("/api/places/{$place->id}", $updatedData);

    }

    public function testUpdateWithDuplicateRecordException()
    {
        $place = Place::factory()->create();

        Place::create([
            'name' => 'Place',
            'city' => 'City',
            'state' => 'State',
            'slug' => 'place-city-state'
        ]);

        $updatedData = [
            'name' => 'Place',
            'city' => 'City',
            'state' => 'State',
        ];

        $response = $this->put("/api/places/{$place->id}", $updatedData);

        $response->assertStatus(Response::HTTP_CONFLICT);
        $response->assertJson([
            'success' => false,
            'message' => "A record with the same slug already exists."
        ]);

    }

    public function testDestroyShouldDeleteElement()
    {
        $place = Place::factory()->create();

        $response = $this->delete("/api/places/{$place->id}");

        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }
}