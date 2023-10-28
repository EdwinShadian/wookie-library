<?php

declare(strict_types=1);

namespace App\Http\Controllers\PublicApi;

use App\Http\Controllers\Controller;
use App\Http\Requests\PublicApi\Book\BookIndexRequest;
use App\Http\Resources\BookResource;
use App\Service\Book\BookService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BookController extends Controller
{
    public function index(
        BookIndexRequest $request,
        BookService $bookService
    ): AnonymousResourceCollection {
        $query = $request->get('q', '');
        $perPage = (int) $request->get('perPage', 10);
        $page = (int) $request->get('page', 1);

        $books = $bookService->getBooksWithSearch($query, $perPage, $page);

        return BookResource::collection($books);
    }

    public function show(BookService $bookService, int $id): BookResource
    {
        return new BookResource($bookService->getBookById($id));
    }
}
