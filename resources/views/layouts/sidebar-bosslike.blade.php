<ul class="list-group advertiser @if(Session::get('usertype') == 'advertiser' or Session::get('usertype') == null)show @endif dnone">
    <li class="list-group-item">
        <i class="fas fa-plus"></i>
        <a href="/task/new">Добавить задание</a>
    </li>
    <li class="list-group-item">
        <i class="fas fa-tasks"></i>
        <a href="/tasks/my">Мои задания</a>
    </li>
</ul>
<ul class="list-group blogger @if(Session::get('usertype') == 'blogger')show @endif dnone">
    <li class="list-group-item">
        <i class="fas fa-tasks"></i>
        <a href="{{ route('tasks.all') }}">Лента заданий</a>
    </li>
    <li class="list-group-item">
        <i class="far fa-money-bill-alt"></i>
        <a href="/profile/history">Выполненные задания</a>
    </li>
</ul>
