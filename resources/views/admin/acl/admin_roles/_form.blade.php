{!! validation('admin')->errors() !!}

<div id="tab-1" class="tab">
    {!! form_admin()->text('name') !!}
    <br /><br /><br /><br />
    <table id="permissions" cellspacing="0" cellpadding="0" border="0">
        <thead>
            <tr>
                <td>Permissions</td>
                @for($i = 1; $i <= 4; $i++)
                    <td>
                        {!! form()->checkbox('dummy') !!}&nbsp; All
                    </td>
                @endfor
            </tr>
        </thead>
        <tbody>
            @foreach($permissions as $group => $permissions)
                <tr>
                    <td>{{ $group }}</td>
                    @foreach($permissions as $permission)
                        <td>
                            {!! form()->checkbox('permissions[]', $permission->id) !!}&nbsp;
                            {{ $permission->label }}
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

@section('bottom_scripts')
    {!! JsValidator::formRequest(App\Http\Requests\AdminRoleRequest::class, '.form') !!}
@append