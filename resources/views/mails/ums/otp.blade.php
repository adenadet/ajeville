<x-mail::message>
<p>Dear {{$user->first_name}},</p>
<p>We received a request for a one-time code for {{$otp->type}}. Your Pin is</p>
<h4>{{$otp->code}}</h4>
<p>Please do not share your OTP with anyone even someone claiming to be a staff of {{config('app.name')}}</p>
<p>Best regards,</p>
<p>
    <strong>{{config('app.name')}}</strong>
</p>

<div class="footer">
    <p>
        <strong>Disclaimer:</strong> 
        This email and any attachments are confidential and intended solely for the recipient. If you have received this email in error, please notify us immediately and delete it. Unauthorized use or distribution of this email is prohibited.
    </p>
</div>
</x-mail::message>