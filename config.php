<?php
session_start();

$host = 'localhost';
$dbname = 'anime_db';
$user = 'root';
$pass = '';

try {
    $bdd = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Erreur : " . $e->getMessage());
}

function redirect($url) {
    header("Location: " . htmlspecialchars($url));
    exit;
}

function is_admin() {
    return isset($_SESSION['is_admin']) && $_SESSION['is_admin'];
}

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

// Détection automatique du type MIME
function getMimeType($filename) {
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    $types = [
        // Formats vidéo
        'mp4' => 'video/mp4',
        'mkv' => 'video/x-matroska',
        'avi' => 'video/x-msvideo',
        'mov' => 'video/quicktime',
        'webm' => 'video/webm',
        'flv' => 'video/x-flv',
        'wmv' => 'video/x-ms-wmv',
        'ogv' => 'video/ogg',
        'm4v' => 'video/x-m4v',
        '3gp' => 'video/3gpp',
        'ts' => 'video/mp2t',
        'mpeg' => 'video/mpeg',
        'mpg' => 'video/mpeg',
        'asf' => 'video/x-ms-asf',
        'vob' => 'video/x-ms-vob',
        'ogm' => 'video/ogg',
        'rm' => 'application/vnd.rn-realmedia',
        'rmvb' => 'application/vnd.rn-realmedia-vbr',
        
        // Formats audio
        'mp3' => 'audio/mpeg',
        'wav' => 'audio/wav',
        'ogg' => 'audio/ogg',
        'flac' => 'audio/flac',
        'aac' => 'audio/aac',
        'wma' => 'audio/x-ms-wma',
        'm4a' => 'audio/mp4',
        
        // Autres formats
        'gif' => 'image/gif',
        'png' => 'image/png',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'webp' => 'image/webp',
        'bmp' => 'image/bmp',
        'svg' => 'image/svg+xml'
    ];
    
    return $types[$ext] ?? 'application/octet-stream'; // Type générique pour les formats inconnus
}

echo '<style>:root { color-scheme: dark; }</style>';
?>