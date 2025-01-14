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
            Dear Member, <br /><br />

            We have erroneously sent you an enrollment link: “LLIBI DEPENDENT ENROLLMENT NOTIFICATION”. This is not intended for Deel renewal enrollment, and we have disabled the link. Kindly disregard. <br /><br />


            <span style="font-size:9px;font-style:italic;">From: "Lacson & Lacson Insurance Brokers Inc." <a href="#">
                    noreply@llibi.app
                </a> <br />
                <b>Subject: LLIBI DEPENDENT ENROLLMENT NOTIFICATION</b><br />
                Good day! We are delighted to welcome LLIBI employees as our valued client.<br />
                In order to enroll dependents, please visit this link <a href="#">Self-Enrolment Portal</a> and accomplish this form online.<br /><br />

                We will ask you to confirm information about yourself and your dependents. Please complete this so that you and/or your dependents may be enrolled in LLIBI’s healthcare plan. You are encouraged to complete this from May 9 to May 11, 2023 to avoid any coverage issues.<br /><br />

                <b>This is an auto-generated Email, please do not share. Doesn’t support replies.</b></span> <br /><br />

            You may access the correct enrollment link with subject: “{{ $subject }}”. <br /><br />

            We apologize for the confusion and inconvenience.

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