@extends('layouts.admin')

@section('title', 'Tambah Layanan')

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Tambah Layanan</h3>
                <p class="text-subtitle text-muted">Tambah jenis layanan surat baru.</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.master.jenis-surat.index') }}">Master Layanan</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Tambah Layanan</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section id="basic-vertical-layouts">
        <div class="row match-height">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Form Tambah Layanan</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <form class="form form-vertical" method="POST" action="{{ route('admin.master.jenis-surat.store') }}">
                                @csrf
                                <div class="form-body">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="name">Nama Layanan</label>
                                                <input type="text" id="name" class="form-control @error('name') is-invalid @enderror" 
                                                    name="name" placeholder="Contoh: Surat Keterangan Usaha" value="{{ old('name') }}">
                                                @error('name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="description">Deskripsi (Opsional)</label>
                                                <textarea id="description" class="form-control @error('description') is-invalid @enderror" 
                                                    name="description" rows="3" placeholder="Deskripsi singkat tentang layanan ini">{{ old('description') }}</textarea>
                                                @error('description')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="template_html">Template HTML (Surat)</label>
                                                <textarea id="template_html" class="form-control @error('template_html') is-invalid @enderror" 
                                                    name="template_html" rows="10" placeholder="Masukkan kode HTML untuk template surat...">{{ old('template_html') }}</textarea>
                                                @error('template_html')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <small class="text-muted">Gunakan placeholder seperti {nama}, {nik}, {alamat} untuk data dinamis.</small>
                                            </div>
                                        </div>
                                        
                                        <div class="col-12 d-flex justify-content-end mt-3">
                                            <button type="submit" class="btn btn-primary me-1 mb-1">Simpan</button>
                                            <a href="{{ route('admin.master.jenis-surat.index') }}" class="btn btn-light-secondary me-1 mb-1">Batal</a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
