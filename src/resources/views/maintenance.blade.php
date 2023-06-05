<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Skijasi</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>window.Laravel = { csrfToken: '{{ csrf_token() }}' }</script>

    <!-- Favicon -->
    <?php
        use NadzorServera\Skijasi\Helpers\Config;

        $favicon = Config::get('favicon');
        $api_prefix = env('MIX_API_ROUTE_PREFIX');
    ?>

    @if(!$favicon || $favicon == '')
        <link rel="shortcut icon" href="{{ asset('skijasi-images/skijasi-logo.png') }}" type="image/png">
    @else
        <link rel="shortcut icon" href="{{'/'.$api_prefix.'/v1/file/view?file='.$favicon}}" type="image/png">
    @endif
</head>
  <body>
      <p>U tijeku je održavanje servera sa nadogradnjama! Ukoliko trebate hitno pristup, molim kontaktirajte osobu zaduženu za server.</p>
  </body>
</html>
