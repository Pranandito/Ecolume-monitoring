<?php

namespace App\Services;

use Google\Client;
use Google\Service\Drive;
use Google\Service\Drive\DriveFile;

class GoogleDriveService
{
    protected Drive $driveService;

    public function __construct()
    {
        $client = new Client();
        $client->setAuthConfig(config('services.google_drive.credentials_path'));
        $client->addScope(Drive::DRIVE);
        $this->driveService = new Drive($client);
    }

    // Upload file ke folder tertentu
    public function uploadFile(string $localPath, string $folderId, ?string $fileName = null): DriveFile
    {
        $fileMetadata = new DriveFile([
            'name' => $fileName ?? basename($localPath),
            'parents' => [$folderId],
        ]);

        return $this->driveService->files->create($fileMetadata, [
            'data' => file_get_contents($localPath),
            'mimeType' => mime_content_type($localPath),
            'uploadType' => 'multipart',
            'fields' => 'id, name, webViewLink, size',
            'supportsAllDrives' => true, // jaga-jaga kalau folder ada di Shared Drive
        ]);
    }

    public function getFolderInfo(string $folderId)
    {
        return $this->driveService->files->get($folderId, [
            'fields' => 'id, name, owners, permissions(id,type,role,emailAddress,displayName)',
            'supportsAllDrives' => true,
        ]);
    }
}
