<?php

namespace App\Http\Controllers\Members;

use App\Http\Controllers\Controller;
use App\Models\Members\hr_members;
use Illuminate\Http\Request;

class DependentController extends Controller
{
  public function getDependents()
  {
    $id = request('id');
    $member_id = request('member_id');

    $deps = hr_members::query()->where('member_id', $member_id)->where('id', '<>', $id)->get();

    return response()->json($deps);
  }
}
