<?php

namespace App\Enums\Broadpath\Members;

enum RelationEnum: string
{
  case SINGLE = 'Single';
  case MARRIED = 'Married';
  case SPOUSE = 'Spouse';
  case SIBLING = 'Sibling';
}
