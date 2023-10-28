<?php

declare(strict_types=1);

namespace App\Http\Controllers\Internal;

use App\Http\Controllers\Controller;
use App\Http\Requests\Internal\Book\BookIndexRequest;
use App\Http\Requests\Internal\Book\BookStoreRequest;
use App\Http\Requests\Internal\Book\BookUpdateRequest;
use App\Http\Resources\BookResource;
use App\Service\Book\BookService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class BookController extends Controller
{
    public function index(
        BookIndexRequest $request,
        BookService $bookService,
    ): AnonymousResourceCollection {
        $query = $request->get('q', '');
        $perPage = (int) $request->get('perPage', 10);
        $page = (int) $request->get('page', 1);

        $books = $bookService->getBooksWithSearch($query, $perPage, $page);

        return BookResource::collection($books);
    }

    public function store(
        BookStoreRequest $request,
        BookService $bookService,
    ): BookResource {
        $data = $request->validated();

        if (empty($data['author'])) {
            $data['author'] = auth()->user()->name;
        }

        $data['user_id'] = auth()->user()->id;

        return new BookResource($bookService->publishBook($data));
    }

    public function show(BookService $bookService, int $id): BookResource
    {
        return new BookResource($bookService->getBookById($id));
    }

    public function update(
        BookUpdateRequest $request,
        BookService $bookService,
        int $id,
    ): BookResource {
        $data = $request->validated();

        $userId = auth()->user()->id;

        return new BookResource($bookService->updateBook($userId, $id, $data));
    }

    public function destroy(
        BookService $bookService,
        int $id,
    ): Response {
        $userId = auth()->user()->id;

        $bookService->deleteBook($userId, $id);

        return response()->noContent();
    }
}
