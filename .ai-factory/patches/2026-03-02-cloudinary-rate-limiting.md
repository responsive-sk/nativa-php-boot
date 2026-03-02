# Cloudinary API Rate Limiting Fix

**Date:** 2026-03-02
**Files:** `src/Infrastructure/Storage/Providers/CloudinaryProvider.php`
**Severity:** high

## Problem

The application was receiving "Too Many Requests" (HTTP 429) errors from Cloudinary's API when uploading or deleting media files. The original implementation had:

1. **No retry logic** - Requests failed immediately on rate limit
2. **No timeout configuration** - Could hang indefinitely on slow connections
3. **No logging** - No visibility into API failures or rate limiting
4. **No exponential backoff** - Would hammer the API even when rate limited

This caused uploads to fail permanently when hitting Cloudinary's rate limits, especially during bulk operations or high-traffic periods.

## Root Cause

The `CloudinaryProvider` class made cURL requests to Cloudinary's API without any rate limit handling. When Cloudinary returned HTTP 429 (rate limit exceeded), the code treated it as a permanent failure and threw an exception immediately.

Cloudinary's free tier has relatively low rate limits (varies by plan, typically ~10-50 requests/minute), and the application had no mechanism to handle temporary throttling.

## Solution

Implemented **exponential backoff with retry logic** specifically for 429 responses:

### Key Changes

1. **Retry Mechanism** (`executeWithRetry()` method)
   - Maximum 3 retry attempts
   - Only retries on HTTP 429 (rate limit) errors
   - Other errors fail immediately

2. **Exponential Backoff** (`calculateBackoff()` method)
   - Attempt 1: 1 second delay
   - Attempt 2: 2 seconds delay
   - Attempt 3: 4 seconds delay
   - Respects `Retry-After` header if provided by Cloudinary

3. **Timeout Configuration**
   - Connection timeout: 30 seconds
   - Total request timeout: 60 seconds
   - Prevents hanging on slow/unresponsive connections

4. **Logging** (all logs prefixed with `[FIX]`)
   - Logs each attempt number
   - Logs rate limit hits and retry delays
   - Logs final success or failure
   - Logs cURL errors for debugging

5. **Type Safety Improvements**
   - Added explicit type checks for file uploads
   - Added type casting for response data
   - Improved return type declarations

### Code Example

```php
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

        if ($result['httpCode'] === 429) {
            $retryAfter = $this->getRetryAfter($result['response']);
            $delayMs = $this->calculateBackoff($attempt, $retryAfter);

            error_log(sprintf(
                '[FIX] Cloudinary rate limit hit (429). Retrying after %d ms...',
                $delayMs
            ));

            usleep($delayMs * 1000);
            continue;
        }

        if ($result['httpCode'] === 200 && $result['response']) {
            $decoded = json_decode($result['response'], true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        break;
    }

    throw new RuntimeException($lastError ?? 'Cloudinary API request failed');
}
```

## Prevention

How to prevent this class of problems in the future:

1. **Always implement retry logic for external APIs** - Any service making HTTP requests to third-party APIs should handle rate limits gracefully

2. **Use exponential backoff** - Don't retry immediately; increase delay between attempts

3. **Respect Retry-After headers** - APIs often specify when to retry; honor their limits

4. **Add logging to all external calls** - Critical for debugging production issues

5. **Configure timeouts** - Always set connection and request timeouts

6. **Consider request queuing** - For bulk operations, queue requests and process them with rate limiting

7. **Monitor API usage** - Track API call frequency to anticipate rate limits before hitting them

## Testing

To test this fix:

1. Upload multiple files in rapid succession through the admin media library
2. Check error logs for `[FIX]` prefixed messages
3. Verify uploads succeed even under high load
4. Monitor for rate limit messages in logs

Example log output when rate limited:
```
[FIX] Cloudinary API request attempt 1/3 to https://api.cloudinary.com/v1_1/...
[FIX] Cloudinary rate limit hit (429). Retrying after 1000 ms...
[FIX] Cloudinary API request attempt 2/3 to https://api.cloudinary.com/v1_1/...
[FIX] Cloudinary API request successful (attempt 2)
```

## Tags

`#rate-limiting` `#cloudinary` `#api` `#retry-logic` `#exponential-backoff` `#curl` `#php`
