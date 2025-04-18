<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;
use PhpOffice\PhpWord\Exception\CopyFileException;
use PhpOffice\PhpWord\Exception\CreateTemporaryFileException;
use PhpOffice\PhpWord\Exception\Exception;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Settings;
use PhpOffice\PhpWord\TemplateProcessor;
use Random\RandomException;

class WordTemplateProcessService
{
    protected static string $macroOpeningChars = '{{';

    protected static string $macroClosingChars = '}}';

    /**
     * @throws CopyFileException
     * @throws CreateTemporaryFileException
     * @throws Exception
     * @throws RandomException
     */
    public static function process(string $templatePath, array $replacementData): string
    {
        if (! isset($templatePath) || ($templatePath === '' || $templatePath === '0') || $replacementData === []) {
            throw new InvalidArgumentException('Не получены файл шаблона или данные для шаблона', 400);
        }

        $templateProcessor = static::getTemplateProcessor($templatePath);

        foreach ($replacementData as $key => $value) {
            $templateProcessor->setValue(static::$macroOpeningChars.$key.static::$macroClosingChars, $value);
        }

        $tempFile = tempnam(sys_get_temp_dir(), 'phpword_').'.docx';
        $templateProcessor->saveAs($tempFile);

        $phpWordContent = IOFactory::createReader()->load($tempFile);

        unlink($tempFile);

        return static::saveAsPdf($phpWordContent);
    }

    /**
     * @throws RandomException
     * @throws Exception
     */
    protected static function saveAsPdf(PhpWord $phpWordContent): string
    {
        $outputPdfPath = 'temp/'.bin2hex(random_bytes(8)).'.pdf';
        $savedFilePath = Storage::path($outputPdfPath);
        Storage::makeDirectory(dirname($outputPdfPath));

        $pdfWriter = IOFactory::createWriter($phpWordContent, 'PDF');
        $pdfWriter->save($savedFilePath);

        return $savedFilePath;
    }

    /**
     * @throws CopyFileException
     * @throws CreateTemporaryFileException
     */
    protected static function getTemplateProcessor(string $templatePath): TemplateProcessor
    {
        $rendererLibraryPath = base_path('/vendor/mpdf/mpdf/src/Mpdf.php');
        Settings::setPdfRenderer(Settings::PDF_RENDERER_MPDF, $rendererLibraryPath);

        return new TemplateProcessor($templatePath);
    }
}
