<div class="notifications-container">
    <span class="notifications-count {{ $count == 0 ? 'hidden' : '' }}">
        {{ $count }}
    </span>
    <a href="#" class="notifications-indicator">
        <i class="fa fa-bell"></i>
    </a>
    <ul class="notifications">
        @if($count > 0)
            @foreach($notifications as $notification)
                <li>
                    <a href="{{ route('admin.notifications.action', $notification->id) }}" target="_blank" class="notification">
                        {{ $notification->data['subject'] }}
                    </a>
                </li>
            @endforeach
            <li>
                <a href="{{ route('admin.notifications.index') }}" class="btn blue full centered small no-margin no-float">View All</a>
            </li>
        @endif
    </ul>
</div>