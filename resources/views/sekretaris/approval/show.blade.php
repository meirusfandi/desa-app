@extends('layouts.app')

@section('content')
<h1 class="text-xl font-bold mb-4">
Detail Surat #{{ $surat->id }}
</h1>

<p><b>Nama Warga:</b> {{ $surat->user->name }}</p>
<p><b>Jenis Surat:</b> {{ $surat->suratType->name }}</p>

<h3 class="mt-4 font-bold">Lampiran</h3>
<ul>
@foreach($surat->files as $file)
    <li>
        <a href="{{ asset('storage/'.$file->file_path) }}" target="_blank">
            {{ $file->file_type }}
        </a>
    </li>
@endforeach
</ul>

<hr class="my-4">

<form method="POST" action="{{ route('sekretaris.approval.approve',$surat) }}">
    @csrf
    <textarea name="notes" placeholder="Catatan (opsional)"></textarea><br>
    <button class="bg-green-600 text-white px-4 py-2">
        Approve
    </button>
</form>

<br>

<form method="POST" action="{{ route('sekretaris.approval.reject',$surat) }}">
    @csrf
    <textarea name="notes" placeholder="Alasan penolakan" required></textarea><br>
    <button class="bg-red-600 text-white px-4 py-2">
        Reject
    </button>
</form>
@endsection
