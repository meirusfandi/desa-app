@extends('layouts.app')

@section('content')
<h1 class="text-xl font-bold mb-4">Ajukan Surat</h1>

@if(session('success'))
<div class="bg-green-100 p-3 mb-4">{{ session('success') }}</div>
@endif

<form method="POST" action="/warga/surat" enctype="multipart/form-data">
    @csrf

    <div class="mb-3">
        <label>Jenis Surat</label>
        <select name="surat_type_id" required>
            <option value="">-- Pilih --</option>
            @foreach($suratTypes as $type)
                <option value="{{ $type->id }}">{{ $type->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="mb-3">
        <label>Upload Dokumen (KTP / KK)</label>
        <input type="file" name="files[]" multiple required>
    </div>

    <button class="bg-blue-600 text-white px-4 py-2">
        Kirim Permohonan
    </button>
</form>

<hr class="my-6">

<h2 class="font-bold">Riwayat Surat</h2>
<table class="mt-2">
@foreach($surats as $surat)
<tr>
    <td>#{{ $surat->id }}</td>
    <td>{{ $surat->suratType->name }}</td>
    <td>{{ $surat->status }}</td>
</tr>
@endforeach
</table>
@endsection
