@extends('layouts.app')

@section('title', 'Daftar Dosen')

@section('content')
    <h1>Daftar Dosen</h1>
    
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>NIDN</th>
                <th>Jabatan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($dosen as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item['nama'] }}</td>
                    <td>{{ $item['nidn'] }}</td>
                    <td>{{ $item['jabatan'] }}</td>
                    <td>
                        <a href="/dosen/{{ Str::slug($item['nama']) }}">Lihat Detail</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection