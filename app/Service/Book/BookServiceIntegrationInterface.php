<?php

declare(strict_types=1);

namespace App\Service\Book;

interface BookServiceIntegrationInterface
{
    /**
     * Get books from web service
     */
    public function getBooks(int $page): array;
}
