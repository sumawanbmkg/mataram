<?php
/**
 * Image Processor dengan Auto-Resize dan WebP Conversion
 * Keamanan: MIME Type Validation, Filename Sanitization
 */

define('KHK_ADMIN', true);
require_once __DIR__ . '/../config/config.php';

class ImageProcessor {
    private $maxWidth;
    private $uploadDir;
    private $allowedMimeTypes;

    public function __construct() {
        $this->maxWidth = MAX_IMAGE_WIDTH;
        $this->uploadDir = UPLOAD_DIR;
        $this->allowedMimeTypes = ALLOWED_MIME_TYPES;
        
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }
    }

    /**
     * Process uploaded image
     * - Validate MIME type
     * - Resize if needed
     * - Convert to WebP
     * - Generate random filename
     */
    public function processUpload($file) {
        // Validate file
        $validation = $this->validateFile($file);
        if (!$validation['success']) {
            return $validation;
        }

        // Get real MIME type from file content
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);

        if (!in_array($mimeType, $this->allowedMimeTypes)) {
            return ['success' => false, 'message' => 'Tipe file tidak diizinkan. Hanya JPG, PNG, WebP, GIF.'];
        }

        // Additional check: verify it's actually an image
        $imageInfo = @getimagesize($file['tmp_name']);
        if ($imageInfo === false) {
            return ['success' => false, 'message' => 'File bukan gambar yang valid.'];
        }

        // Generate random filename
        $randomName = $this->generateRandomFilename();
        
        // Process image
        try {
            $image = $this->loadImage($file['tmp_name'], $mimeType);
            
            if (!$image) {
                return ['success' => false, 'message' => 'Gagal memproses gambar.'];
            }

            // Resize if needed
            $image = $this->resizeImage($image, $imageInfo[0], $imageInfo[1]);

            // Save as WebP
            $webpFilename = $randomName . '.webp';
            $webpPath = $this->uploadDir . $webpFilename;
            
            if (!imagewebp($image, $webpPath, 85)) {
                imagedestroy($image);
                return ['success' => false, 'message' => 'Gagal menyimpan gambar.'];
            }

            // Also save original format as backup
            $originalExt = $this->getExtensionFromMime($mimeType);
            $originalFilename = $randomName . '.' . $originalExt;
            $originalPath = $this->uploadDir . $originalFilename;
            
            $this->saveOriginalFormat($image, $originalPath, $mimeType);

            imagedestroy($image);

            return [
                'success' => true,
                'filename' => $webpFilename,
                'original_filename' => $originalFilename,
                'path' => 'images/news/' . $webpFilename,
                'size' => filesize($webpPath)
            ];

        } catch (Exception $e) {
            error_log("Image processing error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Terjadi kesalahan saat memproses gambar.'];
        }
    }

    /**
     * Validate uploaded file
     */
    private function validateFile($file) {
        if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
            return ['success' => false, 'message' => 'Tidak ada file yang diunggah.'];
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors = [
                UPLOAD_ERR_INI_SIZE => 'File terlalu besar (melebihi batas server).',
                UPLOAD_ERR_FORM_SIZE => 'File terlalu besar.',
                UPLOAD_ERR_PARTIAL => 'File hanya terunggah sebagian.',
                UPLOAD_ERR_NO_FILE => 'Tidak ada file yang diunggah.',
                UPLOAD_ERR_NO_TMP_DIR => 'Folder temporary tidak ditemukan.',
                UPLOAD_ERR_CANT_WRITE => 'Gagal menulis file.',
                UPLOAD_ERR_EXTENSION => 'Upload dihentikan oleh ekstensi.'
            ];
            return ['success' => false, 'message' => $errors[$file['error']] ?? 'Error upload tidak diketahui.'];
        }

        if ($file['size'] > MAX_FILE_SIZE) {
            return ['success' => false, 'message' => 'Ukuran file melebihi batas maksimum (5MB).'];
        }

        return ['success' => true];
    }

    /**
     * Generate random filename
     */
    private function generateRandomFilename() {
        return 'img_' . bin2hex(random_bytes(8)) . '_' . time();
    }

    /**
     * Load image from file
     */
    private function loadImage($path, $mimeType) {
        switch ($mimeType) {
            case 'image/jpeg':
                return imagecreatefromjpeg($path);
            case 'image/png':
                return imagecreatefrompng($path);
            case 'image/gif':
                return imagecreatefromgif($path);
            case 'image/webp':
                return imagecreatefromwebp($path);
            default:
                return false;
        }
    }

    /**
     * Resize image if larger than max width
     */
    private function resizeImage($image, $width, $height) {
        if ($width <= $this->maxWidth) {
            return $image;
        }

        $ratio = $this->maxWidth / $width;
        $newWidth = $this->maxWidth;
        $newHeight = (int)($height * $ratio);

        $resized = imagecreatetruecolor($newWidth, $newHeight);
        
        // Preserve transparency for PNG
        imagealphablending($resized, false);
        imagesavealpha($resized, true);
        $transparent = imagecolorallocatealpha($resized, 255, 255, 255, 127);
        imagefilledrectangle($resized, 0, 0, $newWidth, $newHeight, $transparent);

        imagecopyresampled($resized, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        imagedestroy($image);

        return $resized;
    }

    /**
     * Save in original format
     */
    private function saveOriginalFormat($image, $path, $mimeType) {
        switch ($mimeType) {
            case 'image/jpeg':
                imagejpeg($image, $path, 85);
                break;
            case 'image/png':
                imagepng($image, $path, 8);
                break;
            case 'image/gif':
                imagegif($image, $path);
                break;
        }
    }

    /**
     * Get extension from MIME type
     */
    private function getExtensionFromMime($mimeType) {
        $map = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp'
        ];
        return $map[$mimeType] ?? 'jpg';
    }

    /**
     * Delete image files
     */
    public function deleteImage($filename) {
        $baseName = pathinfo($filename, PATHINFO_FILENAME);
        $extensions = ['webp', 'jpg', 'png', 'gif'];
        
        foreach ($extensions as $ext) {
            $path = $this->uploadDir . $baseName . '.' . $ext;
            if (file_exists($path)) {
                unlink($path);
            }
        }
        
        return true;
    }
}
