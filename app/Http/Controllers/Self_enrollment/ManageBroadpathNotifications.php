<?php

namespace App\Http\Controllers\Self_enrollment;

use App\Http\Controllers\Controller;
//use Illuminate\Http\Request;

use App\Http\Controllers\NotificationController;

class ManageBroadpathNotifications extends Controller
{
    public function getDates()
    {
        return [
            'dateStart'            => '2024-06-13',
            'dateWarningUntouched' => '2024-06-04',
            'dateFinalWarning'     => '2024-06-20',
            'dateFormLocked'       => '2024-06-21'
        ];
    }

    public function rolloverInvite($info)
    {
        $startDate = date('F j', strtotime($this->getDates()['dateStart']));
        $dateFinalWarning = date('F j, Y', strtotime($this->getDates()['dateFinalWarning']));
        $dateFormLocked = date('F j', strtotime($this->getDates()['dateFormLocked']));

        $link = '<a href="https://portal.llibi.app/self-enrollment/broadpath?id=' . $info['hash'] . '">Self-Enrolment Portal</a>';
        //$link = '<a href="http://localhost:3000/self-enrollment/broadpath?id='.$info['hash'].'">Self-Enrolment Portal</a>';

        $body =
            'Dear Member,<br /><br />

        Your Philcare healthcare (HMO) plan under your employer, Broadpath Global Services Inc., is for renewal on July 1, 2024.<br /><br />

        We are pleased to inform you that the current coverage and premiums have been retained.<br /><br />

        In order to confirm your renewal enrollment as well as that of your dependents, please visit this link ' . $link . ' and accomplish this form online.<br /><br />
        
        We will ask you to confirm information about yourself and your dependents. Please complete this so that you and/or your dependents may be enrolled in BroadPath’s renewal. You are encouraged to complete this from ' . $startDate . ' to ' . $dateFinalWarning . ' to avoid any coverage issues.<br /><br />
        
        If you do not respond to this notification by ' . $dateFinalWarning . ', you and your existing dependents will be automatically enrolled in Broadpath’s healthcare plan renewal.<br /><br />

        <b>This is an auto-generated Email, please do not share. Doesn’t support replies and calls.</b>';

        $mailMsg = array(
            'subject' => 'BROADPATH RENEWAL ENROLLMENT NOTIFICATION',
            'body' => '<div style="font-weight:normal;">' . $body . '</div>'
        );

        if (!empty($info['email'])) (new NotificationController)
            ->OldMailNotification($info['name'], $info['email'], $mailMsg);

        if (!empty($info['email2'])) (new NotificationController)
            ->OldMailNotification($info['name'], $info['email2'], $mailMsg);

        $smsBody =
            "From BroadPath & LLIBI:\n\nGood day!\n\nWe are delighted to welcome BroadPath employees as our valued client.\n\nPlease follow enrollment procedure mentioned in the Welcome Email sent over to your personal and company email.\n\nThis is an auto-generated SMS. Doesn’t support replies and calls";

        if (!empty($info['mobile'])) (new NotificationController)
            ->SmsNotification($info['mobile'], $smsBody);
    }

