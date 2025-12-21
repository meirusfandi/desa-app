@extends('layouts.admin')

@section('title', $title)

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>{{ $title }}</h3>
                <p class="text-subtitle text-muted">Daftar surat permohonan warga.</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ $title }}</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-6">
                        <h4 class="card-title">Data {{ $title }}</h4>
                    </div>
                    <div class="col-md-6">
                        <form action="" method="GET">
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" placeholder="Cari nama pemohon..." aria-label="Cari" name="q" value="{{ request('q') }}">
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
                    <table class="table table-striped" id="table1">
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
                                <td>{{ $loop->iteration + $surats->firstItem() - 1 }}</td>
                                <td>{{ $surat->created_at->format('d M Y H:i') }}</td>
                                <td>{{ $surat->user->name ?? 'User Terhapus' }}</td>
                                <td>{{ $surat->suratType->name ?? 'Jenis Terhapus' }}</td>
                                <td>
                                    @if($surat->status == 'submitted')
                                        <span class="badge bg-warning">Menunggu</span>
                                    @elseif($surat->status == 'approved_secretary')
                                        <span class="badge bg-info">Proses TTD</span>
                                    @elseif($surat->status == 'signed')
                                        <span class="badge bg-success">Selesai</span>
                                    @elseif($surat->status == 'rejected')
                                        <span class="badge bg-danger">Ditolak</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $surat->status }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('admin.surat.show', $surat->id) }}" class="btn btn-sm btn-outline-info" title="Detail">
                                            <i class="bi bi-eye"></i>
                                        </a>

                                        @if($surat->status == 'submitted')
                                            <form action="{{ route('admin.surat.approve', $surat->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Setujui surat ini?')">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-success" title="Setujui">
                                                    <i class="bi bi-check-circle"></i>
                                                </button>
                                            </form>
                                            <button type="button" class="btn btn-sm btn-outline-danger" title="Tolak" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $surat->id }}">
                                                <i class="bi bi-x-circle"></i>
                                            </button>

                                            <!-- Reject Modal -->
                                            <div class="modal fade" id="rejectModal{{ $surat->id }}" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <form action="{{ route('admin.surat.reject', $surat->id) }}" method="POST">
                                                            @csrf
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Tolak Pengajuan Surat</h5>
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
                                        @elseif($surat->status == 'approved_secretary')
                                            <a href="{{ route('admin.surat.show', $surat->id) }}" class="btn btn-sm btn-outline-primary" title="Upload TTD">
                                                <i class="bi bi-upload"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">Belum ada data surat.</td>
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
