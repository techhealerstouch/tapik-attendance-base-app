@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'Laravel')
<img src="https://laravel.com/img/notification-logo.png" class="logo" alt="Laravel Logo">
@else
<img src="https://my.tapik.ph/assets/linkstack/images/avatar_1715649856.png" alt="Tapik Logo" style="max-width: 150px; max-height: 150px;" />
@endif
</a>
</td>
</tr>
