<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>

    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container">
    <form class="mt-5 form-inline" action="" method="post">
        @csrf
        <label class="form-text" for="date">Дата</label>
        <input class="form-control" id="date" type="text" name="date"/>
        <button class="form-control" type="submit">Отправить</button>
    </form>

    <div class="mt-0">
        @if(isset($data['error']))
            <strong>{{ $data['error'] }}</strong>
        @elseif(isset($data['result']))
            <span>Температура {{ $data['result']['temp'] }} градусов.</span>
        @endif
    </div>


    @if(isset($data['history']['result']))
        <table class="table mt-5">
            <tr>
                <th>Температура</th>
                <th>Дата</th>
            </tr>
            @foreach( $data['history']['result'] as $data)
                <tr>
                    <td>{{ $data['temp'] }}</td>
                    <td>{{ $data['date_at'] }}</td>
                </tr>
            @endforeach
        </table>
    @endif
</div>
</body>
</html>
