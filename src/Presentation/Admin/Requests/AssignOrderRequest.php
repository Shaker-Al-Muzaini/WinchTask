<?php

namespace Src\Presentation\Admin\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssignOrderRequest extends FormRequest
{
    /**
     * تحديد ما إذا كان المستخدم مصرحاً له بتنفيذ هذا الطلب (Admin)
     */
    public function authorize(): bool
    {
        // يمكنك ربطها بنظام الصلاحيات الخاص بك، حالياً سنجعلها true لتمرير الطلب
        return true;
    }

    /**
     * قواعد التحقق الصارمة (Validation Rules)
     */
    public function rules(): array
    {
        return [
            'order_id' => ['required', 'integer', 'exists:orders,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'order_id.required' => 'رقم الطلب مطلوب لإتمام عملية الإسناد.',
            'order_id.integer' => 'عذراً، رقم الطلب يجب أن يكون صيغة رقمية صحيحة.',
            'order_id.exists' => 'الطلب المحدد غير موجود في منظومة البيانات المعمارية.',
        ];
    }
}
