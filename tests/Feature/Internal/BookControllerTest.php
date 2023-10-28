<?php

declare(strict_types=1);

namespace Tests\Feature\Internal;

use App\Models\Book;
use App\Models\Role;
use App\Models\User;
use Auth;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BookControllerTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Role::factory()->create(['name' => Role::ROLE_AUTHOR]);
        Role::factory()->create(['name' => Role::ROLE_ADMIN]);
        Role::factory()->create(['name' => Role::ROLE_PUBLISHER]);
    }

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
        $user = User::factory()->create();
        $user->roles()->attach(Role::where('name', Role::ROLE_AUTHOR)->first()->id);

        Book::factory()->count(5)->create();
        Book::factory()->create(['title' => 'Romeo and Juliet']);

        Auth::login($user);

        if (empty($query)) {
            $response = $this->get('api/internal/books?perPage=5');
        } else {
            $response = $this->get("api/internal/books?perPage=5&q=$query");
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
     * Check if publisher can publish book
     */
    public function testStore(): void
    {
        $user = User::factory()->create();
        $user->roles()->attach(
            Role::whereIn('name', [Role::ROLE_AUTHOR, Role::ROLE_PUBLISHER])->get()
        );

        Auth::login($user);

        $coverPath = __DIR__.'/../data/cover.jpg';

        $response = $this->post('api/internal/books', [
            'title' => 'The Book of Yoda',
            'author' => 'Yoda',
            'cover' => new UploadedFile($coverPath, 'cover.jpg', 'image/jpeg', test: true),
            'description' => 'Strange written book it is',
            'price' => 69.99,
        ]);
        $response->assertStatus(201);

        $this->assertDatabaseHas(Book::class, [
            'title' => 'The Book of Yoda',
            'author' => 'Yoda',
            'user_id' => $user->id,
            'description' => 'Strange written book it is',
            'price' => 69.99,
        ]);

        $data = json_decode($response->getContent(), true)['data'];
        $book = Book::where([
            'title' => 'The Book of Yoda',
            'author' => 'Yoda',
            'user_id' => $user->id,
        ])->first();

        $this->assertJson(
            $book->toJson(),
            json_encode($data)
        );

        $this->assertFileExists(Storage::disk(env('FILESYSTEM_DISK'))
            ->path(str_replace('storage/', '', $book->cover_url)));
    }

    /**
     * Check if we can get book's details
     */
    public function testShow(): void
    {
        $user = User::factory()->create();
        $user->roles()->attach(Role::where('name', Role::ROLE_AUTHOR)->first()->id);

        $book = Book::factory()->create();

        Auth::login($user);

        $response = $this->get("api/internal/books/$book->id");
        $response->assertOk();

        $data = json_decode($response->getContent(), true)['data'];
        $this->assertJson($book->toJson(), json_encode($data));
    }

    /**
     * Check if publisher can update book's properties
     */
    public function testUpdate(): void
    {
        $user = User::factory()->create();
        $user->roles()->attach(
            Role::whereIn('name', [Role::ROLE_AUTHOR, Role::ROLE_PUBLISHER])->get()
        );

        $book = Book::factory()->create([
            'user_id' => $user->id,
        ]);

        Auth::login($user);

        $response = $this->put("api/internal/books/$book->id", [
            'title' => 'The Book of Yoda',
            'author' => 'Yoda',
        ]);
        $response->assertOk();

        $data = json_decode($response->getContent(), true)['data'];
        $book->refresh();
        $this->assertJson($book->toJson(), json_encode($data));
    }

    /**
     * Check if publisher can unpublish book from library
     */
    public function testDestroy(): void
    {
        $user = User::factory()->create();
        $user->roles()->attach(
            Role::whereIn('name', [Role::ROLE_AUTHOR, Role::ROLE_PUBLISHER])->get()
        );

        $book = Book::factory()->create([
            'user_id'=> $user->id,
        ]);
        $bookId = $book->id;

        Auth::login($user);

        $response = $this->delete("api/internal/books/$bookId");
        $response->assertNoContent();

        $this->assertDatabaseMissing(Book::class, ['id' => $bookId]);
    }
}
