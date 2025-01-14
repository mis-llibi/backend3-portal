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
    <div style='width:800px;font-weight: normal;'>Dear {{ $name }}, <br /><br />

      Good day! <br /><br />

      We are pleased to inform that your healthcare (HMO) plan membership under Philcare (and arranged through Lacson &
      Lacson Insurance Brokers, Inc.) is already <b>active</b>. <br /><br />

      <table style='background-color:#333;'>
        <thead>
          <tr>
            <th style='background-color:#E1E1E1;width:100px;padding:4px;'>Member Type</th>
            <th style='background-color:#E1E1E1;width:100px;padding:4px;'>Last Name</th>
            <th style='background-color:#E1E1E1;width:100px;padding:4px;'>First Name</th>
            <th style='background-color:#E1E1E1;width:100px;padding:4px;'>Middle Name</th>
            <th style='background-color:#E1E1E1;width:100px;padding:4px;'>Certificate No.</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($deps as $row)
            <tr>
              <td style="background-color:#fafafa;padding:4px;">
                {{ $row['relation'] == 'PRINCIPAL' ? 'Principal' : 'Dependent' }}</td>
              <td style="background-color:#fafafa;padding:4px;">{{ ucwords(strtolower($row['last_name'])) }}</td>
              <td style="background-color:#fafafa;padding:4px;">{{ ucwords(strtolower($row['first_name'])) }}</td>
              <td style="background-color:#fafafa;padding:4px;">{{ ucwords(strtolower($row['middle_name'])) }}</td>
              <td style="background-color:#fafafa;padding:4px;">{{ $row['certNo'] }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>

      If you enrolled your dependents and certificate numbers not yet available, please coordinate with your local HR
      team<br /><br />

      Your physical membership card will be delivered to your home address within 3-4 weeks upon you receiving this
      email notification.<br /><br />

      {{-- Philcare cards are targeted to be released prior $date. However, it is dependent on the generation of the
      certificate/membership numbers, and residence of the employee. In the event you do not receive the cards yet, you
      may show your virtual or digital card in the HeyPhil app, or any valid IDs along with your cert/membership number
      as you await of your physical card, and enjoy the benefits starting $date.<br /><br /> --}}

      To offer more convenient and accessible healthcare services and to view your <b>virtual or digital card</b>, you
      may download Philcare’s Hey Phil mobile application. Below is the process for further details. <br /><br />

      1) <b>Download the App</b> <br />
      • Search ‘Hey Phil’ available in Google Play store or in App Store <br />
      • Create Hey Phil account through registration.<br /><br />

      2) <b>Manual Registration</b> <br />
      • Key in certificate number <br />
      • Click ‘verify’ to register <br /><br />

      3) <b>Facebook Registration</b> <br />
      • Once certificate number and birthdate has been verified, select ‘Continue with Facebook’ <br />
      • Key in Facebook credentials such as username and password <br /><br />

      For any medical availment inquiries, you may contact PhilCare’s 24/7 Customer Hotline at (02) 8462-1800 or email
      <a href='mailto:callcenter@philcare.com.ph'>callcenter@philcare.com.ph</a>.<br /><br />

      Please refer to the attached benefits flyer for an overview of your coverage.<br /><br />

      Should you require additional assistance, you may also contact Lacson & Lacson Insurance Brokers, Inc.<br /><br />

      <b>Julie Pasumbal</b><br />
      Corporate Accounts Executive<br />
      Email: <a href='mailto:juliepasumbal@llibi.com'>juliepasumbal@llibi.com</a><br />
      Mobile: 0917-7102889<br /><br />

      <b>This is an auto-generated Email. Doesn’t support replies and calls.</b>

      <br /><br />

      <p style='color:gray;font-size:13px;'>
        <b style='font-size:16px;'>Disclaimer</b><br /><br />

        The information contained in this communication from the sender is confidential. It is intended solely for use
        by the recipient and others authorized to receive it. If you are not the recipient, you are hereby notified that
        any disclosure, copying, distribution or taking action in relation of the contents of this information is
        strictly prohibited and may be unlawful.<br /><br />

        This email has been scanned for viruses and malware, and may have been automatically archived by Mimecast, a
        leader in email security and cyber resilience. Mimecast integrates email defenses with brand protection,
        security awareness training, web security, compliance and other essential capabilities. Mimecast helps protect
        large and small organizations from malicious activity, human error and technology failure; and to lead the
        movement toward building a more resilient world. To find out more, visit our website.
      </p>
    </div>
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
