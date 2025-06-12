<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use App\Models\Image;
use App\Models\News;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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
    public function getByTableAndId($table, int $id): Image
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
    public function uploadImage($file, $folder, $table, $id): Image
    {
        $tableData = [
            'parentable_type' => $table,
            'parentable_id' => $id
        ];

        // Upload the file to storage
        $imageData = self::uploadFile($file, $folder);
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
    public function updateImage($file, $folder, $table, $id)
    {
        // Get an image instance
        $image = $this->getByTableAndId($table, $id);

        if (!$image) {
            return null;
        }

        if (!$file) {
            return $image;
        }

        // Remove old file ftom storage
        self::removeFile($image->image_url);

        // Upload the file to storage
        $imageData = self::uploadFile($file, $folder);
        // Update an image data
        $image->update($imageData);
        return $image;
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
            self::removeFile($image->image_url);
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
    public static function uploadFile($file, $folder): array
    {
        $data = [];
        $folderPath = 'image/' . $folder . '/';

        if ($file) {
            // Generate a unique filename
            $filename = self::generateFilename($file);
            $location = Storage::disk('public')->path($folderPath . $filename);

            // Ensure the folder exists
            if (!Storage::disk('public')->exists($folderPath)) {
                Storage::disk('public')->makeDirectory($folderPath);
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
    public static function removeFile($imageUrl): void
    {
        if (Storage::disk('public')->exists($imageUrl)) {
            Storage::disk('public')->delete($imageUrl);
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
}
