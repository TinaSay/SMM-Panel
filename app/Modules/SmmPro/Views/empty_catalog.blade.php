@extends('layouts.app')
@section('title','Каталог наших услуг')
@section('content')

    <div class="row justify-content-center">
        <div class="col-12">
            <div class="intro-wrapper">
                @if ($intro)
                    <div class="intro alert alert-success alert-dismissable text-center">
                        @if (Auth::user()->role_id == \App\User::ROLE_ADMIN)
                            <div class="intro-editor">
                                <span id="intro" data-type="textarea" data-pk="1" data-url="/ajax/edit-intro" data-title="Редактировать">
                                    {!! $intro->description !!}
                                </span>
                            </div>
                        @else
                            {!! $intro->description !!}
                        @endif
                    </div>
                @else
                    @if (Auth::user()->role_id == \App\User::ROLE_ADMIN)
                        <div class="intro-editor">
                            <a href="#" id="intro" data-type="textarea" data-pk="1" data-url="/ajax/edit-intro" data-title="Редактировать">intro</a>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>

    <div id="app" class="services-catalog-wrapper">
        @include('smmpro::additional')
        <div class="services-catalog-group">
            <h4 class="services-catalog-action">Выберите социальную сеть</h4>

            <ul class="social-network-selection">
                @foreach($socials as $social)
                    <li>
                        <a class="social-network-selection-button">
                            <img src="/uploads/icons/{{$social->icon}}" alt>
                            {{$social->name}}
                        </a>
                        @if(count($social->children) > 0)
                            <ul class="social-network-selection-submenu">
                                @foreach($social->children as $children)
                                    <li>
                                        <a href="{{route('catalog', $children->id)}}">
                                            <img src="/uploads/icons/{{$children->icon}}">
                                            {{$children->name}}
                                        </a>
                                    </li>
                                @endforeach
                                <button class="hide">Скрыть</button>
                            </ul>
                        @endif
                    </li>
                @endforeach
            </ul>

        </div>
    </div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('/js/bootstrap3-editable/css/bootstrap-editable.css') }}">
<style>
    .editable-click, a.editable-click, a.editable-click:hover {
        border: none;
    }
    .editable-pre-wrapped {
        white-space: normal;
    }
</style>
@endpush

@push('scripts')
<script src="{{ asset('/js/bootstrap3-editable/js/bootstrap-editable.min.js') }}"></script>
<script>
    $.fn.editable.defaults.mode = 'inline';
    $.fn.editable.defaults.params = function (params) {
        params._token = $("meta[name=token]").attr("content");
        return params;
    };

    $(function () {
        $('#intro').editable();
    });
</script>
@endpush
