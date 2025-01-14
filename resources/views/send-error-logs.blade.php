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
    <h4>ERROR:</h4>
    <span>member_id: {{ $data['member_id'] }}</span><br>
    <span>first_name: {{ $data['first_name'] }}</span><br>
    <span>last_name: {{ $data['last_name'] }}</span><br>
    <span>dob: {{ $data['dob'] }}</span><br>

    <br>

    <span>is_dependent: {{ $data['is_dependent'] == 1 ? 'YES' : '' }}</span><br>
    <span>dependent_member_id: {{ $data['dependent_member_id'] }}</span><br>
    <span>dependent_first_name: {{ $data['dependent_first_name'] }}</span><br>
    <span>dependent_last_name: {{ $data['dependent_last_name'] }}</span><br>
    <span>dependent_dob: {{ $data['dependent_dob'] }}</span><br>
  </main>
</body>

</html>
