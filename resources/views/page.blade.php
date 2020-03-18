@extends('layouts.app')

@section('content')
  @while(have_posts()) @php the_post() @endphp
  @include('partials.page-header')

  <a name="content"></a>
  <div class="container">
    @php the_content() @endphp
  </div>

  @endwhile
@endsection
