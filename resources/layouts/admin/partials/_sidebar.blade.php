<nav>
    <span>{{ strtoupper(setting()->value('company-name') ?: config('app.name')) }}</span>
    <div class="scroll" tabindex="-1">
        @include('layouts::admin.partials._menu')
    </div>
</nav>