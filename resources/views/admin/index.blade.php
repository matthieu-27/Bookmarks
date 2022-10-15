@extends('layouts.app')

@foreach ($users as $user)
{{ $user->name }}
@if ($user->is_admin)
Admin
@else
User
@endif
<form style="display:inline" action="{{ route("user.destroy", $user->id) }}" method="post">
    @csrf
    @method('delete')
    <input style="display:inline" type="submit" value="Delete!">
</form>
<br>
@endforeach
