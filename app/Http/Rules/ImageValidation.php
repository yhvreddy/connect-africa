<?php
namespace App\Http\Rules;

use Illuminate\Contracts\Validation\Rule;

class ImageValidation implements Rule
{
    public function passes($attribute, $value)
    {
        // Check if the file is an image
        if (!getimagesize($value)) {
            return false;
        }

        // Check if the file format is supported
        $allowedFormats = ['jpg', 'jpeg', 'png', 'svg'];
        $extension = strtolower($value->getClientOriginalExtension());
        if (!in_array($extension, $allowedFormats)) {
            return false;
        }

        // Check if the file size is less than or equal to 2MB
        // $maxSize = 2 * 1024 * 1024; // 2MB in bytes
        // if ($value->getSize() > $maxSize) {
        //     return false;
        // }

        return true;
    }

    public function message()
    {
        return 'The :attribute must be a valid image file (JPEG, JPG, PNG, SVG) with a maximum size of 2MB.';
    }
}
