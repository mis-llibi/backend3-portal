<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  {{-- <title>Document</title> --}}
</head>

<body>
  <p>Request for {{ $lastName ?? '' }}, {{ $firstName ?? '' }} has not been attended for more than {{ $minutes }}
    minutes.</p>
</body>

</html>
