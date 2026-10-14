<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreInventoryAdjustmentRequest extends FormRequest
{
    /**
     * 暂无鉴权, 全通过
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'batch_id' => ['required', 'integer', Rule::exists('batches', 'id')->where('is_delete', 0)],
            'reason_id' => [
                'required',
                'integer',
                Rule::exists('adjustment_reasons', 'id')
                    ->where('is_active', 1)
                    ->where('is_delete', 0),
            ],
            'new_quantity' => ['required', 'integer', 'min:0', 'max:1000000'],
            'note' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * @return string[]
     */
    public function messages(): array
    {
        return [
            'reason_id.exists' => '原因ID不存在',
        ];
    }
}
