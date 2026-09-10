<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CatchErrorException</title>
    <link rel="shortcut icon" type="image/png" href="{{ asset('assets/images/logos/rounded-logo.png') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/styles.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}" />
</head>
<body>

<div class="container">
    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <h4>Any Problem in System</h4>
                    <h5>Error Details : </h5>
                    {{ $message }}
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