    public function rolloverEveryThreeDays($info, $dateFinalWarning, $dateFormLocked)
    {
        $link =
            '<a href="https://portal.llibi.app/self-enrollment/broadpath?id=' . $info['hash'] . '">Self-Enrolment Portal</a>';

        $body =
            'Dear Member, <br /><br />
        We have noticed that you haven’t completed the online enrollment forms. Please visit this link ' . $link . ' to complete this now. You are encouraged to complete this until ' . date('F j, Y', strtotime($dateFinalWarning)) . ' to avoid any coverage issues.
        <br /><br />

        If there are changes in the dependent enrollment, you may make changes until ' . date('F j, Y', strtotime($dateFinalWarning)) . '. Renewal Enrollment will officially close on ' . date('F j, Y', strtotime($dateFormLocked)) . ' and after that, changes will no longer be accepted. If you have already completed the form, you may disregard this message.
        <br /><br />

        <b>This is an auto-generated Email. Doesn’t support replies and calls.</b>';

        $mailMsg = array(
            'subject' => 'BROADPATH RENEWAL ENROLLMENT NOTIFICATION',
            'body' =>
            '<table style="font-weight:normal;width:600px;">
                    <tr>
                        <td></td>
                    </tr>
                    <tr>
                        <td style="font-weight:normal;">' . $body . '</td>
                    </tr>
                </table>'
        );

        if (!empty($info['email'])) (new NotificationController)
            ->OldMailNotification($info['name'], $info['email'], $mailMsg);

        if (!empty($info['email2'])) (new NotificationController)
            ->OldMailNotification($info['name'], $info['email2'], $mailMsg);

        $smsBody =
            "From BroadPath & LLIBI:\n\nWe have noticed that you haven’t completed the online enrollment forms.\n\nPlease visit the link provided in the welcome email to proceed with the enrollment.\n\nThis is an auto-generated SMS. Doesn’t support replies and calls";

        if (!empty($info['mobile'])) (new NotificationController)
            ->SmsNotification($info['mobile'], $smsBody);
    }

    public function rolloverWarningUntouchedForm($info, $dateFinalWarning, $dateFormLocked)
    {
        $startDate = date('F j, Y', strtotime($this->getDates()['dateStart']));

        $link = '<a href="https://portal.llibi.app/self-enrollment/broadpath?id=' . $info['hash'] . '">Self-Enrolment Portal</a>';

        $body =
            'Dear Member, <br /><br />
        
        We have noticed that you did not respond to the renewal notification last ' . $startDate . '.<br /><br />
        
        In order to confirm your renewal enrollment as well as that of your dependents, please visit this link ' . $link . ' to complete this now. You are encouraged to complete this until ' . date('F j, Y', strtotime($dateFinalWarning)) . ' to avoid any coverage issues.<br /><br />
        
        Enrollment will officially close on ' . date('F j, Y', strtotime($dateFormLocked)) . ' and after that, changes will no longer be accepted.<br /><br />

        If you do not respond to this notification by ' . date('F j', strtotime($dateFinalWarning)) . ', you and your existing dependents will be automatically enrolled in Broadpath’s healthcare plan renewal.<br /><br />

        <b>This is an auto-generated Email. Doesn’t support replies and calls.</b>';

        $mailMsg = array(
            'subject' => 'BROADPATH RENEWAL ENROLLMENT NOTIFICATION',
            'body' =>
            '<table style="font-weight:normal;width:600px;">
                    <tr>
                        <td></td>
                    </tr>
                    <tr>
                        <td style="font-weight:normal;">' . $body . '</td>
                    </tr>
                </table>'
        );

        if (!empty($info['email'])) (new NotificationController)
            ->OldMailNotification($info['name'], $info['email'], $mailMsg);

        if (!empty($info['email2'])) (new NotificationController)
            ->OldMailNotification($info['name'], $info['email2'], $mailMsg);

        $smsBody =
            "From BroadPath & LLIBI:\n\nThis is to inform you that today, " . date('F j, Y', strtotime($dateFinalWarning)) . ", is the last day of BroadPath’s open enrollment.\n\nPlease visit the link provided in the welcome email to proceed with the enrollment.\n\nThis is an auto-generated SMS. Doesn’t support replies and calls";

        if (!empty($info['mobile'])) (new NotificationController)
            ->SmsNotification($info['mobile'], $smsBody);
    }

