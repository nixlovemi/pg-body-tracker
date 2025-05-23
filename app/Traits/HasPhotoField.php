<?php

namespace App\Traits;

use Illuminate\Support\Facades\File;
use Illuminate\Http\UploadedFile;
use Intervention\Image\Facades\Image;
use App\Helpers\SysUtils;

trait HasPhotoField {
    public function getPhotoBase64(string $fieldName, ?string $defaultImage=null): ?string
    {
        $filePath = $this->{$fieldName};
        if (null === $filePath) {
            $filePath = $defaultImage;
        }

        return SysUtils::getImageBase64($filePath);
    }

    final public function getBase64String(string $filePath): string
    {
        return SysUtils::getBase64String($filePath);
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
        return SysUtils::getOsPhotosFolder($basePath);
    }

    public static function fGetDbPhotosFolder(string $basePhotoFolder): string
    {
        return 'storage' . $basePhotoFolder;
    }
}
