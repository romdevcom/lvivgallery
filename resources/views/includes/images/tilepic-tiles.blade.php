<div class="tiles-list @if(!empty($gallery)) tile-gallery @endif">
    @php $count = 1 @endphp
    @if(isset($result->representations))
        @foreach($result->representations as $representation)
            @if(!empty($representation->media['tilepic']))
                <div class="tile-img tile-img-{{$count}}"
                     data-src="{{str_replace(array('storage', 'https://lvivart-admin.sitegist.com'), array('media', ''), media_url($representation->media, 'tilepic'))}}"
                     data-width="{{$representation->media['tilepic']['WIDTH']}}"
                     data-height="{{$representation->media['tilepic']['HEIGHT']}}"
                     data-layers="{{$representation->media['tilepic']['PROPERTIES']['layers']}}"
                     data-bitdepth="{{$representation->media['tilepic']['PROPERTIES']['bitdepth']}}"
                     data-quality="{{$representation->media['tilepic']['PROPERTIES']['quality']}}"
                     data-size="{{$representation->media['tilepic']['PROPERTIES']['tile_width']}}"
                >
                    <div class="tile-close">
                        <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 46 30" style="enable-background:new 0 0 46 30;" xml:space="preserve">
                            <polygon class="st0" points="32.19,22.78 24.41,15 32.19,7.22 30.78,5.81 23,13.58 15.22,5.81 13.81,7.22 21.59,15 13.81,22.78 15.22,24.19 23,16.41 30.78,24.19 "/>
                        </svg>
                    </div>
                    <div class="tile-bar">
                        <div id="tileviewerControlZoomOut" class="tile-minus tileviewerControlZoomOut">
                            <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 46 20" style="enable-background:new 0 0 46 20;" xml:space="preserve">
                                <rect x="13" y="9" class="st0" width="20" height="2"/>
                            </svg>
                        </div>
                        <div id="tileviewerControlZoomIn" class="tile-plus tileviewerControlZoomIn">
                            <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 46 30" style="enable-background:new 0 0 46 30;" xml:space="preserve">
                                <polygon class="st0" points="33,14 24,14 24,5 22,5 22,14 13,14 13,16 22,16 22,25 24,25 24,16 33,16 "/>
                            </svg>
                        </div>
                        <div class="tile-center" id="tileviewerControlFitToScreen">
                            <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 122.88 122.92" style="enable-background:new 0 0 122.88 122.92" xml:space="preserve">
                                <g>
                                    <path d="M106.4,43.36c1.68,0,3.04,1.36,3.04,3.04c0,1.68-1.36,3.04-3.04,3.04H76.46c-1.68,0-3.04-1.36-3.04-3.04V15.99 c0-1.68,1.36-3.04,3.04-3.04c1.68,0,3.04,1.36,3.04,3.04v3.08v19.69l9.52-10.06l26.24-27.75c1.15-1.21,3.07-1.27,4.28-0.12 c1.21,1.15,1.27,3.07,0.12,4.28L93.29,33l-9.8,10.36h20.05H106.4L106.4,43.36z M43.37,16.47c0-1.68,1.36-3.04,3.04-3.04 s3.04,1.36,3.04,3.04v29.93c0,1.68-1.36,3.04-3.04,3.04H16c-1.68,0-3.04-1.36-3.04-3.04c0-1.68,1.36-3.04,3.04-3.04h3.11h19.66 l-10.01-9.46L0.96,7.62c-1.22-1.15-1.28-3.07-0.13-4.29C1.97,2.1,3.9,2.05,5.12,3.2l27.99,26.46l10.26,9.7V19.61V16.47L43.37,16.47 z M16.48,79.55c-1.68,0-3.04-1.36-3.04-3.04c0-1.68,1.36-3.04,3.04-3.04h29.94c1.68,0,3.04,1.36,3.04,3.04v30.41 c0,1.68-1.36,3.04-3.04,3.04s-3.04-1.36-3.04-3.04v-3.11V84.15l-9.46,10.01L7.63,121.97c-1.15,1.22-3.07,1.28-4.29,0.13 c-1.22-1.15-1.28-3.07-0.13-4.29l26.46-27.99l9.7-10.26H19.62H16.48L16.48,79.55z M79.51,106.44c0,1.68-1.36,3.04-3.04,3.04 c-1.68,0-3.04-1.36-3.04-3.04V76.51c0-1.68,1.36-3.04,3.04-3.04h30.41c1.68,0,3.04,1.36,3.04,3.04c0,1.68-1.36,3.04-3.04,3.04 h-3.08H84.1l10.07,9.52l27.75,26.23c1.22,1.15,1.28,3.07,0.13,4.29c-1.15,1.22-3.07,1.28-4.29,0.13L89.86,93.34l-10.35-9.78v20.02 V106.44L79.51,106.44z"/>
                                </g>
                            </svg>
                        </div>
                        @if(!empty($gallery))
                            <div class="tile-prev" data-id="{{$count}}">
                                <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 46 20" style="enable-background:new 0 0 46 20;" xml:space="preserve">
                                    <polygon class="st0" points="34,9 18,9 18,7 12,10.01 18,13 18,11 34,11 "/>
                                </svg>
                            </div>
                            <div class="tile-next" data-id="{{$count}}">
                                <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 46 20" style="enable-background:new 0 0 46 20;" xml:space="preserve">
                                    <polygon class="st0" points="12,11 28,11 28,13 34,9.99 28,7 28,9 12,9 "/>
                                </svg>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
            @php $count++ @endphp
            @break
        @endforeach
    @endif
    @if(!empty($gallery))
        @foreach($gallery as $image)
            @if(!empty($image['tile']))
                <div class="tile-img tile-img-{{$count}}"
                     data-src="{{str_replace(array('storage', 'https://lvivart-admin.sitegist.com'), array('media', ''), $image['tile'])}}"
                     data-width="{{$image['tilepic']['WIDTH']}}"
                     data-height="{{$image['tilepic']['HEIGHT']}}"
                     data-layers="{{$image['tilepic']['PROPERTIES']['layers']}}"
                     data-bitdepth="{{$image['tilepic']['PROPERTIES']['bitdepth']}}"
                     data-quality="{{$image['tilepic']['PROPERTIES']['quality']}}"
                     data-size="{{$image['tilepic']['PROPERTIES']['tile_width']}}"
                >
                    <div class="tile-close">
                        <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 46 30" style="enable-background:new 0 0 46 30;" xml:space="preserve">
                            <polygon class="st0" points="32.19,22.78 24.41,15 32.19,7.22 30.78,5.81 23,13.58 15.22,5.81 13.81,7.22 21.59,15 13.81,22.78 15.22,24.19 23,16.41 30.78,24.19 "/>
                        </svg>
                    </div>
                    <div class="tile-bar">
                        <div id="tileviewerControlZoomOut" class="tile-minus tileviewerControlZoomOut">
                            <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 46 20" style="enable-background:new 0 0 46 20;" xml:space="preserve">
                                <rect x="13" y="9" class="st0" width="20" height="2"/>
                            </svg>
                        </div>
                        <div id="tileviewerControlZoomIn" class="tile-plus tileviewerControlZoomIn">
                            <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 46 30" style="enable-background:new 0 0 46 30;" xml:space="preserve">
                                <polygon class="st0" points="33,14 24,14 24,5 22,5 22,14 13,14 13,16 22,16 22,25 24,25 24,16 33,16 "/>
                            </svg>
                        </div>
                        <div class="tile-center" id="tileviewerControlFitToScreen">
                            <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 122.88 122.92" style="enable-background:new 0 0 122.88 122.92" xml:space="preserve">
                                <g>
                                    <path d="M106.4,43.36c1.68,0,3.04,1.36,3.04,3.04c0,1.68-1.36,3.04-3.04,3.04H76.46c-1.68,0-3.04-1.36-3.04-3.04V15.99 c0-1.68,1.36-3.04,3.04-3.04c1.68,0,3.04,1.36,3.04,3.04v3.08v19.69l9.52-10.06l26.24-27.75c1.15-1.21,3.07-1.27,4.28-0.12 c1.21,1.15,1.27,3.07,0.12,4.28L93.29,33l-9.8,10.36h20.05H106.4L106.4,43.36z M43.37,16.47c0-1.68,1.36-3.04,3.04-3.04 s3.04,1.36,3.04,3.04v29.93c0,1.68-1.36,3.04-3.04,3.04H16c-1.68,0-3.04-1.36-3.04-3.04c0-1.68,1.36-3.04,3.04-3.04h3.11h19.66 l-10.01-9.46L0.96,7.62c-1.22-1.15-1.28-3.07-0.13-4.29C1.97,2.1,3.9,2.05,5.12,3.2l27.99,26.46l10.26,9.7V19.61V16.47L43.37,16.47 z M16.48,79.55c-1.68,0-3.04-1.36-3.04-3.04c0-1.68,1.36-3.04,3.04-3.04h29.94c1.68,0,3.04,1.36,3.04,3.04v30.41 c0,1.68-1.36,3.04-3.04,3.04s-3.04-1.36-3.04-3.04v-3.11V84.15l-9.46,10.01L7.63,121.97c-1.15,1.22-3.07,1.28-4.29,0.13 c-1.22-1.15-1.28-3.07-0.13-4.29l26.46-27.99l9.7-10.26H19.62H16.48L16.48,79.55z M79.51,106.44c0,1.68-1.36,3.04-3.04,3.04 c-1.68,0-3.04-1.36-3.04-3.04V76.51c0-1.68,1.36-3.04,3.04-3.04h30.41c1.68,0,3.04,1.36,3.04,3.04c0,1.68-1.36,3.04-3.04,3.04 h-3.08H84.1l10.07,9.52l27.75,26.23c1.22,1.15,1.28,3.07,0.13,4.29c-1.15,1.22-3.07,1.28-4.29,0.13L89.86,93.34l-10.35-9.78v20.02 V106.44L79.51,106.44z"/>
                                </g>
                            </svg>
                        </div>
                        @if(!empty($gallery))
                            <div class="tile-prev" data-id="{{$count}}">
                                <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 46 20" style="enable-background:new 0 0 46 20;" xml:space="preserve">
                                    <polygon class="st0" points="34,9 18,9 18,7 12,10.01 18,13 18,11 34,11 "/>
                                </svg>
                            </div>
                            <div class="tile-next" data-id="{{$count}}">
                                <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 46 20" style="enable-background:new 0 0 46 20;" xml:space="preserve">
                                    <polygon class="st0" points="12,11 28,11 28,13 34,9.99 28,7 28,9 12,9 "/>
                                </svg>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
            @php $count++ @endphp
        @endforeach
    @endif
</div>