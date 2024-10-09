<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Http\Requests\Books\StoreBookRequest;
use App\Http\Requests\Books\UpdateBookRequest;
use App\Services\BookService;
use Illuminate\Http\Request;

class BookController extends Controller
{
    protected $bookService;
    public function __construct(BookService $bookService)
    {
        $this->bookService = $bookService;
    }
    /**
     * Display a listing of the resource.
     */
    /**
     * show all books
     *
     * @param Request $request 
     *
     * @return response  of the status of operation : $books
     */
    public function index(Request $request)
    {
        $author = $request->input('author');
        $title = $request->input('title');
        $published_at = $request->input('published_at');
        $is_active = $request->input('is_active');
        $category_name = $request->input('category_name');

        $books = $this->bookService->allBooks($author, $title, $published_at, $is_active, $category_name, false);

        return response()->json([
            'status' => 'success',
            'data' => [
                'books' => $books
            ]
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    /**
     *  create a new  book
     *
     * @param StoreBookRequest $request 
     *
     * @return response  of the status of operation : the new book
     */
    public function store(StoreBookRequest $request)
    {
        $bookData = $request->validated();
        $bookimage = $request->file('image');

        $book = $this->bookService->createBook($bookData, $bookimage);

        return response()->json([
            'status' => 'success',
            'data' => [
                'book' => $book
            ]
        ], 201);
    }

    /**
     * Display the specified resource.
     */

    /**
     *  show a specific  book
     *
     * @param Book $book 
     *
     * @return response  of the status of operation and book
     */
    public function show(Book $book)
    {
        $book = $this->bookService->oneBook($book);

        return response()->json([
            'status' => 'success',
            'data' => [
                'book' => $book
            ]
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    /**
     *  update a specific  book
     *
     * @param Book $book 
     * @param UpdateBookRequest $request 
     * @return response  of the status of operation : book
     */
    public function update(UpdateBookRequest $request, Book $book)
    {
        $bookData = $request->validated();
        $bookimage = $request->file('image');
        $book = $this->bookService->updateBook($book,  $bookData, $bookimage);
        return response()->json([
            'status' => 'success',
            'data' => [
                'book' => $book
            ]
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    /**
     *  remove a specific  book
     *
     * @param Book $book 
     *
     * @return response  of the status of operation 
     */
    public function destroy(Book $book)
    {
        $this->bookService->deleteBook($book);
        return response()->json(status: 204);
    }


    /**
     * show all  deleted books
     *
     * @param Request $request 
     *
     * @return response  of the status of operation : and the books
     */
    public function deletedBooks(Request $request)
    {
        $author = $request->input('author');
        $title = $request->input('title');
        $published_at = $request->input('published_at');
        $is_active = $request->input('is_active');
        $category_name = $request->input('category_name');

        $books = $this->bookService->allBooks($author, $title, $published_at, $is_active,  $category_name, true);

        return response()->json([
            'status' => 'success',
            'data' => [
                'books' => $books
            ]
        ], 200);
    }

    /**
     * restore a  book
     *
     * @param int $book_id 
     *
     * @return response  of the status of operation and the book
     */
    public function restoreBook($book_id)
    {
        $book = $this->bookService->restoreBook($book_id);
        return response()->json([
            'status' => 'success',
            'data' => [
                'book' => $book
            ]
        ], 200);
    }
    /**
     * force delete a book
     * 
     * @param int $book_id 
     *
     * @return response  of the status of operation 
     */

    public function forceDeleteBook($book_id)
    {
        $this->bookService->forceDeleteBook($book_id);
        return response()->json(status: 204);
    }
}