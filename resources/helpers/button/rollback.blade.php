{!! form()->open(['url' => $url, 'method' => 'POST', 'class' => 'left']) !!}
{!! form()->button('<i class="fa fa-undo"></i>&nbsp; Rollback', ['type' => 'submit', 'class' => 'btn green', 'onclick' => 'return confirm("Are you sure you want to rollback the record to this revision?")']) !!}
{!! form()->close() !!}