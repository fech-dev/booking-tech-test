<?php

use App\Models\Author;

use function Pest\Laravel\patchJson;
use function Pest\Laravel\postJson;

describe('POST /authors', function () {

    test('Can create an author', function () {
        $route = route('authors.store');
        $data = Author::factory()->raw();

        postJson($route, $data)
            ->assertCreated()
            ->assertJson([
                'id' => 1,
                ...$data,
            ]);
    });

    test('Cannot create author if firstname or lastname is not given', function () {
        $route = route('authors.store');
        $data = [];

        postJson($route, $data)
            ->assertUnprocessable()
            ->assertJsonValidationErrorFor('firstname')
            ->assertJsonValidationErrorFor('lastname');
    });
});

describe('PATCH|PUT /authors/{id}', function () {
    test('Can update an author\'s lastname', function () {
        $author = Author::factory()->create();
        $route = route('authors.update', ['author' => $author->id]);

        $data = [
            'lastname' => fake()->lastName(),
        ];

        patchJson($route, $data)
            ->assertOk()
            ->assertJson([
                'id' => $author->id,
                'firstname' => $author->firstname,
                'lastname' => $data['lastname'],
            ]);
    });

    test('Can update an author\'s firstname', function () {
        $author = Author::factory()->create();
        $route = route('authors.update', ['author' => $author->id]);

        $data = [
            'firstname' => fake()->firstName(),
        ];

        patchJson($route, $data)
            ->assertOk()
            ->assertJson([
                'id' => $author->id,
                'firstname' => $data['firstname'],
                'lastname' => $author->lastname,
            ]);
    });
});

describe('POST /authors/{id}', function () {
    test('Can update an author', function () {
        $author = Author::factory()->create();
        $route = route('authors.update', ['author' => $author->id]);

        $data = [
            'lastname' => fake()->lastName(),
        ];

        patchJson($route, $data)
            ->assertOk()
            ->assertJson([
                'id' => $author->id,
                'firstname' => $author->firstname,
                'lastname' => $data['lastname'],
            ]);
    });
});
