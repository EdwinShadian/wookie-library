<?php

declare(strict_types=1);

namespace Tests\Feature\PublicApi;

use App\Models\Book;
use Tests\TestCase;

class BookControllerTest extends TestCase
{
    public static function indexDataProvider(): array
    {
        return [
            ['query' => ''],
            ['query' => 'Romeo and Juliet'],
        ];
    }

    /**
     * Check if we can get list of books with searching by title
     *
     * @dataProvider indexDataProvider
     */
    public function testIndex(string $query): void
    {
        Book::factory()->count(5)->create();
        Book::factory()->create(['title' => 'Romeo and Juliet']);

        if (empty($query)) {
            $response = $this->get('api/public/books?perPage=5');
        } else {
            $response = $this->get("api/public/books?perPage=5&q=$query");
        }

        $response->assertStatus(200);

        $data = json_decode($response->getContent(), true)['data'];

        if (empty($query)) {
            $this->assertCount(5, $data);
            $this->assertJson(Book::all()->toJson(), json_encode($data));
        } else {
            $this->assertJson(
                Book::where('title', 'Romeo and Juliet')->first()->toJson(),
                json_encode($data)
            );
        }
    }

    /**
     * Check if we can get book's details
     */
    public function testShow(): void
    {
        $book = Book::factory()->create();

        $response = $this->get("api/public/books/$book->id");
        $response->assertOk();

        $data = json_decode($response->getContent(), true)['data'];
        $this->assertJson($book->toJson(), json_encode($data));
    }
}
