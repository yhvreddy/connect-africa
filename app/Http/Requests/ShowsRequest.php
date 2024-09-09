<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Traits\HttpResource;
use Illuminate\Support\Facades\Route;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use App\Http\Rules\ImageValidation;
class ShowsRequest extends FormRequest
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
            'title'             =>  'required',
            'poster_image'      =>  ['required', new  ImageValidation()],
            'year'              =>  'required|numeric',
            'rating'            =>  'required',
            'rt_score'          =>  'regex:/^\d+(\.\d+)?$/',
            'imdb_score'        =>  'regex:/^\d+(\.\d+)?$/',
            'google_score'      =>  'regex:/^\d+(\.\d+)?$/',
            'videos.*.link'     =>  'url',
            'videos.*.image'    =>  'image|max:2048|mimes:jpeg,png,jpg',
        ];
    }

    public function messages(){
        return [
            'alpha_spaces' => ':attribute may only contain letters and spaces.',
            'username' => ':attribute already exists.',
            'alpha_num_symbols' => ':attribute contains special characters like only !#@%$.',
        ];
    }

    // Customize the authorization failure behavior
    protected function failedAuthorization(){
        // For example, redirect the user or return a JSON response
        if ($this->wantsJson()) {
            // If the request is an API request, respond with JSON
            return response()->json([
                'message' => 'Authorization error',
            ], JsonResponse::HTTP_FORBIDDEN);
        } else {
            // If it's a regular web request, redirect the user
            return redirect()->route('/')
                ->with('error', 'You are not authorized to perform this action.');
        }
    }

    // Customize the validation failure behavior
    protected function failedValidation(Validator $validator){
        if ($this->wantsJson()) {
            // If the request is an API request, respond with JSON
            throw new HttpResponseException(
                response()->json([
                    'message' => 'Validation error',
                    'errors' => $validator->errors(),
                ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
            );
        } else {
            // If it's a regular web request, redirect back with errors
            throw new HttpResponseException(
                redirect()->back()
                    ->withInput($this->input())
                    ->withErrors($validator)
            );
        }
    }

    // Customize the response when validation fails
    public function response(array $data, $status){
        if ($this->wantsJson()) {
            return response()->json($data, $status);
        } else {
            return redirect()->back()
                ->withInput($this->input())
                ->withErrors($data['errors'] ?? [])
                ->setStatusCode($status);
        }
    }
}
