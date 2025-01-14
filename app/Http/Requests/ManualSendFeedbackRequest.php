<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ManualSendFeedbackRequest extends FormRequest
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
      'loa.*' => 'required|mimes:pdf',
      'email' => 'required|email:rfc,dns',
      'provider_email' => 'nullable|email:rfc,dns',
      'company_id' => 'required|int',
      'email_format_type' => 'required|string',
    ];
  }
}
