@extends('layouts.app')

@section('content')
<h1 class="text-xl font-bold">Dashboard Warga</h1>

<p>Status surat Anda:</p>

<table>
@foreach($surats ?? [] as $surat)
<tr>
    <td>{{ $surat->id }}</td>
    <td>{{ $surat->status }}</td>
</tr>
@endforeach
</table>
@endsection
