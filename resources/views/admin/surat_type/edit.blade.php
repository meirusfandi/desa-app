@extends('layouts.admin')

@section('title', 'Edit Layanan')

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Edit Layanan</h3>
                <p class="text-subtitle text-muted">Ubah data layanan surat.</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.master.jenis-surat.index') }}">Master Layanan</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Edit Layanan</li>
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
                        <h4 class="card-title">Form Edit Layanan</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <form class="form form-vertical" method="POST" action="{{ route('admin.master.jenis-surat.update', $type->id) }}">
                                @csrf
                                @method('PUT')
                                <div class="form-body">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="name">Nama Layanan</label>
                                                <input type="text" id="name" class="form-control @error('name') is-invalid @enderror" 
                                                    name="name" placeholder="Contoh: Surat Keterangan Usaha" value="{{ old('name', $type->name) }}">
                                                @error('name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="description">Deskripsi (Opsional)</label>
                                                <textarea id="description" class="form-control @error('description') is-invalid @enderror" 
                                                    name="description" rows="3" placeholder="Deskripsi singkat tentang layanan ini">{{ old('description', $type->description) }}</textarea>
                                                @error('description')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        
                                        <!-- Dynamic Form Builder -->
                                        <div class="col-12 mt-3">
                                            <h5 class="mb-3">Form Builder (Input Dinamis)</h5>
                                            <div class="table-responsive">
                                                <table class="table table-bordered" id="dynamic-fields-table">
                                                    <thead>
                                                        <tr>
                                                            <th>Label Input</th>
                                                            <th>Tipe Input</th>
                                                            <th>Wajib Diisi?</th>
                                                            <th>Aksi</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="dynamic-fields-body">
                                                        {{-- Fields will be added here via JS --}}
                                                    </tbody>
                                                </table>
                                                <button type="button" class="btn btn-sm btn-success" id="add-field-btn">
                                                    <i class="bi bi-plus"></i> Tambah Field
                                                </button>
                                            </div>
                                        </div>

                                        <div class="col-12 mt-4">
                                            <div class="form-group">
                                                <label for="template_html">Template HTML (Surat)</label>
                                                <textarea id="template_html" class="form-control @error('template_html') is-invalid @enderror" 
                                                    name="template_html" rows="10" placeholder="Masukkan kode HTML untuk template surat...">{{ old('template_html', $type->template_html) }}</textarea>
                                                @error('template_html')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <small class="text-muted">Gunakan placeholder {label_input} untuk data dinamis. Contoh: {nama_usaha}</small>
                                            </div>
                                        </div>
                                        
                                        <div class="col-12 d-flex justify-content-end mt-3">
                                            <button type="submit" class="btn btn-primary me-1 mb-1">Simpan Perubahan</button>
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tableBody = document.getElementById('dynamic-fields-body');
        const addBtn = document.getElementById('add-field-btn');
        let fieldIndex = 0;

        function addFieldRow(label = '', type = 'text', required = false) {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>
                    <input type="text" name="input_fields[${fieldIndex}][label]" class="form-control form-control-sm" placeholder="Label Field" value="${label}" required>
                </td>
                <td>
                    <select name="input_fields[${fieldIndex}][type]" class="form-select form-select-sm">
                        <option value="text" ${type === 'text' ? 'selected' : ''}>Teks Singkat</option>
                        <option value="textarea" ${type === 'textarea' ? 'selected' : ''}>Teks Panjang</option>
                        <option value="number" ${type === 'number' ? 'selected' : ''}>Angka</option>
                        <option value="date" ${type === 'date' ? 'selected' : ''}>Tanggal</option>
                    </select>
                </td>
                <td class="text-center">
                    <input class="form-check-input" type="checkbox" name="input_fields[${fieldIndex}][required]" value="1" ${required ? 'checked' : ''}>
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-danger remove-field-btn">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            `;
            tableBody.appendChild(row);
            fieldIndex++;
        }

        addBtn.addEventListener('click', function() {
            addFieldRow();
        });

        tableBody.addEventListener('click', function(e) {
            if (e.target.closest('.remove-field-btn')) {
                e.target.closest('tr').remove();
            }
        });

        // Populate existing data
        @if(old('input_fields'))
            @foreach(old('input_fields') as $field)
                addFieldRow("{{ $field['label'] }}", "{{ $field['type'] }}", {{ isset($field['required']) ? 'true' : 'false' }});
            @endforeach
        @elseif($type->input_fields)
            @foreach($type->input_fields as $field)
                addFieldRow("{{ $field['label'] }}", "{{ $field['type'] }}", {{ isset($field['required']) ? 'true' : 'false' }});
            @endforeach
        @endif
    });
</script>
@endsection
