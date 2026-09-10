@extends('layouts.app')

@section('title', 'Detail Dosen')

@section('content')
    <h1>Detail Dosen</h1>
    <p><strong>Nama Dosen:</strong> {{ ucfirst($nama) }}</p>
    <a href="/dosen">&laquo; Kembali ke daftar dosen</a>
@endsection