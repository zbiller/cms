<?php

namespace App\Http\Requests\Crud;

use App\Http\Requests\Request;
use Illuminate\Http\UploadedFile;

class LibraryRequest extends Request
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
            'file' => 'required|max:' . UploadedFile::getMaxFilesize(),
        ];
    }
}
