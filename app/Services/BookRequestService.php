<?php

namespace App\Services;

use Illuminate\Http\Exceptions\HttpResponseException;

class BookRequestService
{
    /**
     *  get array of  BookRequestService attributes 
     *
     * @return array   of attributes
     */
    public function attributes()
    {
        return  [
            'title' => 'العنوان',
            'author' => 'المؤلف',
            'published_at' => 'تاريخ النشر',
            'image' => 'الصورة',
            'count' => 'عدد الكتب',
            'price' =>  'سعر الكتاب',
            'category_id' => 'رقم الفئة'
        ];
    }
    /**
     *  
     * @param $validator
     *
     * throw a exception
     */
    public function failedValidation($validator)
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
            'min' => 'حقل :attribute يجب ان  يكون على الاقل 3 محارف',
            'max' => 'حقل :attribute يجب ان  يكون على الاكثر 255 محرف',
            'unique' => 'حقل :attribute  يجب ان يكون غير مكرر ',
            'mimes' =>  "png  او jpg " . 'حقل :attribute  يجب ان تكون من لاحقة ',
            'date_format' => 'حقل :attribute  يجب ان يكون من الشكل سنة-شهر-يوم ',
            'boolean' =>  'حقل :attribute  يجب ان يكون احد القيم ' . '1 ' . 'او' . ' 0',
            'string' => 'حقل :attribute يجب ان يكون نص',
            'exists' => 'حقل :attribute خاطئ يرجى التأكد من رقم الفئة',
        ];
    }
}
