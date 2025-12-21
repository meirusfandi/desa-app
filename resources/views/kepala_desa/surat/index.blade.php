@extends('layouts.admin')

@section('title', 'Penandatanganan Surat')

@section('content')
@php
    $statusOptions = [
        'approved_secretary' => 'Menunggu TTD',
        'signed' => 'Selesai',
        'rejected' => 'Dikembalikan',
        'all' => 'Semua Status',
    ];
    $badgeMap = [
        'approved_secretary' => ['label' => 'Menunggu', 'class' => 'bg-warning'],
        'signed' => ['label' => 'Selesai', 'class' => 'bg-success'],
        'rejected' => ['label' => 'Ditolak', 'class' => 'bg-danger'],
    ];
    $queryParams = request()->except('page');
@endphp

<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Penandatanganan Surat</h3>
                <p class="text-subtitle text-muted">Tinjau detail surat dan unggah dokumen bertanda tangan.</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Penandatanganan</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <div class="btn-group" role="group">
                            @foreach($statusOptions as $value => $label)
                                <a href="{{ route('kepala.surat.index', array_merge($queryParams, ['status' => $value])) }}"
                                    class="btn btn-{{ $currentStatus === $value ? 'primary' : 'light' }}">
                                    {{ $label }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                    <div class="col-lg-4 mt-3 mt-lg-0">
                        <form action="" method="GET">
                            <div class="input-group">
                                <input type="hidden" name="status" value="{{ $currentStatus }}">
                                <input type="text" class="form-control" placeholder="Cari nama pemohon..." name="q" value="{{ request('q') }}">
                                <button class="btn btn-primary" type="submit">Cari</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="card-body">
                @if(!$signatureReady)
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle"></i> Unggah TTD Kepala Desa agar dapat menandatangani surat.
                        <a href="{{ route('kepala.signature.edit') }}" class="alert-link">Kelola TTD</a>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Pemohon</th>
                                <th>Jenis Surat</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($surats as $surat)
                                <tr>
                                    <td>{{ ($surats->firstItem() ?? 0) + $loop->index }}</td>
                                    <td>{{ $surat->created_at->format('d M Y H:i') }}</td>
                                    <td>{{ $surat->user->name ?? 'User Terhapus' }}</td>
                                    <td>{{ $surat->suratType->name ?? 'Jenis Terhapus' }}</td>
                                    <td>
                                        @php($badge = $badgeMap[$surat->status] ?? ['label' => ucfirst($surat->status), 'class' => 'bg-secondary'])
                                        <span class="badge {{ $badge['class'] }}">{{ $badge['label'] }}</span>
                                    </td>
                                    <td>
                                        <a href="{{ route('kepala.surat.show', $surat) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">Belum ada data untuk filter ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $surats->links() }}
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
