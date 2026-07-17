<?php

namespace App\Http\Requests\Api\V1;

use App\Http\Requests\IndexContactRequest as BaseRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class IndexContactRequest extends BaseRequest
{
    // API用のバリデーションエラーレスポンスをカスタマイズ
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'message' => 'バリデーションエラーが発生しました',
            'errors' => $validator->errors(),
        ], 422));
    }

    // APIでは認証を不要にするため true に変更
    public function authorize(): bool
    {
        return true;
    }
}