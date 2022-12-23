<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class GetDetailChapterRequest extends FormRequest
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
            'story_id' => [Rule::exists('stories', 'id')],
            'chapter_id' => [Rule::exists('chapters', 'id')],
            'chapter' => [Rule::exists('chapters', $this->chapter_id)->where('story_id', $this->story_id)->whereNull('deleted_at')],
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'story_id' => $this->story_id,
            'chapter_id' => $this->chapter_id,
        ]);
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json($validator->errors(), 400));
    }
}
