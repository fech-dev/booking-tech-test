<?php

use App\Models\Author;

use function Pest\Laravel\getJson;
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

describe('GET /authors', function () {
    beforeEach(function () {
        Author::factory(20)->create();
    });

    test('Can get a paginated list of authors (first page)', function () {
        $route = route('authors.index');

        getJson($route)
            ->assertOk()
            ->assertJsonCount(15, 'data')
            ->assertJsonPath('total', 20);
    });

    test('Can get a paginated list of authors (second page)', function () {
        $route = route('authors.index', ['page' => 2]);

        getJson($route)
            ->assertOk()
            ->assertJsonCount(5, 'data')
            ->assertJsonPath('current_page', 2)
            ->assertJsonPath('total', 20);
    });
});

describe('GET /authors/{id}', function () {
    test('Can get an author', function () {
        $author = Author::factory()->create();
        $route = route('authors.show', ['author' => $author->id]);

        getJson($route)
            ->assertOk()
            ->assertJson([
                'id' => $author->id,
                'firstname' => $author->firstname,
                'lastname' => $author->lastname,
            ]);
    });

    test('Should respond with 404 error if author is not found', function () {
        $route = route('authors.show', ['author' => 123434]);

        getJson($route)
            ->assertNotFound();
    });
});
