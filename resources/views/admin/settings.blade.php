@extends('layouts.admin')

@section('title', 'Pengaturan Website')

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Pengaturan Website</h3>
                <p class="text-subtitle text-muted">Konfigurasi umum aplikasi.</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Pengaturan</li>
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
                        <h4 class="card-title">Form Pengaturan</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                            @endif

                            <form class="form form-vertical" method="POST" action="{{ route('admin.master.pengaturan.update') }}" enctype="multipart/form-data">
                                @csrf
                                <div class="form-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6>Informasi Umum</h6>
                                            <div class="form-group">
                                                <label for="app_name">Nama Aplikasi</label>
                                                <input type="text" id="app_name" class="form-control"
                                                    name="app_name" value="{{ $settings['app_name'] ?? config('app.name') }}">
                                            </div>
                                            <div class="form-group">
                                                <label for="app_description">Deskripsi Aplikasi</label>
                                                <textarea id="app_description" class="form-control" name="app_description" rows="3">{{ $settings['app_description'] ?? '' }}</textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <h6>Kontak</h6>
                                            <div class="form-group">
                                                <label for="app_email">Email</label>
                                                <input type="email" id="app_email" class="form-control"
                                                    name="app_email" value="{{ $settings['app_email'] ?? '' }}">
                                            </div>
                                            <div class="form-group">
                                                <label for="app_phone">Telepon</label>
                                                <input type="text" id="app_phone" class="form-control"
                                                    name="app_phone" value="{{ $settings['app_phone'] ?? '' }}">
                                            </div>
                                            <div class="form-group">
                                                <label for="app_address">Alamat</label>
                                                <textarea id="app_address" class="form-control" name="app_address" rows="2">{{ $settings['app_address'] ?? '' }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row mt-3">
                                        <div class="col-md-6">
                                            <h6>Wilayah</h6>
                                            <div class="form-group">
                                                <label for="kabupaten_name">Nama Kabupaten</label>
                                                <input type="text" id="kabupaten_name" class="form-control"
                                                    name="kabupaten_name" value="{{ $settings['kabupaten_name'] ?? '' }}">
                                            </div>
                                            <div class="form-group">
                                                <label for="kecamatan_name">Nama Kecamatan</label>
                                                <input type="text" id="kecamatan_name" class="form-control"
                                                    name="kecamatan_name" value="{{ $settings['kecamatan_name'] ?? '' }}">
                                            </div>
                                            <div class="form-group">
                                                <label for="desa_name">Nama Kelurahan/Desa</label>
                                                <input type="text" id="desa_name" class="form-control"
                                                    name="desa_name" value="{{ $settings['desa_name'] ?? '' }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <h6>Alamat Lengkap</h6>
                                            <div class="form-group">
                                                <label for="full_address">Alamat Lengkap</label>
                                                <textarea id="full_address" class="form-control" name="full_address" rows="3">{{ $settings['full_address'] ?? '' }}</textarea>
                                            </div>
                                            <div class="form-group">
                                                <label for="post_code">Kode Pos</label>
                                                <input type="text" id="post_code" class="form-control"
                                                    name="post_code" value="{{ $settings['post_code'] ?? '' }}">
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row mt-3">
                                        <div class="col-md-6">
                                            <h6>Logo & Favicon</h6>
                                            <div class="form-group">
                                                <label for="app_logo">Logo Aplikasi</label>
                                                @if(isset($settings['app_logo']))
                                                    <div class="mb-2">
                                                        <img src="{{ asset('storage/'.$settings['app_logo']) }}" alt="Logo" height="50">
                                                    </div>
                                                @endif
                                                <input type="file" id="app_logo" class="form-control" name="app_logo">
                                            </div>
                                            <div class="form-group">
                                                <label for="app_favicon">Favicon</label>
                                                @if(isset($settings['app_favicon']))
                                                    <div class="mb-2">
                                                        <img src="{{ asset('storage/'.$settings['app_favicon']) }}" alt="Favicon" height="32">
                                                    </div>
                                                @endif
                                                <input type="file" id="app_favicon" class="form-control" name="app_favicon">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <h6>Logo Wilayah</h6>
                                            <div class="form-group">
                                                <label for="logo_desa">Logo Desa</label>
                                                @if(isset($settings['logo_desa']))
                                                    <div class="mb-2">
                                                        <img src="{{ asset('storage/'.$settings['logo_desa']) }}" alt="Logo Desa" height="50">
                                                    </div>
                                                @endif
                                                <input type="file" id="logo_desa" class="form-control" name="logo_desa">
                                            </div>
                                            <div class="form-group">
                                                <label for="logo_kecamatan">Logo Kecamatan</label>
                                                @if(isset($settings['logo_kecamatan']))
                                                    <div class="mb-2">
                                                        <img src="{{ asset('storage/'.$settings['logo_kecamatan']) }}" alt="Logo Kecamatan" height="50">
                                                    </div>
                                                @endif
                                                <input type="file" id="logo_kecamatan" class="form-control" name="logo_kecamatan">
                                            </div>
                                            <div class="form-group">
                                                <label for="logo_kabupaten">Logo Kabupaten</label>
                                                @if(isset($settings['logo_kabupaten']))
                                                    <div class="mb-2">
                                                        <img src="{{ asset('storage/'.$settings['logo_kabupaten']) }}" alt="Logo Kabupaten" height="50">
                                                    </div>
                                                @endif
                                                <input type="file" id="logo_kabupaten" class="form-control" name="logo_kabupaten">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12 d-flex justify-content-end mt-3">
                                        <button type="submit" class="btn btn-primary me-1 mb-1">Simpan Pengaturan</button>
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
