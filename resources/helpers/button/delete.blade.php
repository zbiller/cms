{!! form()->open(['url' => $url, 'method' => 'DELETE']) !!}
{!! form()->button('<i class="fa fa-times"></i>&nbsp; Delete', ['type' => 'submit', 'class' => 'btn red no-margin-top no-margin-bottom no-margin-right', 'onclick' => 'return confirm("Are you sure you want to delete this record?")']) !!}
{!! form()->close() !!}
