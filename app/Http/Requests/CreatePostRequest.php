<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException; //using Validation Exception library to send error messages

use Illuminate\Contracts\Validation\Validator;

class CreatePostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'file'=>'required|mimes:png,jpg,jpeg,gif,doc,pdf,txt,mp4,mov,wav|max:5000',
            'access' => 'required|in:public,private,Public,Private',
            'token' => 'required'
        ];
    }

    // protected function failedValidation(Validator $validator)
    // {
    //     throw new HttpResponseException(response()->json(['status_code' => 400, 'status_message' => 'Input Validations failed to pass', 'missing_params' => $validator->errors()], 400));
    // }
}
