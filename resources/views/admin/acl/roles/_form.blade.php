{!! validation('admin')->errors() !!}

<div id="tab-1" class="tab">
    {!! form_admin()->text('name') !!}
    <br /><br /><br /><br />
    <div class="permissions">
        @foreach($permissions as $name => $group)
            <div class="permission-group">
                <div class="permission-header">
                    <span>{{ $name }}</span>
                    {!! form()->checkbox('dummy') !!}&nbsp;
                </div>
                <div class="permission-content">
                    @foreach($group as $permission)
                        <div>
                            <span>{{ $permission->label }}</span>
                            {!! form()->checkbox('permissions[]', $permission->id) !!}&nbsp;
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</div>

@section('bottom_scripts')
    {!! JsValidator::formRequest(App\Http\Requests\RoleRequest::class, '.form') !!}

    <script type="text/javascript">
        $('input[type="checkbox"][name="dummy"]').click(function () {
            var dummy = $(this);
            var permissions = $(this).closest('.permission-header').next('.permission-content');

            permissions.children('div').each(function (index, selector) {
                $(selector).find('input[type="checkbox"]').prop('checked', dummy.prop('checked'));
            });
        });
    </script>
@append