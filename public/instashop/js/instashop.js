$(document).ready(function () {

    $('#search_input').on('change', function(e) {
        var tag = $(this).val();
        $('.filter_tags').html('<div id="loading"></div>');

        $.ajax({
            url:"/search/tags",
            method:"GET",
            data:{tag:tag},
            success:function(data){
                $('.filter_tags').html(data.output);
                $('.hashtag_filter_select').html(data.hashtags);
                $('#hashtags').slideDown('700');
                $('#filter').slideUp('700');
            }
        });
    });

    $(document).on('click', '.get_more_info', function(e) {
        var code = $(this).data('code');
        $('.postInfo').html('<div id="loading" style="" ></div>');

        $.ajax({
            url:"/search/full",
            method:"GET",
            data:{code:code},
            success:function(data){
                $('.postInfo').html(data);
            }
        });
    });

    $('.hashtag_filter_select').on('change', function(e) {
        var search = $(this).val();
        $('.filter_posts').html('<div id="loading"></div>');

        $.ajax({
            url:"/search/posts",
            method:"GET",
            data:{search:search},
            success:function(data){
                $('.filter_posts').html(data);
            }
        });
    });

    $(document).on('click', '.save_post', function () {
        // $(this).html('<i class="fas fa-spinner"></i>');
        var code = $(this).parents('#showPost').find('.modal-img').data('code'),
            cat = $('#category_id').val();
        $(this).addClass('added_post');
        $.ajax({
            url:"/instashop/create",
            method:"GET",
            data:{code:code, category_id: cat},
            success:function(data){
                toastr[data.status](data.title, data.message);
                if(data.status !== 'success') {
                    $(this).removeClass('added_post');
                }
                // else {
                //     $(this).html('<i class="fas fa-check"></i>');
                // }
            }
        });
    });

    $(document).on('click', '.hashtags-item', function (e) {
        var search = $(this).data('name');
        $('#hashtags').fadeOut('700');
        $('#filter').fadeIn('700');
        $('.filter_posts').html('<div id="loading"></div>');
        $.ajax({
            url:"/search/posts",
            method:"GET",
            data:{search:search},
            success:function(data){
                $('.filter_posts').html(data);
                $('select').select2({ width: 'resolve' });
            }
        });
    });
});