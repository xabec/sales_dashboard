<table class="table" style="text-align: center">

    <p style="text-align: center;">
        Order number: <b>{{ $row->order_number }}</b>
        Order email: <b>{{ $row->email }}</b>
    </p>
    <p style="text-align: center;">
        @if ( $row->order_status)
            Order status: <b>{{ $row->order_status }}</b>
        @elseif ( $row->payment_status )
            Fulfillment status: <b> {{ $row->fullfilment_status }}</b>
            Payment status: <b> {{ $row->payment_status }}</b>
        @endif
    </p>

    <thead>
    <tr>
        <th style="text-align: center" scope="col"><b>Shipping information</b></th>
        <th style="text-align: center" scope="col"><b>Billing information</b></th>
        <th style="text-align: center" scope="col"><b>Payment details</b></th>
        <th style="text-align: center" scope="col"><b>Other</b></th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td>Shipping name:<br>
            <b>{{ $row->shipping_name }}</b></td>
        <td>Billing name:<br>
            <b>{{ $row->billing_name }}</b></td>
        <td><b>{{ $row->tax_method }}<br></b>
            Taxes: <b>{{ $row->taxes }}€</b></td>
        <td>Currency: <b>{{ $row->currency }}</b></td>
    </tr>
    <tr>
        <td>Shipping country:
            <b>{{ $row->shipping_country }}</b></td>
        <td>Billing country:
            <b>{{ $row->billing_country }}</b></td>
        <td><b>{{ $row->shipping_method }}</b><br>
            Cost: <b>{{ $row->shipping_cost }}€</b></td>
        <td>Discount: <b>{{ $row->discount }}€</b></td>
    </tr>
    <tr>
        <td>Shipping address:<br>
            <b>{{ $row->shipping_address_street }}</b></td>
        <td>Billing address:<br>
            <b>{{ $row->billing_address_street }}</b></td>
        <td>Subtotal: <b>{{ $row->subtotal }}€</b></td>
        <th><b>{{ $row->coupon_code_name }}</b></th>
    </tr>
    <tr>
        <td><b>{{ $row->shipping_address_county }}</b></td>
        <td><b>{{ $row->billing_address_county }}</b></td>
        <td><b>{{ $row->payment_method }}</b></td>
        <td><b>{{ $row->gift_cards }}</b></td>
    </tr>
    <tr>
        <td>Shipping city:<br>
            <b>{{ $row->shipping_city }}</b></td>
        <td>Billing city:<br>
            <b>{{ $row->billing_city }}</b></td>
        <td>Total:<b> {{ $row->total }}€</b></td>
        <td><b>{{ $row->coupon_code }}</b></td>
    </tr>
    <tr>
        <td>Shipping state:<br>
            <b>{{ $row->shipping_state }}</b></td>
        <td>Billing state:<br>
            <b>{{ $row->billing_state }}</b></td>
        <td>Order date:<br>
            <b>{{ $row->order_date }}</b></td>
        <td><b></b></td>
    </tr>
    <tr>
        <td>Shipping ZIP:<br>
            <b>{{ $row->shipping_zip }}</b></td>
        <td>Billing ZIP:<br>
            <b>{{ $row->billing_zip }}</b></td>
        <td>Payment date:<br>
            <b>{{ $row->payment_date }}</b></td>
        <td><b></b></td>
    </tr>
    <tr>
        <td>Shipping phone:<br>
            <b>{{ $row->shipping_phone }}</b></td>
        <td>Billing phone:<br>
            <b>{{ $row->billing_number }}</b></td>
        <td>Fulfillment date:<br>
            <b>{{ $row->fulfillment_date }}</b></td>
        <td><b></b></td>
    </tr>
    </tbody>
</table>
