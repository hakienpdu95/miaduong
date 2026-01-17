<?php
namespace App\Services;

use Illuminate\Support\Str;

class CaptchaService
{
    public function generateCaptcha()
    {
        $code = Str::random(5);
        session(['captcha_code' => $code]);
        $image = imagecreatetruecolor(116, 43);
        $bg = imagecolorallocate($image, 255, 255, 255);
        $textColor = imagecolorallocate($image, 0, 0, 0);
        imagefill($image, 0, 0, $bg);
        imagestring($image, 5, 10, 10, $code, $textColor);
        ob_start();
        imagepng($image);
        $data = ob_get_clean();
        imagedestroy($image);
        return 'data:image/png;base64,' . base64_encode($data);
    }

    public function validateCaptcha($input)
    {
        return $input === session('captcha_code');
    }
}