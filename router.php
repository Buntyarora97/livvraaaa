<?php
// Router script for PHP built-in web server
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// Normalize path to prevent directory traversal attacks
$uri = str_replace('\\', '/', $uri);
$uri = preg_replace('#/+#', '/', $uri);
$parts = explode('/', $uri);
$normalized = [];
foreach ($parts as $part) {
    if ($part === '' || $part === '.') {
        continue;
    }
    if ($part === '..') {
        array_pop($normalized);
    } else {
        $normalized[] = $part;
    }
}
$uri = '/' . implode('/', $normalized);

// Root path
if ($uri === '/' || $uri === '') {
    chdir(__DIR__ . '/public');
    require __DIR__ . '/public/index.php';
    return true;
}

// Check for static files in public directory (assets, etc)
if (preg_match('/\.(css|js|jpg|jpeg|png|gif|svg|ico|woff|woff2|ttf|eot)$/i', $uri)) {
    $publicFile = __DIR__ . '/public' . $uri;
    if (file_exists($publicFile)) {
        $mimeTypes = [
            'css' => 'text/css',
            'js' => 'application/javascript',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'svg' => 'image/svg+xml',
            'ico' => 'image/x-icon',
            'woff' => 'font/woff',
            'woff2' => 'font/woff2',
            'ttf' => 'font/ttf',
            'eot' => 'application/vnd.ms-fontobject'
        ];
        $ext = strtolower(pathinfo($publicFile, PATHINFO_EXTENSION));
        $mimeType = $mimeTypes[$ext] ?? 'application/octet-stream';
        header('Content-Type: ' . $mimeType);
        header('Cache-Control: no-cache');
        readfile($publicFile);
        return true;
    }
}

// Admin routes
if (strpos($uri, '/admin') === 0) {
    $adminFile = __DIR__ . $uri;
    $adminDir = realpath(__DIR__ . '/admin');
    $realAdminFile = realpath($adminFile);
    
    // Check for static files FIRST in admin/assets (with validation)
    if (preg_match('/\.(css|js|jpg|jpeg|png|gif|svg|ico|woff|woff2|ttf|eot)$/i', $uri)) {
        if ($realAdminFile !== false && $adminDir !== false && strpos($realAdminFile, $adminDir) === 0) {
            if (file_exists($adminFile)) {
                $mimeTypes = [
                    'css' => 'text/css',
                    'js' => 'application/javascript',
                    'jpg' => 'image/jpeg',
                    'jpeg' => 'image/jpeg',
                    'png' => 'image/png',
                    'gif' => 'image/gif',
                    'svg' => 'image/svg+xml',
                    'ico' => 'image/x-icon',
                    'woff' => 'font/woff',
                    'woff2' => 'font/woff2',
                    'ttf' => 'font/ttf',
                    'eot' => 'application/vnd.ms-fontobject'
                ];
                $ext = strtolower(pathinfo($adminFile, PATHINFO_EXTENSION));
                $mimeType = $mimeTypes[$ext] ?? 'application/octet-stream';
                header('Content-Type: ' . $mimeType);
                header('Cache-Control: no-cache');
                readfile($adminFile);
                return true;
            }
        }
    }
    
    // Check PHP files (directory with index.php or direct .php file)
    if ($realAdminFile !== false && $adminDir !== false && strpos($realAdminFile, $adminDir) === 0) {
        if (file_exists($adminFile)) {
            if (is_dir($adminFile)) {
                if (file_exists($adminFile . '/index.php')) {
                    chdir(dirname($adminFile . '/index.php'));
                    require $adminFile . '/index.php';
                    return true;
                }
            } elseif (pathinfo($adminFile, PATHINFO_EXTENSION) === 'php') {
                chdir(dirname($adminFile));
                require $adminFile;
                return true;
            }
        }
    }
    
    // Try with .php extension (with same validation)
    $adminFilePhp = $adminFile . '.php';
    $realAdminFilePhp = realpath($adminFilePhp);
    if ($realAdminFilePhp !== false && $adminDir !== false && strpos($realAdminFilePhp, $adminDir) === 0) {
        if (file_exists($adminFilePhp)) {
            chdir(dirname($adminFilePhp));
            require $adminFilePhp;
            return true;
        }
    }
}

// Public routes (pages)
$publicFile = __DIR__ . '/public' . $uri;
$publicDir = realpath(__DIR__ . '/public');

// Validate that resolved path is within public directory
$realPublicFile = realpath($publicFile);
if ($realPublicFile !== false && $publicDir !== false && strpos($realPublicFile, $publicDir) === 0) {
    if (file_exists($publicFile)) {
        if (is_dir($publicFile)) {
            $indexFile = $publicFile . '/index.php';
            if (file_exists($indexFile)) {
                chdir(dirname($indexFile));
                require $indexFile;
                return true;
            }
        } else {
            chdir(dirname($publicFile));
            require $publicFile;
            return true;
        }
    }
}

// Try public with .php extension (with validation)
$publicFilePhp = $publicFile . '.php';
$realPublicFilePhp = realpath($publicFilePhp);
if ($realPublicFilePhp !== false && $publicDir !== false && strpos($realPublicFilePhp, $publicDir) === 0) {
    if (file_exists($publicFilePhp)) {
        chdir(dirname($publicFilePhp));
        require $publicFilePhp;
        return true;
    }
}

// 404 Not Found
http_response_code(404);
echo '404 Not Found';
return true;
