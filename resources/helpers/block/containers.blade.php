@if($locations && is_array($locations) && !empty($locations))
    @foreach($locations as $location)
        <div id="tab-{{ $location }}-blocks" class="tab">
            <table class="blocks-table" cellspacing="0" cellpadding="0" border="0">
                @include('helpers::block.table')
            </table>
            <div class="block-assign-container" data-blockable-id="{{ $model->id }}" data-blockable-type="{{ get_class($model) }}" data-location="{{ $location }}">
                <div class="block-assign-select-container">
                    {!! form()->select('block', ['' => ''] + $model->getBlocksOfLocation($location)->pluck('name', 'id')->toArray(), null, ['class' => 'block-assign-select']) !!}
                </div>
                <div class="block-assign-btn-container">
                    <a href="#" class="block-assign btn blue no-margin right">
                        <i class="fa fa-plus"></i>&nbsp; Assign
                    </a>
                </div>
            </div>
        </div>
    @endforeach

    @section('bottom_scripts')
        <script type="text/javascript">
            var token = '{{ csrf_token() }}';

            var assignBlock = function (_this) {
                var assignContainer = _this.closest('.block-assign-container'),
                        assignSelect = assignContainer.find('.block-assign-select'),
                        blocksTab = $('#tab-' + assignContainer.data('location') + '-blocks'),
                        blocksTable = blocksTab.find('table.blocks-table');

                if (assignSelect.val()) {
                    $.ajax({
                        type : 'POST',
                        url: '{{ route('admin.blocks.assign') }}',
                        data: {
                            _token: token,
                            block_id: assignSelect.val(),
                            blockable_id: assignContainer.data('blockable-id'),
                            blockable_type: assignContainer.data('blockable-type'),
                            location: assignContainer.data('location')
                        },
                        beforeSend: function () {
                            blocksTab.css({opacity: 0.5});
                        },
                        complete: function () {
                            blocksTab.css({opacity: 1});
                        },
                        success : function(data) {
                            if (data.status == true) {
                                blocksTable.html(data.html);
                                orderBlocks();
                            } else {
                                init.FlashMessage('error', 'Could not assign the block! Please try again.');
                            }
                        },
                        error: function () {
                            init.FlashMessage('error', 'Something went wrong! Please try again.');
                        }
                    });
                }
            }, unassignBlock = function (_this) {
                var assignContainer = _this.closest('.blocks-table').next('.block-assign-container'),
                        currentRow = _this.closest('tr'),
                        blocksTab = $('#tab-' + assignContainer.data('location') + '-blocks'),
                        blocksTable = blocksTab.find('table.blocks-table');

                $.ajax({
                    type : 'POST',
                    url: '{{ route('admin.blocks.unassign') }}',
                    data: {
                        _token: token,
                        pivot_id: currentRow.attr('data-pivot-id'),
                        block_id: currentRow.attr('data-block-id'),
                        blockable_id: assignContainer.data('blockable-id'),
                        blockable_type: assignContainer.data('blockable-type'),
                        location: assignContainer.data('location')
                    },
                    beforeSend: function () {
                        blocksTab.css({opacity: 0.5});
                    },
                    complete: function () {
                        blocksTab.css({opacity: 1});
                    },
                    success : function(data) {
                        if (data.status == true) {
                            blocksTable.html(data.html);
                            orderBlocks();
                        } else {
                            init.FlashMessage('error', 'Could not unassign the block! Please try again.');
                        }
                    },
                    error: function () {
                        init.FlashMessage('error', 'Something went wrong! Please try again.');
                    }
                });
            }, orderBlocks = function () {
                $(".blocks-table").tableDnD({
                    onDrop: function(table, row){
                        var ord, item;

                        var current = row,
                                next = $(row).next()[0],
                                prev = $(row).prev()[0];

                        var rows = table.tBodies[0].rows,
                                items = {};

                        for (var i = 0; i < rows.length; i++) {
                            items[i + 1] = rows[i].id;
                        }

                        $.ajax({
                            type: 'POST',
                            url: '{{ route('admin.blocks.order') }}',
                            data: {
                                _token: token,
                                items: items
                            }
                        });
                    }
                });
            };

            $(function () {
                $(document).on('click', 'a.block-assign', function (e) {
                    e.preventDefault();

                    assignBlock($(this));
                });

                $(document).on('click', 'a.block-unassign', function (e) {
                    e.preventDefault();

                    unassignBlock($(this));
                });

                orderBlocks();
            });
        </script>
    @append
@endif