<?php

namespace App\Http\Controllers\Api_third_party;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Models\Self_service\Sync;

class MobileApiAccessController extends Controller
{
    public function GetMemberData(Request $request)
    {

        $partner = '';

        switch ($request->header('apiKey')) {
            case 'LLIBIXKMD':
                $partner = 'KMD';
                break;
            case 'LLIBIXDA20231117':
                $partner = 'DA';
                break;
        }
        
        if(!empty($partner)) { 

            $memberId = $request->query('member_id');
            $birthdate = $request->query('birth_date');
            $perPage = $request->query('perPage', 10);

            if ($memberId && $birthdate) {
                $memberData = 
                    Sync::join('partnership', 'masterlist.member_id', '=', 'partnership.member_id')
                        ->where(function ($query) use($partner) {
                            $query->where('partnership.partner', $partner);
                        })
                        ->where('masterlist.member_id', $memberId)
                        ->where('masterlist.birth_date', $birthdate)
                        ->select('masterlist.member_id', 'masterlist.first_name', 'masterlist.last_name', 'masterlist.middle_name', 'masterlist.relation', 'masterlist.birth_date', 'masterlist.incepfrom', 'masterlist.incepto', 'masterlist.company_code', 'masterlist.company_name', 'partnership.partner', 'partnership.allow')
                        ->get();

            } else {
                $memberData = 
                    Sync::join('partnership', 'masterlist.member_id', '=', 'partnership.member_id')
                        ->where(function ($query) use($partner)  {
                            $query->where('partnership.partner', $partner);
                        })
                        ->select('masterlist.member_id', 'masterlist.first_name', 'masterlist.last_name', 'masterlist.middle_name', 'masterlist.relation', 'masterlist.birth_date', 'masterlist.incepfrom', 'masterlist.incepto', 'masterlist.company_code', 'masterlist.company_name', 'partnership.partner', 'partnership.allow')
                        ->paginate($perPage);
            }

            return response()->json($memberData);

        } else {

            return response()->json(['error' => 'Failed api key']);

        }
    }
}