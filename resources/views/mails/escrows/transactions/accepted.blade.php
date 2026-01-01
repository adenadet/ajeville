<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction Accepted | Nairafy Escrow Email</title>
    <style>
        body {font-family: Arial, sans-serif; background-color: #f8f9fa; margin: 0;padding: 0;}
        .container {max-width: 600px; margin: 20px auto; background: #ffffff; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); text-align: center;}
        .logo {margin-bottom: 20px;}
        .message {font-size: 16px; color: #333; line-height: 1.6; text-align: left;}
        .text-muted{font-size: 10px; color: #888; text-align: left;}
        .button {display: inline-block; padding: 12px 20px; margin: 10px; font-size: 16px; color: #fff; text-decoration: none; border-radius: 30px; border: none; cursor: pointer;}
        .btn-green {background-color: #28a745;}
        .btn-red {background-color: #dc3545;}
        .footer {font-size: 12px; color: #888; text-align: center; margin-top: 20px; padding-top: 10px; border-top: 1px solid #ddd;}
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <img src="{{asset(config('app.logo_horizontal'))}}" alt="Nairafy Escrow Limited" width="300">
        </div>
        <div class="message">
        <table>
            <tr>
                <td>
                    <h1 style="text-align: left;">Hello {{$user->name}}</h1>
                    <p style="text-align: left;">The transaction: {{$transaction->title}} [{{$transaction->unique_code}}] has been successfully accepted and is ready for movement.</p>
                    @if ($transaction->buyer_id == $user->id)
                    <p>
                        Please proceed to make payment to fund the escrow.
                        <br />
                        <a href="{{config('app.app_url')}}/escrows/transactions/{{$transaction->unique_code}}" class="button btn-green">Make Payment</a>
                        <a href="{{config('app.app_url')}}/escrows/transactions/{{$transaction->unique_code}}/reject" class="button btn-red">Cancel</a>
                    </p>
                    @endif
                    <p>We are here to protect your money every step of the way.</p>
                    <p>Thank you for using {{config('app.name')}}.</p>

                </td>
            </tr>
            <tr>
                <td class="text-muted">
                    <h3 style="text-align: left;">About {{config('app.name')}}</h3>
                    <p style="text-align: left;">
                        {{config('app.name')}} is the nigeria's most secure payments method from a counterparty risk perspective- safeguarding both buyer and seller as all funds transacted using escrow are kept in trust. As a winner of multiple awards such as the BBB Torch Award for Ethics, {{config('app.name')}} helps you to buy and sell anything safely without the risk of fraud or chargebacks. </p>
                    <h3 style="text-align: left;">How it works</h3>
                    <p style="text-align: left;">{{config('app.name')}} provides an escrow service that protects both the buyer and the seller in a transaction. Transactions take place in the following order:<ol type=1> 
                        <li>The buyer deposits funds into {{config('app.name')}}'s trust account </li>
                        <li>{{config('app.name')}} notifies the seller that the funds are secured, the seller can now safely provide the goods or service to the buyer </li>
                        <li>The buyer is satisfied with the goods or service and instructs {{config('app.name')}} to release the funds to the seller.</li>
                    </ol></p> 
                    <p>If the buyer or seller are not 100% satisfied with the transaction it will go into arbitration with a neutral 3rd party arbitrator. {{config('app.name')}} will disburse the funds according to the arbitrators decision. </p>
                </td>
            </tr>
        </table>
    </div>
    <div class="footer">
        <p><strong>Disclaimer:</strong> This email and any attachments are confidential and intended solely for the recipient. If you have received this email in error, please notify us immediately and delete it. Unauthorized use or distribution of this email is prohibited.</p>
    </div>
</div>
</body>
</html>