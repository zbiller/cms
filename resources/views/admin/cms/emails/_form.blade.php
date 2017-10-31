@if($item->exists)
    @if(isset($on_draft) || isset($on_limbo_draft) || isset($on_revision))
        {!! form_admin()->model($item, ['method' => isset($on_draft) || isset($on_revision) ? 'POST' : 'PUT','class' => 'form', 'files' => true]) !!}
    @else
        {!! form_admin()->model($item, ['url' => $url, 'method' => 'PUT', 'class' => 'form', 'files' => true]) !!}
    @endif
@else
    {!! form_admin()->open(['url' => $url, 'method' => 'POST', 'class' => 'form', 'files' => true]) !!}
@endif

{!! validation('admin')->errors() !!}

{!! form()->hidden('_class', \App\Models\Cms\Email::class) !!}
{!! form()->hidden('_request', \App\Http\Requests\Cms\EmailRequest::class) !!}
{!! form()->hidden('_id', $item->exists ? $item->id : null) !!}
{!! form()->hidden('_back', route('admin.emails.drafts')) !!}

<div id="tab-1" class="tab">
    {!! form_admin()->hidden('type', $item->exists ? $item->type : $type) !!}
    {!! form_admin()->text('name') !!}
    {!! auth()->user()->isDeveloper() ? form_admin()->text('identifier') : '' !!}
</div>
<div id="tab-2" class="tab">
    <div class="box yellow" style="margin-bottom: 20px;">
        <span>
            Please note that in order for the email to work properly, you need to include the defined variables into the "Message" section.<br />
            The variables can be found on the "Variables" tab and should be typed into the message editor with the following syntax --- [variable_name]<br />
            Although recommended, including all variables is not required.<br />
        </span>
    </div>

    {!! form_admin()->text('metadata[from_name]', 'From Name', $item && $item->exists ? $item->metadata('from_name') : null, ['placeholder' => 'Default is ' . $fromName]) !!}
    {!! form_admin()->text('metadata[from_email]', 'From Email', $item && $item->exists ? $item->metadata('from_email') : null, ['placeholder' => 'Default is ' . $fromEmail]) !!}
    {!! form_admin()->text('metadata[reply_to]', 'Reply To', $item && $item->exists ? $item->metadata('reply_to') : null, ['placeholder' => 'Default is ' . $fromEmail]) !!}
    {!! uploader()->field('metadata[attachment]')->label('Attachment')->model($item)->manager() !!}
    {!! form_admin()->text('metadata[subject]', 'Subject') !!}
    {!! form_admin()->editor('metadata[message]', 'Message') !!}
</div>
<div id="tab-3" class="tab">
    @php($variables = App\Models\Cms\Email::getVariables(isset($item) && $item->exists ? $item->type : $type))
    @foreach($variables as $variable => $attributes)
        <span class="title">{{ $attributes['name'] }}</span>
        <p><span style="font-family: 'Open Sans Bold', sans-serif;">Name</span>: {{ $variable }}</p>
        <p>{{ $attributes['label'] }}<br />{{ $attributes['description'] }}</p>
        <p>Use this variable inside the editor for the "Message" input on the "Manage Details" tab. Syntax --- [{{ $variable }}]</p>
    @endforeach
</div>

@if($item->exists && !isset($on_draft) && !isset($on_limbo_draft) && !isset($on_revision))
    {!! draft()->container($item) !!}
    {!! revision()->container($item) !!}
@endif

{!! form_admin()->close() !!}

@if(!isset($on_draft) && !isset($on_limbo_draft) && !isset($on_revision))
    @section('bottom_scripts')
        {!! JsValidator::formRequest(App\Http\Requests\Cms\BlockRequest::class, '.form') !!}
    @append
@endif