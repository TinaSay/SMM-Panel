<template>
    <div>
        <!--<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarToggleExternalContent"
                aria-controls="navbarToggleExternalContent" aria-expanded="true" aria-label="Toggle navigation">
            <span class="list-group">Adminka</span>
        </button>-->
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
            </ul>
        </div>
    </div>

</template>

<script>
    export default {
        name: "admin-sidebar"
    }
</script>

<style scoped>

</style>
