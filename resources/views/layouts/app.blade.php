<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Profil Prodi</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; line-height: 1.6; }
        nav a { margin-right: 15px; text-decoration: none; color: #007bff; font-weight: bold; }
        nav a:hover { text-decoration: underline; }
        footer { margin-top: 30px; padding: 10px 0; border-top: 1px solid #ccc; font-size: 14px; }
        table { border-collapse: collapse; width: 100%; margin-top: 10px; }
        table, th, td { border: 1px solid #ddd; padding: 8px; }
        th { background-color: #f2f2f2; text-align: left; }
    </style>
</head>
<body>

    <nav>
        <a href="/">Home</a>
        <a href="/tentang">Tentang</a>
        <a href="/dosen">Dosen</a>
        <a href="/kontak">Kontak</a>
    </nav>

    <hr>

    <main>
        @yield('content')
    </main>

    <hr>

    <footer>
        &copy; 2026 Program Studi Teknologi Informasi. All rights reserved.
    </footer>

</body>
</html>