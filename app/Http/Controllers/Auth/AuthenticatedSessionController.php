<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Mail\SendUserCredentialToCae;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules;
use Illuminate\Support\Str;

class AuthenticatedSessionController extends Controller
{
  /**
   * Handle an incoming authentication request.
   *
   * @param  \App\Http\Requests\Auth\LoginRequest  $request
   * @return \Illuminate\Http\Response
   */
  public function store(LoginRequest $request)
  {
    $request->authenticate();

    $request->session()->regenerate();

    if ($request->verified > '')
      User::where('email', $request->email)
        ->update([
          'email_verified_at' => date('Y-m-d H:i:s'),
        ]);

    return response()->noContent();
  }

  public function updateProfile(Request $request)
  {
    $request->validate([
      'firstName' => ['required', 'string', 'max:255'],
      'lastName' => ['required', 'string', 'max:255'],
    ]);

    $user = [
      'first_name' => strtoupper($request->firstName),
      'last_name' => strtoupper($request->lastName),
    ];

    if (!empty($request->email)) {
      $request->validate(['email' => ['required', 'string', 'email', 'max:255', 'unique:users']]);
      $user['email'] = $request->email;
    }

    if (!empty($request->password)) {
      $request->validate(['password' => ['required', 'confirmed', Rules\Password::defaults()]]);
      $user['password'] = Hash::make($request->password);
    }

    User::where('id', $request->id)
      ->update($user);
  }

  /**
   * Destroy an authenticated session.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function destroy(Request $request)
  {
    Auth::guard('web')->logout();

    $request->session()->invalidate();

    $request->session()->regenerateToken();

    return response()->noContent();
  }

  public function createUser()
  {
    // user level 1
    $datas = array(
      // array("val0" => "ILAGAN, GLENMORE S.", "val1" => "glenilagan@llibi.com", "val2" => "glenilagan@llibi.com"),
      // array("val0" => "ABELLA, DANIEL EZRA C.", "val1" => "danielabella@llibi.com", "val2" => "danielezra001@gmail.com"),
      // array("val0" => "BAUTISTA, LOUIE IAN F.", "val1" => "louiebautista@llibi.com", "val2" => "bautistalouieian@gmail.com"),
      // array("val0" => "BAYNOSA, JOAN LORAINE C.", "val1" => "joanbaynosa@llibi.com", "val2" => "j_rhaine16@yahoo.com.ph"),
      // array("val0" => "CUBE, ANGELICA ROMALYN", "val1" => "angelicacube1015@gmail.com", "val2" => "angelicacube1015@gmail.com"),
      // array("val0" => "DE CASTRO, JEAN", "val1" => "jeandecastro@llibi.com", "val2" => "jeandecastro78@gmail.com"),
      // array("val0" => "GARCIA, FRINCESS MAE", "val1" => "frincessmaeg@gmail.com", "val2" => "frincessmaeg@gmail.com"),
      // array("val0" => "JAVIER, GRACIEL", "val1" => "NA", "val2" => "javier.grasya95@gmail.com"),
      // array("val0" => "MADRIAGA, DIANNE KATE A.", "val1" => "dianemadriga@llibi.com", "val2" => "diannemadriaga0517@gmail.com"),
      // array("val0" => "MERINO, ROSEMARIE M.", "val1" => "rosemerino@llibi.com", "val2" => "mhaimurao@gmail.com"),
      // array("val0" => "ROSALES, VILMOR ANDRE", "val1" => "rosalesvilmor@gmail.com", "val2" => "rosalesvilmor@gmail.com"),
      // array("val0" => "ROQUID, JUSTINIAN JR", "val1" => "roquidjustinianojr.llibi.cce@gmail.com", "val2" => "roquidjustinianojr.llibi.cce@gmail.com"),
      // array("val0" => "SALAYO, RAYMOND P.", "val1" => "raymondsalayo@llibi.com", "val2" => "Emon.salayo12@gmail.com"),
      // array("val0" => "SALVANIA, ELMON  GABRIEL R.", "val1" => "elmonsalvania@llibi.com", "val2" => "elmonmoralessalvania@gmail.com"),
      // array("val0" => "TORRES, KHRISTEL", "val1" => "khristeltorres@llibi.com", "val2" => "padasastorres@gmail.com"),
      // array("val0" => "UCLARAY, JESSA MAE", "val1" => "NA", "val2" => "jessa.uclaray@gmail.com"),
      // array("val0" => "JORDAN, KATHLEEN", "val1" => "kathleenjords@gmail.com", "val2" => "kathleenjords@gmail.com"),
      // array("val0" => "JAVIER, GRACIEL", "val1" => "javier.grasya95@gmail.com", "val2" => "javier.grasya95@gmail.com"),
      // array("val0" => "UCLARAY, JESSA MAE", "val1" => "jessa.uclaray@gmail.com", "val2" => "jessa.uclaray@gmail.com"),
      // array("val0" => "COSME, JANE", "val1" => "cosmejaneeisen@gmail.com", "val2" => "cosmejaneeisen@gmail.com"),
      // array("val0" => "PALUMAR, JOHN PAUL", "val1" => "jmarkpalumar29@gmail.com", "val2" => "jmarkpalumar29@gmail.com"),
      // array("val0" => "DULCE, DEN LOUISE", "val1" => "dendulce@llibi.com", "val2" => "dendulce@llibi.com"),
    );

    // user level 2
    // $datas = array(
    //   array("val0" => "PALTAO, PHOEBE", "val1" => "phoebepaltao@llibi.com", "val2" => "phoebepaltao@llibi.com"),
    //   array("val0" => "ABAD, DIANA", "val1" => "dianaabad@llibi.com", "val2" => "dianaabad@llibi.com"),
    // );

    // user level 3
    // $datas = array(
    //   array("val0" => "ADMIN, ADMIN", "val1" => "ecnanalis@llibi.com", "val2" => "ecnanalis@llibi.com"),
    // );

    foreach ($datas as $key => $data) {
      $explode_name = explode(",", $data['val0']);
      $password = Str::random(8);
      if ($data['val1'] != 'NA') {
        $user = User::create([
          'first_name' => trim($explode_name[1]),
          'last_name' => trim($explode_name[0]),
          'email' => $data['val1'],
          'password' => bcrypt($password),
          'status' => 1,
          'user_level' => 1,
        ]);
        Mail::to($user->email)->bcc('glenilagan@llibi.com')->send(new SendUserCredentialToCae($user, $password));
      }
    }

    return count($datas);
  }

  public function updateUser(Request $request)
  {
    $user = User::where('email', $request->email)->first();
    $user->password = bcrypt($request->password);
    $user->save();

    Mail::to($user->email)->bcc('glenilagan@llibi.com')->send(new SendUserCredentialToCae($user, $request->password));

    return response()->json($user);
  }
}
