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
    <p>
      Thank you for your interest in signing up for Deel’s healthcare plan. In order to confirm (or decline) your enrollment as well as that of your dependents, please visit this link <a href="{{ $link }}"
        style="font-weight: bold; color: blue;">Self-Enrollment Portal</a> and accomplish this form online.
    </p>

    <p>
      We will ask you to confirm information about yourself and your dependents. Please complete this so that you and/or your dependents may be covered as soon as possible. You are encouraged to complete this within the next 15 days in order to avoid any coverage issues.
    </p>

  </main>
  <br>
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