    public function rolloverWarningLastDay($info, $dateFinalWarning, $dateFormLocked)
    {
        $startDate = date('F j', strtotime($this->getDates()['dateStart']));

        $link = '<a href="https://portal.llibi.app/self-enrollment/broadpath?id=' . $info['hash'] . '">Self-Enrolment Portal</a>';

        $body =
            'This is to inform you that today, ' . date('F j, Y', strtotime($dateFinalWarning)) . ', is the last day of BroadPath’s open enrollment. Please visit this link ' . $link . ' to complete your submission.<br /><br />

        Kindly review your dependents’ information submitted as this will be final and no changes will be accepted after the open enrollment.<br /><br />

        Enrollment will officially close on ' . date('F j, Y', strtotime($dateFormLocked)) . ' and will no longer accept enrollment and any changes.<br /><br />

        If you do not respond to this notification by ' . date('F j', strtotime($dateFinalWarning)) . ', you and your existing dependents will be automatically enrolled in Broadpath’s healthcare plan renewal.<br /><br />

        If you have already completed the form, you may disregard this message.<br /><br />

        <b>This is an auto-generated Email. Doesn’t support replies and calls.</b>';

        $mailMsg = array(
            'subject' => 'BROADPATH RENEWAL ENROLLMENT NOTIFICATION',
            'body' =>
            '<table style="font-weight:normal;width:600px;">
                <tr>
                    <td></td>
                </tr>
                <tr>
                    <td style="font-weight:normal;">' . $body . '</td>
                </tr>
            </table>'
        );

        if (!empty($info['email'])) (new NotificationController)
            ->OldMailNotification($info['name'], $info['email'], $mailMsg);

        if (!empty($info['email2'])) (new NotificationController)
            ->OldMailNotification($info['name'], $info['email2'], $mailMsg);

        $smsBody =
            "From BroadPath & LLIBI:\n\nThis is to inform you that today, " . date('F j, Y', strtotime($dateFinalWarning)) . ", is the last day of BroadPath’s open enrollment.\n\nPlease visit the link provided in the welcome email to proceed with the enrollment.\n\nThis is an auto-generated SMS. Doesn’t support replies and calls";

        if (!empty($info['mobile'])) (new NotificationController)
            ->SmsNotification($info['mobile'], $smsBody);
    }

    public function rolloverReminderLock($info)
    {
        $link = '<a href="https://portal.llibi.app/self-enrollment/broadpath?id=' . $info['hash'] . '">Self-Enrolment Portal</a>';

        $body =
            'Thank you for your enrollment submission. 
        <br /><br />

        Enrollment of your dependents has officially closed. All submission is final and changes will no longer be accepted.
        <br /><br />

        <b>This is an auto-generated Email. Doesn’t support replies and calls.</b>';

        $mailMsg = array(
            'subject' => 'BROADPATH RENEWAL ENROLLMENT NOTIFICATION',
            'body' =>
            '<table style="font-weight:normal;width:600px;">
                    <tr>
                        <td></td>
                    </tr>
                    <tr>
                        <td style="font-weight:normal;">' . $body . '</td>
                    </tr>
                </table>'
        );

        if (!empty($info['email'])) (new NotificationController)
            ->OldMailNotification($info['name'], $info['email'], $mailMsg);

        if (!empty($info['email2'])) (new NotificationController)
            ->OldMailNotification($info['name'], $info['email2'], $mailMsg);

        $smsBody =
            "From BroadPath & LLIBI:\n\nThank you for your enrollment submission.\n\nEnrollment of your dependents has officially closed. All submission is final and no longer allowed to make any changes\n\nThis is an auto-generated SMS. Doesn’t support replies and calls";

        if (!empty($info['mobile'])) (new NotificationController)
            ->SmsNotification($info['mobile'], $smsBody);
    }

