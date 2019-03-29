function getBalance() {
    $.ajax({
        url: '/balance/',
        type: 'GET',
        success: function (response) {
            $('.user_balance').text(response + ' сум');
        },
        error: function () {
            $('.user_balance').text('недоступно');
        }

    })
}



