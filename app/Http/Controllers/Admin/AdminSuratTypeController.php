<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminSuratTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $types = \App\Models\SuratType::latest()->paginate(10);
        return view('admin.surat_type.index', compact('types'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.surat_type.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'template_html' => 'required|string',
        ]);

        \App\Models\SuratType::create($request->all());

        return redirect()->route('admin.master.jenis-surat.index')
            ->with('success', 'Jenis Surat berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $type = \App\Models\SuratType::findOrFail($id);
        return view('admin.surat_type.edit', compact('type'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'template_html' => 'required|string',
        ]);

        $type = \App\Models\SuratType::findOrFail($id);
        $type->update($request->all());

        return redirect()->route('admin.master.jenis-surat.index')
            ->with('success', 'Jenis Surat berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $type = \App\Models\SuratType::findOrFail($id);
        $type->delete();

        return redirect()->route('admin.master.jenis-surat.index')
            ->with('success', 'Jenis Surat berhasil dihapus');
    }
}
