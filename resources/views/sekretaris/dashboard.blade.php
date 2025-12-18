@extends('layouts.app')

@section('content')
<h1>Approval Surat Masuk</h1>

@foreach($surats as $surat)
<div>
    Surat #{{ $surat->id }}
    <form method="POST" action="/sekretaris/approve/{{ $surat->id }}">
        @csrf
        <button>Approve</button>
    </form>
</div>
@endforeach
@endsection
