<?php

namespace App\Http\Requests\Order;

use App\Rules\CheckDelivery;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class AcceptOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
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
            'delivery_id' => ['required', 'exists:users,id', 'integer', 'gt:0', new CheckDelivery],
            'delivery_date' => 'required|date_format:Y-m-d|after:now'
        ];
    }


    public function attributes(): array
    {
        return  [
            'delivery_id' => 'رقم الموظف',
            'delivery_date' => 'وقت التوصيل'
        ];
    }
    /**
     *  
     * @param $validator
     *
     * throw a exception
     */
    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json(
            [
                'status' => 'error',
                'message' => "فشل التحقق يرجى التأكد من صحة القيم مدخلة",
                'errors' => $validator->errors()
            ],
            422
        ));
    }
    /**
     *  get array of  BookRequestService messages 
     * @return array   of messages
     */
    public function messages()
    {
        return  [
            'required' => 'حقل :attribute هو حقل اجباري ',
            'exists' => 'حقل :attribute خاطئ يرجى التأكد من رقم الموظف',
            'integer' => 'حقل :attribute يجب ان يكون رقم',
            'gt' => 'حقل :attribute يجب ان يكون اكبر من الصفر',
            'date_format' => 'حقل :attribute  يجب ان يكون من الشكل سنة-شهر-يوم ',
            'after' => 'حقل :attribute  يجب ان يكون اكبر من تاريخ الحالي ',
        ];
    }
}
