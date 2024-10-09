<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\BookResource;
use App\Models\Book;
use Illuminate\Support\Facades\Storage;

class  BookService
{
    /**
     * show all  book
     * @param string $author  
     * @param string $title  
     * @param date $published_at  
     * @param bool $is_active  
     * @param bool $deletedBooks  
     * @return BookResource books 
     */
    public function allBooks($author, $title, $published_at, $is_active, $category_name, $deletedBooks)
    {

        try {
            if ($deletedBooks) {
                $books = Book::onlyTrashed()
                    ->With('category')
                    ->whereRelation('category', 'name', 'like', "%$category_name%");
            } else {
                $books = Book::With('category')
                    ->whereRelation('category', 'name', 'like', "%$category_name%");
            }
            $books = $books
                ->byAuthor($author)
                ->byTitle($title)
                ->byPublishedAt($published_at)
                ->byIsActive($is_active)
                ->get();
            $books = BookResource::collection($books);
            return  $books;
        } catch (Exception $e) {
            Log::error("error in get all books"  . $e->getMessage());
            throw new Exception("there is something wrong in server");
        }
    }
    /**
     * show a specific book
     * @param  $book  
     * @return BookResource book  
     */
    public function oneBook($book)
    {

        try {
            $category = $book->load('category');
            $book = BookResource::make($book);
            return $book;
        } catch (Exception $e) {
            Log::error("error in  show a  book"  . $e->getMessage());
            throw new Exception("there is something wrong in server");
        }
    }
    /**
     * create a  new book
     * @param  $request  
     * @return BookResource book  
     */
    public function createBook($bookData, $bookimage)
    {
        if ($bookimage != null) {
            $bookData['image'] = $bookimage->store('imagesBooks', 'public');
        }
        $book = Book::create($bookData);
        $book  = BookResource::make($book);
        return  $book;
        try {
        } catch (Exception $e) {
            Log::error("error in create a  book"  . $e->getMessage());
            throw new Exception("there is something wrong in server");
        }
    }
    /**
     * update a specific book
     * @param Book $book  
     * @param  $request  
     * @return BookResource book  
     */
    public function updateBook(Book $book, $bookData, $bookimage)
    {
        try {
            if ($bookimage != null) {
                Storage::delete($book->image);
                $bookData['image'] = $bookimage->store('imagesBooks', 'public');
            }

            $book->update($bookData);
            $book = BookResource::make(Book::find($book->id));
            return  $book;
        } catch (Exception $e) {
            Log::error("error in   update a  book"  . $e->getMessage());
            throw new Exception("there is something wrong in server");
        }
    }

    /**
     * soft delete a specific  book
     * @param Book $book  
     */
    public function deleteBook(Book $book)
    {
        try {
            $book->delete();
        } catch (Exception $e) {
            Log::error("error in  soft delete a  book"  . $e->getMessage());
            throw new Exception("there is something wrong in server");
        }
    }

    /**
     * restore a specific book
     * @param int $book_id      
     * @return BookResource $book
     */
    public function restoreBook($book_id)
    {
        try {
            $book = Book::withTrashed()->find($book_id);
            $book->restore();
            return BookResource::make($book);
        } catch (Exception $e) {
            Log::error("error in restore a book"  . $e->getMessage());
            throw new Exception("there is something wrong in server");
        }
    }

    /**
     * delete a specific book
     * @param Book $book  
     */
    public function forceDeleteBook($book_id)
    {
        try {
            $book = Book::withTrashed()->find($book_id);
            $book->forceDelete();
        } catch (Exception $e) {
            Log::error("error in delete a  book"  . $e->getMessage());
            throw new Exception("there is something wrong in server");
        }
    }
}