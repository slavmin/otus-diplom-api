<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class UploadService
{
    public const string DISK_NAME = 'local';

    protected const string PATH_PREFIX = 'uploads';

    /**
     * Загружает файл в указанное хранилище
     */
    public static function upload(UploadedFile $file, string $path = '', string $disk = self::DISK_NAME): string|false
    {
        return $file->store(self::PATH_PREFIX.DIRECTORY_SEPARATOR.$path, ['disk' => $disk]);
    }

    /**
     * Получает содержимое файла
     */
    public static function get(string $path, string $disk = self::DISK_NAME): ?string
    {
        if (! Storage::disk($disk)->exists($path)) {
            return null;
        }

        return Storage::disk($disk)->get($path);
    }

    /**
     * Скачивает файл
     */
    public static function download(string $path, ?string $name = null, string $disk = self::DISK_NAME): StreamedResponse
    {
        if ($name === null || $name === '' || $name === '0') {
            $name = basename($path);
        }

        return Storage::disk($disk)->download($path, $name);
    }

    /**
     * Обновляет файл
     */
    public static function update(string $oldPath, UploadedFile $newFile, string $newPath = '', string $disk = self::DISK_NAME): string|false
    {
        static::delete($oldPath, $disk);

        return static::upload($newFile, $newPath, $disk);
    }

    /**
     * Удаляет файл
     */
    public static function delete(string $path, string $disk = self::DISK_NAME): bool
    {
        if (! Storage::disk($disk)->exists($path)) {
            return false;
        }

        return Storage::disk($disk)->delete($path);
    }

    /**
     * Проверяет существование файла
     */
    public static function exists(string $path, string $disk = self::DISK_NAME): bool
    {
        return Storage::disk($disk)->exists($path);
    }

    /**
     * Возвращает URL файла
     */
    public static function getUrl(string $path, string $disk = self::DISK_NAME): ?string
    {
        if (! Storage::disk($disk)->exists($path)) {
            return null;
        }

        return Storage::disk($disk)->url($path);
    }

    /**
     * Возвращает PATH файла
     */
    public static function getPath(string $path, string $disk = self::DISK_NAME): ?string
    {
        if (! Storage::disk($disk)->exists($path)) {
            return null;
        }

        return Storage::disk($disk)->path($path);
    }
}
