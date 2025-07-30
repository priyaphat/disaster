<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use App\Models\Image;
use Illuminate\Container\Attributes\Log;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log as FacadesLog;
use Intervention\Image\Drivers\Gd\Driver;

class ImageService
{
    /**
     * Get an image by table name and attribute id
     *
     * @param class $table
     * @param int $id (Updated file request)
     * @return Image
     */
    public function getByTableAndId($table, int $id)
    {
        return Image::where('parentable_type', $table)->where('parentable_id', $id)->first();
    }

    /**
     * Upload an image function
     * @param mixed $file (Uploaded file or base64 string)
     * @param string $table
     * @param int $id (attribute id from model)
     * @return Image
     */
    public function uploadImage($file, $folder, $table, $id, $diskName = Image::PUBLIC): Image
    {
        $tableData = [
            'image_type' => $diskName,
            'parentable_type' => $table,
            'parentable_id' => $id
        ];

        // Upload the file to storage
        $imageData = self::uploadFile($file, $folder, $diskName);
        // Create an image to database
        $data = $tableData + $imageData;
        $image = Image::create($data);
        return $image;
    }

    /**
     * Upload an image function
     * @param mixed $file (Uploaded file or base64 string)
     * @param string $table
     * @param int $id (attribute id from model)
     * @return Image
     */
    public function updateImage($file, $folder, $table, $id, $diskName = Image::PUBLIC): Image
    {
        // Get an image instance
        $image = $this->getByTableAndId($table, $id);

        if (!$image) {
            return self::uploadImage($file, $folder, $table, $id, $diskName);
        }

        if (!$file || !$file instanceof UploadedFile) {
            return $image;
        }

        // Remove old file ftom storage
        self::removeFile($image->image_url, $image->image_type);

        // Upload the file to storage
        $imageData = self::uploadFile($file, $folder, $image->image_type);
        // Update an image data
        $image->update($imageData);
        return $image;
    }

    /**
     * Delete an existing image.
     *
     * @param string $folder
     * @param int $id
     * @return Image
     */
    public function updateToDefault($folder, $table, $id): Image
    {
        $folderPath = 'image/' . $folder . '/';
        $image = $this->getByTableAndId($table, $id);

        if ($image->image_name !== 'default.png') {
            self::removeFile($image->image_url, $image->image_type);
        }

        $image->update([
            'image_name' => 'default.png',
            'image_url' => $folderPath . 'default.png',
        ]);

        return $image->refresh();
    }

    /**
     * Delete an existing image.
     *
     * @param string $folder
     * @param int $id
     * @return bool
     */
    public function deleteImage($table, $id): bool
    {
        $image = $this->getByTableAndId($table, $id);

        if (!$image) {
            return false;
        }

        if ($image->image_name !== 'default.png') {
            self::removeFile($image->image_url, $image->image_type);
        }

        return $image->delete();
    }

    /**
     * Upload an image, resize it, and save its details in the database.
     *
     * @param mixed $file (Uploaded file or base64 string)
     * @param string $folder
     * @return array $data (Image detail : image_name and image_url)
     */
    public static function uploadFile($file, $folder, $diskName = Image::PUBLIC): array
    {
        $data = [];
        $folderPath = 'image/' . $folder . '/';

        if ($file) {
            // Generate a unique filename
            $filename = self::generateFilename($file);
            $location = Storage::disk($diskName)->path($folderPath . $filename);

            // Ensure the folder exists
            if (!Storage::disk($diskName)->exists($folderPath)) {
                Storage::disk($diskName)->makeDirectory($folderPath);
            }

            // Save the file (either base64 or uploaded)
            self::saveFile($file, $location);

            // Resize the image after saving
            self::resizeImage($location);

            $data['image_name'] = $filename;
            $data['image_url'] = $folderPath . $filename;
        } else {
            $data['image_name'] = 'default.png';
            $data['image_url'] = $folderPath . 'default.png';
        }

        return $data;
    }

    /**
     * Remove an image from storage.
     *
     * @param string $imageUrl
     */
    public static function removeFile($imageUrl, $diskName = Image::PUBLIC): void
    {
        if (Storage::disk($diskName)->exists($imageUrl)) {
            Storage::disk($diskName)->delete($imageUrl);
        }
    }

    /**
     * Resize an image using GD Library.
     *
     * @param string $filePath
     * @param int|null $width
     * @param int|null $height
     */
    private static function resizeImage($file, $width = null, $height = 400)
    {
        // create image manager with desired driver
        $manager = new ImageManager(new Driver());
        // read image from file system
        $image = $manager->read($file);
        // resize image proportionally to 300px width
        $image->scale(height: $height);

        return $image;
    }

    /**
     * Check if a string is base64 encoded.
     *
     * @param string $string
     * @return bool
     */
    private static function isBase64($string)
    {
        return strpos($string, 'data:image/') === 0 && strpos($string, ';base64,') !== false;
    }

    /**
     * Generate a unique filename for the uploaded image.
     *
     * @param mixed $file
     * @return string
     */
    private static function generateFilename($file)
    {
        $extension = self::isBase64($file) ? 'png' : $file->getClientOriginalExtension();
        return uniqid() . '.' . $extension;
    }

    /**
     * Save a file (either base64 or uploaded) to the specified location.
     *
     * @param mixed $file
     * @param string $location
     */
    private static function saveFile($file, $location)
    {
        if (self::isBase64($file)) {
            file_put_contents($location, base64_decode(explode(',', $file)[1]));
        } else {
            $file->move(dirname($location), basename($location));
        }
    }

    /**
     * When sending 'image' variable:
     * - If it's null -> do nothing
     * - If it's file -> update an image
     * When 'image' variable is not sentt:
     * - Update an image to default
     */
    public function handleImageUpdate($data, $folder, $table, $id, $diskName = Image::PUBLIC)
    {
        if (isset($data['image'])) {
            return $this->updateImage($data['image'], $folder, $table, $id, $diskName);
        }

        return $this->updateToDefault($folder, $table, $id);
    }
}
