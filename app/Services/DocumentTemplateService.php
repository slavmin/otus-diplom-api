<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\DocumentTemplate;
use App\Models\User;
use Illuminate\Http\UploadedFile;

class DocumentTemplateService
{
    /**
     * @return string[]
     */
    public static function handleData(array $inputData, User $user, ?UploadedFile $uploadedFile, ?DocumentTemplate $documentTemplate = null): array
    {
        if (! is_null($uploadedFile)) {
            $returnData = [
                'file_orig_name' => pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME),
                'file_ext' => $uploadedFile->getClientOriginalExtension(),
                'file_mime_type' => $uploadedFile->getClientMimeType(),
                'file_size' => $uploadedFile->getSize(),
                'disk' => UploadService::DISK_NAME,
            ];

            if (! is_null($documentTemplate)) {
                $returnData['file_path'] = UploadService::update($documentTemplate->getAttribute('file_path'), $uploadedFile, $user->getAttribute('uuid'));
            } else {
                $returnData['file_path'] = UploadService::upload($uploadedFile, $user->getAttribute('uuid'));
            }
        }

        $returnData['name'] = data_get($inputData, 'name');

        return collect($returnData)->except('id')->toArray();
    }
}
