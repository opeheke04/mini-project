<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>403 - Akses Ditolak</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            min-height: 100svh;
            margin: 0;
            display: grid;
            place-items: center;
            background: #1b222e;
            color: #d7dce5;
            font-family: Arial, sans-serif;
        }

        main {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 24px;
            font-size: 14px;
        }

        h1 {
            margin: 0;
            font-size: 16px;
            font-weight: 700;
        }

        .divider {
            width: 2px;
            height: 24px;
            background: #647184;
        }

        p {
            margin: 0;
            color: #b8c0ce;
            letter-spacing: 0.04em;
        }

        @media (max-width: 420px) {
            main {
                gap: 12px;
                font-size: 12px;
            }
        }
    </style>
</head>
<body>
    <main role="alert" aria-labelledby="error-code">
        <h1 id="error-code">403</h1>
        <span class="divider" aria-hidden="true"></span>
        <p>ANDA TIDAK MEMILIKI AKSES.</p>
    </main>
</body>
</html>