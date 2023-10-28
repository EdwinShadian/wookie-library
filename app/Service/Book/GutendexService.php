<?php

declare(strict_types=1);

namespace App\Service\Book;

use GuzzleHttp\Client;

class GutendexService implements BookServiceIntegrationInterface
{
    public function __construct(
        private Client $client,
    ) {
    }

    /**
     * @inheritDoc
     */
    public function getBooks(int $page): array
    {
        $response = $this->client->get("https://gutendex.com/books/?page=$page");

        $data = json_decode($response->getBody()->getContents(), true);

        $booksData = [];

        foreach ($data['results'] as $book) {
            $booksData[] = [
                'title' => $book['title'],
                'author' => $book['authors'][0]['name'] ?? null,
                'description' => implode(' ', $book['subjects']),
                'cover_url' => $book['formats']['image/jpeg'] ?? null,
                'price' => $book['price'] ?? fake()->randomFloat(2, 10, 60),
            ];
        }

        return $booksData;
    }
}
