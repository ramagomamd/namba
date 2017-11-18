<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <!-- Meta -->
        @yield('meta')

        <!-- Styles -->
        <style>
            body { padding-top: 80px; padding-bottom: 100px; }
            .level { display: flex; align-items: center; }
            .flex { flex: 1; }
            .mr-1 { margin-right: 1em; }
            [v-cloak] { display: none; }
            .vcenter {
               display: inline-block;
               vertical-align: middle;
               float: none;
            }
            .fb_iframe_widget, .fb_iframe_widget span, .fb_iframe_widget span iframe[style] {
                width: 100% !important;
            }
        </style>
        @yield('before-styles')

        <!-- Check if the language is set to RTL, so apply the RTL layouts -->
        <!-- Otherwise apply the normal LTR layouts -->
        @langRTL
            {{ Html::style(getRtlCss(mix('css/frontend.css'))) }}
        @else
            {{ Html::style(mix('css/frontend.css')) }}
        @endif

        @yield('after-styles')

        <!-- Scripts -->
        <script>
            window.Laravel = <?php echo json_encode([
                'csrfToken' => csrf_token(),
            ]); ?>
        </script>
    </head>
    <body id="app-layout">
        <div id="app">
            @include('includes.partials.logged-in-as')
            @include('frontend.includes.nav')

            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        @include('includes.partials.messages')
                        @yield('before-content')
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 col-md-push-3">
                        <div class="row">
                            <search></search>
                            <div class="col-md-12">
                                <p class="bg-info lead text-center">Like our page for fresh uploads update</p>
                            </div>
                            <div align="center" style="padding: 1em">
                                <div class="fb-like" data-href="https://www.facebook.com/officialLulamusic/" 
                                data-layout="standard" data-action="like" data-size="small" data-show-faces="true" 
                                data-share="true">
                              </div>
                          </div>
                        </div>
                        <div class="row">
                            @yield('content')
                        </div>
                    </div>
                    <div class="col-md-3 col-md-pull-6">
                        @include('frontend.includes.sidebar')
                    </div>
                    <div class="col-md-3">
                        @include('frontend.includes.sidebar-links')
                    </div>
                </div>
            </div><!-- container -->
        </div><!--#app-->

        <!-- Scripts -->
        @yield('before-scripts')
        <script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-59fdde896af656a5"></script> 
        {!! Html::script(mix('js/frontend.js')) !!}
        @yield('after-scripts')

        @include('includes.partials.ga')
        @include('includes.partials.fb')
    </body>
</html>