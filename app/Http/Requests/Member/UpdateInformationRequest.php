<?php

namespace App\Http\Requests\Member;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInformationRequest extends FormRequest
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
      'last_name' => 'required|string',
      'first_name' => 'required|string',
      'middle_name' => 'nullable|string',
      'email' => 'string|email:rfc,dns',
      'birth_date' => 'required',
    ];
  }
}
