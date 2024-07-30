<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Traits\HttpResponses;
use Illuminate\Support\Facades\Route;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;

class ForgetPinRequest extends FormRequest
{
    use HttpResponses;

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
            'step'      =>  'required|string|in:mobile,otp,pin',
            'mobile'    =>  'integer',
            'otp'       =>  'integer',
            'pin'       =>  'integer'
        ];
    }

    public function messages(){
        return [];
    }

    // Customize the validation failure behavior
    protected function failedValidation(Validator $validator){
        if ($this->wantsJson()) {
            throw new HttpResponseException(
                $this->validation('Validation error', $validator->errors())
            );
        } else {
            throw new HttpResponseException(
                redirect()->back()
                    ->withInput($this->input())
                    ->withErrors($validator)
            );
        }
    }
}
