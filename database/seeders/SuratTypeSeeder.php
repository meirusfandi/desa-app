<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SuratType;

class SuratTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            [
                'name' => 'Surat Pengantar SKCK',
                'description' => 'Surat pengantar untuk pengurusan Surat Keterangan Catatan Kepolisian (SKCK).',
                'template_html' => '<h1>SURAT PENGANTAR SKCK</h1><p>Diberikan kepada {nama} untuk keperluan {keperluan}.</p>',
                'required_documents' => ['ktp', 'kk'],
                'input_fields' => [
                    ['label' => 'Keperluan', 'type' => 'textarea', 'required' => '1'],
                    ['label' => 'Masa Berlaku', 'type' => 'date', 'required' => '1'],
                ],
            ],
            [
                'name' => 'Surat Keterangan Domisili',
                'description' => 'Surat yang menerangkan bahwa penduduk tersebut berdomisili di wilayah desa.',
                'template_html' => '<h1>SURAT KETERANGAN DOMISILI</h1><p>Menerangkan bahwa {nama} benar berdomisili di {alamat_lengkap}.</p>',
                'required_documents' => ['ktp', 'kk'],
                'input_fields' => [
                    ['label' => 'Alamat Lengkap', 'type' => 'textarea', 'required' => '1'],
                    ['label' => 'Lama Berdomisili', 'type' => 'text', 'required' => '1'],
                ],
            ],
            [
                'name' => 'Surat Keterangan Catatan Sipil',
                'description' => 'Surat keterangan terkait catatan kependudukan.',
                'template_html' => '<h1>SURAT KETERANGAN CATATAN SIPIL</h1><p>Data Ayah: {nama_ayah}, Ibu: {nama_ibu}.</p>',
                'required_documents' => ['kk'],
                'input_fields' => [
                    ['label' => 'Nama Ayah', 'type' => 'text', 'required' => '1'],
                    ['label' => 'Nama Ibu', 'type' => 'text', 'required' => '1'],
                    ['label' => 'Tempat Lahir', 'type' => 'text', 'required' => '1'],
                ],
            ],
            [
                'name' => 'Surat Keterangan Usaha (SKU)',
                'description' => 'Surat keterangan kepemilikan usaha bagi penduduk.',
                'template_html' => '<h1>SURAT KETERANGAN USAHA</h1><p>Menerangkan bahwa {nama} memiliki usaha {nama_usaha} dengan omzet {omzet_bulanan}.</p>',
                'required_documents' => ['ktp', 'foto_usaha'],
                'input_fields' => [
                    ['label' => 'Nama Usaha', 'type' => 'text', 'required' => '1'],
                    ['label' => 'Omzet Bulanan', 'type' => 'number', 'required' => '1'],
                    ['label' => 'Alamat Usaha', 'type' => 'textarea', 'required' => '1'],
                ],
            ],
        ];

        foreach ($types as $type) {
            SuratType::updateOrCreate(['name' => $type['name']], $type);
        }
    }
}
