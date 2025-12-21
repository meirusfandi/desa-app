@extends('layouts.admin')

@section('title', 'Persetujuan Surat')

@section('content')
@php
    $statusOptions = [
        'all' => 'Semua Pengajuan',
        'submitted' => 'Permohonan Masuk',
        'approved_secretary' => 'Proses TTD',
        'signed' => 'Selesai',
        'rejected' => 'Ditolak',
    ];
    $badgeMap = [
        'submitted' => ['label' => 'Menunggu', 'class' => 'bg-warning'],
        'approved_secretary' => ['label' => 'Proses TTD', 'class' => 'bg-info'],
        'signed' => ['label' => 'Selesai', 'class' => 'bg-success'],
        'rejected' => ['label' => 'Ditolak', 'class' => 'bg-danger'],
    ];
    $queryParams = request()->except('page');
@endphp

<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Persetujuan Surat</h3>
                <p class="text-subtitle text-muted">Sekretaris dapat meninjau dan memproses pengajuan surat warga.</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Persetujuan Surat</li>
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
                                <a href="{{ route('sekretaris.approval.index', array_merge($queryParams, ['status' => $value])) }}"
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
                                    <td>{{ ($surats->firstItem() ?? 0) + $loop->index + 1 }}</td>
                                    <td>{{ $surat->created_at->format('d M Y H:i') }}</td>
                                    <td>{{ $surat->user->name ?? 'User Terhapus' }}</td>
                                    <td>{{ $surat->suratType->name ?? 'Jenis Terhapus' }}</td>
                                    <td>
                                        @php($badge = $badgeMap[$surat->status] ?? ['label' => ucfirst($surat->status), 'class' => 'bg-secondary'])
                                        <span class="badge {{ $badge['class'] }}">{{ $badge['label'] }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('sekretaris.approval.show', $surat) }}" class="btn btn-sm btn-outline-info">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            @if($surat->status === 'submitted')
                                                <form action="{{ route('sekretaris.approval.approve', $surat) }}" method="POST" class="d-inline" onsubmit="return confirm('Setujui surat ini?')">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-success">
                                                        <i class="bi bi-check-circle"></i>
                                                    </button>
                                                </form>
                                                <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $surat->id }}">
                                                    <i class="bi bi-x-circle"></i>
                                                </button>

                                                <div class="modal fade" id="rejectModal{{ $surat->id }}" tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <form action="{{ route('sekretaris.approval.reject', $surat) }}" method="POST">
                                                                @csrf
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title">Tolak Pengajuan</h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <div class="form-group">
                                                                        <label>Alasan Penolakan</label>
                                                                        <textarea name="notes" class="form-control" rows="3" required placeholder="Masukkan alasan penolakan..."></textarea>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                                    <button type="submit" class="btn btn-danger">Tolak</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
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
