<div>
    <a href="https://www.burnvideo.net" target="_blank">
        <img src="https://www.burnvideo.net/assets/frontend/images/logo.png" alt = "Burn Video" >
    </a>
</div>

<h3>Hello, {!! $username !!}</h3>
<p>Thank you for preserving your memories with Burn Video.</p>
<p>We have received your order and all media files have been uploaded successfully.</p>
<p>Please have your push notifications "ON" for the app, we will send a message to your phone when your order ships USPS.</p>
<p>If you have any questions about this order, please email us at <a href="mailto:info@burnvideo.net" target="_top">info@burnvideo.net</a> so we can help you.</p>
<p>Thank you for your business.</p>

<h4>Your Order {!!$ordernum!!}</h4>
<p>(placed on {!!$orderDateTime!!})</p>
<style type="text/css">
    .bb td, .bb th {
     border-bottom: 1px solid black !important;
    }
</style>
<table width="100%" style="border-bottom:solid 3px darkgray">
    <tr>
        <td style="background:gray;">Billing Information</td>
        <td style="background:gray;">Shipping Method</td>
    </tr>
    <tr>
        <td>{!!$orderBilling!!}</td>
        <td>USPS Shipping</td>
    </tr>
    <tr>
        <td colspan="2" style="height:20px;"></td>
    </tr>
    <tr>
        <td colspan = "2">
            <table width="100%">
                <tr style="background:lightgreen;">
                    <td>Shipping Information</td>
                    <td>DVD Count</td>
                    <td>Subtotal</td>
                </tr>
                {!! $orderShippingsPrice !!}
            </table>
        </td>
    </tr>
    @if($promoCode)
    <tr>
        <td colspan = "2" align="right">
            <B> Promo </B> : {!!$promoCode!!}
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <B> Discount </B> : ${!!$discountPrice!!}
        </td>
    </tr>
    @endif
    <tr>
        <td colspan = 2 align="right">
            <B> Total </B> : ${!!$totalPrice!!}
        </td>
    </tr>
</table>

