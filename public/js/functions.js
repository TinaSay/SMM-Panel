function getBalance() {
    $.ajax({
        url: '/balance/',
        type: 'GET',
        success: function (response) {
            $('#user_balance').text(response + ' сум');
        },
        error: function () {
            $('#user_balance').text('недоступно');
        }

    })
}

function getRawBalance() {
    $.ajax({
        url: '/raw-balance/',
        type: 'GET',
        success: function (response) {
            /*return response/100;*/
            console.log(response);
        },
        error: function () {
            console.log('error');
        }

    })
}
