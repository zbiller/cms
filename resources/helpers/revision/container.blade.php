<div id="tab-revisions" class="tab">
    <div class="loading loading-revisions">
        <img src="{{ asset('/build/assets/img/admin/loading.gif') }}" />
    </div>
    <table class="revisions-table"
       data-revisionable-id="{{ $model->id }}"
       data-revisionable-type="{{ get_class($model) }}"
       cellspacing="0"
       cellpadding="0"
       border="0"
    ></table>
</div>