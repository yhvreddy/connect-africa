<?php

namespace App\Http\Requests\APIs;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Traits\HttpResponses;
use Illuminate\Support\Facades\Route;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class UserUpdatePinRequest extends FormRequest
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
            'mobile' => 'required|integer',
            'pin' => 'required|integer'
        ];
    }

    public function messages(){
        return [
            'alpha_spaces' => ':attribute may only contain letters and spaces.',
            'username' => ':attribute already exists.',
            'alpha_num_symbols' => ':attribute contains special characters like only !#@%$.',
        ];
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
