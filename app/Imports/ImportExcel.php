<?php

declare(strict_types=1);

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ImportExcel implements SkipsEmptyRows, ToArray, ToCollection, WithHeadingRow
{
    use SkipsErrors;

    public function collection(Collection $collection): Collection
    {
        return $collection;
    }

    public function array(array $array): array
    {
        return $array;
    }
}
