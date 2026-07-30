<?php

/*
|--------------------------------------------------------------------------
| Arabic validation messages
|--------------------------------------------------------------------------
|
| Only the rules this application actually uses are translated. Anything not
| listed here falls back to English rather than to a broken key.
|
*/

return [
    'required' => 'حقل :attribute مطلوب.',
    'email' => 'أدخل بريداً إلكترونياً صحيحاً.',
    'unique' => 'هذا :attribute مستخدم بالفعل.',
    'confirmed' => 'تأكيد :attribute غير مطابق.',
    'current_password' => 'كلمة المرور الحالية غير صحيحة.',
    'lowercase' => 'يجب أن يكون :attribute بأحرف صغيرة.',
    'numeric' => 'يجب أن يكون :attribute رقماً.',
    'regex' => 'صيغة :attribute غير صحيحة.',
    'file' => 'يجب أن يكون :attribute ملفاً.',
    'decimal' => 'يجب أن يحتوي :attribute على :decimal خانة عشرية.',
    'enum' => 'القيمة المختارة لـ :attribute غير صالحة.',

    'min' => [
        'numeric' => 'يجب ألّا يقلّ :attribute عن :min.',
        'string' => 'يجب ألّا يقلّ :attribute عن :min حرفاً.',
        'file' => 'يجب ألّا يقلّ حجم :attribute عن :min كيلوبايت.',
    ],

    'max' => [
        'numeric' => 'يجب ألّا يزيد :attribute عن :max.',
        'string' => 'يجب ألّا يزيد :attribute عن :max حرفاً.',
        'file' => 'يجب ألّا يزيد حجم :attribute عن :max كيلوبايت.',
    ],

    'mimes' => 'يجب أن يكون :attribute ملفاً من نوع: :values.',

    'password' => [
        'letters' => 'يجب أن تحتوي كلمة المرور على حرف واحد على الأقل.',
        'mixed' => 'يجب أن تحتوي كلمة المرور على حرف كبير وآخر صغير.',
        'numbers' => 'يجب أن تحتوي كلمة المرور على رقم واحد على الأقل.',
        'symbols' => 'يجب أن تحتوي كلمة المرور على رمز واحد على الأقل.',
        'uncompromised' => 'ظهرت كلمة المرور هذه في تسريب بيانات. اختر غيرها.',
    ],

    'attributes' => [
        'name' => 'الاسم',
        'email' => 'البريد الإلكتروني',
        'password' => 'كلمة المرور',
        'password_confirmation' => 'تأكيد كلمة المرور',
        'current_password' => 'كلمة المرور الحالية',
        'phone_number' => 'رقم الهاتف',
        'amount' => 'المبلغ',
        'method' => 'طريقة الصرف',
        'full_name' => 'الاسم الكامل',
        'note' => 'الملاحظة',
        'spreadsheet' => 'ملف الجدول',
    ],
];
