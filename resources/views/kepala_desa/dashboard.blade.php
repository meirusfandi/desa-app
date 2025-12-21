@extends('layouts.admin')

@section('title', 'Dashboard Kepala Desa')

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Dashboard Kepala Desa</h3>
                <p class="text-subtitle text-muted">Monitor permohonan yang menunggu tanda tangan dan progres penyelesaian.</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Kepala Desa</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        @if(!$signatureReady)
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-circle"></i> Belum ada file TTD Kepala Desa. Unggah terlebih dahulu agar dapat menandatangani surat secara digital.
            <a href="{{ route('kepala.signature.edit') }}" class="alert-link">Kelola TTD</a>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif
        <div class="row">
            <div class="col-md-4">
                <div class="card card-statistic-2">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted">Menunggu TTD</h6>
                            <h3 class="mb-0">{{ $totalQueue }}</h3>
                        </div>
                        <div class="icon flex-shrink-0 text-warning">
                            <i class="bi bi-clipboard-check fs-2"></i>
                        </div>
                    </div>
                    <div class="card-footer text-muted">Butuh tindakan Anda</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-statistic-2">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted">Sudah Ditandatangani</h6>
                            <h3 class="mb-0">{{ $totalSigned }}</h3>
                        </div>
                        <div class="icon flex-shrink-0 text-success">
                            <i class="bi bi-check-circle fs-2"></i>
                        </div>
                    </div>
                    <div class="card-footer text-muted">Tersedia untuk warga</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-statistic-2">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted">Dikembalikan</h6>
                            <h3 class="mb-0">{{ $totalReturned }}</h3>
                        </div>
                        <div class="icon flex-shrink-0 text-danger">
                            <i class="bi bi-arrow-counterclockwise fs-2"></i>
                        </div>
                    </div>
                    <div class="card-footer text-muted">Membutuhkan perbaikan</div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="card-title">Antrian Terbaru</h4>
                    <p class="text-muted mb-0">5 pengajuan terakhir yang menunggu tanda tangan Anda.</p>
                </div>
                <a href="{{ route('kepala.surat.index') }}" class="btn btn-primary">
                    Kelola Semua Surat
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Tanggal</th>
                                <th>Pemohon</th>
                                <th>Jenis Surat</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentSurats as $surat)
                                <tr>
                                    <td>{{ $surat->id }}</td>
                                    <td>{{ $surat->created_at->format('d M Y H:i') }}</td>
                                    <td>{{ $surat->user->name ?? 'User Terhapus' }}</td>
                                    <td>{{ $surat->suratType->name ?? 'Jenis Terhapus' }}</td>
                                    <td>
                                        @php
                                            $badgeMap = [
                                                'approved_secretary' => ['label' => 'Menunggu', 'class' => 'bg-warning'],
                                                'signed' => ['label' => 'Selesai', 'class' => 'bg-success'],
                                                'rejected' => ['label' => 'Ditolak', 'class' => 'bg-danger'],
                                            ];
                                            $badge = $badgeMap[$surat->status] ?? ['label' => ucfirst($surat->status), 'class' => 'bg-secondary'];
                                        @endphp
                                        <span class="badge {{ $badge['class'] }}">{{ $badge['label'] }}</span>
                                    </td>
                                    <td>
                                        <a href="{{ route('kepala.surat.show', $surat) }}" class="btn btn-sm btn-outline-primary">
                                            Detail
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">Belum ada data untuk ditampilkan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
