<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\User;
use App\Notifications\CreatedFromTemplateNotification;
use App\Services\WordTemplateProcessService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Attributes\WithoutRelations;
use Illuminate\Queue\Middleware\Skip;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

class CreateDocumentFromTemplateJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        #[WithoutRelations]
        private readonly User $user,
        private readonly array $templateData,
        private readonly string $templateId,
    ) {
        //
    }

    /**
     * Get the middleware the job should pass through.
     */
    public function middleware(): array
    {
        return [
            Skip::when(fn (): bool => $this->shouldSkip()),
        ];
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $template = $this->user->documentTemplates()->find($this->templateId);

        $templateFilePath = Storage::disk($template->getAttribute('disk'))->path($template->getAttribute('file_path'));

        $createdDocuments = [];

        foreach ($this->templateData as $data) {
            $filePath = WordTemplateProcessService::process($templateFilePath, $data);

            $docName = $template->getAttribute('name').(array_key_exists('ФИО', $data) ? '_'.$data['ФИО'] : '');

            $createdDocuments[$filePath] = [
                'as' => $docName.'_'.date('Y-m-d H:i:s').'.pdf',
                'mime' => 'application/pdf',
            ];
        }

        $this->user->notify(new CreatedFromTemplateNotification($createdDocuments));

        foreach (array_keys($createdDocuments) as $file) {
            unlink($file);
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(?Throwable $exception): void
    {
        Log::error($exception->getMessage(), [
            'file' => $exception->getFile(),
            'message' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);

        $this->delete();
    }

    private function shouldSkip(): bool
    {
        return $this->user->documentTemplates()->find($this->templateId) === null;
    }
}
