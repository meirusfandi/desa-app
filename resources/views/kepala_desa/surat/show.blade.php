@extends('layouts.admin')

@section('title', 'Detail Penandatanganan Surat')

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Detail Pengajuan Surat</h3>
                <p class="text-subtitle text-muted">Periksa data permohonan sebelum menandatangani.</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('kepala.surat.index') }}">Penandatanganan</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Detail</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title">Informasi Surat</h4>
                        @php
                            $badgeMap = [
                                'approved_secretary' => ['label' => 'Menunggu', 'class' => 'bg-warning'],
                                'signed' => ['label' => 'Selesai', 'class' => 'bg-success'],
                                'rejected' => ['label' => 'Ditolak', 'class' => 'bg-danger'],
                            ];
                            $badge = $badgeMap[$surat->status] ?? ['label' => ucfirst($surat->status), 'class' => 'bg-secondary'];
                        @endphp
                        <span class="badge {{ $badge['class'] }}">{{ $badge['label'] }}</span>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h6>Pemohon</h6>
                                <p>{{ $surat->user->name ?? '-' }}</p>
                            </div>
                            <div class="col-md-6">
                                <h6>Jenis Surat</h6>
                                <p>{{ $surat->suratType->name ?? '-' }}</p>
                            </div>
                        </div>

                        <hr>
                        <h5 class="mb-3">Data Form Dinamis</h5>
                        <div class="row">
                            @if($surat->data)
                                @foreach($surat->data as $key => $value)
                                    <div class="col-md-6 mb-3">
                                        <h6 class="text-capitalize">{{ str_replace('_', ' ', $key) }}</h6>
                                        <p class="text-muted">{{ $value }}</p>
                                    </div>
                                @endforeach
                            @else
                                <div class="col-12">
                                    <p class="text-muted">Tidak ada data tambahan.</p>
                                </div>
                            @endif
                        </div>

                        <hr>
                        <h5 class="mb-3">Lampiran Dokumen</h5>
                        <div class="row">
                            @forelse($surat->files as $file)
                                <div class="col-md-4 mb-3">
                                    <div class="card border p-2 text-center">
                                        <i class="bi bi-file-earmark-text mb-2" style="font-size: 2rem;"></i>
                                        <small class="d-block mb-2 text-truncate">{{ basename($file->file_path) }}</small>
                                        <a href="{{ asset('storage/' . $file->file_path) }}" target="_blank" class="btn btn-sm btn-primary">Lihat</a>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12">
                                    <p class="text-muted">Tidak ada lampiran.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Aksi Kepala Desa</h4>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        @if($surat->status === 'approved_secretary')
                            @if(!$signatureReady)
                                <div class="alert alert-warning">
                                    <p class="mb-2"><i class="bi bi-exclamation-triangle"></i> Unggah file TTD Kepala Desa terlebih dahulu sebelum menandatangani surat.</p>
                                    <a href="{{ route('kepala.signature.edit') }}" class="btn btn-sm btn-warning">Kelola TTD</a>
                                </div>
                            @else
                                <form action="{{ route('kepala.surat.sign', $surat) }}" method="POST" class="mb-3">
                                    @csrf
                                    <div class="form-group mb-3">
                                        <label>Catatan (opsional)</label>
                                        <textarea name="notes" class="form-control" rows="3" placeholder="Catatan untuk warga (opsional)..."></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-success w-100" onclick="return confirm('Tandatangani surat ini?')">
                                        <i class="bi bi-pen"></i> Tandatangani & Kirim
                                    </button>
                                </form>

                                <button type="button" class="btn btn-outline-danger w-100" data-bs-toggle="modal" data-bs-target="#rejectModal">
                                    <i class="bi bi-arrow-counterclockwise"></i> Kembalikan ke Sekretaris
                                </button>
                            @endif
                        @elseif($surat->status === 'signed')
                            <div class="alert alert-success">
                                <p class="mb-2"><i class="bi bi-check-circle"></i> Surat sudah selesai dan dapat diakses warga.</p>
                                @if($surat->signed_file)
                                    <a href="{{ asset('storage/' . $surat->signed_file) }}" target="_blank" class="btn btn-sm btn-success">Download File</a>
                                @endif
                            </div>
                        @elseif($surat->status === 'rejected')
                            <div class="alert alert-warning">
                                <p class="mb-2"><i class="bi bi-exclamation-triangle"></i> Surat dikembalikan.</p>
                                <h6 class="mb-1">Catatan:</h6>
                                <p class="mb-0">{{ $surat->notes }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

@if($surat->status === 'approved_secretary' && $signatureReady)
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('kepala.surat.reject', $surat) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Kembalikan Pengajuan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Catatan Untuk Sekretaris/Warga</label>
                        <textarea name="notes" class="form-control" rows="3" required placeholder="Sebutkan alasan pengembalian..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Kembalikan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection
