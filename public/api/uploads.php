<?php
require_once __DIR__ . '/../../api/helpers.php';

// Uploads by teachers: PDFs and MP4 videos. Limit 50MB.
$maxSize = 50 * 1024 * 1024; // 50 MB

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  jsonError('Méthode invalide.');
}

ensureRole('enseignant');

if (empty($_FILES['file'])) {
  jsonError('Aucun fichier envoyé.');
}

$file = $_FILES['file'];
if ($file['error'] !== UPLOAD_ERR_OK) {
  jsonError('Erreur lors de l\'upload.');
}

if ($file['size'] > $maxSize) {
  jsonError('Fichier trop volumineux (max 50MB).');
}

$allowedPdf = ['application/pdf'];
$allowedVideo = ['video/mp4', 'video/MP4', 'application/octet-stream'];

$finfo = new finfo(FILEINFO_MIME_TYPE);
$mime = $finfo->file($file['tmp_name']);

$ext = '';
$destDir = '';
if (in_array($mime, $allowedPdf, true)) {
  $ext = 'pdf';
  $destDir = __DIR__ . '/../../public/uploads/pdfs/';
} elseif (in_array($mime, $allowedVideo, true) || preg_match('/mp4/i', $file['name'])) {
  $ext = 'mp4';
  $destDir = __DIR__ . '/../../public/uploads/videos/';
} else {
  jsonError('Type de fichier non autorisé. Autorisé: PDF, MP4. Detected: ' . $mime);
}

if (!is_dir($destDir)) {
  @mkdir($destDir, 0755, true);
}

$filename = time() . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
$destPath = $destDir . $filename;

if (!move_uploaded_file($file['tmp_name'], $destPath)) {
  jsonError('Impossible de déplacer le fichier uploadé.');
}

// Return web-accessible path
$webPath = 'uploads/' . ($ext === 'pdf' ? 'pdfs/' : 'videos/') . $filename;
jsonSuccess('Fichier uploadé.', ['path' => $webPath]);
