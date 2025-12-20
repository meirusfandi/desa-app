@extends('layouts.warga')

@section('title', 'Riwayat Pengajuan Surat')

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Riwayat Pengajuan Surat</h3>
                <p class="text-subtitle text-muted">Daftar surat yang telah Anda ajukan.</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('warga.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Surat</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">Data Pengajuan</h4>
                <a href="{{ route('warga.surat.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Buat Pengajuan
                </a>
            </div>
            <div class="card-body">
                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif
                @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-striped" id="table1">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Jenis Surat</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($surats as $surat)
                            <tr>
                                <td>{{ $loop->iteration + $surats->firstItem() - 1 }}</td>
                                <td>{{ $surat->created_at->format('d M Y') }}</td>
                                <td>{{ $surat->suratType->name }}</td>
                                <td>
                                    @if($surat->status == 'submitted')
                                        <span class="badge bg-warning">Menunggu</span>
                                    @elseif($surat->status == 'approved_secretary')
                                        <span class="badge bg-info">Diproses</span>
                                    @elseif($surat->status == 'signed')
                                        <span class="badge bg-success">Selesai</span>
                                    @elseif($surat->status == 'rejected')
                                        <span class="badge bg-danger">Ditolak</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $surat->status }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($surat->status == 'submitted')
                                        <a href="{{ route('warga.surat.edit', $surat->id) }}" class="btn btn-sm btn-warning mb-1" title="Edit">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <form action="{{ route('warga.surat.destroy', $surat->id) }}" method="POST" onsubmit="return confirm('Yakin ingin membatalkan pengajuan ini?');" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger mb-1" title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    @else
                                        <span class="badge bg-light-secondary text-secondary">Locked</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">Belum ada riwayat pengajuan.</td>
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
