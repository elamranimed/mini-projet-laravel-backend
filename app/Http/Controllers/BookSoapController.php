<?php

namespace App\Http\Controllers;

use App\Models\Book;

class BookSoapController extends Controller
{
    private function respond(array $payload): string
    {
        return json_encode($payload);
    }

    public function getAllBooks()
    {
        try {
            return $this->respond([
                'status' => 'success',
                'data' => Book::all()->toArray(),
            ]);
        } catch (\Throwable $e) {
            return $this->respond(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function getBook($id)
    {
        try {
            $book = Book::find($id);
            return $book
                ? $this->respond(['status' => 'success', 'data' => $book->toArray()])
                : $this->respond(['status' => 'error', 'message' => 'Livre non trouvé']);
        } catch (\Throwable $e) {
            return $this->respond(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function getBooksByAuthor($author)
    {
        try {
            $books = Book::where('author', 'LIKE', '%' . $author . '%')->get();
            return $books->isNotEmpty()
                ? $this->respond(['status' => 'success', 'data' => $books->toArray()])
                : $this->respond(['status' => 'error', 'message' => 'Aucun livre trouvé pour cet auteur']);
        } catch (\Throwable $e) {
            return $this->respond(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function createBook($title, $author = null, $published_year = null, $genre = null)
    {
        try {
            $data = [
                'title' => $title,
                'author' => $author ?: null,
                'published_year' => $published_year ?: null,
                'genre' => $genre ?: null,
            ];

            $book = Book::create($data);

            return $this->respond([
                'status' => 'success',
                'data' => $book->toArray(),
            ]);
        } catch (\Throwable $e) {
            return $this->respond(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function updateBook($id, $title = null, $author = null, $published_year = null, $genre = null)
    {
        try {
            $book = Book::find($id);
            if (!$book) {
                return $this->respond(['status' => 'error', 'message' => 'Book not found']);
            }

            $data = array_filter(
                [
                    'title' => $title,
                    'author' => $author,
                    'published_year' => $published_year,
                    'genre' => $genre,
                ],
                fn($v) => $v !== null
            );

            $book->update($data);

            return $this->respond([
                'status' => 'success',
                'data' => $book->fresh()->toArray(),
            ]);
        } catch (\Throwable $e) {
            return $this->respond(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function deleteBook($id)
    {
        try {
            $book = Book::find($id);
            if (!$book) {
                return $this->respond(['status' => 'error', 'message' => 'Book not found']);
            }

            $book->delete();

            return $this->respond(['status' => 'success']);
        } catch (\Throwable $e) {
            return $this->respond(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