    public function invite($info)
    {
        $startDate = date('F j', strtotime($this->getDates()['dateStart']));
        $dateFinalWarning = date('F j, Y', strtotime($this->getDates()['dateFinalWarning']));

        $link = '<a href="https://portal.llibi.app/self-enrollment/broadpath?id=' . $info['hash'] . '">Self-Enrolment Portal</a>';

        $body =
            'Good day!
        We are delighted to welcome BroadPath employees as our valued client.<br />
        In order to enroll dependents, please visit this link ' . $link . ' and accomplish this form online.<br /><br />
        
        We will ask you to confirm information about yourself and your dependents. Please complete this so that you and/or your dependents may be enrolled in BroadPath’s healthcare plan. You are encouraged to complete this from ' . $startDate . ' to ' . $dateFinalWarning . ' to avoid any coverage issues.<br /><br />
        
        <b>This is an auto-generated Email, please do not share. Doesn’t support replies and calls.</b>';

        $mailMsg = array(
            'subject' => 'BROADPATH DEPENDENT ENROLLMENT NOTIFICATION',
            'body' => '<div style="font-weight:normal;">' . $body . '</div>'
        );

        if (!empty($info['email'])) (new NotificationController)
            ->OldMailNotification($info['name'], $info['email'], $mailMsg);

        if (!empty($info['email2'])) (new NotificationController)
            ->OldMailNotification($info['name'], $info['email2'], $mailMsg);

        $smsBody =
            "From BroadPath & LLIBI:\n\nGood day!\n\nWe are delighted to welcome BroadPath employees as our valued client.\n\nPlease follow enrollment procedure mentioned in the Welcome Email sent over to your personal and company email.\n\nThis is an auto-generated SMS. Doesn’t support replies and calls";

        if (!empty($info['mobile'])) (new NotificationController)
            ->SmsNotification($info['mobile'], $smsBody);
    }

    public function submittedWithoutDep($info)
    {
        $startDate = date('F j', strtotime($this->getDates()['dateStart']));
        $dateFinalWarning = date('F j, Y', strtotime($this->getDates()['dateFinalWarning']));
        $dateFormLocked = date('F j, Y', strtotime($this->getDates()['dateFormLocked']));

        $body =
            'Thank you for submitting your enrollment.<br /><br />

        If there are changes in the dependent enrollment, you may make changes until ' . $dateFinalWarning . '. Enrollment will officially close on ' . $dateFormLocked . ' and will no longer accept any changes.
        <br /><br />

        <b>This is an auto-generated Email. Doesn’t support replies and calls.</b>';

        $mailMsg = array(
            'subject' => 'BROADPATH DEPENDENT ENROLLMENT NOTIFICATION',
            'body' => '<div style="font-weight:normal;">' . $body . '</div>'
        );

        if (!empty($info['email'])) (new NotificationController)
            ->OldMailNotification($info['name'], $info['email'], $mailMsg);

        if (!empty($info['email2'])) (new NotificationController)
            ->OldMailNotification($info['name'], $info['email2'], $mailMsg);

        $smsBody =
            "From BroadPath & LLIBI:\n\nThank you for submitting your enrollment, you can view your enrolled dependents in the confirmation email sent to you.\n\nThis is an auto-generated SMS. Doesn’t support replies and calls";

        if (!empty($info['mobile'])) (new NotificationController)
            ->SmsNotification($info['mobile'], $smsBody);
    }

    public function submittedWithDep($info)
    {
        $startDate = date('F j', strtotime($this->getDates()['dateStart']));
        $dateFinalWarning = date('F j, Y', strtotime($this->getDates()['dateFinalWarning']));
        $dateFormLocked = date('F j, Y', strtotime($this->getDates()['dateFormLocked']));

        $body =
            'Thank you for submitting your enrollment. Your submission is final and can no longer make any changes. <br /><br />
        
        Below is the summary of your dependents: <br /><br />

        ' . $info['depInfo'] . '
        
        ' . $info['succeeding'] . '

        <br />

        ' . $info['premiumComputation'] . '

        <br />

        Your registered home address for card delivery is ' . $info['address'] . '
        <br /><br />

        <b>This is an auto-generated Email. Doesn’t support replies and calls.</b>';

        $mailMsg = array(
            'subject' => 'BROADPATH DEPENDENT ENROLLMENT NOTIFICATION',
            'body' => '<div style="font-weight:normal;">' . $body . '</div>'
        );

        if (!empty($info['email'])) (new NotificationController)
            ->OldMailNotification($info['name'], $info['email'], $mailMsg);

        if (!empty($info['email2'])) (new NotificationController)
            ->OldMailNotification($info['name'], $info['email2'], $mailMsg);

        $smsBody =
            "From BroadPath & LLIBI:\n\nThank you for submitting your enrollment, you can view your enrolled dependents in the confirmation email sent to you.\n\nThis is an auto-generated SMS. Doesn’t support replies and calls";

        if (!empty($info['mobile'])) (new NotificationController)
            ->SmsNotification($info['mobile'], $smsBody);
    }

