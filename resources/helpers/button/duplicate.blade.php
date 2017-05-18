{!! form()->open(['url' => $url, 'method' => 'POST', 'class' => 'left']) !!}
{!! form()->button('<i class="fa fa-files-o"></i>&nbsp; Duplicate', ['type' => 'submit', 'class' => 'btn-duplicate btn dark-blue', 'onclick' => 'return confirm("Are you sure you want to duplicate this record?")']) !!}
{!! form()->close() !!}