<div class="container">
    <div class="row" style="display: flex;">
        <div class="filter_block filters">
            <label>
                Поиск по возможным тегам
                <input type="text" name="search" id="search_type" />
            </label>
        </div>
        <div class="full_filter filters">
            <label style="float: left;">
                Поиск по заданному тегу по месту локации
                <input type="text" name="full_search" id="full_search" />
            </label>
            <div class="location" style="display: inline-flex;">
                <div class="list-group-item radio">
                    <label><input type="radio" checked="checked" name="location" class="common_selector country" value="узбекистан">Узбекистан</label>
                </div>
                <div class="list-group-item radio">
                    <label><input type="radio" name="location" class="common_selector country" value="uzbekistan">Uzbekistan</label>
                </div>
                <div class="list-group-item radio">
                    <label><input type="radio" name="location" class="common_selector country" value="ташкент">Ташкент</label>
                </div>
                <div class="list-group-item radio">
                    <label><input type="radio" name="location" class="common_selector country" value="tashkent">Tashkent</label>
                </div>
            </div>
        </div>
        <div class="tag_variations">
            <div class="filter_tags">

            </div>
            {{--@if($tags)--}}
                {{--@foreach($tags as $tag)--}}
                    {{--<button data-name="{{ $tag->name }}">{{ $tag->name }} ({{ $tag->mediaCount }} постов)</button>--}}
                {{--@endforeach--}}
            {{--@endif--}}
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="row filter_data">

            </div>
            {{--@if($medias)--}}
                {{--@foreach($medias as $media)--}}
                    {{--<div class="col">--}}
                        {{--<img src="{{ $media['squareImages'][1] }}" />--}}
                        {{--<h5><a href="{{ $media['link'] }}" target="_blank">{{ $media['caption'] }}</a></h5>--}}
                        {{--<div class="media_info">--}}
                            {{--<span class="media_created">{{ Carbon\Carbon::createFromFormat('U', $media['createdTime'])->format('d.m.Y') }}</span>--}}
                            {{--@if(!empty($media['videoViews']))--}}
                                {{--<span class="video_views">{{ $media['videoViews'] }}</span>--}}
                            {{--@endif--}}
                            {{--<span class="likes_count">{{ $media['likesCount'] }}</span>--}}
                            {{--<span class="comments_count">{{ $media['commentsCount'] }}</span>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                {{--@endforeach--}}
            {{--@endif--}}
        </div>
    </div>
</div>
<style>
    #loading
    {
        text-align:center;
        background: url({{ asset('images/loader_rocket.gif') }}) no-repeat center;
        height: 500px;
    }
    .filters {
        border: 1px solid #eee;
        padding: 20px;
    }
</style>
<script
        src="http://code.jquery.com/jquery-3.3.1.min.js"
        integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
        crossorigin="anonymous"></script>
<script>
    $(document).ready(function () {
        // $('select').select2();

        $('#search_type').on('change', function(e) {
            var tag = $(this).val();
            $('.filter_tags').html('<div id="loading" style="" ></div>');

            jQuery.ajax({
                url:"/search/tags",
                method:"GET",
                data:{tag:tag},
                success:function(data){
                    $('.filter_tags').html(data);
                }
            });
        });

        $('#full_search').on('change', function(e) {
            var tag = $(this).val();
            var loc = $('.location .country:checked').val();
            $('.filter_data').html('<div id="loading" style="" ></div>');

            jQuery.ajax({
                url:"/search/full",
                method:"GET",
                data:{tag:tag, location:loc},
                success:function(data){
                    $('.filter_data').html(data);
                }
            });
        });

        $('.country').on('change', function(e) {
            if($(this).is(':checked')) {
                var loc = $(this).val();
                var tag = $('#full_search').val();
                $('.filter_data').html('<div id="loading" style="" ></div>');

                jQuery.ajax({
                    url:"/search/full",
                    method:"GET",
                    data:{tag:tag, location:loc},
                    success:function(data){
                        $('.filter_data').html(data);
                    }
                });
            }
        });

        $(document).on('click', '.filter_tags button', function (e) {

            var search = $(this).data('name');
            $('.filter_data').html('<div id="loading" style="" ></div>');

            jQuery.ajax({
                url:"/search/posts",
                method:"GET",
                data:{search:search},
                success:function(data){
                    $('.filter_data').html(data);
                }
            });
        });
    });
</script>
