@extends('layouts.app')

@section('content')
<h1>Surat Siap Ditandatangani</h1>

@foreach($surats as $surat)
<form method="POST" action="/kepala-desa/sign/{{ $surat->id }}">
    @csrf
    <button>Tanda Tangani</button>
</form>
@endforeach
@endsection
