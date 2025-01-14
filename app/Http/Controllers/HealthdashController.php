<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Healthdash;

class HealthdashController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    $healthdash = Healthdash::query()->get('email_address');

    return response()->json($healthdash);
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    //
  }

  /**
   * Display the specified resource.
   *
   * @param  \App\Models\Healthdash  $healthdash
   * @return \Illuminate\Http\Response
   */
  public function show(Healthdash $healthdash)
  {
    //
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \App\Models\Healthdash  $healthdash
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, Healthdash $healthdash)
  {
    //
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  \App\Models\Healthdash  $healthdash
   * @return \Illuminate\Http\Response
   */
  public function destroy(Healthdash $healthdash)
  {
    //
  }
}
