<?php

namespace App\Http\Controllers\Members;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Members\PendingDocument;
use App\Http\Requests\Member\UploadPendingDocumentRequest;
use Illuminate\Support\Facades\Log;

class PendingDocumentController extends Controller
{
  public function pendingDocuments($member_id)
  {
    $documents = PendingDocument::query()->where('link_id', $member_id)->get();

    return response()->json($documents);
  }

  public function uploadPendingDocuments(UploadPendingDocumentRequest $request)
  {
    $member_id = $request->member_id;
    $document_id_array = $request->document_id;
    $file = $request->file('file');


    foreach ($document_id_array as $key => $document_id) {
      $filename = $file[$key]->getClientOriginalName();

      // store to digitalspaces
      $fileLink = $file[$key]->store(env('APP_ENV') . "/members/pending-attacment/$member_id", 'broadpath');

      // save to db
      PendingDocument::where('id', $document_id)
        ->update([
          'link_id' => $member_id,
          'file_name' => $filename,
          'file_link' => $fileLink,
        ]);
    }

    return response()->noContent();
  }
}
