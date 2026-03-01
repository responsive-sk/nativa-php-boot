<?php

declare(strict_types=1);

namespace Infrastructure\Paths\Filesystem;

/**
 * Exception thrown when filesystem operations fail
 */
class FilesystemException extends \RuntimeException
{
    public static function cannotReadFile(string $path, string $reason = ''): self
    {
        $message = "Cannot read file: {$path}";
        if ($reason) {
            $message .= " ({$reason})";
        }
        return new self($message);
    }

    public static function cannotWriteFile(string $path, string $reason = ''): self
    {
        $message = "Cannot write file: {$path}";
        if ($reason) {
            $message .= " ({$reason})";
        }
        return new self($message);
    }

    public static function cannotDeleteFile(string $path, string $reason = ''): self
    {
        $message = "Cannot delete file: {$path}";
        if ($reason) {
            $message .= " ({$reason})";
        }
        return new self($message);
    }

    public static function cannotCreateDirectory(string $path, string $reason = ''): self
    {
        $message = "Cannot create directory: {$path}";
        if ($reason) {
            $message .= " ({$reason})";
        }
        return new self($message);
    }

    public static function cannotListDirectory(string $path, string $reason = ''): self
    {
        $message = "Cannot list directory: {$path}";
        if ($reason) {
            $message .= " ({$reason})";
        }
        return new self($message);
    }

    public static function cannotCopyFile(string $source, string $destination, string $reason = ''): self
    {
        $message = "Cannot copy file from {$source} to {$destination}";
        if ($reason) {
            $message .= " ({$reason})";
        }
        return new self($message);
    }

    public static function cannotMoveFile(string $source, string $destination, string $reason = ''): self
    {
        $message = "Cannot move file from {$source} to {$destination}";
        if ($reason) {
            $message .= " ({$reason})";
        }
        return new self($message);
    }

    public static function cannotGetFileInfo(string $path, string $info, string $reason = ''): self
    {
        $message = "Cannot get {$info} for file: {$path}";
        if ($reason) {
            $message .= " ({$reason})";
        }
        return new self($message);
    }

    public static function cannotSetPermissions(string $path, string $reason = ''): self
    {
        $message = "Cannot set permissions for: {$path}";
        if ($reason) {
            $message .= " ({$reason})";
        }
        return new self($message);
    }
}
