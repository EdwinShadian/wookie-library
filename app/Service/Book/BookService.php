<?php

declare(strict_types=1);

namespace App\Service\Book;

use App\Models\Book;
use App\Models\Role;
use App\Models\User;
use App\Service\FileService;
use Illuminate\Validation\UnauthorizedException;

class BookService
{
    public function __construct(
        private BookServiceIntegrationInterface $bookServiceIntegration,
        private FileService $fileService,
    ) {
    }

    public function addBooksToLibrary(int $count): void
    {
        $bookCount = 0;
        $page = 1;

        while ($bookCount < $count) {
            $books = $this->bookServiceIntegration->getBooks($page);

            foreach ($books as $bookData) {
                $book = Book::firstOrCreate($bookData);

                if ($book->wasRecentlyCreated) {
                    $bookCount++;
                }

                if ($bookCount === $count) {
                    break;
                }
            }

            $page++;
        }
    }

    public function getBooksWithSearch(
        string $query = '',
        int $perPage = 10,
        int $page = 1
    ) {
        if (! empty($query)) {
            return Book::search($query, $perPage, $page, 'title')
                ->paginate($perPage, ['*'], 'page', $page);
        }

        return Book::paginate($perPage, ['*'], 'page', $page);
    }

    public function getBookById(int $id): Book
    {
        return Book::findOrFail($id);
    }

    public function publishBook(array $data): Book
    {
        if (isset($data['cover'])) {
            $coverUrl = $this->fileService->upload($data['cover']);

            unset($data['cover']);
            $data['cover_url'] = $coverUrl;
        }

        return Book::create($data);
    }

    public function updateBook(int $userId, int $id, array $data): Book
    {
        $book = Book::findOrFail($id);
        $user = User::findOrFail($userId);

        $this->checkAuthor($user, $book);

        if (isset($data['cover'])) {
            $coverUrl = $this->fileService->upload($data['cover']);

            unset($data['cover']);
            $data['cover_url'] = $coverUrl;
        }

        $book->updateOrFail($data);

        return $book;
    }

    public function deleteBook(int $userId, int $id): void
    {
        $book = Book::findOrFail($id);
        $user = User::findOrFail($userId);

        $this->checkAuthor($user, $book);

        $book->delete();
    }

    private function checkAuthor(User $user, Book $book): void
    {
        if ($user->id !== $book->user->id && ! $user->hasRole(Role::ROLE_ADMIN)) {
            throw new UnauthorizedException('Forbidden', 403);
        }
    }
}
