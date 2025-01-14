<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  {{-- <title>Document</title> --}}
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body>
  <main>
    <div class="flex gap-3 mb-3">
      <div class="personal w-80">
        <h1 class="font-bold">PERSNONAL INFORMATION</h1>
        <div>
          <span><strong>Name</strong></span>
          <span>{{ $complaint['last_name'] }}, {{ $complaint['first_name'] }} {{ $complaint['middle_name'] }}</span>
        </div>
        <div>
          <span><strong>ER Card No.</strong></span>
          <span>{{ $complaint['ercard_no'] }}</span>
        </div>
        <div>
          <span><strong>Date of Birth</strong></span>
          <span>{{ $complaint['dob'] }}</span>
        </div>
        <div>
          <span><strong>Company</strong></span>
          <span>{{ $complaint['company_name'] }}</span>
        </div>
        <div>
          <span><strong>Email</strong></span>
          <span>{{ $complaint['email'] }}</span>
        </div>
      </div>
      <div class="dependent w-80">
        <h1 class="font-bold">DEPENDENT INFORMATION</h1>
        <div>
          <span><strong>Name</strong></span>
          <span>{{ $complaint['deps_last_name'] }}, {{ $complaint['deps_first_name'] }}
            {{ $complaint['deps_middle_name'] }}</span>
        </div>
        <div>
          <span><strong>ER Card No.</strong></span>
          <span>{{ $complaint['deps_ercard_no'] }}</span>
        </div>
        <div>
          <span><strong>Date of Birth</strong></span>
          <span>{{ $complaint['deps_dob'] }}</span>
        </div>
      </div>
    </div>

    <a href="http://localhost:3000/complaint/{{ $complaint['uuid'] }}">Click Here Ma'am Mai</a>
  </main>

</body>

</html>
