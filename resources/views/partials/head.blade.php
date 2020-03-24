<head>
  <meta charset="utf-8">
  <meta http-equiv="x-ua-compatible" content="ie=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  @php wp_head() @endphp
  @if($scripts = \App\Controllers\App::headScripts())
    {!! $scripts !!}
  @endif
  <style>
    html, body {
      background-image: linear-gradient(180deg, {!! get_theme_mod('main_color', '#e05263') !!} 19%, rgba(255, 255, 255, 0) 70%);
    }
  </style>
</head>
