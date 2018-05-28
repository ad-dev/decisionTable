<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Decision table</title>


</head>
<body>
<h2>CSV file</h2>
<pre>{!! $csv_data !!}</pre>
<h2>Result</h2>
<pre>{!! $output !!}</pre>
</body>
</html>
