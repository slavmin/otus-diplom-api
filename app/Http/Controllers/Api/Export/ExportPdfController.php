<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Export;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Export\ExportPdfRequest;
use App\Jobs\CreateDocumentFromTemplateJob;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class ExportPdfController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @throws Exception
     */
    public function __invoke(ExportPdfRequest $request): JsonResponse
    {
        $cachedData = Cache::get($request->input('key'), []);

        if (empty($cachedData)) {
            abort(404, 'Not found');
        }

        $cachedRows = data_get($cachedData, 'rows');

        CreateDocumentFromTemplateJob::dispatchAfterResponse(auth()->user(), $cachedRows, $request->input('template_id'));

        Cache::delete($request->input('key'));

        return response()->json(['data' => 'Dispatched to queue.']);
    }
}