    public function approvedWithCertificateNo($info)
    {
        $insSms = implode("\n", $info['insSms']);
        $name = ucwords(strtolower($info['name']));

        $insMail = implode(" ", $info['insMail']);

        $date = 'July 1';

        //addressComplete <br /><br />
        $body =
            "<div style='width:800px;font-weight: normal;'>Dear $name, <br /><br />
        
        Good day! <br /><br />

        We are pleased to inform that your healthcare (HMO) plan membership under Philcare (and arranged through Lacson & Lacson Insurance Brokers, Inc.) is already <b>active</b>. <br /><br />

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
                $insMail
            </tbody>
        </table>
        
        For existing members, your Philcare membership card is reactivated and may be used for availment. Philcare will not issue new membership cards.<br /><br />

        For new members, your Philcare membership card will be delivered to your home address within 2-3 weeks upon receiving this notification.<br /><br />

        To offer more convenient and accessible healthcare services and to view your <b>virtual or digital card</b>, you may download Philcare’s Hey Phil mobile application. Below is the process and infographics are attached for further details. <br /><br />
        
        1) <b>Download the App</b> <br />
            • Search ‘Hey Phil’ available in Google Play store or in App Store <br />
            • Create Hey Phil account through registration.<br /><br />

        2) <b>Manual Registration</b> <br />
            • Key in certificate number <br />
            • Click ‘verify’ to register <br /><br />

        3) <b>Facebook Registration</b> <br />
            • Once certificate number and birthdate has been verified, select ‘Continue with Facebook’ <br />
            • Key in Facebook credentials such as username and password <br /><br />

        For any medical availment inquiries, you may contact PhilCare’s 24/7 Customer Hotline at (02) 8462-1800 or email <a href='mailto:callcenter@philcare.com.ph'>callcenter@philcare.com.ph</a>.<br /><br />

        Please refer to the attached benefits flyer for an overview of your coverage.<br /><br />

        You may also refer to the pre-recorded <a href='https://philcare-my.sharepoint.com/personal/christine_magdaong_philcare_com_ph/_layouts/15/stream.aspx?id=%2Fpersonal%2Fchristine%5Fmagdaong%5Fphilcare%5Fcom%5Fph%2FDocuments%2FAttachments%2Fvideo1504235462%2Emp4&wdLOR=c2F07CFF3%2D4EEF%2D499E%2DAA0A%2D3079A551E6C0&ct=1667963948353&or=Outlook-Body&cid=62383CD0-0666-46B4-8095-11931B62BA6F&ga=1'>Benefits Orientation Video</a> for an in-depth presentation of your benefits, policy exclusion and availment process.<br /><br />
        
        Should you require additional assistance, you may also contact Lacson & Lacson Insurance Brokers, Inc.<br /><br />

            <b>Julie Pasumbal</b><br />
            Corporate Accounts Executive<br />
            Email: <a href='mailto:juliepasumbal@llibi.com'>juliepasumbal@llibi.com</a><br />
            Mobile: 0917-7102889<br /><br />

            <b>This is an auto-generated Email. Doesn’t support replies and calls.</b>

            <br /></br />

            <p style='color:gray;font-size:13px;'>
            <b style='font-size:16px;'>Disclaimer</b><br /><br />

            The information contained in this communication from the sender is confidential. It is intended solely for use by the recipient and others authorized to receive it. If you are not the recipient, you are hereby notified that any disclosure, copying, distribution or taking action in relation of the contents of this information is strictly prohibited and may be unlawful.<br /><br />

            This email has been scanned for viruses and malware, and may have been automatically archived by Mimecast, a leader in email security and cyber resilience. Mimecast integrates email defenses with brand protection, security awareness training, web security, compliance and other essential capabilities. Mimecast helps protect large and small organizations from malicious activity, human error and technology failure; and to lead the movement toward building a more resilient world. To find out more, visit our website.
            </p>

        </div>";

        /* You may also refer to the pre-recorded <a href='https://philcare-my.sharepoint.com/:v:/g/personal/christine_magdaong_philcare_com_ph/EWVWPZ05NJBLnakk7bo2ArMBDUj2_k7J_E1liUuRjdKwnA?CT=1667963948353&OR=Outlook-Body&CID=62383CD0-0666-46B4-8095-11931B62BA6F&wdLOR=c2F07CFF3-4EEF-499E-AA0A-3079A551E6C0' target='_blank'>Benefits Orientation Video</a> for an in-depth presentation of your benefits, policy exclusion and availment process.<br /><br /> */

        $attachment = [
            //'public/Self_enrollment/Broadpath/001Welcome_pack/BROADPATH - WELCOME LETTER 2024.pdf',
            //'public/Self_enrollment/Broadpath/001Welcome_pack/Broadpath NTP.pdf',
            'public/Self_enrollment/Broadpath/001Welcome_pack/SOC_SME LUXE - BROADPATH.pdf',
        ];

        $mailMsg = array(
            'subject' => 'BROADPATH DEPENDENT ENROLLMENT NOTIFICATION',
            'body' => '<div style="font-weight:normal;">' . $body . '</div>',
            'attachment' => $attachment
        );

        if (!empty($info['email'])) (new NotificationController)
            ->OldMailNotification($info['name'], $info['email'], $mailMsg);

        if (!empty($info['email2'])) (new NotificationController)
            ->OldMailNotification($info['name'], $info['email2'], $mailMsg);

        $smsBody =
            "From BroadPath & LLIBI:\n\nDear $name, \n\nGood day! \nWe are pleased to inform that your healthcare (HMO) plan membership under Philcare (and arranged through Lacson & Lacson Insurance Brokers, Inc.) is already active. \n\n$insSms \n\nYour physical membership card will be delivered to your home address within 2-3 weeks. In the meantime, you may present your virtual or digital card in the HeyPhil app or certificate number to Philcare’s accredited facilities, along with valid ID, in order to make claim a while awaiting for your physical card. \n\nThis is an auto-generated SMS. Doesn’t support replies and calls.";

        if (!empty($info['mobile'])) (new NotificationController)
            ->SmsNotification($info['mobile'], $smsBody);
    }

    public function everyThreeDays($info, $dateFinalWarning, $dateFormLocked)
    {
        $link =
            '<a href="https://portal.llibi.app/self-enrollment/broadpath?id=' . $info['hash'] . '">Self-Enrolment Portal</a>';

        $body =
            'We have noticed that you haven’t completed the online enrollment forms. Please visit this link ' . $link . ' to complete this now. You are encouraged to complete this until ' . date('F j, Y', strtotime($dateFinalWarning)) . ' to avoid any coverage issues.
        <br /><br />

        If there are changes in the dependent enrollment, you may make changes until ' . date('F j, Y', strtotime($dateFinalWarning)) . '. Enrollment will officially close on ' . date('F j, Y', strtotime($dateFormLocked)) . ' and will no longer accept any changes.
        <br /><br />

        If you have already completed the form, you may disregard this message.
        <br /><br />

        <b>This is an auto-generated Email. Doesn’t support replies and calls.</b>';

        $mailMsg = array(
            'subject' => 'BROADPATH DEPENDENT ENROLLMENT NOTIFICATION',
            'body' =>
            '<table style="font-weight:normal;width:500px;">
                    <tr>
                        <td></td>
                    </tr>
                    <tr>
                        <td style="font-weight:normal;">' . $body . '</td>
                    </tr>
                </table>'
        );

        if (!empty($info['email'])) (new NotificationController)
            ->OldMailNotification($info['name'], $info['email'], $mailMsg);

        if (!empty($info['email2'])) (new NotificationController)
            ->OldMailNotification($info['name'], $info['email2'], $mailMsg);

        $smsBody =
            "From BroadPath & LLIBI:\n\nWe have noticed that you haven’t completed the online enrollment forms.\n\nPlease visit the link provided in the welcome email to proceed with the enrollment.\n\nThis is an auto-generated SMS. Doesn’t support replies and calls";

        if (!empty($info['mobile'])) (new NotificationController)
            ->SmsNotification($info['mobile'], $smsBody);
    }

    public function warningLastDay($info, $dateFinalWarning, $dateFormLocked)
    {
        $link = '<a href="https://portal.llibi.app/self-enrollment/broadpath?id=' . $info['hash'] . '">Self-Enrolment Portal</a>';

        $body =
            'This is to inform you that today, ' . date('F j, Y', strtotime($dateFinalWarning)) . ', is the last day of BroadPath’s open enrollment. Please visit this link ' . $link . ' to complete your submission.<br /><br />

        Kindly review your dependents’ information submitted as this will be final and no changes will be accepted after the open enrollment.<br /><br />

        Enrollment will officially close on ' . date('F j, Y', strtotime($dateFormLocked)) . ' and will no longer accept enrollment and any changes.
        If you have already completed the form, you may disregard this message.<br /><br />

        <b>This is an auto-generated Email. Doesn’t support replies and calls.</b>';

        $mailMsg = array(
            'subject' => 'BROADPATH DEPENDENT ENROLLMENT NOTIFICATION',
            'body' =>
            '<table style="font-weight:normal;width:500px;">
                    <tr>
                        <td></td>
                    </tr>
                    <tr>
                        <td style="font-weight:normal;">' . $body . '</td>
                    </tr>
                </table>'
        );

        if (!empty($info['email'])) (new NotificationController)
            ->OldMailNotification($info['name'], $info['email'], $mailMsg);

        if (!empty($info['email2'])) (new NotificationController)
            ->OldMailNotification($info['name'], $info['email2'], $mailMsg);

        $smsBody =
            "From BroadPath & LLIBI:\n\nThis is to inform you that today, " . date('F j, Y', strtotime($dateFinalWarning)) . ", is the last day of BroadPath’s open enrollment.\n\nPlease visit the link provided in the welcome email to proceed with the enrollment.\n\nThis is an auto-generated SMS. Doesn’t support replies and calls";

        if (!empty($info['mobile'])) (new NotificationController)
            ->SmsNotification($info['mobile'], $smsBody);
    }

    public function reminderLock($info)
    {
        $link = '<a href="https://portal.llibi.app/self-enrollment/broadpath?id=' . $info['hash'] . '">Self-Enrolment Portal</a>';

        $body =
            'Thank you for your enrollment submission. 
        <br /><br />

        Enrollment of your dependents has officially closed. All submission is final and no longer allowed to make any changes.
        <br /><br />

        <b>This is an auto-generated Email. Doesn’t support replies and calls.</b>';

        $mailMsg = array(
            'subject' => 'BROADPATH DEPENDENT ENROLLMENT NOTIFICATION',
            'body' =>
            '<table style="font-weight:normal;width:500px;">
                    <tr>
                        <td></td>
                    </tr>
                    <tr>
                        <td style="font-weight:normal;">' . $body . '</td>
                    </tr>
                </table>'
        );

        if (!empty($info['email'])) (new NotificationController)
            ->OldMailNotification($info['name'], $info['email'], $mailMsg);

        if (!empty($info['email2'])) (new NotificationController)
            ->OldMailNotification($info['name'], $info['email2'], $mailMsg);

        $smsBody =
            "From BroadPath & LLIBI:\n\nThank you for your enrollment submission.\n\nEnrollment of your dependents has officially closed. All submission is final and no longer allowed to make any changes\n\nThis is an auto-generated SMS. Doesn’t support replies and calls";

        if (!empty($info['mobile'])) (new NotificationController)
            ->SmsNotification($info['mobile'], $smsBody);
    }
}
