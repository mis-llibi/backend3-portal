<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeedbackCorporate extends Model
{
  use HasFactory;

  protected $table = 'feedbacks_corporate';
  protected $fillable = [
    'company_code', 'member_id', 'approval_code', 'question1', 'question2', 'question3', 'question4', 'question5', 'comments'
  ];

  protected function question1(): Attribute
  {
    return Attribute::make(
      get: function (int $value) {
        return match ($value) {
          1 => 'Very Difficult',
          2 => 'Difficult',
          3 => 'Moderate',
          4 => 'Easy',
          5 => 'Very Easy',
          default => 'No Rating'
        };
      },
    );
  }
  protected function question2(): Attribute
  {
    return Attribute::make(
      get: function (int $value) {
        return match ($value) {
          0 => 'No',
          1 => 'Yes',
          default => 'No Rating'
        };
      },
    );
  }
  protected function question3(): Attribute
  {
    return Attribute::make(
      get: function (int $value) {
        return match ($value) {
          0 => 'No',
          1 => 'Yes',
          default => 'No Rating'
        };
      },
    );
  }
  protected function question4(): Attribute
  {
    return Attribute::make(
      get: function (int $value) {
        return match ($value) {
          1 => 'Very Difficult',
          2 => 'Difficult',
          3 => 'Moderate',
          4 => 'Easy',
          5 => 'Very Easy',
          default => 'No Rating'
        };
      },
    );
  }
  protected function question5(): Attribute
  {
    return Attribute::make(
      get: function (int $value) {
        return match ($value) {
          1 => 'Very Difficult',
          2 => 'Difficult',
          3 => 'Moderate',
          4 => 'Easy',
          5 => 'Very Easy',
          default => 'No Rating'
        };
      },
    );
  }
}
