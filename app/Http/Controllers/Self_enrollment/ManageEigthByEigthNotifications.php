<?php

namespace App\Http\Controllers\Self_enrollment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Controllers\NotificationController;

class ManageEigthByEigthNotifications extends Controller
{
    public function getDates()
    {
        return [
            'dateStart'        => '2023-04-24',
            'dateFinalWarning' => '2023-05-25',
            'dateFormLocked'   => '2023-05-26'
        ];
    }

    public function invite($info)
    {
        $startDate = date('F j', strtotime($this->getDates()['dateStart']));
        $dateFinalWarning = date('F j, Y', strtotime($this->getDates()['dateFinalWarning']));

        $link = '<a href="https://portal.llibi.app/self-enrollment/8x8?id='.$info['hash'].'">Self-Enrolment Portal</a>';

        $body =
        'Good day!
        We are delighted to welcome 8x8 employees as our valued client.<br />
        In order to enroll dependents, please visit this link '.$link.' and accomplish this form online.<br /><br />
        
        We will ask you to confirm information about yourself and your dependents. Please complete this so that you and/or your dependents may be enrolled in 8x8’s healthcare plan. You are encouraged to complete this from '.$startDate.' to '.$dateFinalWarning.' to avoid any coverage issues.<br /><br />
        
        <b>This is an auto-generated Email, please do not share. Doesn’t support replies and calls.</b>';

        $mailMsg = array(
            'subject' => '8x8 DEPENDENT ENROLLMENT NOTIFICATION',
            'body' => '<div style="font-weight:normal;">'.$body.'</div>'
        );

        if(!empty($info['email']))
            (new NotificationController)
                ->MailNotification($info['name'], $info['email'], $mailMsg);
                
        if(!empty($info['email2']))
            (new NotificationController)
                ->MailNotification($info['name'], $info['email2'], $mailMsg);

        $smsBody = 
        "From LLIBI:\n\nGood day!\n\nWe are delighted to welcome 8x8 employees as our valued client.\n\nPlease follow enrollment procedure mentioned in the Welcome Email sent over to your personal and company email.\n\nThis is an auto-generated SMS. Doesn’t support replies and calls";

        if(!empty($info['mobile']))
            (new NotificationController)
                ->SmsNotification($info['mobile'], $smsBody);
    }

    public function withSalarySendListToBenAdmin($list)
    {
        $tbl = '';
        foreach($list as $row) {
            $tbl .= 
            '<tr>
                <td style="background-color:#fafafa;">'.$row['employee_id'].'</td>
                <td style="background-color:#fafafa;">'.$row['name'].'</td>
                <td style="background-color:#fafafa;">'.$row['salary'].'</td>
            </tr>';
        }

        $body = 
        'Hi Mr. Sam,
        
        <br /><br />

        Here is the list of 8x8 Employees eligible for life insurance.
        
        <br /><br />

        <table style="width:350px;border:2px solid black">
            <thead>
                <tr>
                    <th style="background-color:#fafafa;font-weight:bold;">Employee ID</th>
                    <th style="background-color:#fafafa;font-weight:bold;">Name</th>
                    <th style="background-color:#fafafa;font-weight:bold;">Salary</th>
                </tr>
            </thead>
            <tbody>
                '.$tbl.'
            </tbody>
        </table>

        <br />

        <b>This is an auto-generated Email. Doesn’t support replies and calls.</b>';

        $mailMsg = array(
            'subject' => '8x8 DEPENDENT ENROLLMENT NOTIFICATION',
            'body' => 
                '<table style="font-weight:normal;width:500px;">
                    <tr>
                        <td style="font-weight:normal;">'.$body.'</td>
                    </tr>
                </table>'
        );

        (new NotificationController)
            ->MailNotification('SUP SAM', 'markimperial@llibi.com', $mailMsg);
            
        $smsBody = 
        "Hi Mr. Sam:\n\nThere’s a new list of 8x8 employee eligible for life insurance, please check your email.\n\nThis is an auto-generated SMS. Doesn’t support replies and calls";

        (new NotificationController)
            ->SmsNotification('09989829829', $smsBody);
    }

