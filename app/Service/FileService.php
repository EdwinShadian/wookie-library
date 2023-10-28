<?php

declare(strict_types=1);

namespace App\Service;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FileService
{
    private const MIME_TYPES_TO_DIR_MAP = [
        'image/jpeg' => 'images',
        'image/png' => 'images',
    ];

    public function upload(UploadedFile $file): string
    {
        $filePath = Storage::disk(env('FILESYSTEM_DISK'))
            ->putFile(self::MIME_TYPES_TO_DIR_MAP[$file->getMimeType()], $file);

        return Storage::disk(env('FILESYSTEM_DISK'))->url($filePath);
    }
}
