@istranslatable
    <div class="dropdown">
        <a href="#" class="dropdown-current">
            {{ $language->name }} &nbsp;<i class="fa fa-caret-down"></i>
        </a>
        @if($languages->count())
            <ul class="dropdown-choices">
                @foreach($languages as $language)
                    <li>
                        <a href="{{ route('admin.languages.change', $language->id) }}">
                            {{ $language->name }}
                        </a>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
@endistranslatable