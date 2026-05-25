$cssUrls = @(
    "https://posadameridavacacional.com/wp-content/uploads/elementor/google-fonts/css/lato.css?ver=1742220995",
    "https://posadameridavacacional.com/wp-content/uploads/elementor/google-fonts/css/poppins.css?ver=1742221149"
)

$destDir = "c:\xampp\htdocs\sistema-web-posada\public\fonts\files"
$cssDir = "c:\xampp\htdocs\sistema-web-posada\public\fonts\css"

if (-not (Test-Path $destDir)) { New-Item -ItemType Directory -Path $destDir -Force | Out-Null }
if (-not (Test-Path $cssDir)) { New-Item -ItemType Directory -Path $cssDir -Force | Out-Null }

foreach ($cssUrl in $cssUrls) {
    try {
        Write-Output "Descargando CSS: $cssUrl"
        $response = Invoke-WebRequest -Uri $cssUrl -UseBasicParsing -ErrorAction Stop
        $cssContent = $response.Content
        if (-not $cssContent) { Write-Error "Contenido vacío para $cssUrl"; continue }

        # Extraer todos los .woff2 usando url(...) para evitar problemas de comillas
        $matches = [regex]::Matches($cssContent, 'url\(([^)]+\.woff2)\)')
        $seen = @{}
        foreach ($m in $matches) {
            $trimChars = @("'", '"', " ")
            $url = $m.Groups[1].Value.Trim($trimChars)
            if (-not $seen.ContainsKey($url)) {
                $seen[$url] = $true
                try {
                    $uri = [uri]$url
                    $filename = [System.IO.Path]::GetFileName($uri.LocalPath)
                    $out = Join-Path $destDir $filename
                    if (-not (Test-Path $out)) {
                        Write-Output "Descargando fuente $url -> $out"
                        Invoke-WebRequest -Uri $url -OutFile $out -UseBasicParsing -ErrorAction Stop
                    } else {
                        Write-Output "Fuente ya existe: $out"
                    }

                    # Reemplazar URL remota por ruta relativa en el CSS
                    $cssContent = $cssContent -replace [regex]::Escape($url), "../files/$filename"
                } catch {
                    Write-Error ("Fallo con fuente {0}: {1}" -f $url, $_)
                }
            }
        }

        # Guardar CSS local (tomar el nombre de archivo sin query)
        $uriObj = [uri]$cssUrl
        $cssName = [System.IO.Path]::GetFileName($uriObj.LocalPath)
        if (-not $cssName) { $cssName = "fonts-local.css" }
        $outCss = Join-Path $cssDir $cssName
        Write-Output "Guardando CSS local: $outCss"
        Set-Content -Path $outCss -Value $cssContent -Encoding UTF8

    } catch {
        Write-Error ('Fallo al procesar {0}: {1}' -f $cssUrl, $_)
    }
}

Write-Output "Proceso completado. Fuentes en: $destDir, CSS en: $cssDir"