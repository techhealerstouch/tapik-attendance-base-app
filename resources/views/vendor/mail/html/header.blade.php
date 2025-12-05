@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'Laravel')
<img src="https://laravel.com/img/notification-logo.png" class="logo" alt="Laravel Logo">
@else
<img src="{{ asset('assets/linkstack/images/'.findFile('avatar')) }}" alt="{{ config('app.name') }} Logo" style="max-width: 150px; max-height: 150px;" />
@endif
</a>
</td>
</tr>
