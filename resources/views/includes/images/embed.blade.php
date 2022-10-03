<a href="#" class="object-visual__artwork mb30px">
    @if(isset($result->representations))
        @foreach($result->representations as $representation)
            <img src="{{media_url($representation->media, 'page')}}" alt="{{$result->name}}" class="embed3d-open" itemprop="image">
        @endforeach
    @endif
    <mark class="object-visual__mark">
        <img src="/img/3D.png" alt="ico mark">
    </mark>
</a>
<div class="embed3d-popup">
    <div class="embed3d-content">
        <iframe frameborder="0" allowfullscreen mozallowfullscreen="true" webkitallowfullscreen="true" allow="fullscreen; autoplay; vr" xr-spatial-tracking execution-while-out-of-viewport execution-while-not-rendered web-share src="{{$result->embed3d}}?autostart=1&ui_infos=0&ui_controls=0&ui_stop=0"></iframe>
    </div>
    <div class="popup-bar">
        <div class="tile-close">
            <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 46 30" style="enable-background:new 0 0 46 30;" xml:space="preserve">
                <polygon class="st0" points="32.19,22.78 24.41,15 32.19,7.22 30.78,5.81 23,13.58 15.22,5.81 13.81,7.22 21.59,15 13.81,22.78 15.22,24.19 23,16.41 30.78,24.19 "/>
            </svg>
        </div>
    </div>
</div>