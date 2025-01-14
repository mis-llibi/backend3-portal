<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  {{-- <title>Document</title> --}}
</head>

<body>
  <main>
    <p>Dear CAE,</p>

    <p>Member is trying to access Client Care Portal and is experiencing membership validation error.
      Please validate member’s information with their company HR. Below are the member details: </p>

    <h4>Principal Details:</h4>
    <span>Company: {{ $member_info['company'] }}</span><br>
    <span>Principal Member Id: {{ $member_info['member_id'] }}</span><br>
    <span>First Name: {{ $member_info['first_name'] }} </span><br>
    <span>Last Name: {{ $member_info['last_name'] }} </span><br>
    <span>Birthday: {{ $member_info['dob'] }} </span><br>
    <span>Mobile Number: {{ $member_info['mobile'] }} </span><br>
    <span>Email: {{ $member_info['email'] }} </span><br>

    @if ($member_info['is_dependent'] == 1)
      <h4>Dependent Details:</h4>
      <span>Dependent Member Id: {{ $member_info['dependent_member_id'] }}</span><br>
      <span>First Name: {{ $member_info['dependent_first_name'] }} </span><br>
      <span>Last Name: {{ $member_info['dependent_last_name'] }} </span><br>
      <span>Birthday: {{ $member_info['dependent_dob'] }} </span><br>
    @endif

    <br>

    <span style="font-weight: bold;">REMARKS:</span><br>
    {!! $remarks !!}
  </main>

  <br />
  <br />

  <hr>
  <footer>
    <div>
      <img src="https://llibi.app/images/lacson-logo.png" alt="LLIBI LOGO" width="200">
    </div>
    <div>
      <small style="color: gray;">&#9400; {{ date('Y') }} Lacson & Lacson Insurance Brokers, Inc. | 15th Floor
        Burgundy Corporate Tower 252 Sen. Gil Puyat Ave., Makati City, 1200</small>
    </div>
    <div>
      <small style="font-weight: bold; text-transform: uppercase;">This is an auto-generated Email. Does not support
        replies.</small>
    </div>
  </footer>
</body>

</html>
