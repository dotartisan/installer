<?php

namespace Dotartisan\Installer\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InstallRequest extends FormRequest
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
            'db.host' => 'required',
            'db.port' => 'required',
            'db.username' => 'required',
            'db.password' => 'nullable',
            'db.database' => 'required',
            'admin.name' => 'required',
            'admin.email' => 'required|email',
            'admin.password' => 'required|confirmed',
            'website.app_name' => 'required',
            'website.app_email' => 'required|email',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'db.host' => 'host',
            'db.port' => 'port',
            'db.username' => 'username',
            'db.password' => 'password',
            'db.database' => 'database',
            'admin.name' => 'full name',
            'admin.email' => 'email',
            'admin.password' => 'password',
            'website.app_name' => 'application title',
            'website.app_email' => 'contact email',
        ];
    }
}
