@extends('layouts.admin')

@section('title', $title)

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Detail Pengajuan Surat</h3>
                <p class="text-subtitle text-muted">Informasi lengkap permohonan surat warga.</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.surat.masuk') }}">Surat Menyurat</a></li>
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
                        <div>
                            @if($surat->status == 'submitted')
                                <span class="badge bg-warning">Menunggu</span>
                            @elseif($surat->status == 'approved_secretary')
                                <span class="badge bg-info">Proses TTD</span>
                            @elseif($surat->status == 'signed')
                                <span class="badge bg-success">Selesai</span>
                            @elseif($surat->status == 'rejected')
                                <span class="badge bg-danger">Ditolak</span>
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h6>Nama Pemohon:</h6>
                                <p>{{ $surat->user->name }}</p>
                            </div>
                            <div class="col-md-6">
                                <h6>Jenis Surat:</h6>
                                <p>{{ $surat->suratType->name }}</p>
                            </div>
                        </div>

                        <hr>
                        <h5 class="mb-3">Data Form Dinamis</h5>
                        <div class="row">
                            @if($surat->data)
                                @foreach($surat->data as $key => $value)
                                    <div class="col-md-6 mb-3">
                                        <h6 class="text-capitalize">{{ str_replace('_', ' ', $key) }}:</h6>
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
                        <h4 class="card-title">Aksi Pengolahan</h4>
                    </div>
                    <div class="card-body">
                        @if($surat->status == 'submitted')
                            <form action="{{ route('admin.surat.approve', $surat->id) }}" method="POST" class="mb-3">
                                @csrf
                                <button type="submit" class="btn btn-success w-100" onclick="return confirm('Setujui surat ini?')">
                                    <i class="bi bi-check-circle"></i> Setujui & Teruskan TTD
                                </button>
                            </form>
                            <button type="button" class="btn btn-danger w-100" data-bs-toggle="modal" data-bs-target="#rejectModalShow">
                                <i class="bi bi-x-circle"></i> Tolak Pengajuan
                            </button>
                        @elseif($surat->status == 'approved_secretary')
                            <h6 class="mb-3">Upload File Selesai (TTD)</h6>
                            <form action="{{ route('admin.surat.upload-signed', $surat->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group mb-3">
                                    <input type="file" name="signed_file" class="form-control" accept=".pdf" required>
                                    <small class="text-muted">Pilih file PDF yang sudah ditandatangani.</small>
                                </div>
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-upload"></i> Selesai & Kirim ke Warga
                                </button>
                            </form>
                        @elseif($surat->status == 'signed')
                            <div class="alert alert-success">
                                <i class="bi bi-check-circle"></i> Surat ini telah selesai diproses.
                                <hr>
                                <a href="{{ asset('storage/' . $surat->signed_file) }}" target="_blank" class="btn btn-sm btn-success">Download File TTD</a>
                            </div>
                        @elseif($surat->status == 'rejected')
                            <div class="alert alert-danger">
                                <i class="bi bi-exclamation-triangle"></i> Pengajuan ditolak.
                                <hr>
                                <h6>Alasan:</h6>
                                <p>{{ $surat->notes }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModalShow" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.surat.reject', $surat->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tolak Pengajuan Surat</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Alasan Penolakan</label>
                        <textarea name="notes" class="form-control" rows="3" required placeholder="Alasan penolakan agar diketahui warga..."></textarea>
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
@endsection
