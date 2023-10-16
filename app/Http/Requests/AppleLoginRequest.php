<?php

namespace App\Http\Requests;

use App\Dto\AppleLoginData;
use Illuminate\Foundation\Http\FormRequest;

class AppleLoginRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
       return [
            'name' => 'nullable',
            'email' => 'nullable',
            "social_type" => 'nullable',
            'social_id' => 'required',
            'fcm_token' => 'nullable'
        ];
    }

     public function payload(): AppleLoginData
    {
        return AppleLoginData::of([
            "name" => $this->name,
            "email" => $this->email,
            "social_id" => $this->social_id,
            "social_type" => $this->social_type,
            "fcm_token" => $this->fcm_token
        ]);
    }
}
