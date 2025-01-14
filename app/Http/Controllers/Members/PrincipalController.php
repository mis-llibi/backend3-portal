<?php

namespace App\Http\Controllers\Members;

use App\Http\Controllers\Controller;
use App\Models\Members\hr_members;
use Illuminate\Http\Request;

class PrincipalController extends Controller
{
  public function fetchPrincipal()
  {
    $search = request('q');

    $members = hr_members::query()->principal()
      ->with('contact')
      ->select(
        'id',
        'member_id',
        'relationship_id',
        'first_name',
        'last_name',
        'middle_name',
        'birth_date',
        'gender',
        'civil_status',
        'date_hired',
        'reg_date',
      );
    if ($search) {
      $members = $members->where(function ($query) use ($search) {
        $query->where('first_name', 'LIKE', "%$search%");
        $query->orWhere('last_name', 'LIKE', "%$search%");
      });
    }

    $members = $members->take(100)
      ->latest()
      ->get();

    return response()->json($members);
  }
}
