<div class="additional-sidebar">
    <span class="additional-sidebar-title">Выберите социальную сеть</span>
    <ul class="additional-sidebar-select">
        @foreach($socials as $social)
            <li>
                <a class="additional-sidebar-select-button">
                    <img src="/uploads/icons/{{$social->icon}}" alt>
                    {{$social->name}}
                </a>
                @if(count($social->children) > 0)
                    <ul class="additional-sidebar-submenu">
                        @foreach($social->children as $children)
                            <li>
                                <a href="{{route('catalog', $children->id)}}">
                                    <img src="/uploads/icons/{{$children->icon}}">
                                    {{$children->name}}
                                </a>
                            </li>
                        @endforeach
                        <button class="hide">Скрыть</button>
                    </ul>
                @endif
            </li>
        @endforeach
    </ul>
</div>
