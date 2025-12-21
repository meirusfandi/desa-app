@extends('layouts.admin')

@section('title', 'Dashboard Sekretaris')

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Dashboard Sekretaris</h3>
                <p class="text-subtitle text-muted">Pantau status surat dan lanjutkan proses persetujuan dengan cepat.</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Sekretaris</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-md-4">
                <div class="card card-statistic-2">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted">Permohonan Masuk</h6>
                            <h3 class="mb-0">{{ $totalPending }}</h3>
                        </div>
                        <div class="icon flex-shrink-0 text-warning">
                            <i class="bi bi-envelope-open fs-2"></i>
                        </div>
                    </div>
                    <div class="card-footer text-muted">Menunggu persetujuan awal</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-statistic-2">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted">Proses TTD</h6>
                            <h3 class="mb-0">{{ $totalProcess }}</h3>
                        </div>
                        <div class="icon flex-shrink-0 text-info">
                            <i class="bi bi-pen fs-2"></i>
                        </div>
                    </div>
                    <div class="card-footer text-muted">Menunggu tanda tangan Kepala Desa</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-statistic-2">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted">Selesai</h6>
                            <h3 class="mb-0">{{ $totalCompleted }}</h3>
                        </div>
                        <div class="icon flex-shrink-0 text-success">
                            <i class="bi bi-check-circle fs-2"></i>
                        </div>
                    </div>
                    <div class="card-footer text-muted">Telah dikirim ke warga</div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="card-title">Pengajuan Terbaru</h4>
                    <p class="text-muted mb-0">Ringkasan 5 surat terakhir dari warga.</p>
                </div>
                <a href="{{ route('sekretaris.approval.index') }}" class="btn btn-primary">
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
                                                'submitted' => ['label' => 'Menunggu', 'class' => 'bg-warning'],
                                                'approved_secretary' => ['label' => 'Proses TTD', 'class' => 'bg-info'],
                                                'signed' => ['label' => 'Selesai', 'class' => 'bg-success'],
                                                'rejected' => ['label' => 'Ditolak', 'class' => 'bg-danger'],
                                            ];
                                            $badge = $badgeMap[$surat->status] ?? ['label' => ucfirst($surat->status), 'class' => 'bg-secondary'];
                                        @endphp
                                        <span class="badge {{ $badge['class'] }}">{{ $badge['label'] }}</span>
                                    </td>
                                    <td>
                                        <a href="{{ route('sekretaris.approval.show', $surat) }}" class="btn btn-sm btn-outline-primary">
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
