var $csrf = $('meta[name="csrf-token"]').attr('content');
var minUzb = 30, midUzb = 60, topUzb = 90;
var minSng = 60, midSng = 90, topSng = 150;

$(document).on('click', '.update-user-social-info', function () {
    var rotate = $(this);
    rotate.addClass('social-update-rotate');
    $.ajax({
        url: '/profile/social/update/' + $(this).data('social'),
        type: 'GET',
        data: {_token: $csrf},
        success: function (resp) {
            rotate.removeClass('social-update-rotate');
            toastr[resp.status](resp.title, resp.message);
            var socialClass = resp.social + 'Acc';
            $('#' + socialClass).load(location.href + ' #' + socialClass + ">*", "");
        }
    });
});

function hide(task) {
    var $csrf = $('meta[name="csrf-token"]').attr('content');
    $.ajax({
        url: '/task/hide/' + task,
        type: 'GET',
        data: {_token: $csrf},
        success: function (resp) {
            $(".card[data-id=" + task + "]").addClass("d-none");
            toastr.success('Задание скрыто из вашей ленты');
        }
    });
}

function checkConnectedProfile(id) {
    var $csrf = $('meta[name="csrf-token"]').attr('content');
    return $.ajax({
        url: '/profile/check/' + id,
        type: 'GET',
        data: {_token: $csrf}
    });
}

$(document).ready(function () {
    $(document).on('click', '.do-action', function (event) {

        var $this = $(this),
            $id = $this.attr('data-id'),
            $url = '/task/show/' + $id,
            $commentId = $this.parents('.card-body').find('.randComment').data('id'),
            $check = $this.attr('data-check'),
            $watch = false,
            windowParams = 1025;

        // var profileCheck = checkConnectedProfile($id);
        // profileCheck.then(function (profData) {
        //     if (profData.status) {
                if ($check === "true") {
                    event.preventDefault();
                    event.stopImmediatePropagation();
                    checkTask($id, $commentId, $this, $check);
                } else {
                    popUp = window.open($url, "thePopUp", "toolbar=0,location=0,directories=0,status=1,menubar=0,titlebar=0,scrollbars=1,resizable=0,width=" + windowParams + ",height=" + windowParams);
                }
                $this.parents('.card-body').find('.do-action').removeClass('do-action');

                function someFunctionToCallWhenPopUpCloses(watch) {
                    window.setTimeout(function () {
                        if (popUp.closed) {
                            if ($watch == true) {
                                checkTask($id, $commentId, $this, $check, true);
                            } else {
                                checkTask($id, $commentId, $this, $check, false);
                            }
                        }
                    }, 1);
                }

                if ($check !== "true") {
                    window.setTimeout(function () {
                        $watch = true;
                    }, 35000);
                    var win = window.open($url, "thePopUp", "toolbar=0,location=0,directories=0,status=1,menubar=0,titlebar=0,scrollbars=1,resizable=0,width=" + windowParams + ",height=" + windowParams);
                    var pollTimer = window.setInterval(function () {
                        if (win.closed !== false) {
                            window.clearInterval(pollTimer);
                            someFunctionToCallWhenPopUpCloses();
                        }
                    }, 200);
                }
            // } else {
            //     window.location.href = '/profile';
            // }
        // });
    });

    $(document).on('click', '.copy_to_clipboard', function (e) {
        var element = $(this).prev('.randComment').text();
        copyToClipboard(element);
    });

    $('.close-icon').on('click', function (e) {
        e.preventDefault();
    })
});

function copyToClipboard(element) {
    var $temp = $("<input>");
    $("body").append($temp);
    $temp.val(element).select();
    document.execCommand("copy");
    $temp.remove();
}

