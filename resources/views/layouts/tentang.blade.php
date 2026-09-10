@extends('layouts.app')

@section('title', 'Tentang Prodi')

@section('content')
    <h1>Jurusan: {{ $jurusan }}</h1>
    <h3>Program Studi: {{ $namaProdi }}</h3>

    <h4>Status Akreditasi:</h4>
    @if ($akreditasi == 'Unggul')
        <p style="color: green; font-weight: bold;">Program Studi Terakreditasi Unggul</p>
    @elseif ($akreditasi == 'Baik Sekali')
        <p style="color: blue; font-weight: bold;">Program Studi Terakreditasi Baik Sekali</p>
    @else
        <p>Informasi akreditasi belum tersedia.</p>
    @endif

    <hr>

    <h4>Visi</h4>
    <p>{{ $visi }}</p>

    <h4>Misi</h4>
    <ul>
        @foreach ($misi as $item)
            <li>{{ $item }}</li>
        @endforeach
    </ul>
@endsection