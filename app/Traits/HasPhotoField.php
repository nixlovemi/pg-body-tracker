<?php

namespace App\Traits;

use Illuminate\Support\Facades\File;
use Illuminate\Http\UploadedFile;
use Image;

trait HasPhotoField {
    public function getPhotoBase64(string $fieldName, ?string $defaultImage=null): ?string
    {
        $filePath = $this->{$fieldName};
        if (null === $filePath) {
            $filePath = $defaultImage;
        }

        // check if file exists
        if (@file_exists($filePath)) {
            return $this->getBase64String($filePath);
        }

        // check if file exists in public
        $publicPath = str_replace('app' . DIRECTORY_SEPARATOR, '', public_path(self::fGetOsPhotosFolder($filePath)));
        if (file_exists($publicPath)) {
            return $this->getBase64String($publicPath);
        }

        // check if file exists in storage
        $idx = strpos($filePath, 'storage/');
        $publicPath = substr($filePath, $idx + strlen('storage/'));
        $publicPath = storage_path(self::fGetOsPhotosFolder(DIRECTORY_SEPARATOR . $publicPath));
        if (File::exists($publicPath)) {
            return $this->getBase64String($publicPath);
        }

        return null;
    }

    final public function getBase64String(string $filePath): string
    {
        $mimeType = mime_content_type($filePath);
        $base64 = base64_encode(file_get_contents($filePath));
        return "data:$mimeType;base64,$base64";
    }

    final public function setPhotoUrl(
        string $field,
        ?UploadedFile $file,
        string $basePhotoFolder,
        int $fitSize,
        string $fileNameWoExt,
    ): void {
        // check if file is null
        if (null === $file) {
            return;
        }

        // check folder
        $destinationPath = storage_path(self::fGetOsPhotosFolder($basePhotoFolder));
        if (!File::exists($destinationPath)) {
            File::makeDirectory($destinationPath, 0755, true);
        }

        // check if $this->{$field} has value, if yes, use remove method
        if ($this->{$field}) {
            $this->removePhotoUrl($field, $basePhotoFolder);
        }

        // save image
        $newFileName = $fileNameWoExt . '.' . $file->extension();
        $saveFilePath = $destinationPath  . DIRECTORY_SEPARATOR  . $newFileName;

        $img = Image::make($file->path());
        $retSave = $img->fit($fitSize)->save($saveFilePath);
        if ($retSave) {
            $this->{$field} = self::fGetDbPhotosFolder($basePhotoFolder) . $newFileName;
            $this->save();
        }
    }

    final public function removePhotoUrl(string $field, string $basePhotoFolder): void
    {
        // remove file
        $fileName = basename($this->{$field});
        $filePath = storage_path(self::fGetOsPhotosFolder($basePhotoFolder) . DIRECTORY_SEPARATOR . $fileName);
        if (File::exists($filePath)) {
            File::delete($filePath);
        }

        // update field
        $this->{$field} = null;
        $this->save();
    }

    public static function fGetOsPhotosFolder(string $basePath): string
    {
        $basePath = str_replace('/', DIRECTORY_SEPARATOR, $basePath);
        return env('APP_PREFIX_FOLDER').$basePath;
    }

    public static function fGetDbPhotosFolder(string $basePhotoFolder): string
    {
        return 'storage' . $basePhotoFolder;
    }
}
