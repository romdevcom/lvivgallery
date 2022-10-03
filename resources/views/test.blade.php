@extends('layouts.main')
@section('content')
    <span class="zoomify-open" data-src="https://lvivart-admin.sitegist.com/media/collectiveaccess/images/0/86664_ca_object_representations_media_9_original_zdata/">
        Open 1
    </span>
    <span class="zoomify-open" data-src="https://lvivart-admin.sitegist.com/media/collectiveaccess/images/0/65753_ca_object_representations_media_7_original_zdata/">
        Open 2
    </span>
    <div id="zoomify-container" class="zoomify-container zoomify-slider">
        <div class="zoomify-close">
            <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 46 30" style="enable-background:new 0 0 46 30;" xml:space="preserve">
                <polygon class="st0" points="32.19,22.78 24.41,15 32.19,7.22 30.78,5.81 23,13.58 15.22,5.81 13.81,7.22 21.59,15 13.81,22.78 15.22,24.19 23,16.41 30.78,24.19 "/>
            </svg>
        </div>
        <div id="zoomify" class="zoomify-item"></div>
        <div class="zoomify-arrows">
            <div class="zoomify-prev">
                <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 46 20" style="enable-background:new 0 0 46 20;" xml:space="preserve">
                    <polygon class="st0" points="34,9 18,9 18,7 12,10.01 18,13 18,11 34,11 "/>
                </svg>
            </div>
            <div class="zoomify-next">
                <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 46 20" style="enable-background:new 0 0 46 20;" xml:space="preserve">
                    <polygon class="st0" points="12,11 28,11 28,13 34,9.99 28,7 28,9 12,9 "/>
                </svg>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script src="/js/openseadragon.min.js?ver={{date('Hsi')}}"></script>
@endsection