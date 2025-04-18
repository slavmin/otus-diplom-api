<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\DocumentTemplate;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\DocumentTemplate\StoreDocumentTemplateRequest;
use App\Http\Resources\User\DocumentTemplate\DocumentTemplateCollection;
use App\Http\Resources\User\DocumentTemplate\DocumentTemplateResource;
use App\Models\DocumentTemplate;
use App\Services\DocumentTemplateService;
use App\Services\UploadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class DocumentTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $documentTemplates = auth()->user()?->documentTemplates()->get() ?? [];

        return response()->json(new DocumentTemplateCollection($documentTemplates));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @throws ValidationException
     */
    public function store(StoreDocumentTemplateRequest $request): JsonResponse
    {
        $templateData = DocumentTemplateService::handleData($request->validated(), $request->user(), $request->file('file'));

        $documentTemplate = $request->user()->documentTemplates()->create($templateData);

        return response()->json(['data' => new DocumentTemplateResource($documentTemplate)]);
    }

    /**
     * Display the specified resource.
     */
    public function show(DocumentTemplate $template): JsonResponse
    {
        $documentTemplate = auth()->user()?->documentTemplates()->findOrFail($template->getKey());

        return response()->json(['data' => new DocumentTemplateResource($documentTemplate)]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @throws ValidationException
     */
    public function update(StoreDocumentTemplateRequest $request, DocumentTemplate $template): JsonResponse
    {
        $templateData = $templateData = DocumentTemplateService::handleData($request->validated(), $request->user(), $request->file('file'), $template);

        auth()->user()?->documentTemplates()->where('id', $template->getKey())->update($templateData);

        return response()->json(['data' => new DocumentTemplateResource($template->fresh())]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DocumentTemplate $template, UploadService $uploadService): JsonResponse
    {
        $documentTemplate = auth()->user()?->documentTemplates()->findOrFail($template->getKey());

        $uploadService::delete($documentTemplate->getAttribute('file_path'));

        $documentTemplate->delete();

        return response()->json(['data' => 'Deleted successfully']);
    }
}
