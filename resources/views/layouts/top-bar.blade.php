<div id="nav">
    <i class="fas fa-bars float-left menu-btn d-lg-none open-menu"></i>
    <ul class="nav justify-content-end">
        <li class="nav-item nav-link">
          <span class="badge badge-success badge-circle">
              <i class="fas fa-star"></i>
              <span id="user_balance"></span>
          </span>

        </li>
        <li class="nav-item nav-link d-none d-md-block dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                <span>ID: {{ Auth::id() }}</span>
            </a>
            <ul class="dropdown-menu">
                <li><i class="fas fa-money-bill"></i><a href="/deposit">Мой баланс</a></li>
                <li><i class="far fa-user"></i><a href="/info/edit">Мои данные</a></li>
                <li><i class="fas fa-cog"></i><a href="/profile">Мои настройки</a></li>
            </ul>
        </li>

        <li class="nav-item nav-link">
            <a href="/logout"><i class="fa fa-sign-out-alt yellow-icons"></i></a>
        </li>
    </ul>
</div>
@push('scripts')
    <script src="{{asset('js/functions.js')}}"></script>
    <script>
        $(document).ready(function () {
            getBalance();


        });
    </script>
@endpush
