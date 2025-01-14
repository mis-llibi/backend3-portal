<?php

namespace App\Http\Controllers\Self_enrollment;

use App\Http\Controllers\Controller;
//use Illuminate\Http\Request;

use App\Http\Controllers\NotificationController;

class ManageDeelNotifications extends Controller
{
    public function getDates()
    {
        return [
            'dateStart'        => '2024-09-16',
            'dateFinalWarning' => '2024-09-27',
            'dateFormLocked'   => '2024-09-28'
        ];
    }

    public function rolloverInvitePhilCare($info)
    {
        $startDate = date('F j', strtotime($this->getDates()['dateStart']));
        $dateFinalWarning = date('F j, Y', strtotime($this->getDates()['dateFinalWarning']));
        $dateFormLocked = date('F j', strtotime($this->getDates()['dateFormLocked']));

        $link = '<a href="https://portal.llibi.app/self-enrollment/deel?id=' . $info['hash'] . '">Self-Enrolment Portal</a>';
        //$link = '<a href="http://localhost:3000/self-enrollment/deel?id=' . $info['hash'] . '">Self-Enrolment Portal</a>';

        $body =
            'Dear Member,<br /><br />

        Your Philcare healthcare (HMO) plan under your employer, Deel, is for renewal on November 1, 2024. <br /><br />

        We are pleased to inform you that the current coverage and premiums have been retained.<br /><br />

        In order to confirm your renewal enrollment as well as that of your dependents, please visit this link ' . $link . ' and accomplish this form online.<br /><br />
        
        We will ask you to confirm information about yourself and your dependents. Please complete this so that you and/or your dependents may be enrolled in Deel’s renewal. You are encouraged to complete this from ' . $startDate . ' to ' . $dateFinalWarning . ' to avoid any coverage issues.<br /><br />
        
        If you do not respond to this notification by ' . $dateFormLocked . ', you and your existing dependents will be automatically enrolled in Deel’s healthcare plan renewal.<br /><br />

        <b>This is an auto-generated Email, please do not share. Does not support replies.</b>';

        $mailMsg = array(
            'subject' => 'DEEL RENEWAL ENROLLMENT NOTIFICATION',
            'body' => '<div style="font-weight:normal;">' . $body . '</div>',
            'bcc' =>  'deelrenewal@llibi.com',
            'name' => 'Deel Renewal',
        );

        if (!empty($info['email'])) (new NotificationController)
            ->OldMailNotification($info['name'], $info['email'], $mailMsg);

        if (!empty($info['email2'])) (new NotificationController)
            ->OldMailNotification($info['name'], $info['email2'], $mailMsg);

        $smsBody =
            "From Deel & LLIBI:\n\nGood day!\n\nWe are delighted to welcome Deel employees as our valued client.\n\nPlease follow enrollment procedure mentioned in the Welcome Email sent over to your personal and company email.\n\nThis is an auto-generated SMS.Does not support replies and calls";

        if (!empty($info['mobile'])) (new NotificationController)
            ->SmsNotification($info['mobile'], $smsBody);
    }

    public function rolloverInviteMaxiCare($info)
    {
        $startDate = date('F j', strtotime($this->getDates()['dateStart']));
        $dateFinalWarning = date('F j, Y', strtotime($this->getDates()['dateFinalWarning']));
        $dateFormLocked = date('F j', strtotime($this->getDates()['dateFormLocked']));

        $link = '<a href="https://portal.llibi.app/self-enrollment/deel?id=' . $info['hash'] . '">Self-Enrolment Portal</a>';
        //$link = '<a href="http://localhost:3000/self-enrollment/deel?id=' . $info['hash'] . '">Self-Enrolment Portal</a>';

        $body =
            'Dear Member,<br /><br />

        Your Philcare healthcare (HMO) plan under your employer, Deel, will transition to new healthcare (HMO) vendor, Maxicare, on November 1, 2024.<br /><br />

        In order to change your enrollment as well as that of your dependents, please visit this link ' . $link . ' and accomplish this form online.<br /><br />
        
        We will ask you to confirm information about yourself and your dependents. Please complete this so that you and/or your dependents may be enrolled in Deel’s Maxicare transition. You are encouraged to complete this from ' . $startDate . ' to ' . $dateFinalWarning . ' to avoid any coverage issues.<br /><br />
        
        If you do not respond to this notification by ' . $dateFormLocked . ', you and your existing dependents under Philcare will automatically be transitioned and enrolled in Deel’s Maxicare healthcare plan.<br /><br />

        <b>This is an auto-generated Email, please do not share. Does not support replies.</b>';

        $mailMsg = array(
            'subject' => 'DEEL RENEWAL ENROLLMENT NOTIFICATION',
            'body' => '<div style="font-weight:normal;">' . $body . '</div>',
            'bcc' =>  'deelrenewal@llibi.com',
            'name' => 'Deel Renewal',
        );

        if (!empty($info['email'])) (new NotificationController)
            ->OldMailNotification($info['name'], $info['email'], $mailMsg);

        if (!empty($info['email2'])) (new NotificationController)
            ->OldMailNotification($info['name'], $info['email2'], $mailMsg);

        $smsBody =
            "From Deel & LLIBI:\n\nGood day!\n\nWe are delighted to welcome Deel employees as our valued client.\n\nPlease follow enrollment procedure mentioned in the Welcome Email sent over to your personal and company email.\n\nThis is an auto-generated SMS. Does not support replies and calls";

        if (!empty($info['mobile'])) (new NotificationController)
            ->SmsNotification($info['mobile'], $smsBody);
    }

