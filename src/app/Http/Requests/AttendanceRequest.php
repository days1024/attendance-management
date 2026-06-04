<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AttendanceRequest extends FormRequest
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
        'request_clock_in' => ['required',
        'date_format:H:i',],
        'request_clock_out' => ['required', 
        'date_format:H:i','after:request_clock_in'],

        'request_break_start.*' => [
            'nullable',
            'date_format:H:i',
            'after:request_clock_in',
            'before:request_clock_out',
        ],

        'request_break_end.*' => [
            'nullable',
            'date_format:H:i',
            'before:request_clock_out',
        ],

        'reason' => ['required'],
    ];
    }

    public function messages(): array
{
    return [
        'request_clock_out.after' =>
            '出勤時間もしくは退勤時間が不適切な値です',

        'request_break_start.*.after' =>
            '休憩時間が不適切な値です',

        'request_break_start.*.before' =>
            '休憩時間が不適切な値です',

        'request_break_end.*.before' =>
            '休憩時間もしくは退勤時間が不適切な値です',

        'reason.required' =>
            '備考を記入してください',
        
        'request_clock_in.date_format' => '時刻は HH:MM 形式で入力してください',

        'request_clock_out.date_format' => '時刻は HH:MM 形式で入力してください',

        'request_break_start.*.date_format' => '休憩開始時刻は HH:MM 形式で入力してください',

        'request_break_end.*.date_format' => '休憩終了時刻は HH:MM 形式で入力してください',
    ];
}

    public function withValidator($validator)
{
    $validator->after(function ($validator) {

        $starts = $this->input('request_break_start', []);
        $ends = $this->input('request_break_end', []);

        $breaks = [];

        foreach ($starts as $index => $start) {
        if (empty($start) || empty($ends[$index])) {
        continue;
        }

        $breaks[] = [
        'start' => $start,
        'end' => $ends[$index],
        'index' => $index,
        ];
    }

        foreach ($breaks as $i => $break1) {
            foreach ($breaks as $j => $break2) {

                if ($i >= $j) {
                    continue;
                }

                if (
                    $break1['start'] < $break2['end'] &&
                    $break2['start'] < $break1['end']
                ) {
                    $validator->errors()->add(
                        "request_break_start.{$break2['index']}",
                        '休憩時間が重複しています'
                    );
                }
            }
        }
    });
}
}
