<div id="coronas">
  <img src="@asset('images/corona.svg')" width="180" height="180"
       style="top:-80px;right:-30px;animation-duration:40s;">
  <img src="@asset('images/corona.svg')" width="120" height="120"
       style="top:264px;left:10%;animation-duration:52s;">
  <img src="@asset('images/corona.svg')" width="650" height="650"
       style="top:214px;right:-220px;animation-duration:48s;">
  <img src="@asset('images/corona.svg')" width="285" height="285"
       style="top:370px;left:0;right:0;margin: 0 auto;animation-duration:43s;">
  <img src="@asset('images/corona.svg')" width="410" height="410"
       style="top:989px;left:-279px;animation-duration:58s;">
  <img src="@asset('images/corona.svg')" width="490" height="490"
       style="top:1450px;right:-239px;animation-duration:38s;">
</div>

<header class="banner navbar navbar-expand-lg navbar-light" role="navigation">
  <div class="container">
    <a class="navbar-brand" href="{{ home_url('/') }}" rel="home" itemprop="url">
      <img width="120" height="84" src="{{ \App\Controllers\App::siteLogo() }}" class="logo"
           alt="{{ get_bloginfo('name', 'display') }}"
           itemprop="logo">
      <p class="sitename sr-only">{{ get_bloginfo('name', 'display') }}</p>
    </a>

    <button class="navbar-toggler collapsed" type="button" data-toggle="collapse" data-target="#navbarCollapse"
            aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
      {{--<span class="navbar-toggler-icon"></span>--}}
    </button>
    <nav class="collapse navbar-collapse" id="navbarCollapse">
      @if (has_nav_menu('primary_navigation'))
        {!! wp_nav_menu(['theme_location' => 'primary_navigation', 'container' => false, 'menu_class' => 'navbar-nav menu-primary']) !!}
      @endif
    </nav>
  </div>

</header>
