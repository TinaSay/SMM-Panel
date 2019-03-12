<div id="nav">
    <i class="fas fa-bars float-left menu-btn d-lg-none open-menu"></i>
    <ul class="nav justify-content-end">
        <li class="nav-item nav-link d-none d-md-block">
            <span>ID: {{ Auth::id() }}</span>
        </li>
        <li class="nav-item nav-link dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                {{--<span>{{ \App\User::getUserBalance() }} сум</span>--}}
            </a>
            <ul class="dropdown-menu">
                <li class="text-center">
                    <a href="/deposit">Пополнить</a>
                </li>
            </ul>
        </li>
        <li class="nav-item nav-link">
            <cart-dropdown></cart-dropdown>
        </li>
        <li class="nav-item nav-link">
            <a href="/logout"><i class="fa fa-sign-out-alt yellow-icons"></i></a>
        </li>
    </ul>
</div>
@push('scripts')
    <script>
        $(document).ready(function () {


        });
    </script>
@endpush
