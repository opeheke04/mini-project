<?php

use Illuminate\Support\Facades\Route;

// 1. Route Home
Route::get('/', function () {
    return view('layouts.home');
});

// 2. Route Tentang (Mengirim data string & akreditasi)
Route::get('/tentang', function () {
    $namaProdi = 'Teknik Informatika';
    $jurusan = 'Teknologi Informasi';
    $akreditasi = 'Baik Sekali';
    $visi = 'Mencetak lulusan TI yang kompeten, inovatif, dan berdaya saing global.';
    $misi = [
        'Menyelenggarakan pendidikan berkualitas di bidang Teknologi Informasi.',
        'Melakukan penelitian tepat guna untuk masyarakat dan industri.',
        'Melaksanakan pengabdian kepada masyarakat berbasis ilmu pengetahuan dan teknologi.'
    ];

    return view('layouts.tentang', compact('namaProdi', 'jurusan', 'akreditasi', 'visi', 'misi'));
});

// 3. Route Dosen (Mengirim array data dosen)
Route::get('/dosen', function () {
    $dosen = [
        [
            'nama' => 'Budi Santoso',
            'nidn' => '0012345678',
            'jabatan' => 'Dosen'
        ],
        [
            'nama' => 'Siti Aminah',
            'nidn' => '0012345679',
            'jabatan' => 'Dosen'
        ],
        [
            'nama' => 'Andi Wijaya',
            'nidn' => '0012345680',
            'jabatan' => 'Ketua Program Studi'
        ],
    ];

    return view('layouts.dosen', compact('dosen'));
});

// 4. Route Detail Dosen (Route Parameter)
Route::get('/dosen/{nama}', function ($nama) {
    return view('layouts.detail-dosen', compact('nama'));
});

// 5. Route Kontak
Route::get('/kontak', function () {
    $email = 'ti@politeknik.ac.id';
    $telepon = '(0341) 551611';
    $alamat = 'Jl. Soekarno Hatta No. 9, Malang';

    return view('layouts.kontak', compact('email', 'telepon', 'alamat'));
});