    public function approvedWithLifeInsuranceNo($info)
    {
        $name = ucwords(strtolower($info['name']));

        //addressComplete <br /><br />
        $body = 
        "<div style='width:800px;font-weight: normal;'>Dear $name, <br /><br />
        
        Good day! <br /><br />

        We are pleased to inform that your Life Insurance plan membership under Generali (and arranged through Lacson & Lacson Insurance Brokers, Inc.) is already <b>active</b>. <br /><br />
        
        To offer more convenient and accessible life insurance services and to view your <b>virtual or digital card</b>. <br /><br />
        
    
            <b>This is an auto-generated Email. Doesn’t support replies and calls.</b>
        </div>";

        $attachment = [
            'public/Self_enrollment/8x8/001Welcome_pack/DEEL - WELCOME LETTER.pdf',
            'public/Self_enrollment/8x8/001Welcome_pack/SOC_SME LUXE-DEEL (Updated)_111022.pdf',
        ];

        $mailMsg = array(
            'subject' => '8x8 DEPENDENT ENROLLMENT NOTIFICATION',
            'body' => '<div style="font-weight:normal;">'.$body.'</div>',
            'attachment' => $attachment
        );

        if(!empty($info['email']))
            (new NotificationController)
                ->MailNotification($info['name'], $info['email'], $mailMsg);
        if(!empty($info['email2']))
            (new NotificationController)
                ->MailNotification($info['name'], $info['email2'], $mailMsg);

        $smsBody = 
        "LLIBI: Dear $name, \n\nGood day! \nWe are pleased to inform that your life insurance membership under Generali (and arranged through Lacson & Lacson Insurance Brokers, Inc.) is already active. \n\nThis is an auto-generated SMS. Doesn’t support replies and calls.";

        if(!empty($info['mobile']))
            (new NotificationController)
                ->SmsNotification($info['mobile'], $smsBody);
    }

    public function submittedWithoutDep($info)
    {
        $startDate = date('F j', strtotime($this->getDates()['dateStart']));
        $dateFinalWarning = date('F j, Y', strtotime($this->getDates()['dateFinalWarning']));
        $dateFormLocked = date('F j, Y', strtotime($this->getDates()['dateFormLocked']));

        $body = 
        'Thank you for submitting your enrollment.<br /><br />

        If there are changes in the dependent enrollment, you may make changes until '.$dateFinalWarning.'. Enrollment will officially close on '.$dateFormLocked.' and will no longer accept any changes.
        <br /><br />

        <b>This is an auto-generated Email. Doesn’t support replies and calls.</b>';

        $mailMsg = array(
            'subject' => '8x8 DEPENDENT ENROLLMENT NOTIFICATION',
            'body' => '<div style="font-weight:normal;">'.$body.'</div>'
        );

        if(!empty($info['email']))
            (new NotificationController)
                ->MailNotification($info['name'], $info['email'], $mailMsg);
        if(!empty($info['email2']))
                (new NotificationController)
                    ->MailNotification($info['name'], $info['email2'], $mailMsg);

        $smsBody = 
        "From LLIBI:\n\nThank you for submitting your enrollment, you can view your enrolled dependents in the confirmation email sent to you.\n\nThis is an auto-generated SMS. Doesn’t support replies and calls";

        if(!empty($info['mobile']))
            (new NotificationController)
                ->SmsNotification($info['mobile'], $smsBody);
    }

    public function submittedWithDep($info)
    {
        $startDate = date('F j', strtotime($this->getDates()['dateStart']));
        $dateFinalWarning = date('F j, Y', strtotime($this->getDates()['dateFinalWarning']));
        $dateFormLocked = date('F j, Y', strtotime($this->getDates()['dateFormLocked']));

        $body = 
        'Thank you for submitting your enrollment.  Below is the summary of your dependents: <br /><br />

        '.$info['depInfo'].'
        
        '.$info['succeeding'].'

        <br />

        '.$info['premiumComputation'].'

        If there are changes in the dependent enrollment, you may make changes until '.$dateFinalWarning.'. Enrollment will officially close on '.$dateFormLocked.' and will no longer accept any changes.
        <br /><br />

        <b>This is an auto-generated Email. Doesn’t support replies and calls.</b>';

        $mailMsg = array(
            'subject' => '8x8 DEPENDENT ENROLLMENT NOTIFICATION',
            'body' => '<div style="font-weight:normal;">'.$body.'</div>'
        );

        if(!empty($info['email']))
            (new NotificationController)
                ->MailNotification($info['name'], $info['email'], $mailMsg);
        if(!empty($info['email2']))
            (new NotificationController)
                ->MailNotification($info['name'], $info['email2'], $mailMsg);

        $smsBody = 
        "From LLIBI:\n\nThank you for submitting your enrollment, you can view your enrolled dependents in the confirmation email sent to you.\n\nThis is an auto-generated SMS. Doesn’t support replies and calls";

        if(!empty($info['mobile']))
            (new NotificationController)
                ->SmsNotification($info['mobile'], $smsBody);
    }

