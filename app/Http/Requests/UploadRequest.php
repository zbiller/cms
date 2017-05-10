<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class UploadRequest extends Request
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
        if (config('upload.database.save') === true) {
            return [
                'name' => [
                    'required',
                ],
                'original_name' => [
                    'required',
                ],
                'path' => [
                    'required',
                ],
                'full_path' => [
                    'required',
                    Rule::unique(config('upload.database.table'), 'name')
                        ->ignore($this->route('upload') ? $this->route('upload')->id : null)
                ],
                'extension' => [
                    'required',
                ],
                'size' => [
                    'required',
                    'numeric',
                ],
                'mime' => [
                    'required',
                ],
                'type' => [
                    'required',
                    'numeric',
                ],
            ];
        }

        return [];
    }
}
