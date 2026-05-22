<?php

namespace Src\Presentation\Admin\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDriverRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'phone' => ['required', 'string', 'max:20', 'unique:drivers,phone'],
            'winch_type' => ['required', 'string', 'max:100'],
            'lat' => ['required', 'numeric', 'between:-90,90'],
            'lng' => ['required', 'numeric', 'between:-180,180'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'يرجى إدخال اسم السائق الثنائي على الأقل.',
            'phone.required' => 'رقم جوال السائق مطلوب لتفعيل الحساب.',
            'phone.unique' => 'رقم الجوال هذا مسجل مسبقاً لسائق آخر في المنظومة.',
            'winch_type.required' => 'يجب تحديد نوع الونش (سطحة، رافعة، إلخ).',
            'lat.between' => 'إحداثيات خط العرض (Latitude) غير صحيحة جغرافياً.',
            'lng.between' => 'إحداثيات خط الطول (Longitude) غير صحيحة جغرافياً.',
        ];
    }
}
