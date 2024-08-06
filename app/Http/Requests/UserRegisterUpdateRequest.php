<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Traits\HttpResource;
use Illuminate\Support\Facades\Route;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class UserRegisterUpdateRequest extends FormRequest
{
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
            'name'      =>  'required',
            'mobile'    =>  'required',
            'email' => [
                        'sometimes',
                        'email',
                        Rule::unique('users')->ignore($this->user->id, 'id', 'App\Models\User')
                    ],
            // 'password' => 'sometimes|alpha_num_symbols'
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

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if($this->password == null){
            $this->request->remove('password');
        }
        if($this->email == null){
            $this->request->remove('email');
        }
    }
}
