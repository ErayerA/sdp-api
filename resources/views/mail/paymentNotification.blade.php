<!doctype html>

<html>
<head>
    <title>Payment {{($result->success ? 'Successful' : 'Failed')}}</title>
</head>

<body>
<h1>Hey {{ $result->user->name }}!</h1>
<p>
    We would like to inform you that we {{($result->success ? 'successfully charged' : 'were not able to charge')}}  CUR{{$result->price }}
    from you for your subscription to "{{$result->subscription->plan->name}}".
</p>
</body>
</html>
