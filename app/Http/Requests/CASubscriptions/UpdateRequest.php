<?php

namespace App\Http\Requests\CASubscriptions;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
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
            'user_id' => [
                'required',
                'integer',
                'exists:users,id'
            ],
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
            ],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages()
    {
        return [
            'user_id.required' => 'The user ID is required.',
            'user_id.integer' => 'The user ID must be an integer.',
            'user_id.exists' => 'The user ID does not exist in the users table.',
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
}
