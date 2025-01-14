<?php

namespace App\Http\Controllers\Self_enrollment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Controllers\NotificationController;

class ManageLlibiNotifications extends Controller
{
    public function getDates()
    {
        return [
            'dateStart'        => '2023-05-09',
            'dateFinalWarning' => '2023-05-11',
            'dateFormLocked'   => '2023-05-12'
        ];
    }

    public function invite($info)
    {
        $startDate = date('F j', strtotime($this->getDates()['dateStart']));
        $dateFinalWarning = date('F j, Y', strtotime($this->getDates()['dateFinalWarning']));

        $link = '<a href="https://portal.llibi.app/self-enrollment/llibi?id='.$info['hash'].'">Self-Enrolment Portal</a>';

        $body =
        'Good day!
        We are delighted to welcome LLIBI employees as our valued client.<br />
        In order to enroll dependents, please visit this link '.$link.' and accomplish this form online.<br /><br />
        
        We will ask you to confirm information about yourself and your dependents. Please complete this so that you and/or your dependents may be enrolled in LLIBI’s healthcare plan. You are encouraged to complete this from '.$startDate.' to '.$dateFinalWarning.' to avoid any coverage issues.<br /><br />
        
        <b>This is an auto-generated Email, please do not share. Doesn’t support replies and calls.</b>';

        $mailMsg = array(
            'subject' => 'LLIBI DEPENDENT ENROLLMENT NOTIFICATION',
            'body' => '<div style="font-weight:normal;">'.$body.'</div>'
        );

        if(!empty($info['email']))
            (new NotificationController)
                ->MailNotification($info['name'], $info['email'], $mailMsg);
                
        if(!empty($info['email2']))
            (new NotificationController)
                ->MailNotification($info['name'], $info['email2'], $mailMsg);

        $smsBody = 
        "From LLIBI:\n\nGood day!\n\nWe are delighted to welcome LLIBI employees as our valued client.\n\nPlease follow enrollment procedure mentioned in the Welcome Email sent over to your personal and company email.\n\nThis is an auto-generated SMS. Doesn’t support replies and calls";

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
            'subject' => 'LLIBI DEPENDENT ENROLLMENT NOTIFICATION',
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
        
        <br />

        If there are changes in the dependent enrollment, you may make changes until '.$dateFinalWarning.'. Enrollment will officially close on '.$dateFormLocked.' and will no longer accept any changes.
        <br /><br />

        <b>This is an auto-generated Email. Doesn’t support replies and calls.</b>';

        $mailMsg = array(
            'subject' => 'LLIBI DEPENDENT ENROLLMENT NOTIFICATION',
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

    public function everyThreeDays($info, $dateFinalWarning, $dateFormLocked)
    {
        $link = 
        '<a href="https://portal.llibi.app/self-enrollment/llibi?id='.$info['hash'].'">Self-Enrolment Portal</a>';

        $body = 
        'We have noticed that you haven’t completed the online enrollment forms. Please visit this link '.$link.' to complete this now. You are encouraged to complete this until '.date('F j, Y', strtotime($dateFinalWarning)).' to avoid any coverage issues.
        <br /><br />

        If there are changes in the dependent enrollment, you may make changes until '.date('F j, Y', strtotime($dateFinalWarning)).'. Enrollment will officially close on '.date('F j, Y', strtotime($dateFormLocked)).' and will no longer accept any changes.
        <br /><br />

        If you have already completed the form, you may disregard this message.
        <br /><br />

        <b>This is an auto-generated Email. Doesn’t support replies and calls.</b>';

        $mailMsg = array(
            'subject' => 'LLIBI DEPENDENT ENROLLMENT NOTIFICATION',
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
        $link = '<a href="https://portal.llibi.app/self-enrollment/llibi?id='.$info['hash'].'">Self-Enrolment Portal</a>';

        $body =
        'This is to inform you that today, '.date('F j, Y', strtotime($dateFinalWarning)).', is the last day of Llibi’s open enrollment. Please visit this link '.$link.' to complete your submission.<br /><br />

        Kindly review your dependents’ information submitted as this will be final and no changes will be accepted after the open enrollment.<br /><br />

        Enrollment will officially close on '.date('F j, Y', strtotime($dateFormLocked)).' and will no longer accept enrollment and any changes.
        If you have already completed the form, you may disregard this message.<br /><br />

        <b>This is an auto-generated Email. Doesn’t support replies and calls.</b>';

        $mailMsg = array(
            'subject' => 'LLIBI DEPENDENT ENROLLMENT NOTIFICATION',
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
        "From LLIBI:\n\nThis is to inform you that today, ".date('F j, Y', strtotime($dateFinalWarning)).", is the last day of Llibi’s open enrollment.\n\nPlease visit the link provided in the welcome email to proceed with the enrollment.\n\nThis is an auto-generated SMS. Doesn’t support replies and calls";

        if(!empty($info['mobile']))
            (new NotificationController)
                ->SmsNotification($info['mobile'], $smsBody);
    }

    public function reminderLock($info)
    {
        $link = '<a href="https://portal.llibi.app/self-enrollment/llibi?id='.$info['hash'].'">Self-Enrolment Portal</a>';

        $body = 
        'Thank you for your enrollment submission. 
        <br /><br />

        Enrollment of your dependents has officially closed. All submission is final and no longer allowed to make any changes.
        <br /><br />

        <b>This is an auto-generated Email. Doesn’t support replies and calls.</b>';

        $mailMsg = array(
            'subject' => 'LLIBI DEPENDENT ENROLLMENT NOTIFICATION',
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
