<?php
if($currentPage == 1) {
    $i = 1;
} else {
    $i = (\App\Http\Controllers\Api\CreditController::$invoiceItemsPerPage * ($currentPage - 1)) + 1;
}
$sub_total = 0;
$tax_total = 0;
$total = 0;
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ 'Credit Invoice - ' . $credit->bill_number . '.pdf' }}</title>
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
<div class="container-fluid">
    <hr>
    <div class="row mt-3">
        <div class="col col-4">
            {{--<img src="{{ asset('storage/' . $productOwnerData->logo) }}" width="40px" style="padding-bottom: 10px" alt="">--}}
        </div>
        <div class="col col-4 text-center">
            <h4 class="title is-4"><strong>CREDIT INVOICE</strong></h4>
        </div>
        <div class="col col-4 text-right">
            <h5><strong>INVOICE# {{ $credit->bill_number }}</strong></h5>
            <h5><strong>DATE: </strong>{{ $credit->bill_date->format("d-m-Y") }}</h5>
        </div>
    </div>
    <hr>
    <div class="row mt-3">
        <div class="col col-6">
            <strong>Sold By :</strong>
            <address>
                <h5><strong>{{ $productOwnerData->name }}</strong></h5>
                {{ $productOwnerData->address_line_1 }}<br>
                {{ $productOwnerData->city }}, {{ $productOwnerData->pin }}<br>
                Phone : {{ $productOwnerData->contact_phone }}<br>
                @if($productOwnerData->contact_email != null || $productOwnerData->contact_email !== '')
                Email : {{ $productOwnerData->contact_email }}<br>
                @endif
                <h6><strong>{{ App\Models\IdType::query()->find($productOwnerData->id_type_id)->name }}# </strong>{{ $productOwnerData->identification }}</h6>
            </address>
        </div>

        <div class="col col-6">
            <div class="text-right">
                <strong>Bill To : </strong>
                <address>
                    <h5><strong>{{ $credit->customer->name }}</strong></h5>
                    {{ $credit->customer->address_line_1 }}<br>
                    {{ $credit->customer->city }}, {{ $credit->customer->pin }}<br>
                    Phone : {{ $credit->customer->contact_phone }}<br>
                    @if($credit->customer->contact_email != null || $credit->customer->contact_email != '')
                        Email : {{ $credit->customer->contact_email }}<br>
                    @endif
                    <h6><strong>{{ $credit->customer->idType->name }}# </strong>{{ $credit->customer->identification }}</h6>
                </address>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col col-12">
            <table class="table table-bordered table-sm">
                <thead>
                <tr>
                    <th class="text-center">#</th>
                    <th class="text-center">Products</th>
                    <th class="text-center">HSN</th>
                    <th class="text-center">Batch</th>
                    <th class="text-center">Expiry</th>
                    <th class="text-center">MRP</th>
                    <th class="text-center">Price</th>
                    <th class="text-center">Quantity</th>
                    <th class="text-center">Taxable</th>
                    <th class="text-center">Tax</th>
                    <th class="text-center">Item Total</th>
                </tr>
                </thead>
                <tbody>
                @foreach($items as $item)
                    <?php
                        $tax_included_price = $item->price;
                        $tax_excluded_price = ($tax_included_price / (( $item->tax / 100 ) + 1 ));
                    ?>
                    <tr>
                        <td>{{ $i++ }}</td>
                        <td>{{ $item->productStock->product->name }}</td>
                        <td class="text-center">{{ $item->productStock->hsn }}</td>
                        <td class="text-center">{{ $item->productStock->batch }}</td>
                        <td class="text-center">{{ $item->productStock->expiry->format('M - Y') }}</td>
                        <td class="text-right">₹{{ number_format($item->productStock->mrp, 2, '.', ',') }}</td>
                        <td class="text-right">₹{{ number_format($item->price, 2, '.', ',') }}</td>
                        <td class="text-center">{{ $item->quantity }}</td>
                        <td class="text-right">₹{{ number_format($item->price * $item->quantity, 2, '.', ',') }}</td>
                        <td class="text-right">₹{{ number_format($item->tax, 2, '.', ',') }}</td>
                        <td class="text-right">₹{{ number_format($item->total, 2, '.', ',') }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @if($pageCount == $currentPage)
    <div class="row mt-4">
        <div class="col col-8">
            <?php
                $round_off_amount = round($credit->total);
                $round_off = $round_off_amount - $credit->total;
                $total = $round_off_amount;
                $total_string = App\Utilities\CurrencyFormatter::getIndianCurrency($total)
            ?>
            <div class="row">
                <div class="col col-12">
                    <h5><strong>TAX SUMMARY</strong></h5>
                    <table class="table table-bordered table-sm">
                        <thead>
                        <tr>
                            <th class="text-center" style="">Tax</th>
                            <th class="text-center" style="">Taxable</th>
                            <th class="text-center" style="">SGST</th>
                            <th class="text-center" style="">AMT</th>
                            <th class="text-center" style="">CGST</th>
                            <th class="text-center" style="">AMT</th>
                            <th class="text-center" style="">TOTAL</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($taxGroupList as $taxGroup)
                            <tr>
                                <th class="text-center" style="">
                                    {{ $taxGroup->tax_percent }}%
                                </th>
                                <td class="text-right" style="font-family: 'DejaVu Sans', sans-serif">
                                    ₹{{ number_format($taxGroup->total, 2, '.', ',') }}
                                </td>
                                <th class="text-center" style="">
                                    {{ $taxGroup->tax_percent / 2 }}%
                                </th>
                                <td class="text-right" style="font-family: 'DejaVu Sans', sans-serif">
                                    ₹{{ number_format(($taxGroup->total * ($taxGroup->tax_percent / 2)) / 100, 2, '.', ',') }}
                                </td>
                                <th class="text-center" style="">
                                    {{ $taxGroup->tax_percent / 2  }}%
                                </th>
                                <td class="text-right" style="font-family: 'DejaVu Sans', sans-serif">
                                    ₹{{ number_format(($taxGroup->total * ($taxGroup->tax_percent / 2)) / 100, 2, '.', ',') }}
                                </td>
                                <td class="text-right" style="font-family: 'DejaVu Sans', sans-serif">
                                    ₹{{ number_format(($taxGroup->total * ($taxGroup->tax_percent)) / 100, 2, '.', ',') }}
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col col-12">
                    <h5><strong>{{ "Total: " . $total_string }}</strong></h5>
                </div>
            </div>
        </div>

        <div class="col col-3 offset-1 float-right">
            <h5><strong>CREDIT SUMMARY</strong></h5>
            <table class="table table-bordered table-sm">
                <tbody>
                <tr>
                    <th class="text-center" style="">Subtotal : </th>
                    <td class="text-right" style="font-size: 15px;font-family: 'DejaVu Sans', sans-serif">
                        <strong>₹{{ number_format($credit->sub_total, 2, '.', ',') }}</strong>
                    </td>
                </tr>
                <tr>
                    <th class="text-center" style="">Tax : </th>
                    <td class="text-right" style="font-size: 15px;font-family: 'DejaVu Sans', sans-serif">
                        <strong>₹{{ number_format($credit->tax, 2, '.', ',') }}</strong>
                    </td>
                </tr>
                <tr>
                    <th class="text-center" style="">Round Off : </th>
                    <td class="text-right" style="font-size: 13px;font-family: 'DejaVu Sans', sans-serif">
                        <strong>₹{{ number_format($round_off, 2, '.', ',') }}</strong>
                    </td>
                </tr>
                <tr>
                    <th class="text-center" style="font-size: 15px">Total : </th>
                    <td class="text-right" style="font-size: 15px;font-family: 'DejaVu Sans', sans-serif">
                        <strong>₹{{ number_format($total, 2, '.', ',') }}</strong>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
    @endif

    @if($pageCount == $currentPage)
    <div class="row mt-5">
        <div class="col col-8"></div>
        <div class="col col-4">
            <h4 class="subtitle mt-5 text-right">
                Signature
            </h4>
        </div>
    </div>
    @endif

</div>
</body>
</html>
