@extends('layouts.admin')
@section('title',$title)

@section('content')
<h1 class="text-xl font-bold mb-4">{{ $title }}</h1>

<table class="w-full bg-white rounded shadow">
    <thead>
        <tr class="border-b">
            <th class="p-2">ID</th>
            <th>Warga</th>
            <th>Jenis Surat</th>
            <th>Status</th>
            <th>Tanggal</th>
        </tr>
    </thead>
    <tbody>
        @foreach($surats as $surat)
        <tr class="border-b">
            <td class="p-2">{{ $surat->id }}</td>
            <td>{{ $surat->user->name }}</td>
            <td>{{ $surat->suratType->name }}</td>
            <td>{{ $surat->status }}</td>
            <td>{{ $surat->created_at->format('d-m-Y') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
