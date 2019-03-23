@extends('layouts.app')
@section('title','Категории')
@section('content')
    <a href="{{ route('category.create') }}" class="btn btn-primary btn-lilac mb-3">Добавить новую</a>
    @if($categories->isEmpty())
        <h3>Нет категорий</h3>
    @else
        @if(session()->has('success'))
            <input type="hidden" id="success-session" value="{{ session('success') }}">
        @elseif((session()->has('fail')))
            <input type="hidden" id="fail-session" value="{{ session('fail') }}">
        @endif

        <?php $tree = json_encode($categories); ?>
        <div class="cf nestable-lists">
            <div>
                <button class="btn btn-outline-secondary mb-5" type="button" id="toggle-collapse">
                    Свернуть/развернуть все
                </button>
            </div>

            <div class="dd nestable" id="nestable">
                <ol class='dd-list dd3-list'>
                </ol>
            </div>
        </div>
    @endif
    {{--<div class="mt-3">
        <a href="{{ route('task.new') }}" class="btn btn-lilac">На главную</a>
    </div>--}}
@endsection
@push('scripts')
    <style>
        span.btn.btn-xs.pull-right {
            padding: 3px 3px;
        }

        .dd {
            position: relative;
            display: block;
            margin: 0;
            padding: 0;
            max-width: 600px;
            list-style: none;
            font-size: 13px;
            line-height: 20px;
        }

        .dd-list {
            display: block;
            position: relative;
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .dd-list .dd-list {
            padding-left: 30px;
        }

        .dd-collapsed .dd-list {
            display: none;
        }

        .dd-item,
        .dd-empty,
        .dd-placeholder {
            display: block;
            position: relative;
            margin: 0;
            padding: 0;
            min-height: 20px;
            font-size: 13px;
            line-height: 20px;
        }

        .dd-handle {
            display: block;
            height: 30px;
            margin: 5px 0;
            padding: 5px 10px;
            color: #333;
            text-decoration: none;
            font-weight: bold;
            border: 1px solid #ccc;
            background: #fafafa;
            background: -webkit-linear-gradient(top, #fafafa 0%, #eee 100%);
            background: -moz-linear-gradient(top, #fafafa 0%, #eee 100%);
            background: linear-gradient(top, #fafafa 0%, #eee 100%);
            -webkit-border-radius: 3px;
            border-radius: 3px;
            box-sizing: border-box;
            -moz-box-sizing: border-box;
            cursor: move;
            margin: 0 0 10px;
            background: #dbdbdb;
            /*    color: #6f6f6f;*/
            padding: 5px 12px
        }

        .dd-handle:hover {
            color: #2ea8e5;
            background: #fff;
        }

        .dd-item > button {
            position: relative;
            cursor: pointer;
            float: left;
            width: 25px;
            height: 30px;
            margin: 0px 0px;
            padding: 0;
            text-indent: 100%;
            white-space: nowrap;
            overflow: hidden;
            border: 0;
            background: #4CAF50;
            font-size: 12px;
            line-height: 1;
            color: #fff;
            text-align: center;
            font-weight: bold;
        }

        .dd-item > button:before {
            content: '+';
            display: block;
            position: absolute;
            width: 100%;
            text-align: center;
            text-indent: 0;
        }

        .dd-item > button[data-action="collapse"]:before {
            content: '-';
        }

        .dd-placeholder,
        .dd-empty {
            margin: 5px 0;
            padding: 0;
            min-height: 30px;
            background: #f2fbff;
            border: 1px dashed #b6bcbf;
            box-sizing: border-box;
            -moz-box-sizing: border-box;
        }

        .dd-empty {
            border: 1px dashed #bbb;
            min-height: 100px;
            background-color: #e5e5e5;
            background-image: -webkit-linear-gradient(45deg, #fff 25%, transparent 25%, transparent 75%, #fff 75%, #fff),
            -webkit-linear-gradient(45deg, #fff 25%, transparent 25%, transparent 75%, #fff 75%, #fff);
            background-image: -moz-linear-gradient(45deg, #fff 25%, transparent 25%, transparent 75%, #fff 75%, #fff),
            -moz-linear-gradient(45deg, #fff 25%, transparent 25%, transparent 75%, #fff 75%, #fff);
            background-image: linear-gradient(45deg, #fff 25%, transparent 25%, transparent 75%, #fff 75%, #fff),
            linear-gradient(45deg, #fff 25%, transparent 25%, transparent 75%, #fff 75%, #fff);
            background-size: 60px 60px;
            background-position: 0 0, 30px 30px;
        }

        .dd-dragel {
            position: absolute;
            pointer-events: none;
            z-index: 9999;
        }

        .dd-dragel > .dd-item .dd-handle {
            margin-top: 0;
        }

        .dd-dragel .dd-handle {
            -webkit-box-shadow: 2px 4px 6px 0 rgba(0, 0, 0, .1);
            box-shadow: 2px 4px 6px 0 rgba(0, 0, 0, .1);
        }

        /**
        * Nestable Extras
        */
        .nestable-lists {
            display: block;
            clear: both;
            padding: 30px 0;
            width: 100%;
            border: 0;
            border-top: 2px solid #ddd;
            border-bottom: 2px solid #ddd;
        }

        #nestable-menu {
            padding: 0;
            margin: 20px 0;
        }

        #nestable-output,
        #nestable2-output {
            width: 100%;
            height: 7em;
            font-size: 0.75em;
            line-height: 1.333333em;
            font-family: Consolas, monospace;
            padding: 5px;
            box-sizing: border-box;
            -moz-box-sizing: border-box;
        }

        #nestable2 .dd-handle {
            color: #fff;
            border: 1px solid #999;
            background: #bbb;
            background: -webkit-linear-gradient(top, #bbb 0%, #999 100%);
            background: -moz-linear-gradient(top, #bbb 0%, #999 100%);
            background: linear-gradient(top, #bbb 0%, #999 100%);
        }

        #nestable2 .dd-handle:hover {
            background: #bbb;
        }

        #nestable2 .dd-item > button:before {
            color: #fff;
        }

        .dd {
        / / float: left;
        / / width: 48 %;
            width: 80%;
        }

        .dd + .dd {
            margin-left: 2%;
        }

        .dd-hover > .dd-handle {
            background: #2ea8e5 !important;
        }

        /**
        * Nestable Draggable Handles
        */
        .dd3-content {
            display: block;
            height: 30px;
            margin: 5px 0;
            padding: 5px 10px 5px 40px;
            color: #333;
            text-decoration: none;
            font-weight: bold;
            border: 1px solid #ccc;
            background: #fafafa;
            background: -webkit-linear-gradient(top, #fafafa 0%, #eee 100%);
            background: -moz-linear-gradient(top, #fafafa 0%, #eee 100%);
            background: linear-gradient(top, #fafafa 0%, #eee 100%);
            -webkit-border-radius: 3px;
            border-radius: 3px;
            box-sizing: border-box;
            -moz-box-sizing: border-box;
        }

        .dd3-content:hover {
            color: #2ea8e5;
            background: #fff;
        }

        .dd-dragel > .dd3-item > .dd3-content {
            margin: 0;
        }

        .dd3-item > button {
            margin-left: 30px;
        }

        .dd3-handle {
            position: absolute;
            margin: 0;
            left: 0;
            top: 0;
            cursor: pointer;
            width: 30px;
            text-indent: 100%;
            white-space: nowrap;
            overflow: hidden;
            border: 1px solid #aaa;
            background: #ddd;
            background: -webkit-linear-gradient(top, #ddd 0%, #bbb 100%);
            background: -moz-linear-gradient(top, #ddd 0%, #bbb 100%);
            background: linear-gradient(top, #ddd 0%, #bbb 100%);
            border-top-right-radius: 0;
            border-bottom-right-radius: 0;
        }

        .dd3-handle:before {
            content: '≡';
            display: block;
            position: absolute;
            left: 0;
            top: 3px;
            width: 100%;
            text-align: center;
            text-indent: 0;
            color: #fff;
            font-size: 20px;
            font-weight: normal;
        }

        .dd3-handle:hover {
            background: #ddd;
        }


        /*
        * Nestable++
        */
        .button-delete {
            position: absolute;
            top: 1px;
            right: -60px;
            padding: 3px 7px !important;
        }

        .button-edit {
            position: absolute;
            top: 1px;
            right: -30px;
        }

        #menu-editor {
            margin-top: 40px;
        }

        #saveButton {
            padding-right: 30px;
            padding-left: 30px;
        }

        .output-container {
            margin-top: 20px;
        }

        #json-output {
            margin-top: 20px;
        }

        #nestable .dd-list .dd-item > button {
            position: relative;
            cursor: pointer;
            float: left;
            width: 25px;
            height: 30px !important;
            margin: 0 5px 0 0;
            padding: 0;
            text-indent: 100%;
            white-space: nowrap;
            overflow: hidden;
            border: 0;
            background: #4CAF50 !important;
            font-size: 12px;
            line-height: 1;
            color: #fff;
            text-align: center;
            font-weight: bold;
        }

        .dd-item > button[data-action="collapse"]:before {
            content: '-' !important;
        }

        .dd-item > button:before {
            content: '+' !important;
            display: block;
            position: absolute;
            width: 100%;
            text-align: center;
            text-indent: 0;
        }
    </style>
    <script>

        $(document).ready(function () {
            var obj = '{!! $tree !!}';
            var output = '';

            var session = $('#success-session').val();
            if (session != null) {
                window.toastr.success(session);
            }

            function buildItem(item, child) {
                var html = "<li class='dd-item' data-id='" + item.id + "' data-name='" + item.name + "'>";
                if (child === true) {
                    html += '<button class="collapse_btn" data-action="collapse" type="button">Collapse</button><button class="expand_btn" data-action="expand" type="button" style="display: none;">Expand</button>';
                }
                html += "<div class='dd-handle'>" + item.name + "</div>";
                html += '<span class="button-edit btn btn-success btn-xs pull-right" data-owner-id="' + item.id + '">' +
                    '                  <i class="fas fa-edit" aria-hidden="true"></i></span>';
                html += '<span class="button-delete btn btn-danger btn-xs pull-right" data-owner-id="' + item.id + '">' +
                    '<i class="fas fa-times" aria-hidden="true"></i></span>';

                if (item.children) {
                    html += "<ol class='dd-list'>";
                    $.each(item.children, function (index, sub) {
                        (sub.parent_id && sub.children.length === 0) ? child = false : true;
                        html += buildItem(sub, child);
                    });
                    html += "</ol>";
                }

                html += '</li>';

                return html;
            }

            $.each(JSON.parse(obj), function (index, item) {
                var child = true;
                (item.children.length === 0) ? child = false : true;
                output += buildItem(item, child);
            });
            var nestableList = $("#nestable > .dd-list");

            var prepareEdit = function () {
                var targetId = $(this).data('owner-id');

                window.location.href = '/category/edit/' + targetId;

            };
            var prepareDelete = function () {
                var targetId = $(this).data('owner-id');
                $.ajax({
                    url: '/category/destroy/' + targetId,
                    type: "DELETE",
                    data: {_token: '{!! csrf_token() !!}'},
                    success: function (response) {
                        if (response.status == 1) {
                            window.toastr.success(response.message);
                            var deletedElem = $('.dd-item[data-id=' + targetId + ']');
                            deletedElem.remove();
                        } else {
                            window.toastr.error(response.message);
                        }
                    },
                    error: function () {
                        window.toastr.error('Категория уже удалена. Нажмите F5');
                    }
                });
            };

            $(document).on("click", "#nestable .button-edit", prepareEdit);
            $(document).on("click", "#nestable .button-delete", prepareDelete);
            $(document).on("click", ".dd-item button", function (e) {
                var target = $(e.currentTarget),
                    action = target.data('action');
                if (action === 'collapse') {
                    $(this).next('.expand_btn').show();
                    $(this).hide();
                }
                if (action === 'expand') {
                    $(this).hide();
                    $(this).prev('.collapse_btn').show();
                }

            });

            var updateOutput = function (e) {

                var list = e.length ? e : $(e.target), output = list.data('output');
                var data = window.JSON.stringify(list.nestable('serialize'));

                $.ajax({
                    method: "post",
                    url: '{!! route('taxonomy.rebuild-tree') !!}',
                    data: {data: data, _token: '{!! csrf_token() !!}'}
                })
                    .done(function (msg) {
                        console.log(msg);
                        if (msg.status) {
                            window.toastr.success(msg.message);
                        } else {
                            window.toastr.error(msg.message);
                        }
                    });
            };

            $('#nestable').nestable({
                group: 1,
                maxDepth: 7
            }).on('change', updateOutput);
            $('.dd-list').html(output);

            $('#toggle-collapse').on('click', function () {
                $('.dd3-list > .dd-item').toggleClass('dd-collapsed');
            });

        });

    </script>
@endpush
