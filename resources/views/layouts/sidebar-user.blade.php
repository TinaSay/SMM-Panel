<ul class="list-group">
    <li class="list-group-item">
        <i class="fas fa-cog"></i>
        <a href="/profile">Настройки аккаунтов</a>
    </li>
</ul>
<div class="bordered">
    <h6 class="title">Быстрая накрутка</h6>
</div>
<ul class="list-group">
    <li class="list-group-item">
        <i class="fas fa-shopping-cart"></i>
        <a href="/catalog">Купить накрутку</a>
    </li>
    <li class="list-group-item">
        <i class="fas fas fa-list"></i>
        <a href="/my-orders">Мои заказы</a>
    </li>
    <li class="list-group-item">
        <i class="fas fa-money-check-alt"></i>
        <a href="/deposit">Пополнение баланса</a>
    </li>
</ul>
<div class="bordered">
    <p class="info">{{ Auth::user()->login }}</p>
</div>
<ul class="list-group">
    <li class="list-group-item">
        <p class="info">Ваш баланс {{ \App\User::getUserBalance() }} сум</p>
        <i class="far fa-credit-card"></i>
        <a href="/deposit">Пополнить</a>
    </li>
</ul>

<div class="bordered">
    <ul class="list-group">
        <li class="list-group-item">
            <p class="info">
                <i class="fa fa-sign-out-alt"></i>
                <a href="/logout">Выход</a></p>
        </li>
    </ul>
</div>
