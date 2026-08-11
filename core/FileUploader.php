<?php
/**
 * FileUploader.php — Secure File Upload Helper
 * 
 * Provides strict validation, MIME-type verification, extension whitelisting,
 * and randomized filename generation to prevent arbitrary file upload vulnerabilities (RCE).
 */

class FileUploader
{
    private const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp', 'pdf'];
    
    private const ALLOWED_MIME_TYPES = [
        'image/jpeg' => ['jpg', 'jpeg'],
        'image/png'  => ['png'],
        'image/webp' => ['webp'],
        'application/pdf' => ['pdf']
    ];

    private const MAX_FILE_SIZE = 10485760; // 10 MB

    /**
     * Upload and sanitize a single uploaded file
     *
     * @param array $file The $_FILES['field_name'] array
     * @param string $destinationDir Absolute or relative destination directory
     * @param string $prefix Optional filename prefix
     * @return array ['status' => bool, 'filename' => string, 'error' => string|null]
     */
    public static function upload(array $file, string $destinationDir, string $prefix = 'doc_'): array
    {
        // 1. Check for upload errors
        if (!isset($file['error']) || is_array($file['error'])) {
            return ['status' => false, 'filename' => '', 'error' => 'Invalid upload parameter.'];
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errorMsg = match ($file['error']) {
                UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'File exceeds maximum upload size (10MB).',
                UPLOAD_ERR_PARTIAL => 'File was only partially uploaded.',
                UPLOAD_ERR_NO_FILE => 'No file was uploaded.',
                UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder.',
                UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
                default => 'Unknown upload error.'
            };
            return ['status' => false, 'filename' => '', 'error' => $errorMsg];
        }

        // 2. Check file size
        if ($file['size'] > self::MAX_FILE_SIZE) {
            return ['status' => false, 'filename' => '', 'error' => 'File size exceeds the 10MB limit.'];
        }

        // 3. Verify extension
        $originalExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($originalExt, self::ALLOWED_EXTENSIONS, true)) {
            return ['status' => false, 'filename' => '', 'error' => 'Invalid file extension. Only JPG, PNG, WEBP, and PDF files are allowed.'];
        }

        // 4. Verify MIME type using finfo
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);

        if (!array_key_exists($mimeType, self::ALLOWED_MIME_TYPES)) {
            return ['status' => false, 'filename' => '', 'error' => 'Invalid file type. Uploaded content does not match allowed media.'];
        }

        // Check that extension matches MIME type
        if (!in_array($originalExt, self::ALLOWED_MIME_TYPES[$mimeType], true)) {
            // Correct extension to the canonical one for this MIME
            $originalExt = self::ALLOWED_MIME_TYPES[$mimeType][0];
        }

        // 5. Ensure destination directory exists
        if (!is_dir($destinationDir)) {
            if (!mkdir($destinationDir, 0755, true) && !is_dir($destinationDir)) {
                return ['status' => false, 'filename' => '', 'error' => 'Failed to create upload destination directory.'];
            }
        }

        // 6. Generate secure, unique filename
        $uniqueFilename = $prefix . bin2hex(random_bytes(12)) . '.' . $originalExt;
        $targetPath = rtrim($destinationDir, '/\\') . DIRECTORY_SEPARATOR . $uniqueFilename;

        // 7. Move file
        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            return ['status' => false, 'filename' => '', 'error' => 'Failed to save uploaded file.'];
        }

        return [
            'status' => true,
            'filename' => $uniqueFilename,
            'error' => null
        ];
    }
}
