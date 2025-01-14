<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  {{-- <title>Document</title> --}}
</head>

<body>
  Hi <b>{{ $first_name }},</b>
  <br /><br />

  <p>Please print a copy of LOA and present to the accredited provider upon availment with doctor’s laboratory referral.
  </p>
  <p>This is a pre-approved Outpatient Procedure LOA with approval code for guaranteed amount indicated. If the
    guaranteed amount is less than the actual laboratory cost or there are additional laboratory procedures as advised
    by the doctor, please contact our Client Care Hotline for re-approval.</p>

  <p>Outpatient procedures shall be paid by the member if your diagnosis will fall under the following policy exclusions
    including but not limited to: </p>

  <span>1. Routine child health examination</span><br>
  <span>2. Essentially Normal findings</span><br>
  <span>3. General Medical Examination and Investigation</span><br>
  <span>4. Disorder of Refraction</span><br>
  <span>5. Supervision of Normal pregnancy</span><br>

  <p>Other policy exclusions of Ateneo de Manila University not stated above shall be strictly enforced. </p>

  <p>For further inquiry and assistance, feel free to contact us through following channels: </p>

  <span>ADMU Onsite</span><br>
  <span>Mondays to Fridays 8:00AM to 5:00PM, except holidays</span><br>
  <span>Mobile number: 0917-851 9514</span><br>
  <span>Email: onsiteadmu@llibi.com</span><br>
  <br>

  <span>24/7 Client Care Hotline:</span><br>
  <span>Manila Line: (02) 8236-6492</span><br>
  <span>Mobile number for Calls Only:</span><br>
  <span>0917-8055424</span><br>
  <span>0917-8855424</span><br>
  <span>0919-0749433</span><br>
  <span>Email: clientcare@llibi.com</span><br>
  <br>

  <span style="font-weight: bold; color: red;">
    CONFIDENTIALITY STATEMENT
  </span>
  <br>
  <span style="font-style: italic;">
    This email may contain personal sensitive information and is intended for the recipients to which it is addressed
    only. If you are not the recipient to whom this email is addressed, reproduction or dissemination of any part of
    this email is strictly prohibited. If you received this in error, please contact sender and immediately delete this
    email and any attachments.
  </span>
  <br />

  <br />
  <div>
    <div>
      We value your feedback: <a href="{{ $feedback_url }}">
        Please click here
      </a>
    </div>
    <div>
      <a href="{{ $feedback_url }}">
        <img src="{{ $homepage }}/storage/ccportal_1.jpg" alt="Feedback Icon" width="300">
      </a>
    </div>
  </div>
  <br /><br />

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
