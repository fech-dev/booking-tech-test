<?php

use App\Models\Author;

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
