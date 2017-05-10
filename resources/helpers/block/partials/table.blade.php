<thead>
    <tr class="even nodrag nodrop">
        <td>Name</td>
        <td>Type</td>
        <td class="actions-blocks">Actions</td>
    </tr>
</thead>
<tbody>
    @php
        $blocksInLocation = $model->getBlocksInLocation($location);
        $shouldInheritBlocks = (bool)$blocksInLocation->count() == 0;
        $inheritedBlocks = $shouldInheritBlocks ? $model->getInheritedBlocks($location) : null;
    @endphp
    @if($blocksInLocation->count())
        @foreach($blocksInLocation as $block)
            <tr id="{{ $block->pivot->id }}" data-block-id="{{ $block->id }}" data-pivot-id="{{ $block->pivot->id }}" class="{!! $disabled === true ? 'nodrag nodrop' : '' !!}">
                <td>{{ $block->name ?: 'N/A' }}</td>
                <td>{{ $block->type ?: 'N/A' }}</td>
                <td>
                    {!! button()->action('View', route('admin.blocks.edit', $block->id), 'fa-eye', 'yellow no-margin-left no-margin-top no-margin-bottom', ['target' => '_blank']) !!}
                    {!! button()->action('Remove', route('admin.blocks.unassign'), 'fa-times', 'block-unassign red no-margin-top no-margin-bottom no-margin-right' . ($disabled === true ? ' disabled' : '')) !!}
                </td>
            </tr>
        @endforeach
    @else
        <tr class="no-blocks-assigned nodrag nodrop">
            <td colspan="10">
                @if($inheritedBlocks && $inheritedBlocks->count() > 0)
                    <div class="block-inheritance">
                        <span>This record inherits the following blocks: </span>
                        <em>{{ $inheritedBlocks->implode('name', ', ') }}.</em>
                        <span>Assigning blocks here, will overwrite the inherited blocks.</span>
                    </div>
                @else
                    There are no blocks assigned to this location
                @endif
            </td>
        </tr>
    @endif
</tbody>