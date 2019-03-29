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
</ul>
<div class="bordered">
    <h6 class="title">Мой аккаунт</h6>
</div>
<ul class="list-group bordered">
    <li class="list-group-item">
        <i class="fas fa-cog"></i>
        <a href="/profile">Аккаунты социальных сетей</a>
    </li>
    <li class="list-group-item">
        <i class="fas fa-money-check-alt"></i>
        <a href="/deposit">Пополнить баланс</a>
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

<div class="topped">

    <div class="bordered">
        <ul class="list-group">
            <li class="dropdown open">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown"
                   aria-expanded="true">
                    <div class="img-holder img-thumbnail border-0 ava"
                         style="background-image: url({{ (!empty(Auth::user()->avatar)) ? asset('uploads/'.Auth::user()->avatar) : asset('images/ava.png') }})">
                    </div>
                    <div class="text">
                        <span class="nav-link">{{ Auth::user()->email }}</span>
                        <span class="nav-link pt-0">ID: {{ Auth::user()->billing_id }}</span>
                    </div>
                </a>
                <ul class="dropdown-menu">
                    <li><i class="fas fa-money-bill"></i><a href="/deposit">Мой баланс (<span class="user_balance"></span>)</a></li>
                    <li><i class="far fa-user"></i><a href="/info/edit">Мои данные</a></li>
                    <li><i class="fas fa-cog"></i><a href="/profile">Мои настройки</a></li>
                </ul>
            </li>
        </ul>
    </div>

</div>