function checkTask($id, $commentId, $this, $check, $watch = false) {
    var $csrf = $('meta[name="csrf-token"]').attr('content');
    $this.parents('.card-body').find('.make_action_but').html($this.parents('.card-body').find('.make_action_but').nextAll('.while_checking').html());
    $.ajax({
        url: '/tasks/check/' + $id,
        type: 'GET',
        data: {_token: $csrf, comment: $commentId, check: $check, watch: $watch},
        success: function (resp) {
            if (resp.original.status !== 'success') {
                if ($this.parents('.card-body').find('.make_action_but').attr('data-check') === "false") {
                    $this.parents('.card-body').find('.make_action_but').html($this.parents('.card-body').find('.make_action_but').nextAll('.needs_checking').html());
                    $this.parents('.card-body').find('.make_action_but.withComments').addClass('do-action');
                    $this.parents('.card-body').find('.make_action_but').attr('data-check', "true");
                } else {
                    $this.parents('.card-body').find('.make_action_but').html($this.parents('.card-body').find('.make_action_but').nextAll('.is_initial').html());
                    $this.parents('.card-body').find('.make_action_but.withComments').removeClass('do-action');
                    $this.parents('.card-body').find('.make_action_but').attr('data-check', "false");
                }
                $this.parents('.card-body').find('.link_but').not('.withComments').addClass('do-action');
            } else {
                $this.parents('.card-body').find('.make_action_but').html($this.parents('.card-body').find('.make_action_but').nextAll('.is_ready').html());
                getBalance();
                // $this.parents('.card-body').parent().slideUp(700);
            }
            toastr[resp.original.status](resp.original.title, resp.original.message);
        }
    });
}

