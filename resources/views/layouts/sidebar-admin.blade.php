<div class="bordered">
    <h6 class="title">Администрирование</h6>
</div>
<div class="collapse show" id="navbarToggleExternalContent">
    <ul class="list-group">
        <li class="list-group-item">
            <i class="fas fa-user-friends"></i>
            <a href="/users">Пользователи</a>
        </li>
        <li class="list-group-item">
            <i class="fas fa-layer-group"></i>
            <a href="/categories">Категории</a>
        </li>
        <li class="list-group-item">
            <i class="fas fa-layer-group"></i>
            <a href="/services">Сервисы</a>
        </li>
        <li class="list-group-item">
            <i class="fas fa-money-check-alt"></i>
            <a href="/orders">Заказы</a>
        </li>
        <li class="list-group-item">
            <i class="fas fa-blog" style="padding-left: 5px;"></i>
            <a href="/blogs/1/topics">Правила</a>
        </li>
        <li class="list-group-item">
            <i class="fa fa-question-circle"></i>
            <a href="/help/admin">
                Тех. поддержка
                @if (Help::hasUnread())
                    <span class="badge badge-danger">{{ Help::getUnread()->count() }}</span>
                @endif
            </a>
        </li>
    </ul>
</div>
