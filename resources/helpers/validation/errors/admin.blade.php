@if($errors->any())
    <div class="validation">
        @foreach($errors->getMessages() as $field => $messages)
            @foreach($messages as $message)
                <p class="error">{!! $message !!}</p>
            @endforeach
        @endforeach
    </div>
    <br /><br /><br />
@endif