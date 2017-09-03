@if($message)
    <div class="flash {!! $type !!}">
        <a><i class="fa fa-times"></i></a>
        <p>{!! $message !!}</p>
    </div>
@endif