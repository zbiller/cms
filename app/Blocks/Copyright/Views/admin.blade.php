{!! uploader()->field('metadata[footer_logo]')->label('Logo')->model($item)->types('image')->manager() !!}
{!! form_admin()->text('metadata[copyright]', 'Copyright') !!}