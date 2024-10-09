<?php

namespace App\Http\Requests\Books;

use App\Services\BookRequestService;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class UpdateBookRequest extends FormRequest
{
    protected $bookRequestService;
    public function __construct(BookRequestService $bookRequestService)
    {
        $this->bookRequestService = $bookRequestService;
    }
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'sometimes|min:3|max:255|unique:books,title|string',
            'author' => 'sometimes|min:3|max:255|string',
            'image' => 'sometimes|mimes:png,jpg',
            'published_at' => 'sometimes|date_format:Y-m-d|before:now',
        ];
    }

    public function attributes(): array
    {
        return  $this->bookRequestService->attributes();
    }
    public function failedValidation(Validator $validator)
    {
        $this->bookRequestService->failedValidation($validator);
    }
    public function messages(): array
    {
        return $this->bookRequestService->messages();
    }
}