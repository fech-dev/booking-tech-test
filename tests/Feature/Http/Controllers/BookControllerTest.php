<?php

use App\Models\Author;
use App\Models\Book;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\deleteJson;
use function Pest\Laravel\getJson;
use function Pest\Laravel\patchJson;
use function Pest\Laravel\postJson;

beforeEach(function () {
    $this->withHeaders([
        'Authorization' => config('app.api_key'),
    ]);
});

describe('POST /books', function () {
    test('can create a book', function () {
        $author = Author::factory()->create();
        $data = Book::factory()->raw();

        $author_ids = [$author->id];

        $route = route('books.store');

        postJson($route, [
            ...$data,
            'author_ids' => $author_ids,
        ])
            ->assertCreated()
            ->assertJson($data);
        // ->assertJsonPath('authors.0', $author->toArray());

        assertDatabaseHas('books', $data);

        $book = Book::select('id')->whereTitle($data['title'])->first();
        assertDatabaseHas('author_book', ['book_id' => $book->id, 'author_id' => $author->id]);

    });
});

describe('GET /books', function () {
    test('can get a paginated list of books', function () {
        $author = Author::factory()->create();
        $books = Book::factory(5)->create();
        $author->books()->attach($books->pluck('id'));

        $route = route('books.index');

        getJson($route)
            ->assertOk()
            ->assertJsonCount(5, 'data')
            ->assertJsonPath('total', 5)
            ->assertJsonPath('current_page', 1);
    });
});

describe('GET /books/{id}', function () {
    test('can get a book', function () {
        $author = Author::factory()->create();
        $book = Book::factory()->create();
        $author->books()->attach($book->id);

        $route = route('books.show', ['book' => $book->id]);

        getJson($route)
            ->assertOk()
            ->assertJson([
                'title' => $book->title,
                'subtitle' => $book->subtitle,
                'publisher' => $book->publisher,
                'description' => $book->description,
            ]);
    });
});

describe('PATCH|PUT /books/{id}', function () {
    test('can update a book', function () {
        $author = Author::factory()->create();
        $book = Book::factory()->create();
        $author->books()->attach($book->id);

        $route = route('books.update', ['book' => $book->id]);

        $newSubtitle = fake()->paragraph();

        $expectedBook = [
            'title' => $book->title,
            'subtitle' => $newSubtitle,
            'publisher' => $book->publisher,
            'description' => $book->description,
        ];

        patchJson($route, ['subtitle' => $newSubtitle])
            ->assertOk()
            ->assertJson($expectedBook);

        assertDatabaseHas('books', $expectedBook);
    });

    test('can update book\'s authors', function () {
        $author = Author::factory(2)->create();
        $book = Book::factory()->create();
        $author[0]->books()->attach($book->id);

        $route = route('books.update', ['book' => $book->id]);

        $expectedBook = [
            'title' => $book->title,
            'subtitle' => $book->subtitle,
            'publisher' => $book->publisher,
            'description' => $book->description,
        ];

        patchJson($route, ['author_ids' => $author->pluck('id')])
            ->assertOk()
            ->assertJson($expectedBook)
            ->assertJsonCount(2, 'authors');

        assertDatabaseHas('books', $expectedBook);
    });
});

describe('DESTROY /books/{id}', function () {
    test('Can destroy a book', function () {
        $author = Author::factory()->create();
        $book = Book::factory()->create();
        $author->books()->attach($author->id);

        $route = route('books.destroy', ['book' => $book->id]);

        deleteJson($route)->assertOk();

        assertDatabaseMissing('books', ['id' => $author->id]);
        assertDatabaseMissing('author_book', ['book_id' => $book->id, 'author_id' => $author->id]);
    });

});
