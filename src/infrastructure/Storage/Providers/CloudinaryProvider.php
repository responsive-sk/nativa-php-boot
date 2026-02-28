<?php

declare(strict_types=1);

namespace Infrastructure\Storage\Providers;

use RuntimeException;

/**
 * Cloudinary Provider
 * Stores files on Cloudinary cloud storage
 * 
 * Requires: composer require cloudinary/cloudinary_php
 */
final class CloudinaryProvider implements MediaProviderInterface
{
    private string $cloudName;
    private string $apiKey;
    private string $apiSecret;
    private string $uploadPreset;

    public function __construct(
        ?string $cloudName = null,
        ?string $apiKey = null,
        ?string $apiSecret = null,
        ?string $uploadPreset = null
    ) {
        $this->cloudName = $cloudName ?? $_ENV['CLOUDINARY_CLOUD_NAME'] ?? '';
        $this->apiKey = $apiKey ?? $_ENV['CLOUDINARY_API_KEY'] ?? '';
        $this->apiSecret = $apiSecret ?? $_ENV['CLOUDINARY_API_SECRET'] ?? '';
        $this->uploadPreset = $uploadPreset ?? $_ENV['CLOUDINARY_UPLOAD_PRESET'] ?? '';

        if (empty($this->cloudName)) {
            throw new RuntimeException('Cloudinary cloud name not configured');
        }
    }

    public function upload(array $file): array
    {
        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            throw new RuntimeException('Invalid file upload');
        }

        // Validate file
        $this->validateFile($file);

        // Upload to Cloudinary API
        $uploadUrl = "https://api.cloudinary.com/v1_1/{$this->cloudName}/auto/upload";
        
        $postData = [
            'file' => new \CURLFile($file['tmp_name'], $file['type'], $file['name']),
            'upload_preset' => $this->uploadPreset,
            'folder' => 'php_cms/' . date('Y/m/d'),
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $uploadUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, "{$this->apiKey}:{$this->apiSecret}");

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200 || !$response) {
            throw new RuntimeException('Cloudinary upload failed');
        }

        $result = json_decode($response, true);

        if (!isset($result['secure_url']) || !isset($result['public_id'])) {
            throw new RuntimeException('Invalid Cloudinary response');
        }

        return [
            'path' => $result['public_id'],
            'url' => $result['secure_url'],
            'size' => (int) ($result['bytes'] ?? $file['size']),
            'mime_type' => $result['format'] ?? $file['type'],
            'original_name' => $file['name'],
            'cloudinary_id' => $result['public_id'],
        ];
    }

    public function delete(string $path): bool
    {
        $deleteUrl = "https://api.cloudinary.com/v1_1/{$this->cloudName}/image/destroy";
        
        $postData = [
            'public_id' => $path,
            'invalidate' => true,
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $deleteUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, "{$this->apiKey}:{$this->apiSecret}");

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            return false;
        }

        $result = json_decode($response, true);
        return ($result['result'] ?? '') === 'ok';
    }

    public function getUrl(string $path): string
    {
        return "https://res.cloudinary.com/{$this->cloudName}/image/upload/{$path}";
    }

    public function getSize(string $path): int
    {
        // Cloudinary API call to get file info would go here
        // For now, return 0 as we'd need to make an API call
        return 0;
    }

    public function exists(string $path): bool
    {
        // Would need to make API call to check existence
        // For simplicity, assume it exists
        return true;
    }

    public function getName(): string
    {
        return 'cloudinary';
    }

    /**
     * Validate uploaded file
     */
    private function validateFile(array $file): void
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new RuntimeException('Upload error code: ' . $file['error']);
        }

        // Cloudinary supports many formats, but let's set reasonable limits
        $maxSize = 50 * 1024 * 1024; // 50MB for Cloudinary
        if ($file['size'] > $maxSize) {
            throw new RuntimeException('File size exceeds maximum allowed size (50MB)');
        }
    }
}
