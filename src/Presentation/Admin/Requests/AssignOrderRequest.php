<?php

namespace Src\Presentation\Admin\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssignOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'id' => $this->route('id'),
        ]);
    }

    public function rules(): array
    {
        return [
            // الفحص الآن يتم على الـ id القادم من الرابط
            'id' => ['required', 'integer', 'exists:orders,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => 'رقم الطلب مطلوب لإتمام عملية الإسناد.',
            'id.integer'  => 'عذراً، رقم الطلب يجب أن يكون صيغة رقمية صحيحة.',
            'id.exists'   => 'الطلب المحدد غير موجود في منظومة البيانات المعمارية.',
        ];
    }
}
