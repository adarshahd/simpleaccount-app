<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ 'Receipt - ' . $receipt->bill_number . '.pdf' }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Source+Sans+Pro:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Source Sans Pro', sans-serif;
        }
        .table {
            font-size: 12px;
        }
        .row {
            display: -webkit-box !important;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="text-center mt-3">
        <h4><strong>CASH RECEIPT</strong></h4>
    </div>
    <hr>
    <div class="row mt-3">
        <div class="col col-8">
            <div class="row">
                @if($productOwnerData->logo != null || $productOwnerData->logo != '')
                <div class="col col-4">
                    <figure>
                        {{ $productOwnerData->logo }}
                    </figure>
                </div>
                @endif
                <div class="col col-8">
                    <address>
                        <h4><strong>{{ $productOwnerData->name }}</strong></h4>
                        {{ $productOwnerData->address_line_1 }}<br/>
                        {{ $productOwnerData->city }}, {{ $productOwnerData->pin }}
                    </address>
                </div>
            </div>
        </div>
        <div class="col col-4 text-right">
            <h5>Date: <strong>{{ $receipt->bill_date->format('d M, Y') }}</strong></h5>
        </div>
    </div>
    <hr>
    <div class="row mt-3">
        <div class="col col-8">
            From,
            <address>
                <h5>{{ $customer->name }}</h5>
                {{ $customer->address_line_1 }}<br/>
                {{ $customer->city }}, {{ $customer->pin }}
            </address>
        </div>
        <div class="col col-4">
            <h3 class="text-right mr-2"><strong>₹{{ number_format($receipt->total, 2) }}</strong></h3>
            <div class="text-right">
                Payment Method: <strong>{{ $receipt->payment_method }}</strong><br/>
                @if($receipt->payment_reference != null && $receipt->payment_reference != '')
                Payment Reference: <strong>{{ $receipt->payment_reference }}</strong>
                @endif
                <h5>Balance: ₹{{ $balanceAmount }}</h5>
            </div>
        </div>
    </div>
    <div class="row mt-2">
        <div class="col col-8">
            <br/>
            <h5><strong>{{ App\Utilities\CurrencyFormatter::getIndianCurrency($receipt->total) }}</strong></h5>
        </div>
        <div class="col col-4 text-right">
            <br/>
            <h6>For <strong>{{ $productOwnerData->name }}</strong></h6><br/>
            Signature
        </div>
    </div>
    <hr>
    @if(App\Http\Controllers\Api\AppSettingController::getReceiptBillFooter() != '')
        {!! App\Http\Controllers\Api\AppSettingController::getReceiptBillFooter() !!}
    @else
        <div class="text-center">
            <strong>Thank you for your business!</strong>
        </div>
    @endif
</div>
</body>
</html>
