<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Models\Book;
use Illuminate\Http\JsonResponse;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $books = Book::with('authors')->paginate();

        return response()->json($books, JsonResponse::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBookRequest $request): JsonResponse
    {
        $data = $request->except('author_id');

        $book = Book::create($data);
        $book->authors()->attach($request->get('author_ids'));

        $book->load('authors');

        return response()->json($book, JsonResponse::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(Book $book): JsonResponse
    {
        $book->load('authors');

        return response()->json($book, JsonResponse::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBookRequest $request, Book $book): JsonResponse
    {
        $data = $request->except('author_ids');
        $book->update($data);

        if ($request->has('author_ids')) {
            $book->authors()->sync($request->get('author_ids'));
        }

        $book->load('authors')->refresh();

        return response()->json($book, JsonResponse::HTTP_OK);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Book $book)
    {
        $book->authors()->detach();
        $book->delete();

        return response()->json(null, JsonResponse::HTTP_OK);
    }
}
