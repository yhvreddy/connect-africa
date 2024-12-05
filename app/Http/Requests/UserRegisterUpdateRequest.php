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
            'disability_type'   => 'string',
            'country_id'   => 'integer',
            'subscription_id' => [
                'required',
                'integer',
                'exists:subscriptions,id'
            ],
            'subscription_type_id' => [
                'required',
                'integer',
                'exists:subscriptions_types,id'
            ],
            'subscription_plan_id' => [
                'required',
                'integer',
                'exists:subscriptions_plans,id'
            ],
            'subscription_payment_id' => [
                'required',
                'integer',
                'exists:subscriptions_payment_methods,id'
            ]
        ];
    }

    public function messages()
    {
        return [
            'alpha_spaces' => ':attribute may only contain letters and spaces.',
            'username' => ':attribute already exists.',
            'alpha_num_symbols' => ':attribute contains special characters like only !#@%$.',
            'subscription_id.required' => 'The subscription ID is required.',
            'subscription_id.integer' => 'The subscription ID must be an integer.',
            'subscription_id.exists' => 'The subscription ID does not exist in the subscriptions table.',
            'subscription_type_id.required' => 'The subscription type ID is required.',
            'subscription_type_id.integer' => 'The subscription type ID must be an integer.',
            'subscription_type_id.exists' => 'The subscription type ID does not exist in the subscriptions_types table.',
            'subscription_plan_id.required' => 'The subscription plan ID is required.',
            'subscription_plan_id.integer' => 'The subscription plan ID must be an integer.',
            'subscription_plan_id.exists' => 'The subscription plan ID does not exist in the subscriptions_plans table.',
            'subscription_payment_id.required' => 'The subscription payment ID is required.',
            'subscription_payment_id.integer' => 'The subscription payment ID must be an integer.',
            'subscription_payment_id.exists' => 'The subscription payment ID does not exist in the subscriptions_payments table.',
        ];
    }

    // Customize the validation failure behavior
    protected function failedValidation(Validator $validator)
    {
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
        if ($this->password == null) {
            $this->request->remove('password');
        }
        if ($this->email == null) {
            $this->request->remove('email');
        }
    }
}
