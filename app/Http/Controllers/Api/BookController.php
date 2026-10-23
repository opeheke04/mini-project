<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookResource;
use App\Models\Book;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BookController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return BookResource::collection(Book::query()->orderBy('id')->get());
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['nullable', 'integer', 'min:0'],
        ]);

        $book = Book::create($validated);

        return (new BookResource($book))
            ->response()
            ->setStatusCode(201);
    }

    public function show(string $id): BookResource|JsonResponse
    {
        $book = Book::find($id);

        if (! $book) {
            return response()->json(['message' => 'Book not found'], 404);
        }

        return new BookResource($book);
    }

    public function update(Request $request, string $id): BookResource|JsonResponse
    {
        $book = Book::find($id);

        if (! $book) {
            return response()->json(['message' => 'Book not found'], 404);
        }

        $isPartialUpdate = $request->isMethod('PATCH');
        $required = $isPartialUpdate ? 'sometimes' : 'required';
        $present = $isPartialUpdate ? 'sometimes' : 'present';

        $validated = $request->validate([
            'title' => [$required, 'string', 'max:255'],
            'description' => [$present, 'nullable', 'string'],
            'status' => [$present, 'nullable', 'integer', 'min:0'],
        ]);

        $book->update($validated);

        return new BookResource($book->refresh());
    }

    public function destroy(string $id): JsonResponse
    {
        $book = Book::find($id);

        if (! $book) {
            return response()->json(['message' => 'Book not found'], 404);
        }

        $book->delete();

        return response()->json(['message' => 'Book deleted'], 200);
    }
}
