$cssFiles = @(
    "c:\xampp\htdocs\sistema-web-posada\public\fonts\css\lato.css",
    "c:\xampp\htdocs\sistema-web-posada\public\fonts\css\poppins.css",
    "c:\xampp\htdocs\sistema-web-posada\public\fonts\css\roboto.css",
    "c:\xampp\htdocs\sistema-web-posada\public\fonts\css\asap.css"
)

$destDir = "c:\xampp\htdocs\sistema-web-posada\public\fonts\files"
if (-not (Test-Path $destDir)) { New-Item -ItemType Directory -Path $destDir -Force | Out-Null }

$urls = @()
foreach ($file in $cssFiles) {
    if (Test-Path $file) {
        $content = Get-Content -Raw -Path $file -ErrorAction SilentlyContinue
        if ($content) {
            $matches = [regex]::Matches($content, 'https?://[^\s)"]+?\.woff2')
            foreach ($m in $matches) { $urls += $m.Value }
        }
    }
}

$urls = $urls | Select-Object -Unique

foreach ($u in $urls) {
    try {
        $uri = [uri]$u
        $filename = [System.IO.Path]::GetFileName($uri.LocalPath)
        $out = Join-Path $destDir $filename
        if (-not (Test-Path $out)) {
            Write-Output "Descargando $u -> $out"
            Invoke-WebRequest -Uri $u -OutFile $out -UseBasicParsing -ErrorAction Stop
        } else {
            Write-Output "Ya existe $out, omitiendo"
        }
    } catch {
        Write-Error ("Fallo al descargar {0}: {1}" -f $u, $_)
    }
}

Write-Output "Descarga completada. Archivos en: $destDir"