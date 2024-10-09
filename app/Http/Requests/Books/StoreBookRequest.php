<?php

namespace App\Http\Requests\Books;

use App\Services\BookRequestService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class StoreBookRequest extends FormRequest
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
            'title' => 'required|min:3|max:255|unique:books,title|string',
            'author' => 'required|min:3|max:255|string',
            'image' => 'required|mimes:png,jpg',
            'count' => 'required|integer|gt:0',
            'price' => 'required|numeric|gt:0',
            'published_at' => 'required|date_format:Y-m-d|before:now',
            'category_id' => 'required|integer|gt:0|exists:categories,id',
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
        $messages = $this->bookRequestService->messages();
        $messages['required'] = 'حقل :attribute هو حقل اجباري ';
        $messages['exists'] = 'حقل :attribute خاطئ , ترجة تأكد من رقم الفئة';
        $messages['integer'] = 'حقل :attribute يجب ان يكون عدد صحيح';
        $messages['gt'] = 'حقل :attribute يجب ان يكون عدد اكبر من الصفر';
        $messages['numeric'] = 'حقل :attribute   يجب ان يكون عدد ';
        return $messages;
    }
}
