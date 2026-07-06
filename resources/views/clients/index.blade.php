<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Clients</title>
    <style>body{font-family:system-ui,Segoe UI,Roboto,Helvetica,Arial,sans-serif;padding:20px}</style>
  </head>
  <body>
    <h1>Clients (MVC)</h1>
    <ul>
      @foreach($clients as $client)
        <li>{{ $client->name }} @if($client->email) &lt;{{ $client->email }}&gt;@endif</li>
      @endforeach
    </ul>
  </body>
</html>
