<?php

declare(strict_types=1);

namespace App\Services;

use App\Imports\ImportExcel;
use Illuminate\Http\UploadedFile;
use InvalidArgumentException;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\HeadingRowImport;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;

class ImportExcelService
{
    /**
     * @param  string  $format  Valid values: 'none', 'slug'
     */
    public static function getHeadingRowArray(UploadedFile $file, string $format = 'none'): array
    {
        if (! in_array($format, ['none', 'slug'])) {
            throw new InvalidArgumentException("Invalid format. Allowed values: 'none', 'slug'", 400);
        }

        HeadingRowFormatter::default($format);

        $header = (new HeadingRowImport())->toArray($file);

        return static::trimArrayKeyValueRecursive(
            static::removeEmptyRowValues(
                static::flattenArray($header)
            )
        );
    }

    public static function getRowsArray(UploadedFile $file): array
    {
        $rows = Excel::toArray((new ImportExcel()), $file);

        return static::trimArrayKeyValueRecursive(
            static::removeNullRowValues($rows[0] ?? [])
        );
    }

    public static function getTagsFromHeadingRow(array $array): array
    {
        return array_map(fn ($item): ?string => mb_strtolower((string) preg_replace(['/[\s\p{P}]+/u'], '', (string) $item)), $array);
    }

    protected static function removeNullRowValues(array $inputArray): array
    {
        return array_map(fn ($item): ?array => array_filter(
            json_decode(json_encode($item), true), fn ($value): bool => $value !== null), $inputArray
        );
    }

    protected static function removeEmptyRowValues(array $inputArray): array
    {
        return array_values(array_filter($inputArray, fn ($key, $value): bool => $key !== $value, ARRAY_FILTER_USE_BOTH));
    }

    protected static function flattenArray(array $array): array
    {
        $resultArr = [];

        array_walk_recursive($array, function ($value) use (&$resultArr): void {
            $resultArr[] = $value;
        });

        return array_values($resultArr);
    }

    protected static function trimArrayKeyValueRecursive(array $inputArray): array
    {
        $result = [];
        foreach ($inputArray as $key => $value) {
            $newKey = is_string($key) ? mb_trim($key) : $key;
            $newValue = is_string($value)
                ? mb_trim($value)
                : (is_iterable($value) ? static::trimArrayKeyValueRecursive($value) : $value);
            $result[$newKey] = $newValue;
        }

        return $result;
    }
}
