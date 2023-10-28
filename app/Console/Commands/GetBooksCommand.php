<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Service\Book\BookService;
use Illuminate\Console\Command;
use Throwable;

class GetBooksCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:get-books {count : Count of books which needs to get from web}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get some new books from web';

    /**
     * Execute the console command.
     */
    public function handle(BookService $bookService): void
    {
        $this->info('Get books from web...');

        $count = (int) $this->argument('count');

        if (0 === $count) {
            $this->error('Count must be more than zero!');

            return;
        }

        try {
            $bookService->addBooksToLibrary($count);
        } catch (Throwable $e) {
            $this->error($e->getMessage());

            return;
        }

        $this->info('Books are in library!');
    }
}