    public function approvedWithCertificateNo($info)
    {
            $insSms = implode("\n", $info['insSms']);
            $name = ucwords(strtolower($info['name']));

            $insMail = implode(" ", $info['insMail']);

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
            
            You may also refer to the pre-recorded <a href='https://philcare-my.sharepoint.com/:v:/g/personal/christine_magdaong_philcare_com_ph/EWVWPZ05NJBLnakk7bo2ArMBDUj2_k7J_E1liUuRjdKwnA?CT=1667963948353&OR=Outlook-Body&CID=62383CD0-0666-46B4-8095-11931B62BA6F&wdLOR=c2F07CFF3-4EEF-499E-AA0A-3079A551E6C0' target='_blank'>Benefits Orientation Video</a> for an in-depth presentation of your benefits, policy exclusion and availment process.<br /><br />

            Should you require additional assistance, you may also contact Lacson & Lacson Insurance Brokers, Inc.<br /><br />

                <b>Beatrice Abesamis</b><br />
                Corporate Accounts Executive<br />
                Email: <a href='mailto:beatriceabesamis@llibi.com'>beatriceabesamis@llibi.com</a><br />
                Mobile: 0917-8795928<br /><br />

                <b>Louie Jane Padua</b><br />
                Asst. Manager - Corporate Account Executive <br />
                Email: <a href='mailto:louiepadua@llibi.com'>louiepadua@llibi.com</a><br />
                Mobile: 0917-5017637 <br /><br />

                <b>This is an auto-generated Email. Doesn’t support replies and calls.</b>
            </div>";

            $attachment = [
                'public/Self_enrollment/8x8/001Welcome_pack/DEEL - WELCOME LETTER.pdf',
                'public/Self_enrollment/8x8/001Welcome_pack/SOC_SME LUXE-DEEL (Updated)_111022.pdf',
            ];

            $mailMsg = array(
                'subject' => '8x8 DEPENDENT ENROLLMENT NOTIFICATION',
                'body' => '<div style="font-weight:normal;">'.$body.'</div>',
                'attachment' => $attachment
            );

            if(!empty($info['email']))
                (new NotificationController)
                    ->MailNotification($info['name'], $info['email'], $mailMsg);
            if(!empty($info['email2']))
                (new NotificationController)
                    ->MailNotification($info['name'], $info['email2'], $mailMsg);

            $smsBody = 
            "LLIBI: Dear $name, \n\nGood day! \nWe are pleased to inform that your healthcare (HMO) plan membership under Philcare (and arranged through Lacson & Lacson Insurance Brokers, Inc.) is already active. \n\n$insSms \n\nYour physical membership card will be delivered to your home address within the next 2 to 3 weeks. In the meantime, you may present your certificate number to Philcare’s accredited facilities, along with a valid ID, in order to make a claim while awaiting your physical card. \n\nThis is an auto-generated SMS. Doesn’t support replies and calls.";

            if(!empty($info['mobile']))
                (new NotificationController)
                    ->SmsNotification($info['mobile'], $smsBody);
    }

    public function everyThreeDays($info, $dateFinalWarning, $dateFormLocked)
    {
        $link = 
        '<a href="https://portal.llibi.app/self-enrollment/8x8?id='.$info['hash'].'">Self-Enrolment Portal</a>';

        $body = 
        'We have noticed that you haven’t completed the online enrollment forms. Please visit this link '.$link.' to complete this now. You are encouraged to complete this until '.date('F j, Y', strtotime($dateFinalWarning)).' to avoid any coverage issues.
        <br /><br />

        If there are changes in the dependent enrollment, you may make changes until '.date('F j, Y', strtotime($dateFinalWarning)).'. Enrollment will officially close on '.date('F j, Y', strtotime($dateFormLocked)).' and will no longer accept any changes.
        <br /><br />

        If you have already completed the form, you may disregard this message.
        <br /><br />

        <b>This is an auto-generated Email. Doesn’t support replies and calls.</b>';

        $mailMsg = array(
            'subject' => '8x8 DEPENDENT ENROLLMENT NOTIFICATION',
            'body' => 
                '<table style="font-weight:normal;width:500px;">
                    <tr>
                        <td></td>
                    </tr>
                    <tr>
                        <td style="font-weight:normal;">'.$body.'</td>
                    </tr>
                </table>'
        );

        if(!empty($info['email']))
            (new NotificationController)
                ->MailNotification($info['name'], $info['email'], $mailMsg);

        if(!empty($info['email2']))
            (new NotificationController)
            ->MailNotification($info['name'], $info['email2'], $mailMsg);

        $smsBody = 
        "From LLIBI:\n\nWe have noticed that you haven’t completed the online enrollment forms.\n\nPlease visit the link provided in the welcome email to proceed with the enrollment.\n\nThis is an auto-generated SMS. Doesn’t support replies and calls";

        if(!empty($info['mobile']))
            (new NotificationController)
                ->SmsNotification($info['mobile'], $smsBody);
    }