    /* public function rolloverEveryThreeDays($info, $dateFinalWarning, $dateFormLocked)
    {

        $startDate = date('F j', strtotime($this->getDates()['dateStart']));
        $dateFinalWarning = date('F j, Y', strtotime($this->getDates()['dateFinalWarning']));
        $dateFormLocked = date('F j', strtotime($this->getDates()['dateFormLocked']));

        $link =
            '<a href="https://portal.llibi.app/self-enrollment/deel?id=' . $info['hash'] . '">Self-Enrolment Portal</a>';

        $body =
            'Dear Member, <br /><br />
        We have noticed that you haven’t completed the online enrollment forms. Please visit this link ' . $link . ' to complete this now. You are encouraged to complete this until ' . date('F j, Y', strtotime($dateFinalWarning)) . ' to avoid any coverage issues.
        <br /><br />

        If there are changes in the dependent enrollment, you may make changes until ' . date('F j, Y', strtotime($dateFinalWarning)) . '. Renewal Enrollment will officially close on ' . date('F j, Y', strtotime($dateFormLocked)) . ' and after that, changes will no longer be accepted. 
        <br /><br />

        If you do not respond to this notification by ' . date('F j', strtotime($dateFinalWarning)) . ', you and your existing dependents will be automatically enrolled in Deel’s healthcare plan renewal.
        <br /><br />

        If you have already completed the form, you may disregard this message.
        <br /><br />

        <b>This is an auto-generated Email. Does not support replies.</b>';

        $mailMsg = array(
            'subject' => 'DEEL RENEWAL ENROLLMENT NOTIFICATION',
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
            "From Deel & LLIBI:\n\nWe have noticed that you haven’t completed the online enrollment forms.\n\nPlease visit the link provided in the welcome email to proceed with the enrollment.\n\nThis is an auto-generated SMS. Does not support replies and calls";

        if (!empty($info['mobile'])) (new NotificationController)
            ->SmsNotification($info['mobile'], $smsBody);
    } */

