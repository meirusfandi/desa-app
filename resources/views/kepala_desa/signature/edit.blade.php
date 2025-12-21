@extends('layouts.admin')

@section('title', 'Pengaturan TTD Kepala Desa')

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Pengaturan Tanda Tangan</h3>
                <p class="text-subtitle text-muted">Unggah file TTD digital yang akan ditempel pada setiap surat selesai.</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Pengaturan TTD</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Unggah File TTD</h4>
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

                <form action="{{ route('kepala.signature.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="signature">Pilih File TTD (PNG/JPG)</label>
                                <input type="file" name="signature" id="signature" class="form-control @error('signature') is-invalid @enderror" accept="image/png,image/jpeg" required>
                                <small class="text-muted">Gunakan latar belakang transparan agar hasil lebih rapi.</small>
                                @error('signature')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-upload"></i> Simpan TTD
                            </button>
                        </div>
                        <div class="col-md-6">
                            <label>Pratinjau Saat Ini</label>
                            <div class="border rounded p-3 text-center">
                                @if($signaturePath)
                                    <img src="{{ asset('storage/' . $signaturePath) }}" alt="TTD Kepala Desa" class="img-fluid" style="max-height: 200px; object-fit: contain;">
                                @else
                                    <p class="text-muted mb-0">Belum ada file TTD yang diunggah.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>
@endsection
