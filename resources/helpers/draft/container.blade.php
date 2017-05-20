@permission('drafts-list')
    <div id="tab-drafts" class="tab">
        <div class="loading loading-drafts">
            <img src="{{ asset('/build/assets/img/admin/loading.gif') }}" />
        </div>
        <table class="drafts-table" data-draftable-id="{{ $model->id }}" data-draftable-type="{{ get_class($model) }}" cellspacing="0" cellpadding="0" border="0"></table>
    </div>
@endpermission