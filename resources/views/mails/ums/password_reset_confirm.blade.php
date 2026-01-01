<x-mail::message>
<div class="logo" style="margin-bottom: 20px; text-align: center;">
    <img src="{{asset(config('app.logo_horizontal'))}}" alt="Nairafy Escrow Limited" width="300">
</div>
<div class="message" style="font-size: 16px; color: #333; line-height: 1.6; text-align: left;">
    <p>Dear {{$user->first_name}},</p>
    <p>Your password has been successfully changed.</p>
    <p>If you did not initiate this password reset, kindly reset your password immediately.</p>
    <p>If you requested this change, continue to enjoy your service at {{config('app.name')}}</p>
    <p>Best regards,</p>
    <p>
        <strong>{{config('app.name')}}</strong>
    </p>
</div>
<div class="footer" style="font-size: 12px; color: #888; text-align: center; margin-top: 20px; padding-top: 10px; border-top: 1px solid #ddd;">
    <p>
        <strong>Disclaimer:</strong> 
        This email and any attachments are confidential and intended solely for the recipient. If you have received this email in error, please notify us immediately and delete it. Unauthorized use or distribution of this email is prohibited.
    </p>
</div>
</x-mail::message>
