@extends('layouts.warga')

@section('title', 'Edit Pengajuan Surat')

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Edit Pengajuan Surat</h3>
                <p class="text-subtitle text-muted">Perbarui data pengajuan surat Anda.</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('warga.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('warga.surat.index') }}">Surat</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Edit</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section id="basic-vertical-layouts">
        <div class="row match-height">
            <div class="col-12 col-md-8 mx-auto">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Form Edit</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <form class="form form-vertical" action="{{ route('warga.surat.update', $surat->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="form-body">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="surat_type_id">Jenis Layanan Surat</label>
                                                <select name="surat_type_id" id="surat_type_id" class="form-select" required>
                                                    <option value="">-- Pilih Jenis Surat --</option>
                                                    @foreach($suratTypes as $type)
                                                    <option value="{{ $type->id }}" {{ $surat->surat_type_id == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                                                    @endforeach
                                                </select>
                                                @error('surat_type_id')
                                                    <small class="text-danger">{{ $message }}</small>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-12 mt-3">
                                            <div class="alert alert-light-warning color-warning">
                                                <i class="bi bi-exclamation-triangle"></i> Upload file baru hanya jika ingin menambahkan dokumen. Dokumen lama akan tetap tersimpan.
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="doc_add_1">Dokumen Tambahan 1</label>
                                                <input type="file" class="form-control" name="files[doc_add_1]" id="doc_add_1">
                                            </div>
                                        </div>
                                        
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="doc_add_2">Dokumen Tambahan 2</label>
                                                <input type="file" class="form-control" name="files[doc_add_2]" id="doc_add_2">
                                            </div>
                                        </div>
                                        
                                        @error('files.*')
                                            <div class="col-12">
                                                <small class="text-danger">{{ $message }}</small>
                                            </div>
                                        @enderror

                                        <div class="col-12 d-flex justify-content-end mt-4">
                                            <a href="{{ route('warga.surat.index') }}" class="btn btn-light-secondary me-1 mb-1">Kembali</a>
                                            <button type="submit" class="btn btn-primary me-1 mb-1">Simpan Perubahan</button>
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