    public function warningLastDay($info, $dateFinalWarning, $dateFormLocked)
    {
        $link = '<a href="https://portal.llibi.app/self-enrollment/8x8?id='.$info['hash'].'">Self-Enrolment Portal</a>';

        $body =
        'This is to inform you that today, '.date('F j, Y', strtotime($dateFinalWarning)).', is the last day of 8x8’s open enrollment. Please visit this link '.$link.' to complete your submission.<br /><br />

        Kindly review your dependents’ information submitted as this will be final and no changes will be accepted after the open enrollment.<br /><br />

        Enrollment will officially close on '.date('F j, Y', strtotime($dateFormLocked)).' and will no longer accept enrollment and any changes.
        If you have already completed the form, you may disregard this message.<br /><br />

        <b>This is an auto-generated Email. Doesn’t support replies and calls.</b>';

        $mailMsg = array(
            'subject' => '8x8 DEPENDENT ENROLLMENT NOTIFICATION',
            'body' => 
                '<table style="font-weight:normal;width:500px;">
                    <tr>
                        <td></td>
                    </tr>
                    <tr>
                        <td style="font-weight:normal;">'.$body.'</td>
                    </tr>
                </table>'
        );

        if(!empty($info['email']))
            (new NotificationController)
                ->MailNotification($info['name'], $info['email'], $mailMsg);

        if(!empty($info['email2']))
            (new NotificationController)
                ->MailNotification($info['name'], $info['email2'], $mailMsg);

        $smsBody = 
        "From LLIBI:\n\nThis is to inform you that today, ".date('F j, Y', strtotime($dateFinalWarning)).", is the last day of 8x8’s open enrollment.\n\nPlease visit the link provided in the welcome email to proceed with the enrollment.\n\nThis is an auto-generated SMS. Doesn’t support replies and calls";

        if(!empty($info['mobile']))
            (new NotificationController)
                ->SmsNotification($info['mobile'], $smsBody);
    }

    public function reminderLock($info)
    {
        $link = '<a href="https://portal.llibi.app/self-enrollment/8x8?id='.$info['hash'].'">Self-Enrolment Portal</a>';

        $body = 
        'Thank you for your enrollment submission. 
        <br /><br />

        Enrollment of your dependents has officially closed. All submission is final and no longer allowed to make any changes.
        <br /><br />

        <b>This is an auto-generated Email. Doesn’t support replies and calls.</b>';

        $mailMsg = array(
            'subject' => '8x8 DEPENDENT ENROLLMENT NOTIFICATION',
            'body' => 
                '<table style="font-weight:normal;width:500px;">
                    <tr>
                        <td></td>
                    </tr>
                    <tr>
                        <td style="font-weight:normal;">'.$body.'</td>
                    </tr>
                </table>'
        );

        if(!empty($info['email']))
            (new NotificationController)
                ->MailNotification($info['name'], $info['email'], $mailMsg);

        if(!empty($info['email2']))
            (new NotificationController)
                ->MailNotification($info['name'], $info['email2'], $mailMsg);
            
        $smsBody = 
        "From LLIBI:\n\nThank you for your enrollment submission.\n\nEnrollment of your dependents has officially closed. All submission is final and no longer allowed to make any changes\n\nThis is an auto-generated SMS. Doesn’t support replies and calls";

        if(!empty($info['mobile']))
            (new NotificationController)
                ->SmsNotification($info['mobile'], $smsBody);
    }
}
