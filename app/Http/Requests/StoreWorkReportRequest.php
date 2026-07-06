<?php

namespace App\Http\Requests;

use App\Models\WorkReport;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreWorkReportRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', WorkReport::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'input_date' => ['required', 'date', 'before_or_equal:today'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'work_plan' => ['required', 'string', 'max:5000'],
            'work_activity' => ['required', 'string', 'max:5000'],
            'work_result' => ['required', 'string', 'max:5000'],
            'attachment' => ['nullable', 'file', 'mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,zip', 'max:10240'],
        ];
    }

    public function messages(): array
    {
        return [
            'end_time.after' => 'Jam akhir harus setelah jam awal.',
            'attachment.max' => 'Ukuran file maksimal 10 MB.',
            'attachment.mimes' => 'Format file tidak didukung.',
        ];
    }
}
