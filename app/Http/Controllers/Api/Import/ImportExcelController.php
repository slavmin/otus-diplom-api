<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Import;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Import\ImportExcelRequest;
use App\Services\ImportExcelService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class ImportExcelController extends Controller
{
    protected const int CACHE_KEY_TTL = 60 * 5;

    /**
     * Handle the incoming request.
     */
    public function __invoke(ImportExcelRequest $request): JsonResponse
    {
        $uploadedFile = $request->file('file');

        $headerArr = ImportExcelService::getHeadingRowArray($uploadedFile);
        $bodyArr = ImportExcelService::getRowsArray($uploadedFile);

        $cacheKey = Str::uuid()->toString();

        $importedData = [
            'header' => $headerArr,
            'rows' => $bodyArr,
        ];

        // Сохраняем в кэш
        Cache::put($cacheKey, $importedData, self::CACHE_KEY_TTL);

        return response()->json([
            'data' => [
                'key' => $cacheKey,
                ...$importedData,
            ],
        ]);
    }
}
