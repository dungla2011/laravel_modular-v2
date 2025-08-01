<?php

namespace Modules\Base\Abstracts;

use Illuminate\Foundation\Http\FormRequest;

abstract class BaseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Override in child classes for authorization logic
    }

    /**
     * Get the validation rules that apply to the request.
     */
    abstract public function rules(): array;

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'required' => ':attribute là bắt buộc.',
            'string'   => ':attribute phải là chuỗi.',
            'max'      => ':attribute không được vượt quá :max ký tự.',
            'min'      => ':attribute phải có ít nhất :min ký tự.',
            'email'    => ':attribute phải là email hợp lệ.',
            'unique'   => ':attribute đã tồn tại.',
            'exists'   => ':attribute không tồn tại.',
            'numeric'  => ':attribute phải là số.',
            'integer'  => ':attribute phải là số nguyên.',
            'boolean'  => ':attribute phải là true hoặc false.',
            'date'     => ':attribute phải là ngày hợp lệ.',
            'image'    => ':attribute phải là hình ảnh.',
            'mimes'    => ':attribute phải có định dạng: :values.',
            'in'       => ':attribute phải là một trong: :values.',
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator): void
    {
        if ($this->expectsJson()) {
            $response = response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ.',
                'errors'  => $validator->errors(),
            ], 422);

            throw new \Illuminate\Validation\ValidationException($validator, $response);
        }

        parent::failedValidation($validator);
    }

    /**
     * Get validated data with additional processing.
     */
    public function getValidatedData(): array
    {
        $data = $this->validated();

        return $this->transformData($data);
    }

    /**
     * Transform validated data before processing.
     * Override this method in child classes.
     */
    protected function transformData(array $data): array
    {
        return $data;
    }
}