$(document).ready(function () {

    $('.submit-task').on('click', function () {
        var commentsCounter = $('.comment-counter').text();
        $('.counter').val(commentsCounter);

    });

    var $speed = 1;
    totalPoints();

    $('.comment-number').last().text($('.comment-block .comment-input').length);
    $('.comment-counter').text($('.comment-block .comment-input').length);

    $("#comment-1").emojioneArea({
        hideSource: true
    });

    $('.add-comment-btn').on('click', function () {
        var currentId = $('.comment-input').last().find('textarea').attr('id');
        currentId = parseInt(currentId.substr(currentId.indexOf("-") + 1)) + 1;
        var nextId = 'comment-' + currentId;

        $('.comment-input').last().clone().addClass('next').appendTo($('.comment-block')).find('textarea').val('');
        // .find('.emojionearea').remove()
        var commentsCounter = $('.comment-block .comment-input').length;
        $('.remove-comment:last').removeClass('d-none');
        $('.remove-comment:not(:last)').addClass('d-none');
        $('.comment-number').last().text(commentsCounter);
        $('.comment-input').last().find('.emojionearea').remove();
        $('.comment-input').last().find('textarea').attr('id', nextId);
        $('.comment-counter').text(commentsCounter);
        console.log(nextId);
        console.log("#" + nextId);
        $("#" + nextId).emojioneArea({
            hideSource: true
        });
    });

    $(document).on('click', '.remove-comment i', function (e) {
        e.preventDefault();
        $(this).parent().parent().parent().parent().remove();
        if ($('.comment-input').hasClass('next')) {
            $('.remove-comment:last').removeClass('d-none');
        }
        var commentsCounter = $('.comment-block .comment-input').length;
        $('.comment-counter').text(commentsCounter);
    });

    $('#service_id').on('change', function () {
        $selectedService = $(this).find(':selected').attr('data-name');

        if ($selectedService == 'Comment') {
            $('.comments-block').removeClass('d-none');
        } else {
            $('.comments-block').addClass('d-none');
        }
    });

    $(document).on('click', '.btn-speed', function () {
        $speed = parseInt($(this).attr('data-speed'));
        $('.btn-speed').removeClass('aactive');
        $(this).addClass('aactive');

        $value = $('#priority_input').val();
        $points = $('#points').val();
        if ($value === 'sng') {
            $('#points').val(minSng * $speed);
        } else {
            $('#points').val(minUzb * $speed);
        }

        $points = $('#sng_points').val();
        $('#sng_points').val(minSng * $speed);
        totalPoints();
    });
    $(document).on('click', '#priority .btn', function () {
        var speed = $('.btn-speed.aactive').data('speed');
        $value = $(this).attr('data-value');
        $('#priority .btn').removeClass('aactive');
        $(this).addClass('aactive');
        $('#priority_input').val($value);
        if ($value == 'uzbsng') {
            $('#sngp').show();
            $('#sngq').show();

            $('label[for="points"]').html('Оплата исполнителю для Узбекистана');
            $('label[for="amount"]').html('Количество выполнений для Узбекистана');
            if(speed == '1') {
                $('#points').val(minUzb).attr('min', minUzb);
            } else if(speed == '3') {
                $('#points').val(midUzb).attr('min', midUzb);
            } else {
                $('#points').val(topUzb).attr('min', topUzb);
            }
        } else if ($value == 'sng') {
            $('#sngp').hide();
            $('#sngq').hide();
            $('label[for="points"]').html('Оплата исполнителю для СНГ.');
            $('label[for="amount"]').html('Количество выполнений для СНГ.');

            if(speed == '1') {
                $('#points').val(minSng).attr('min', minSng);
            } else if(speed == '3') {
                $('#points').val(midSng).attr('min', midSng);
            } else {
                $('#points').val(topSng).attr('min', topSng);
            }
        } else {
            $('label[for="points"]').html('Оплата исполнителю для Узбекистана');
            $('label[for="amount"]').html('Количество выполнений для Узбекистана');
            $('#sngp').hide();
            $('#sngq').hide();
            if(speed == '1') {
                $('#points').val(minUzb).attr('min', minUzb);
            } else if(speed == '3') {
                $('#points').val(midUzb).attr('min', midUzb);
            } else {
                $('#points').val(topUzb).attr('min', topUzb);
            }
        }
        totalPoints();
    });


    $(document).on('input change paste keyup', '.prices', function () {
        $id = $(this).attr('id');
        if ($id == 'points') {
            $('.btn-speed').removeClass('aactive');
            if ($(this).val() < minUzb) {
                $('.btn-speed[data-speed="1"]').addClass('aactive');
            } else if ($(this).val() < midUzb) {
                $('.btn-speed[data-speed="3"]').addClass('aactive');
            } else {
                $('.btn-speed[data-speed="5"]').addClass('aactive');
            }
        } else if ($id == 'sng_points') {
            $('.btn-speed').removeClass('aactive');
            if ($(this).val() < minSng) {
                $('.btn-speed[data-speed="1"]').addClass('aactive');
            } else if ($(this).val() < midSng) {
                $('.btn-speed[data-speed="3"]').addClass('aactive');
            } else {
                $('.btn-speed[data-speed="5"]').addClass('aactive');
            }
        }
        totalPoints();
    });

    function totalPoints() {
        $value = $('#priority_input').val();

        var _points = $('#points').val() * 2;
        var _amount = $('#amount').val();
        var _totalPoints = _points * _amount;
        if ($value === 'uzbsng') {
            var sng_points = $('#sng_points').val() * 2;
            var sng_amount = $('#sng_amount').val();
            var sng_totalPoints = sng_points * sng_amount;
            _totalPoints = _totalPoints + sng_totalPoints;
        }
        // $('input:radio[name="gender"]').change(
        //     function () {
        //         var genderPoints = 0;
        //         if ($(this).is(':checked')) {
        //             if (this.id == 'female-gender' || this.id == 'male-gender') {
        //                 genderPoints = _totalPoints * 2;
        //                 $('.totalPoints').text(genderPoints);
        //             } else {
        //                 $('.totalPoints').text(_totalPoints);
        //             }
        //         }
        //     });
        $('.totalPoints').text(_totalPoints);

    }


    $(document).on('click', '.refresh-task-list', function (e) {
        e.preventDefault();
        var $taskList = $('.task-list');
        $taskList.html('<div class="lds-ripple"><div></div><div></div></div>');
        $taskList.load(location.href + ' .task-list' + ">*", "");
    });

    $(document).find('.hide').on('click', function(){
        $(this).parent().slideUp();
        $(this).parent().parent().children('a').removeClass('selected');
    });
    $(document).find('.buyit').on('click', function(){
        $id = $(this).data('id');
        $category_id = $(this).data('cat');
        $qty = $(this).data('qty');
        $link = $('.products_cont').find('.products[data-cat="'+$category_id+'"]').find('.prod_link').val();
        $('#servid').val($id);
        $('#link').val($link);
        $('#qty').val($qty);
        $('#order-post').submit();
    })
    $(document).find(".additional-sidebar-select-button, .social-network-selection-button").click(function (e) {
        e.preventDefault();
        $true = $(this).hasClass('selected');
        $(".additional-sidebar-select-button, .social-network-selection-button").removeClass('selected');
        $('.additional-sidebar-submenu, .social-network-selection-submenu').slideUp();


        if (!$true) {
            $(this).toggleClass('selected');
            $(this).parent().find('.additional-sidebar-submenu, .social-network-selection-submenu').slideToggle();
        }
    });


    //Скролл у сайдбара
    (function ($) {
        $(".additional-sidebar").mCustomScrollbar({
            autoHideScrollbar: true,
            theme: "minimal-dark"
        });
    })(jQuery);

    //Фильтрация на странице Истории
    $(function(){
        filter_data();

        if(window.location.href.indexOf("history") > -1) {
            $('input[name="daterange"]').daterangepicker({
                opens: 'center',
                timePicker: true,
                // startDate: moment().startOf('hour'),
                // endDate: moment().startOf('hour').add(32, 'hour'),
                minYear: 2019,
                timePicker24Hour: true,
                // maxYear: parseInt(moment().format('YYYY'),10),
                locale: {
                    format: 'DD.MM.YYYY',
                    cancelLabel: 'Очистить',
                    applyLabel: 'Применить',
                    daysOfWeek: [
                        "Пн",
                        "Вт",
                        "Ср",
                        "Чт",
                        "Пт",
                        "Сб",
                        "Вс"
                    ],
                    monthNames: [
                        "Январь",
                        "Февраль",
                        "Март",
                        "Апрель",
                        "Май",
                        "Июнь",
                        "Июль",
                        "Август",
                        "Сентябрь",
                        "Октябрь",
                        "Ноябрь",
                        "Декабрь"
                    ]
                }
            }, function (start, end, label) {
                filter_data(null, start.format('YYYY-MM-DD'), end.format('YYYY-MM-DD'));
            });
        }


        $(document).on('click', '.dropdown a.common_selector', function(){
            $(this).parents('.dropdown').find(".btn:first-child").text($(this).text());
            // $(this).parents('.dropdown').find(".btn:first-child").val($(this).text());
            $(this).siblings().removeClass('selected_filter');
            $(this).addClass('selected_filter');
            filter_data();
        });

        function filter_data(page, start, end)
        {
            var type = $('.history_type.selected_filter').data('value');
            action = $('.history_action.selected_filter').data('value');
            $('#transaction_history tbody').html('<td class="loader" colspan="5"><div class="lds-ripple"><div></div><div></div></div></td>');

            $.ajax({
                url:"/profile/history/data",
                method:"GET",
                data:{type:type, action:action, start:start, end:end, page:page},
                success:function(data) {
                    $('#transaction_history tbody').html(data.output);
                    $('.pagination-cover').html(data.pagination);
                }
            });
        }

        $(document).on('click', '.pagination-cover .pagination a', function(e) {
            e.preventDefault();
            var p = 0;
            var attr = $(this).attr('rel');
            if (typeof attr !== typeof undefined && attr !== false) {
                if($(this).attr('rel') === 'next') {
                    p = parseInt($('.pagination li.active span').text()) + 1;
                } else if($(this).attr('rel') === 'prev') {
                    p = parseInt($('.pagination li.active span').text()) - 1;
                }
            } else {
                p = parseInt($(this).text());
            }
            filter_data(p);
        });

    });

    $(document).on('click', '.task-callback-but', function () {
        var id = $(this).data('id');
        $('#complainTaskId').val(id);
    });
});

function number_format(number, decimals, dec_point, thousands_sep) {
    var i, j, kw, kd, km;

    // input sanitation & defaults
    if( isNaN(decimals = Math.abs(decimals)) ){
        decimals = 2;
    }
    if( dec_point == undefined ){
        dec_point = ",";
    }
    if( thousands_sep == undefined ){
        thousands_sep = ".";
    }

    i = parseInt(number = (+number || 0).toFixed(decimals)) + "";

    if( (j = i.length) > 3 ){
        j = j % 3;
    } else{
        j = 0;
    }

    km = (j ? i.substr(0, j) + thousands_sep : "");
    kw = i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thousands_sep);
    //kd = (decimals ? dec_point + Math.abs(number - i).toFixed(decimals).slice(2) : "");
    kd = (decimals ? dec_point + Math.abs(number - i).toFixed(decimals).replace(/-/, 0).slice(2) : "");

    return km + kw + kd;
}