    @push('functions')
        <link rel="stylesheet" href="{{ asset('instashop/css/select2.min.css') }}">
        <link rel="stylesheet" href="{{ asset('instashop/css/style.css') }}">
        <link rel="stylesheet" href="{{ asset('instashop/css/media.css') }}">
    @endpush
    @extends('layouts.app')
    @section('title','Instashop')
    @section('content')
        <section class="search-anything">
            <div class="container">
                <div class="row">
                    <div class="col-lg-4">
                        <div class="search-item">
                            <div class="caption">
                                <h1 class="h1">Hey!</h1>
                                <p class="desc">I'm picstar</p>
                            </div>
                            {{--<form>--}}
                            <div class="form-group">
                                <input class="form-control" id="search_input" type="text" placeholder="type here...">
                            </div>
                            {{--</form>--}}
                        </div>
                    </div>
                    <div class="col-lg-8">
                        <div class="hashtags filter_tags" id="hashtags">


                        </div>
                        <div class="filter" id="filter">
                            <div class="caption">
                                <h1 class="h1 main_search_title"></h1>
                                <p class="desc main_search_count"></p>
                            </div>
                            <div class="filter-item">
                                <select class="form-control select2 select2-product hashtag_filter_select" name="hashtag_select" style="width: 100%">

                                </select>
                            </div>
                            <div class="row gallery filter_posts">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="modal fade" id="showPost" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        {{--<h5 class="modal-title" id="exampleModalLongTitle">Modal title</h5>--}}
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="postInfo">

                    </div>
                    <div class="modal-footer">
                        {{--<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>--}}
                        <button type="button" class="btn btn-primary save_post">Сохранить</button>
                    </div>
                </div>
            </div>
        </div>

    @endsection

    <style>
        #loading
        {
            text-align:center;
            background: url({{ asset('images/loader_rocket.gif') }}) no-repeat center;
            height: 500px;
        }
    </style>

    @push('scripts')
        <script type="text/javascript" src="{{ asset('instashop/js/select2.min.js') }}"></script>
        <script type="text/javascript" src="{{ asset('instashop/js/instashop.js') }}"></script>
    @endpush
