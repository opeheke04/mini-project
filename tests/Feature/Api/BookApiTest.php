<?php

namespace Tests\Feature\Api;

use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_books_can_be_listed_as_a_resource_collection(): void
    {
        $book = Book::query()->create([
            'title' => 'Laravel API',
            'description' => 'REST API practice',
            'status' => 1,
        ]);

        $this->getJson('/api/books')
            ->assertOk()
            ->assertJsonPath('data.0.id', $book->id)
            ->assertJsonPath('data.0.title', 'Laravel API')
            ->assertJsonPath('data.0.status', 1);
    }

    public function test_a_book_can_be_created_and_shown(): void
    {
        $response = $this->postJson('/api/books', [
            'title' => 'Laravel API',
            'description' => 'REST API practice',
            'status' => 1,
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('data.title', 'Laravel API')
            ->assertJsonPath('data.description', 'REST API practice')
            ->assertJsonPath('data.status', 1);

        $bookId = $response->json('data.id');

        $this->assertDatabaseHas('books', [
            'id' => $bookId,
            'title' => 'Laravel API',
        ]);

        $this->getJson("/api/books/{$bookId}")
            ->assertOk()
            ->assertJsonPath('data.id', $bookId);
    }

    public function test_a_book_can_be_updated_with_put_or_patch(): void
    {
        $book = Book::query()->create([
            'title' => 'Original title',
            'description' => 'Description',
            'status' => 1,
        ]);

        $this->patchJson("/api/books/{$book->id}", ['status' => 0])
            ->assertOk()
            ->assertJsonPath('data.status', 0)
            ->assertJsonPath('data.title', 'Original title');

        $this->putJson("/api/books/{$book->id}", [
            'title' => 'Updated title',
            'description' => null,
            'status' => 0,
        ])
            ->assertOk()
            ->assertJsonPath('data.title', 'Updated title')
            ->assertJsonPath('data.description', null)
            ->assertJsonPath('data.status', 0);
    }

    public function test_a_book_can_be_deleted(): void
    {
        $book = Book::query()->create(['title' => 'Delete me']);

        $this->deleteJson("/api/books/{$book->id}")
            ->assertOk()
            ->assertExactJson(['message' => 'Book deleted']);

        $this->assertDatabaseMissing('books', ['id' => $book->id]);
    }

    public function test_book_endpoints_return_json_errors_for_invalid_requests(): void
    {
        $this->postJson('/api/books', ['status' => -1])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['title', 'status']);

        $this->getJson('/api/books/999')
            ->assertNotFound()
            ->assertExactJson(['message' => 'Book not found']);
    }
}
