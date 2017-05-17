@if($locations && is_array($locations) && !empty($locations))
    @foreach($locations as $location)
        <div id="tab-{{ $location }}-blocks" class="tab">
            <table class="blocks-table" cellspacing="0" cellpadding="0" border="0">
                @include('helpers::block.partials.table')
            </table>
            @if($disabled === false)
                <div class="block-assign-container" data-blockable-id="{{ $model->id }}" data-blockable-type="{{ get_class($model) }}" data-location="{{ $location }}">
                    <div class="block-assign-select-container">
                        {!! form()->select('block', ['' => ''] + $model->getBlocksOfLocation($location)->pluck('name', 'id')->toArray(), null, ['class' => 'block-assign-select']) !!}
                    </div>
                    <div class="block-assign-btn-container">
                        <a href="#" class="block-assign btn green no-margin right">
                            <i class="fa fa-plus"></i>&nbsp; Assign
                        </a>
                    </div>
                </div>
            @endif
        </div>
    @endforeach

    @include('helpers::block.partials.scripts')
@endif