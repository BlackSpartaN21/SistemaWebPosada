$sourceHtml = "public/index.html"
$destRoot = "public/vendor"

if (-Not (Test-Path $sourceHtml)) {
    Write-Error "Archivo no encontrado: $sourceHtml"
    exit 1
}

$html = Get-Content -Raw -Path $sourceHtml

$linkPattern = '<link[^>]+href="(?<url>https?://[^"]+)"[^>]*>'
$scriptPattern = '<script[^>]+src="(?<url>https?://[^"]+)"[^>]*>'

$matches = @()
$matches += [regex]::Matches($html, $linkPattern)
$matches += [regex]::Matches($html, $scriptPattern)

$urls = $matches | ForEach-Object { $_.Groups["url"].Value } | Select-Object -Unique

$downloadUrls = $urls | Where-Object {
    ($_ -match '\.(css|js)(\?|$)') -or
    ($_ -match 'googletagmanager\.com/gtm\.js') -or
    ($_ -match 'google\.com/recaptcha')
}

if (-Not $downloadUrls) {
    Write-Host "No se encontraron URLs CSS/JS para descargar."
    exit 0
}

foreach ($url in $downloadUrls) {
    try {
        $cleanUrl = $url -replace '\?.*$', ''
        $uri = [uri]$cleanUrl
        $remoteHost = $uri.Host
        $path = $uri.AbsolutePath.TrimStart('/')
        $localRelative = Join-Path $remoteHost $path
        $destFile = Join-Path $destRoot $localRelative
        $destDir = Split-Path $destFile -Parent

        if (-Not (Test-Path $destDir)) {
            New-Item -ItemType Directory -Path $destDir -Force | Out-Null
        }

        if (Test-Path $destFile) {
            Write-Host "Ya existe: $destFile"
            continue
        }

        Write-Host "Descargando $url -> $destFile"
        Invoke-WebRequest -Uri $url -OutFile $destFile -ErrorAction Stop
    }
    catch {
        Write-Warning ('Error descargando {0}: {1}' -f $url, $_.Exception.Message)
    }
}

Write-Host "Descarga completa. Los archivos están en: $destRoot"