{!! form()->open(['url' => route('admin.uploads.store'), 'method' => 'POST', 'id' => 'uploads', 'class' => 'dropzone', 'files' => true]) !!}
    <div class="ddTitle" style="width: 358px;">
        Drag & drop files or click the area to upload
    </div>
{!! form()->close() !!}

@section('bottom_scripts')
    <script type="text/javascript">
        Dropzone.options.uploads = {
            success: function(file, response){
                if (response.status == true) {
                    return file.previewElement.classList.add("dz-success");
                } else {
                    var node, _i, _len, _ref, _results;
                    var message = response.message;

                    file.previewElement.classList.add("dz-error");

                    _ref = file.previewElement.querySelectorAll("[data-dz-errormessage]");
                    _results = [];

                    for (_i = 0, _len = _ref.length; _i < _len; _i++) {
                        node = _ref[_i];
                        _results.push(node.textContent = message);
                    }

                    return _results;
                }
            }
        };
    </script>
@append