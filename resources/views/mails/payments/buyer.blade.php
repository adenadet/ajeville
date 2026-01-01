@extends('mails.layouts.nairafy')

@section('message')
    <table width="100%" cellpadding="3" cellspacing="3" style="font-size: 14px; line-height: 24px;">
        <tbody>
            <tr>
                <td colspan=2 style="text-align: center">
                    <h3>Transaction Details</h3>
                </td>
            </tr>
            <tr style="border: 0 0 1px 0 solid #333333">
                <td width="50%">Reference Number</td><td style="text-align: right; color: #000000;"><strong>{{ $transaction->unique_code}}</strong></td>
            </tr>
            <tr style="border: 0 0 1px 0 solid #333333; padding: 5px">   
                <td width="50%">Date</td><td style="text-align: right; color: #000000;">{{ \Carbon\Carbon::parse($transaction->date)->format('jS M, Y') }}</td>
            </tr>
            <tr style="border: 0 0 1px 0 solid #333333; padding: 5px">   
                <td width="50%">Payment Channel</td><td style="text-align: right; color: #000000;">{{ $payment->channel }}</td>
            </tr>
            <tr>
                <td width="50%">Amount</td><td style="text-align: right; color: #000000;">{{ number_format($transaction->amount, 2)}}
            </tr>
            <tr>
                <td width="50%">Charges</td><td style="text-align: right; color: #000000;">{{ number_format(($transaction->amount * 0.03), 2)}}</td>
            </tr>
            <tr>
                <td width="50%">Total</td><td style="text-align: right; color: #000000;">{{ number_format(($transaction->amount * 1.03), 2)}}</td>
            </tr>
        </tbody>
    </table>
@endsection

@section('header')
    <img src="{{(is_null($seller->company)||is_null($seller->company->logo)) ? 'https://dashboard.nairafy.ng/img/logo/nairafy-horizontal-logo.png' : 'https://dashboard.nairafy.ng/company/logo/'.$seller->company->logo }}" height="50px" width="auto" />
    <h3>{{$seller->company->name ?? $seller->first_name.' '.$seller->last_name }} has received your payment.</h3>
@endsection