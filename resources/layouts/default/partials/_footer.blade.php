<!--========== FOOTER ==========-->
<footer class="footer">
    <!-- Links -->
    <div class="section-seperator">
        <div class="content-md container" style="padding-top: 30px; padding-bottom: 30px;">
            <div class="row" style="text-align: center;">
                @foreach(menu()->get('bottom') as $menu)
                    <a href="{{ $menu->url }}" style="display: inline-block; margin: 15px;">
                        {{ $menu->name }}
                    </a>&nbsp;{{ !$loop->last ? '|' : '' }}
                @endforeach
            </div>
            <!--// end row -->
        </div>
    </div>
    <!-- End Links -->

    {!! block()->holder($page, 'footer') !!}
</footer>
<!--========== END FOOTER ==========-->

{{ Html::script(elixir('assets/js/front/app.js')) }}

@section('bottom_styles') @show
@section('bottom_scripts') @show