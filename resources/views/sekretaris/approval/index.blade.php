@extends('layouts.app')

@section('content')
<h1 class="text-xl font-bold mb-4">Surat Masuk</h1>

@if(session('success'))
<div class="bg-green-100 p-3 mb-4">{{ session('success') }}</div>
@endif

<table border="1" cellpadding="6">
<tr>
    <th>ID</th>
    <th>Warga</th>
    <th>Jenis Surat</th>
    <th>Aksi</th>
</tr>

@foreach($surats as $surat)
<tr>
    <td>#{{ $surat->id }}</td>
    <td>{{ $surat->user->name }}</td>
    <td>{{ $surat->suratType->name }}</td>
    <td>
        <a href="{{ route('sekretaris.approval.show',$surat) }}">
            Detail
        </a>
    </td>
</tr>
@endforeach
</table>
@endsection
