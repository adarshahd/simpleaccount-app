<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Source+Sans+Pro:wght@400;600&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Source Sans Pro', sans-serif;
            font-size: 11px;
        }
    </style>
</head>
<body>
<hr>
<div class="container-fluid">
    <div class="row">
        <div class="col col-12">
            {!! App\Http\Controllers\Api\AppSettingController::getCreditBillFooter() !!}
        </div>
    </div>
</div>
</body>
</html>
