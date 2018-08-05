<html>
<head>
	<meta charset="UTF-8">
	<title>Raar Url</title>
</head>
<body>
	<form action="{{ route('url.post') }}" method="POST">
		{!! csrf_field() !!}
		<input type="text" name="original" placeholder="Original Url">
		<br>
		@if($errors->has('original'))
			<small>Please insert a valid url</small>
		@endif
		<button type="submit">Shorten</button>
	</form>
	
	<strong>TOTAL ENTRIES: {{ count($urls) }}</strong>
	<table>
		<tr>
			<th>Hash</th>
			<th>Original Url</th>
			<th>Expire Date</th>
			<th>Created Date</th>
		</tr>
		@foreach($urls as $url)
			<tr>
				<td>
					<a href="{{ route('url.redirect', $url->hash) }}" target="_blank">
						{{ $url->hash }}
					</a>
				</td>
				<td>{{ $url->original }}</td>
				<td>{{ $url->expire_at }}</td>
				<td>{{ $url->created_at }}</td>
			</tr>
		@endforeach
	</table>
</body>
</html>