    public function rolloverLastDayRenewal($info, $dateFinalWarning, $dateFormLocked)
    {
        $startDate = date('F j', strtotime($this->getDates()['dateStart']));
        $dateFinalWarning = date('F j, Y', strtotime($this->getDates()['dateFinalWarning']));
        $dateFormLocked = date('F j', strtotime($this->getDates()['dateFormLocked']));

        $link =
            '<a href="https://portal.llibi.app/self-enrollment/deel?id=' . $info['hash'] . '">Self-Enrolment Portal</a>';

        $body =
            'Dear Member, <br /><br />

            We have noticed that you did not complete submission of your online enrollment forms for Deel’s healthcare plan renewal on November 1, 2024.   

        <br /><br />
        In order to complete your renewal enrollment as well as that of your dependents, please visit this link ' . $link . ' and complete submission of your form online.
        <br /><br />

        If you do not respond by Thursday, October 3, 11:59 PM, we will rollover your and your dependents’ existing information for Deel’s healthcare plan renewal.
        <br /><br />

        <b>This is an auto-generated Email. Does not support replies.</b>';

        $mailMsg = array(
            'subject' => 'DEEL RENEWAL ENROLLMENT NOTIFICATION',
            'bcc' =>  'deelrenewal@llibi.com',
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
            ->MailNotification($info['name'], $info['email'], $mailMsg);

        if (!empty($info['email2'])) (new NotificationController)
            ->MailNotification($info['name'], $info['email2'], $mailMsg);

        $smsBody =
            "From Deel & LLIBI:\n\nWe have noticed that you haven’t completed the online enrollment forms.\n\nPlease visit the link provided in the welcome email to proceed with the enrollment.\n\nThis is an auto-generated SMS. Does not support replies and calls";

        if (!empty($info['mobile'])) (new NotificationController)
            ->SmsNotification($info['mobile'], $smsBody);
    }

    public function rolloverForce($info, $dateFinalWarning, $dateFormLocked)
    {

        $startDate = date('F j', strtotime($this->getDates()['dateStart']));
        $dateFinalWarning = date('F j, Y', strtotime($this->getDates()['dateFinalWarning']));
        $dateFormLocked = date('F j', strtotime($this->getDates()['dateFormLocked']));

        $link =
            '<a href="https://portal.llibi.app/self-enrollment/deel?id=' . $info['hash'] . '">Self-Enrolment Portal</a>';

        $body =
            'Dear Member, <br /><br />

            In view of Deel’s renewal on November 1, 2024, this is to notify that we will rollover your and your dependents’ existing information for Deel’s healthcare plan renewal.    <br /><br />
            Should you require additional assistance, you may also contact Lacson & Lacson Insurance Brokers, Inc. <br /><br />

            <b>Beatrice Abesamis</b><br/>
            Corporate Accounts Executive<br />
            Email: beatriceabesamis@llibi.com<br />
            Mobile: 0917-8795928<br /><br />

            <b>Louie Jane Padua</b><br />
            Asst. Manager - Corporate Account Executive<br />
            Email: louiepadua@llibi.com<br />
            Mobile: 0917-5017637<br /><br />


        <b>This is an auto-generated Email. Does not support replies.</b>';

        $mailMsg = array(
            'subject' => 'DEEL RENEWAL ENROLLMENT NOTIFICATION',
            'bcc' =>  'deelrenewal@llibi.com',
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
            ->MailNotification($info['name'], $info['email'], $mailMsg);

        if (!empty($info['email2'])) (new NotificationController)
            ->MailNotification($info['name'], $info['email2'], $mailMsg);

        $smsBody =
            "From Deel & LLIBI:\n\nWe have noticed that you haven’t completed the online enrollment forms.\n\nPlease visit the link provided in the welcome email to proceed with the enrollment.\n\nThis is an auto-generated SMS. Does not support replies and calls";

        if (!empty($info['mobile'])) (new NotificationController)
            ->SmsNotification($info['mobile'], $smsBody);
    }

    public function rolloverLastDayEnrollment($info, $dateFinalWarning, $dateFormLocked)
    {

        $startDate = date('F j', strtotime($this->getDates()['dateStart']));
        $dateFinalWarning = date('F j, Y', strtotime($this->getDates()['dateFinalWarning']));
        $dateFormLocked = date('F j', strtotime($this->getDates()['dateFormLocked']));

        $link =
            '<a href="https://portal.llibi.app/self-enrollment/deel?id=' . $info['hash'] . '">Self-Enrolment Portal</a>';

        $body =
            'Dear Member, <br /><br />

            We have noticed that you did not complete submission of your online enrollment forms for Deel’s healthcare plan renewal on November 1, 2024.   

        <br /><br />
        In order to complete your renewal enrollment as well as that of your dependents, please visit this link ' . $link . ' and complete submission of your form online.
        <br /><br />

        If you do not respond by Thursday, October 3, 11:59 PM, you and your dependents will not be enrolled in Deel’s healthcare plan renewal.
        <br /><br />

        <b>This is an auto-generated Email. Does not support replies.</b>';

        $mailMsg = array(
            'subject' => 'DEEL ENROLLMENT NOTIFICATION',
            'bcc' =>  'deelrenewal@llibi.com',
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
            ->MailNotification($info['name'], $info['email'], $mailMsg);

        if (!empty($info['email2'])) (new NotificationController)
            ->MailNotification($info['name'], $info['email2'], $mailMsg);
    }

    public function rolloverWarningUntouchedForm($info, $dateFinalWarning, $dateFormLocked)
    {
        $startDate = date('F j, Y', strtotime($this->getDates()['dateStart']));

        $link = '<a href="https://portal.llibi.app/self-enrollment/deel?id=' . $info['hash'] . '">Self-Enrolment Portal</a>';

        $body =
            'Dear Member, <br /><br />
        
        We have noticed that you did not respond to the renewal notification last ' . $startDate . '.<br /><br />
        
        In order to confirm your renewal enrollment as well as that of your dependents, please visit this link ' . $link . ' to complete this now. You are encouraged to complete this until ' . date('F j, Y', strtotime($dateFinalWarning)) . ' to avoid any coverage issues.<br /><br />
        
        Enrollment will officially close on ' . date('F j, Y', strtotime($dateFormLocked)) . ' and after that, changes will no longer be accepted.<br /><br />

        If you do not respond to this notification by ' . date('F j', strtotime($dateFinalWarning)) . ', you and your existing dependents will be automatically enrolled in Deel’s healthcare plan renewal.<br /><br />

        <b>This is an auto-generated Email. Does not support replies.</b>';

        $mailMsg = array(
            'subject' => 'DEEL RENEWAL ENROLLMENT NOTIFICATION',
            'bcc' =>  'ecnanalis@llibi.com',
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
            ->MailNotification($info['name'], $info['email'], $mailMsg);

        if (!empty($info['email2'])) (new NotificationController)
            ->MailNotification($info['name'], $info['email2'], $mailMsg);

        /* $smsBody =
            "From Deel & LLIBI:\n\nThis is to inform you that today, " . date('F j, Y', strtotime($dateFinalWarning)) . ", is the last day of Deel’s open enrollment.\n\nPlease visit the link provided in the welcome email to proceed with the enrollment.\n\nThis is an auto-generated SMS. Does not support replies and calls";

        if (!empty($info['mobile'])) (new NotificationController)
            ->SmsNotification($info['mobile'], $smsBody); */
    }

    public function rolloverWarningLastDay($info, $dateFinalWarning, $dateFormLocked)
    {
        $startDate = date('F j', strtotime($this->getDates()['dateStart']));

        $link = '<a href="https://portal.llibi.app/self-enrollment/deel?id=' . $info['hash'] . '">Self-Enrolment Portal</a>';

        $body =
            'This is to inform you that today, ' . date('F j, Y', strtotime($dateFinalWarning)) . ', is the last day of Deel’s open enrollment. Please visit this link ' . $link . ' to complete your submission.<br /><br />

        Kindly review your dependents’ information submitted as this will be final and no changes will be accepted after the open enrollment.<br /><br />

        Enrollment will officially close on ' . date('F j, Y', strtotime($dateFormLocked)) . ' and will no longer accept enrollment and any changes.<br /><br />

        If you do not respond to this notification by ' . date('F j', strtotime($dateFormLocked)) . ', you and your existing dependents will be automatically enrolled in Deel’s healthcare plan renewal.<br /><br />

        If you have already completed the form, you may disregard this message.<br /><br />

        <b>This is an auto-generated Email. Does not support replies.</b>';

        $mailMsg = array(
            'subject' => 'DEEL RENEWAL ENROLLMENT NOTIFICATION',
            'bcc' =>  'deelrenewal@llibi.com',
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
            ->MailNotification($info['name'], $info['email'], $mailMsg);

        if (!empty($info['email2'])) (new NotificationController)
            ->MailNotification($info['name'], $info['email2'], $mailMsg);

        /* $smsBody =
            "From Deel & LLIBI:\n\nThis is to inform you that today, " . date('F j, Y', strtotime($dateFinalWarning)) . ", is the last day of Deel's open enrollment.\n\nPlease visit the link provided in the welcome email to proceed with the enrollment.\n\nThis is an auto-generated SMS. Does not support replies and calls";

        if (!empty($info['mobile'])) (new NotificationController)
            ->SmsNotification($info['mobile'], $smsBody); */
    }

    public function rolloverReminderLock($info)
    {
        $link = '<a href="https://portal.llibi.app/self-enrollment/deel?id=' . $info['hash'] . '">Self-Enrolment Portal</a>';

        $body =
            'Thank you for your enrollment submission. 
        <br /><br />

        Enrollment of your dependents has officially closed. All submission is final and changes will no longer be accepted.
        <br /><br />

        <b>This is an auto-generated Email. Does not support replies.</b>';

        $mailMsg = array(
            'subject' => 'DEEL RENEWAL ENROLLMENT NOTIFICATION',
            'bcc' =>  'deelrenewal@llibi.com',
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
            ->MailNotification($info['name'], $info['email'], $mailMsg);

        if (!empty($info['email2'])) (new NotificationController)
            ->MailNotification($info['name'], $info['email2'], $mailMsg);

        $smsBody =
            "From Deel & LLIBI:\n\nThank you for your enrollment submission.\n\nEnrollment of your dependents has officially closed. All submission is final and no longer allowed to make any changes\n\nThis is an auto-generated SMS. Does not support replies and calls";

        if (!empty($info['mobile'])) (new NotificationController)
            ->SmsNotification($info['mobile'], $smsBody);
    }

    public function invitePhilCare($info)
    {
        $link = 'https://portal.llibi.app/self-enrollment/deel?id=' . $info['hash'];

        $mailMsg = array(
            'body' => view('self-enrollment/send-invite-philcare', [
                'link' => $link
            ]),
            'subject' => 'PHILCARE - HMO ENROLLMENT',
            'bcc' =>  'deelrenewal@llibi.com',
            'name' => 'Deel Renewal',
        );

        /*  $mailMsg = array(
            'subject' => 'DEEL DEPENDENT ENROLLMENT NOTIFICATION',
            'body' => '<div style="font-weight:normal;">' . $body . '</div>',
            'bcc' =>  'deelrenewal@llibi.com',
            'name' => 'Deel Renewal',
        );*/

        if (!empty($info['email'])) (new NotificationController)
            ->MailNotification($info['name'], $info['email'], $mailMsg);

        if (!empty($info['email2'])) (new NotificationController)
            ->MailNotification($info['name'], $info['email2'], $mailMsg);

        $smsBody =
            "From LLIBI:\n\nGood day!\n\nWe are delighted to welcome Deel employees as our valued client.\n\nPlease follow enrollment procedure mentioned in the Welcome Email sent over to your personal and company email.\n\nThis is an auto-generated SMS. Does not support replies and calls";

        if (!empty($info['mobile'])) (new NotificationController)
            ->SmsNotification($info['mobile'], $smsBody);
    }

    public function inviteMaxicare($info)
    {

        $link = 'https://portal.llibi.app/self-enrollment/deel?id=' . $info['hash'];

        $mailMsg = array(
            'body' => view('self-enrollment/send-invite-maxicare', [
                'link' => $link
            ]),
            'subject' => 'MAXICARE - HMO ENROLLMENT',
            'bcc' =>  'deelrenewal@llibi.com',
            'name' => 'Deel Renewal',
        );

        /*  $mailMsg = array(
            'subject' => 'DEEL DEPENDENT ENROLLMENT NOTIFICATION',
            'body' => '<div style="font-weight:normal;">' . $body . '</div>',
            'bcc' =>  'deelrenewal@llibi.com',
            'name' => 'Deel Renewal',
        );
        */
        if (!empty($info['email'])) (new NotificationController)
            ->MailNotification($info['name'], $info['email'], $mailMsg);

        if (!empty($info['email2'])) (new NotificationController)
            ->MailNotification($info['name'], $info['email2'], $mailMsg);

        $smsBody =
            "From LLIBI:\n\nGood day!\n\nWe are delighted to welcome Deel employees as our valued client.\n\nPlease follow enrollment procedure mentioned in the Welcome Email sent over to your personal and company email.\n\nThis is an auto-generated SMS. Does not support replies and calls";

        if (!empty($info['mobile'])) (new NotificationController)
            ->SmsNotification($info['mobile'], $smsBody);
    }

    public function inviteWarning($info)
    {

        $link = 'https://portal.llibi.app/self-enrollment/deel?id=' . $info['hash'];

        $mailMsg = array(
            'body' => view('self-enrollment/send-warning-for-1-and-2', [
                'link' => $link
            ]),
            'subject' => 'DEEL ENROLLMENT NOTIFICATION',
            'bcc' =>  'deelrenewal@llibi.com',
            'name' => 'Deel Renewal',
        );

        /*  $mailMsg = array(
            'subject' => 'DEEL DEPENDENT ENROLLMENT NOTIFICATION',
            'body' => '<div style="font-weight:normal;">' . $body . '</div>',
            'bcc' =>  'deelrenewal@llibi.com',
            'name' => 'Deel Renewal',
        );
        */
        if (!empty($info['email'])) (new NotificationController)
            ->MailNotification($info['name'], $info['email'], $mailMsg);

        if (!empty($info['email2'])) (new NotificationController)
            ->MailNotification($info['name'], $info['email2'], $mailMsg);

        /* $smsBody =
            "From LLIBI:\n\nGood day!\n\nWe are delighted to welcome Deel employees as our valued client.\n\nPlease follow enrollment procedure mentioned in the Welcome Email sent over to your personal and company email.\n\nThis is an auto-generated SMS. Does not support replies and calls";

        if (!empty($info['mobile'])) (new NotificationController)
            ->SmsNotification($info['mobile'], $smsBody); */
    }

    public function inviteApology($info)
    {
        if ($info['vendor'] == 'PHILCARE' && $info['is_renewal'] == 0) {
            $subject = 'PHILCARE - HMO ENROLLMENT';
        } else if ($info['vendor'] == 'MAXICARE' && $info['is_renewal'] == 0) {
            $subject = 'MAXICARE - HMO ENROLLMENT';
        } else {
            $subject = 'DEEL RENEWAL ENROLLMENT NOTIFICATION';
        }

        $mailMsg = array(
            'body' => view('self-enrollment/send-apology-error-link', [
                'subject' => $subject,
            ]),
            'subject' => 'DEEL: ERRONEOUS EMAIL LINK',
            'bcc' =>  'deelrenewal@llibi.com',
            'name' => 'Deel Renewal',
        );

        if (!empty($info['email'])) (new NotificationController)
            ->MailNotification($info['name'], $info['email'], $mailMsg);

        if (!empty($info['email2'])) (new NotificationController)
            ->MailNotification($info['name'], $info['email2'], $mailMsg);
    }

    public function invite($info)
    {
        $startDate = date('F j', strtotime($this->getDates()['dateStart']));
        $dateFinalWarning = date('F j, Y', strtotime($this->getDates()['dateFinalWarning']));

        $link = '<a href="https://portal.llibi.app/self-enrollment/deel?id=' . $info['hash'] . '">Self-Enrolment Portal</a>';

        $body =
            'Good day!
        We are delighted to welcome DEEL employees as our valued client.<br />
        In order to enroll dependents, please visit this link ' . $link . ' and accomplish this form online.<br /><br />
        
        We will ask you to confirm information about yourself and your dependents. Please complete this so that you and/or your dependents may be enrolled in DEEL’s healthcare plan. You are encouraged to complete this from ' . $startDate . ' to ' . $dateFinalWarning . ' to avoid any coverage issues.<br /><br />
        
        <b>This is an auto-generated Email, please do not share. Does not support replies.</b>';

        $mailMsg = array(
            'subject' => 'DEEL DEPENDENT ENROLLMENT NOTIFICATION',
            'body' => '<div style="font-weight:normal;">' . $body . '</div>',
            'bcc' =>  'deelrenewal@llibi.com',
            'name' => 'Deel Renewal',
        );

        if (!empty($info['email'])) (new NotificationController)
            ->MailNotification($info['name'], $info['email'], $mailMsg);

        if (!empty($info['email2'])) (new NotificationController)
            ->MailNotification($info['name'], $info['email2'], $mailMsg);

        $smsBody =
            "From LLIBI:\n\nGood day!\n\nWe are delighted to welcome Deel employees as our valued client.\n\nPlease follow enrollment procedure mentioned in the Welcome Email sent over to your personal and company email.\n\nThis is an auto-generated SMS. Does not support replies and calls";

        if (!empty($info['mobile'])) (new NotificationController)
            ->SmsNotification($info['mobile'], $smsBody);
    }

    public function submittedOptOut($info)
    {
        $body =
            'Dear Member,<br /><br />

        We have noted your instructions to opt out. You and your dependents will not be enrolled in the Deel healthcare program.
        <br /><br />

        <b>This is an auto-generated Email. Does not support replies.</b>';

        $mailMsg = array(
            'subject' => 'DEEL DEPENDENT ENROLLMENT NOTIFICATION',
            'body' => '<div style="font-weight:normal;">' . $body . '</div>',
            'bcc' =>  'deelrenewal@llibi.com',
            'name' => 'Deel Renewal',
        );

        if (!empty($info['email'])) (new NotificationController)
            ->OldMailNotification($info['name'], $info['email'], $mailMsg);
        if (!empty($info['email2'])) (new NotificationController)
            ->OldMailNotification($info['name'], $info['email2'], $mailMsg);

        $smsBody =
            "From Deel & LLIBI:\n\nWe have noted your instructions to opt out. You and your dependents will not be enrolled in the Deel healthcare program.\n\nThis is an auto-generated SMS. Does not support replies and calls";

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

        <b>This is an auto-generated Email. Does not support replies.</b>';

        $mailMsg = array(
            'subject' => 'DEEL DEPENDENT ENROLLMENT NOTIFICATION',
            'body' => '<div style="font-weight:normal;">' . $body . '</div>',
            'bcc' =>  'deelrenewal@llibi.com',
            'name' => 'Deel Renewal',
        );

        if (!empty($info['email'])) (new NotificationController)
            ->OldMailNotification($info['name'], $info['email'], $mailMsg);
        if (!empty($info['email2'])) (new NotificationController)
            ->OldMailNotification($info['name'], $info['email2'], $mailMsg);

        $smsBody =
            "From Deel & LLIBI:\n\nThank you for submitting your enrollment, you can view your enrolled dependents in the confirmation email sent to you.\n\nThis is an auto-generated SMS. Does not support replies and calls";

        if (!empty($info['mobile'])) (new NotificationController)
            ->SmsNotification($info['mobile'], $smsBody);
    }

    public function submittedWithDepRollover($info)
    {
        $body =
            'You have successfully rolled over enrollment of your existing dependents. Submission is final and you can no longer allowed to make any changes. <br /><br />
        
        Below is the summary of your dependents: <br /><br />

        ' . (!empty($info['depInfo']) ? $info['depInfo'] : '<b>No Dependent/s Included</b>') . '
        <br />

        ' . $info['premiumComputation'] . '
        <br />

        Your registered home address for card delivery is ' . $info['address'] . '
        <br /><br />

        <b>This is an auto-generated Email. Does not support replies.</b>';

        $mailMsg = array(
            'subject' => 'DEEL DEPENDENT ENROLLMENT NOTIFICATION',
            'body' => '<div style="font-weight:normal;">' . $body . '</div>',
            'bcc' =>  'deelrenewal@llibi.com',
            'name' => 'Deel Renewal',
        );

        if (!empty($info['email'])) (new NotificationController)
            ->OldMailNotification($info['name'], $info['email'], $mailMsg);

        if (!empty($info['email2'])) (new NotificationController)
            ->OldMailNotification($info['name'], $info['email2'], $mailMsg);

        $smsBody =
            "From Deel & LLIBI:\n\nThank you for submitting your enrollment, you can view your enrolled dependents in the confirmation email sent to you.\n\nThis is an auto-generated SMS. Does not support replies and calls";

        if (!empty($info['mobile'])) (new NotificationController)
            ->SmsNotification($info['mobile'], $smsBody);
    }

    public function submittedWithDepUpdateExisting($info)
    {
        $body =
            'You have successfully updated your civil status and/or dependent/s’ enrollment and information. Submission is final and you are no longer allowed to make any changes.<br /><br />
            
            Below is the summary of your dependents:  <br /><br />
        
        ' . $info['depInfo'] . '
        <br />

        Your registered home address for card delivery is ' . $info['address'] . '
        <br /><br />

        <b>This is an auto-generated Email. Does not support replies.</b>';

        $mailMsg = array(
            'subject' => 'DEEL DEPENDENT ENROLLMENT NOTIFICATION',
            'body' => '<div style="font-weight:normal;">' . $body . '</div>',
            'bcc' =>  'deelrenewal@llibi.com',
            'name' => 'Deel Renewal',
        );

        if (!empty($info['email'])) (new NotificationController)
            ->OldMailNotification($info['name'], $info['email'], $mailMsg);

        if (!empty($info['email2'])) (new NotificationController)
            ->OldMailNotification($info['name'], $info['email2'], $mailMsg);

        $smsBody =
            "From Deel & LLIBI:\n\nThank you for submitting your enrollment, you can view your enrolled dependents in the confirmation email sent to you.\n\nThis is an auto-generated SMS. Does not support replies and calls";

        if (!empty($info['mobile'])) (new NotificationController)
            ->SmsNotification($info['mobile'], $smsBody);
    }

    public function everyThreeDays($info, $dateFinalWarning, $dateFormLocked)
    {
        $link =
            '<a href="https://portal.llibi.app/self-enrollment/deel?id=' . $info['hash'] . '">Self-Enrolment Portal</a>';

        $body =
            'We have noticed that you haven’t completed the online enrollment forms. Please visit this link ' . $link . ' to complete this now. You are encouraged to complete this until ' . date('F j, Y', strtotime($dateFinalWarning)) . ' to avoid any coverage issues.
        <br /><br />

        If there are changes in the dependent enrollment, you may make changes until ' . date('F j, Y', strtotime($dateFinalWarning)) . '. Enrollment will officially close on ' . date('F j, Y', strtotime($dateFormLocked)) . ' and will no longer accept any changes.
        <br /><br />

        If you have already completed the form, you may disregard this message.
        <br /><br />

        <b>This is an auto-generated Email. Does not support replies.</b>';

        $mailMsg = array(
            'subject' => 'DEEL DEPENDENT ENROLLMENT NOTIFICATION',
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
            ->MailNotification($info['name'], $info['email'], $mailMsg);

        if (!empty($info['email2'])) (new NotificationController)
            ->MailNotification($info['name'], $info['email2'], $mailMsg);

        $smsBody =
            "From LLIBI:\n\nWe have noticed that you haven’t completed the online enrollment forms.\n\nPlease visit the link provided in the welcome email to proceed with the enrollment.\n\nThis is an auto-generated SMS. Does not support replies and calls";

        if (!empty($info['mobile'])) (new NotificationController)
            ->SmsNotification($info['mobile'], $smsBody);
    }

    public function warningLastDay($info, $dateFinalWarning, $dateFormLocked)
    {
        $link = '<a href="https://portal.llibi.app/self-enrollment/deel?id=' . $info['hash'] . '">Self-Enrolment Portal</a>';

        $body =
            'This is to inform you that today, ' . date('F j, Y', strtotime($dateFinalWarning)) . ', is the last day of Deel’s open enrollment. Please visit this link ' . $link . ' to complete your submission.<br /><br />

        Kindly review your dependents’ information submitted as this will be final and no changes will be accepted after the open enrollment.<br /><br />

        Enrollment will officially close on ' . date('F j, Y', strtotime($dateFormLocked)) . ' and will no longer accept enrollment and any changes.
        If you have already completed the form, you may disregard this message.<br /><br />

        <b>This is an auto-generated Email. Does not support replies.</b>';

        $mailMsg = array(
            'subject' => 'DEEL DEPENDENT ENROLLMENT NOTIFICATION',
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
            ->MailNotification($info['name'], $info['email'], $mailMsg);

        if (!empty($info['email2'])) (new NotificationController)
            ->MailNotification($info['name'], $info['email2'], $mailMsg);

        $smsBody =
            "From LLIBI:\n\nThis is to inform you that today, " . date('F j, Y', strtotime($dateFinalWarning)) . ", is the last day of Deel’s open enrollment.\n\nPlease visit the link provided in the welcome email to proceed with the enrollment.\n\nThis is an auto-generated SMS. Does not support replies and calls";

        if (!empty($info['mobile'])) (new NotificationController)
            ->SmsNotification($info['mobile'], $smsBody);
    }

    public function reminderLock($info)
    {
        $link = '<a href="https://portal.llibi.app/self-enrollment/deel?id=' . $info['hash'] . '">Self-Enrolment Portal</a>';

        $body =
            'Thank you for your enrollment submission. 
        <br /><br />

        Enrollment of your dependents has officially closed. All submission is final and no longer allowed to make any changes.
        <br /><br />

        <b>This is an auto-generated Email. Does not support replies.</b>';

        $mailMsg = array(
            'subject' => 'DEEL DEPENDENT ENROLLMENT NOTIFICATION',
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
            ->MailNotification($info['name'], $info['email'], $mailMsg);

        if (!empty($info['email2'])) (new NotificationController)
            ->MailNotification($info['name'], $info['email2'], $mailMsg);

        $smsBody =
            "From LLIBI:\n\nThank you for your enrollment submission.\n\nEnrollment of your dependents has officially closed. All submission is final and no longer allowed to make any changes\n\nThis is an auto-generated SMS. Does not support replies and calls";

        if (!empty($info['mobile'])) (new NotificationController)
            ->SmsNotification($info['mobile'], $smsBody);
    }
}
