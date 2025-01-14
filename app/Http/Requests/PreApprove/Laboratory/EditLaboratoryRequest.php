<?php

namespace App\Http\Requests\PreApprove\Laboratory;

use Illuminate\Foundation\Http\FormRequest;

class EditLaboratoryRequest extends FormRequest
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
      'laboratory' => 'required|string',
      'cost' => 'required|regex:/^[0-9]{1,3}(,[0-9]{3})*(\.[0-9]+)*$/',
      'cost2' => 'required|regex:/^[0-9]{1,3}(,[0-9]{3})*(\.[0-9]+)*$/',
    ];
  }

  // public function messages(): array
  // {
  //   return [
  //     'cost.required' => 'Class is required',
  //     'cost2.required' => 'Class2 is required',
  //   ];
  // }

  public function attributes(): array
  {
    return [
      'cost' => 'Class',
      'cost2' => 'Class 2',
    ];
  }
}
