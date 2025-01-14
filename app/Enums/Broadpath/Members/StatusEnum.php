<?php

namespace App\Enums\Broadpath\Members;

enum StatusEnum: int
{
    /* 
    * #####################
    * # LEGENDS OF STATUS #
    * #####################

    * 1 pending member/submission
    * 3 Pending deletion
    * 5 pending correction
    * 8 pending change plan

    * all this status is considered as active
    * 4 approved/active members
    * 6 approved correction
    * 7 approved deletion
    * 9 approved change plan
    * 10 disapproved member
  */

  case ACTIVE_MEMBER = 4;
  case APPROVED_CORRECTION = 6;
  case APPROVED_DELETION = 7;
  case APPROVED_CHANGE_PLAN = 9;

  case DISAPPROVED_MEMBER = 10;

  case PENDING_MEMBER = 1;
  case PENDING_DELETION = 3;
  case PENDING_CORRECTION = 5;
  case PENDING_CHANGE_PLAN = 8;

  case PENDING_DOCUMENTS = 11;
}
