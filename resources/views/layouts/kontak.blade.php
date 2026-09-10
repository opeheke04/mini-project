@extends('layouts.app')

@section('title', 'Kontak')

@section('content')
    <h1>Hubungi Kami</h1>
    <p>Jika Anda memiliki pertanyaan mengenai Program Studi, silakan hubungi kami melalui:</p>
    <ul>
        <li><strong>Email:</strong> {{ $email }}</li>
        <li><strong>Telepon:</strong> {{ $telepon }}</li>
        <li><strong>Alamat:</strong> {{ $alamat }}</li>
    </ul>
@endsection