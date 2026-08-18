<!DOCTYPE html>
<html lang="pt-BR" class="dark">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>ProjectLens</title>
        <link rel="icon" type="image/svg+xml" href="{{ asset('build/favicon.svg') }}">
        @php
            $manifestPath = public_path('build/.vite/manifest.json');

            if (file_exists($manifestPath)) {
                $manifest = json_decode(file_get_contents($manifestPath), true);
                $entry = $manifest['src/main.ts'] ?? null;

                if ($entry) {
                    foreach ($entry['css'] ?? [] as $css) {
                        echo '<link rel="stylesheet" href="' . asset('build/' . $css) . '">';
                    }

                    $imports = $entry['imports'] ?? [];
                    foreach ($imports as $import) {
                        if (isset($manifest[$import])) {
                            echo '<script type="module" src="' . asset('build/' . $manifest[$import]['file']) . '"></script>';
                        }
                    }

                    echo '<script type="module" src="' . asset('build/' . $entry['file']) . '"></script>';
                }
            }
        @endphp
    </head>
    <body>
        <div id="app"></div>
    </body>
</html>