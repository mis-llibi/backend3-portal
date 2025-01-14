<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  {{-- <title>Document</title> --}}
</head>

<body style="font-family:Arial, Helvetica, sans-serif;">
  <p style="font-weight:normal;">
    Hi <b>{{ $first_name }},</b><br /><br />
    Your LOA request is <b>approved</b>. Please print a copy LOA and present to the accredited provider upon
    availment.<br /><br />
    For further inquiry and assistance, feel free to contact us through our 24/7 Client Care Hotline.<br /><br />
    Manila Line: (02) 8236-6492<br />
    Mobile number for Calls Only:<br />
    0917-8055424<br />
    0917-8855424<br />
    0919-0749433<br />

    Email: clientcare@llibi.com<br /><br />
  <div>
    <div>
      We value your feedback: <a
        href="{{ $homepage }}/feedback/corporate?q={{ $q }}&company_code={{ $company_code }}&member_id={{ $member_id }}">
        Please click here
      </a>
    </div>
    <div>
      <a
        href="{{ $homepage }}/feedback/corporate?q={{ $q }}&company_code={{ $company_code }}&member_id={{ $member_id }}">
        <img src="{{ $homepage }}/storage/ccportal_1.jpg" alt="Feedback Icon" width="300">
      </a>
    </div>
  </div>
  <br /><br />
  <b>This is an auto-generated Email. Doesn’t support replies and calls.</b>
  </p>


</body>

</html>
