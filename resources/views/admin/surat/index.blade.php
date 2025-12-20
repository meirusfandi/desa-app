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
                                    {{-- Placeholder for logic to view detail --}}
                                    <a href="#" class="btn btn-sm btn-info">Detail</a>
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
