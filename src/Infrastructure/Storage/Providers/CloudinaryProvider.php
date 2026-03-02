<?php

declare(strict_types=1);

namespace Infrastructure\Storage\Providers;

use RuntimeException;

/**
 * Cloudinary Provider
 * Stores files on Cloudinary cloud storage
 *
 * Requires: composer require cloudinary/cloudinary_php
 *
 * Rate Limiting:
 * - Implements exponential backoff for 429 errors
 * - Maximum 3 retries with increasing delays (1s, 2s, 4s)
 * - Configurable timeouts (default: 30s connect, 60s total)
 */
final class CloudinaryProvider implements MediaProviderInterface
{
    private string $cloudName;
    private string $apiKey;
    private string $apiSecret;
    private string $uploadPreset;

    /** @var int Maximum retry attempts for rate-limited requests */
    private const MAX_RETRIES = 3;

    /** @var int Base delay in milliseconds for exponential backoff */
    private const BASE_DELAY_MS = 1000;

    /** @var int Connection timeout in seconds */
    private const CONNECT_TIMEOUT = 30;

    /** @var int Total request timeout in seconds */
    private const TIMEOUT = 60;

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

    /**
     * @param array<array-key, mixed> $file
     * @return array{path: string, url: string, size: int, mime_type: string}
     */
    #[\Override]
    public function upload(array $file): array
    {
        if (!isset($file['tmp_name']) || !is_string($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            throw new RuntimeException('Invalid file upload');
        }

        // Validate file
        $this->validateFile($file);

        // Upload to Cloudinary API
        $uploadUrl = "https://api.cloudinary.com/v1_1/{$this->cloudName}/auto/upload";

        $postData = [
            'file' => new \CURLFile($file['tmp_name'], (string) ($file['type'] ?? ''), (string) ($file['name'] ?? '')),
            'upload_preset' => $this->uploadPreset,
            'folder' => 'php_cms/' . date('Y/m/d'),
        ];

        // Execute request with retry logic for rate limiting
        $result = $this->executeWithRetry($uploadUrl, $postData);

        if (!isset($result['secure_url']) || !isset($result['public_id'])) {
            throw new RuntimeException('Invalid Cloudinary response');
        }

        return [
            'path' => (string) $result['public_id'],
            'url' => (string) $result['secure_url'],
            'size' => (int) ($result['bytes'] ?? $file['size']),
            'mime_type' => (string) ($result['format'] ?? $file['type']),
        ];
    }

    /**
     * Execute cURL request with exponential backoff retry for 429 errors
     *
     * @param array<string, mixed> $postData
     * @return array<string, mixed> Decoded JSON response
     * @throws RuntimeException if all retries fail
     */
    private function executeWithRetry(string $url, array $postData): array
    {
        $attempt = 0;
        $lastError = null;

        while ($attempt < self::MAX_RETRIES) {
            $attempt++;

            error_log(sprintf(
                '[FIX] Cloudinary API request attempt %d/%d to %s',
                $attempt,
                self::MAX_RETRIES,
                $url
            ));

            $result = $this->executeCurl($url, $postData);

            // Check for rate limit (429)
            if ($result['httpCode'] === 429) {
                $retryAfter = $this->getRetryAfter($result['response']);
                $delayMs = $this->calculateBackoff($attempt, $retryAfter);

                error_log(sprintf(
                    '[FIX] Cloudinary rate limit hit (429). Retrying after %d ms...',
                    $delayMs
                ));

                usleep($delayMs * 1000);
                $lastError = 'Cloudinary API rate limit exceeded';
                continue;
            }

            // Check for success
            if ($result['httpCode'] === 200 && $result['response']) {
                $decoded = json_decode($result['response'], true);
                if (is_array($decoded)) {
                    error_log(sprintf(
                        '[FIX] Cloudinary API request successful (attempt %d)',
                        $attempt
                    ));
                    return $decoded;
                }
            }

            // Non-retryable error or invalid response
            $lastError = sprintf(
                'Cloudinary API request failed (HTTP %d)',
                $result['httpCode']
            );
            break;
        }

        error_log(sprintf(
            '[FIX] Cloudinary API request failed after %d attempts: %s',
            $attempt,
            $lastError ?? 'Unknown error'
        ));

        throw new RuntimeException($lastError ?? 'Cloudinary API request failed');
    }

    /**
     * Execute a single cURL request
     *
     * @param array<string, mixed> $postData
     * @return array{httpCode: int, response: string|null}
     */
    private function executeCurl(string $url, array $postData): array
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, "{$this->apiKey}:{$this->apiSecret}");

        // Timeout configuration
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, self::CONNECT_TIMEOUT);
        curl_setopt($ch, CURLOPT_TIMEOUT, self::TIMEOUT);

        // Follow redirects
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 5);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($response === false && !empty($curlError)) {
            error_log(sprintf(
                '[FIX] cURL error: %s',
                $curlError
            ));
        }

        return [
            'httpCode' => $httpCode,
            'response' => $response ?: null,
        ];
    }

    /**
     * Calculate exponential backoff delay
     *
     * @param int $attempt Current attempt number (1-based)
     * @param int|null $retryAfter Retry-After header value in seconds
     * @return int Delay in milliseconds
     */
    private function calculateBackoff(int $attempt, ?int $retryAfter): int
    {
        // Use Retry-After header if provided
        if ($retryAfter !== null && $retryAfter > 0) {
            return $retryAfter * 1000;
        }

        // Exponential backoff: base_delay * 2^(attempt-1)
        // Attempt 1: 1000ms, Attempt 2: 2000ms, Attempt 3: 4000ms
        return self::BASE_DELAY_MS * (2 ** ($attempt - 1));
    }

    /**
     * Extract Retry-After from response (header or body)
     *
     * @param string|null $response Response body
     * @return int|null Retry-After seconds, or null if not found
     */
    private function getRetryAfter(?string $response): ?int
    {
        if (!$response) {
            return null;
        }

        $data = json_decode($response, true);
        if (is_array($data) && isset($data['error']['retry_after'])) {
            return (int) $data['error']['retry_after'];
        }

        return null;
    }

    #[\Override]
    public function delete(string $path): bool
    {
        $deleteUrl = "https://api.cloudinary.com/v1_1/{$this->cloudName}/image/destroy";

        $postData = [
            'public_id' => $path,
            'invalidate' => true,
        ];

        try {
            $result = $this->executeWithRetry($deleteUrl, $postData);
            return ($result['result'] ?? '') === 'ok';
        } catch (RuntimeException $e) {
            error_log(sprintf(
                '[FIX] Cloudinary delete failed for %s: %s',
                $path,
                $e->getMessage()
            ));
            return false;
        }
    }

    #[\Override]
    public function getUrl(string $path): string
    {
        return "https://res.cloudinary.com/{$this->cloudName}/image/upload/{$path}";
    }

    #[\Override]
    public function getSize(string $path): int
    {
        // Cloudinary API call to get file info would go here
        // For now, return 0 as we'd need to make an API call
        return 0;
    }

    #[\Override]
    public function exists(string $path): bool
    {
        // Would need to make API call to check existence
        // For simplicity, assume it exists
        return true;
    }

    #[\Override]
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
