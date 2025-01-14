<?php

namespace App\Enums\Broadpath\Members;

enum RelationEnum: string
{
  case EMPLOYEE = 'Principal';
  case CHILD = 'Child';
  case SPOUSE = 'Spouse';
  case SIBLING = 'Sibling';
  case PARENT = 'Parent';
}
