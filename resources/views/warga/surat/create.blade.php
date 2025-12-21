@extends('layouts.warga')

@section('title', 'Buat Pengajuan Surat')

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Buat Pengajuan Surat</h3>
                <p class="text-subtitle text-muted">Isi formulir untuk mengajukan surat baru.</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('warga.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('warga.surat.index') }}">Surat</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Buat</li>
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
                        <h4 class="card-title">Form Pengajuan</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <form class="form form-vertical" action="{{ route('warga.surat.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="form-body">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="surat_type_id">Jenis Layanan Surat</label>
                                                <select name="surat_type_id" id="surat_type_id" class="form-select" required>
                                                    <option value="">-- Pilih Jenis Surat --</option>
                                                    @foreach($suratTypes as $type)
                                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                                    @endforeach
                                                </select>
                                                @error('surat_type_id')
                                                    <small class="text-danger">{{ $message }}</small>
                                                @enderror
                                            </div>
                                        </div>

                                        <!-- Dynamic Fields Container -->
                                        <div id="dynamic-fields-container" class="col-12 mt-3">
                                            {{-- Dynamic fields will be rendered here --}}
                                        </div>

                                        <div class="col-12 mt-3">
                                            <div class="alert alert-light-primary color-primary">
                                                <i class="bi bi-info-circle"></i> Silakan upload dokumen pendukung (KTP, KK, Pengantar RT/RW, dll) sesuai persyaratan layanan.
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="doc_1">Dokumen 1 (Wajib)</label>
                                                <input type="file" class="form-control" name="files[doc_1]" id="doc_1" required>
                                            </div>
                                        </div>
                                        
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="doc_2">Dokumen 2 (Opsional)</label>
                                                <input type="file" class="form-control" name="files[doc_2]" id="doc_2">
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="doc_3">Dokumen 3 (Opsional)</label>
                                                <input type="file" class="form-control" name="files[doc_3]" id="doc_3">
                                            </div>
                                        </div>
                                        
                                        @error('files.*')
                                            <div class="col-12">
                                                <small class="text-danger">{{ $message }}</small>
                                            </div>
                                        @enderror

                                        <div class="col-12 d-flex justify-content-end mt-4">
                                            <a href="{{ route('warga.surat.index') }}" class="btn btn-light-secondary me-1 mb-1">Kembali</a>
                                            <button type="submit" class="btn btn-primary me-1 mb-1">Kirim Pengajuan</button>
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const typeSelect = document.getElementById('surat_type_id');
        const container = document.getElementById('dynamic-fields-container');
        const suratTypes = @json($suratTypes);

        typeSelect.addEventListener('change', function() {
            const selectedId = this.value;
            const selectedType = suratTypes.find(t => t.id == selectedId);
            
            container.innerHTML = '';
            
            if (selectedType && selectedType.input_fields) {
                selectedType.input_fields.forEach(field => {
                    const slug = field.label.toLowerCase().replace(/ /g, '_').replace(/[^\w-]+/g, '');
                    const required = field.required ? 'required' : '';
                    const label = field.label + (field.required ? ' <span class="text-danger">*</span>' : '');
                    
                    let inputHtml = '';
                    if (field.type === 'textarea') {
                        inputHtml = `<textarea name="data[${slug}]" class="form-control" ${required}></textarea>`;
                    } else {
                        inputHtml = `<input type="${field.type}" name="data[${slug}]" class="form-control" ${required}>`;
                    }

                    const div = document.createElement('div');
                    div.className = 'form-group mb-3';
                    div.innerHTML = `
                        <label>${label}</label>
                        ${inputHtml}
                    `;
                    container.appendChild(div);
                });
            }
        });
    });
</script>
@endsection
