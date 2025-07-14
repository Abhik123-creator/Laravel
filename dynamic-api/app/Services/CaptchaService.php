<?php

namespace App\Services;

use Illuminate\Support\Facades\Session;

class CaptchaService
{
    public static function generate($difficulty = 'medium')
    {
        $captcha = self::generateCaptcha($difficulty);
        
        // Store the answer in session
        Session::put('captcha_answer', $captcha['answer']);
        Session::put('captcha_id', uniqid());
        
        return [
            'question' => $captcha['question'],
            'image' => self::generateImage($captcha['question']),
            'id' => Session::get('captcha_id')
        ];
    }

    public static function verify($userAnswer, $captchaId = null)
    {
        $sessionAnswer = Session::get('captcha_answer');
        $sessionId = Session::get('captcha_id');
        
        // Clear the captcha from session after verification attempt
        Session::forget(['captcha_answer', 'captcha_id']);
        
        if (!$sessionAnswer) {
            return false;
        }
        
        // Optional: verify captcha ID matches
        if ($captchaId && $captchaId !== $sessionId) {
            return false;
        }
        
        return (int)$userAnswer === (int)$sessionAnswer;
    }

    private static function generateCaptcha($difficulty)
    {
        switch ($difficulty) {
            case 'easy':
                return self::generateEasyCaptcha();
            case 'hard':
                return self::generateHardCaptcha();
            case 'medium':
            default:
                return self::generateMediumCaptcha();
        }
    }

    private static function generateEasyCaptcha()
    {
        $num1 = rand(1, 10);
        $num2 = rand(1, 10);
        $operation = '+';
        
        return [
            'question' => "{$num1} {$operation} {$num2} = ?",
            'answer' => $num1 + $num2
        ];
    }

    private static function generateMediumCaptcha()
    {
        $operations = ['+', '-', '*'];
        $operation = $operations[array_rand($operations)];
        
        switch ($operation) {
            case '+':
                $num1 = rand(10, 50);
                $num2 = rand(1, 30);
                $answer = $num1 + $num2;
                break;
            case '-':
                $num1 = rand(20, 50);
                $num2 = rand(1, $num1 - 1);
                $answer = $num1 - $num2;
                break;
            case '*':
                $num1 = rand(2, 12);
                $num2 = rand(2, 9);
                $answer = $num1 * $num2;
                break;
        }
        
        return [
            'question' => "{$num1} {$operation} {$num2} = ?",
            'answer' => $answer
        ];
    }

    private static function generateHardCaptcha()
    {
        $operations = ['+', '-', '*'];
        $operation1 = $operations[array_rand($operations)];
        $operation2 = $operations[array_rand($operations)];
        
        $num1 = rand(5, 20);
        $num2 = rand(2, 10);
        $num3 = rand(1, 8);
        
        // Calculate step by step to ensure correct order of operations
        switch ($operation1) {
            case '+':
                $intermediate = $num1 + $num2;
                break;
            case '-':
                $intermediate = $num1 - $num2;
                break;
            case '*':
                $intermediate = $num1 * $num2;
                break;
        }
        
        switch ($operation2) {
            case '+':
                $answer = $intermediate + $num3;
                break;
            case '-':
                $answer = $intermediate - $num3;
                break;
            case '*':
                $answer = $intermediate * $num3;
                break;
        }
        
        return [
            'question' => "({$num1} {$operation1} {$num2}) {$operation2} {$num3} = ?",
            'answer' => $answer
        ];
    }

    private static function generateImage($question)
    {
        // Create a PNG image with the math question using GD
        $width = 300;
        $height = 80;
        $fontSize = 18;
        $fontFile = __DIR__ . '/../../resources/fonts/arial.ttf'; // Use a TTF font (ensure this file exists)

        // Create image
        $im = imagecreatetruecolor($width, $height);

        // Colors
        $backgroundColor = imagecolorallocate($im, rand(230,255), rand(230,255), rand(230,255));
        $textColor = imagecolorallocate($im, rand(0,60), rand(0,60), rand(0,60));
        $noiseColor = imagecolorallocate($im, rand(180,220), rand(180,220), rand(180,220));

        // Fill background
        imagefilledrectangle($im, 0, 0, $width, $height, $backgroundColor);

        // Add noise lines
        for ($i = 0; $i < 15; $i++) {
            imageline($im, rand(0, $width), rand(0, $height), rand(0, $width), rand(0, $height), $noiseColor);
        }

        // Add noise dots
        for ($i = 0; $i < 30; $i++) {
            imagefilledellipse($im, rand(10, $width-10), rand(10, $height-10), rand(1,3), rand(1,3), $noiseColor);
        }

        // Add the text (centered)
        if (file_exists($fontFile)) {
            $bbox = imagettfbbox($fontSize, 0, $fontFile, $question);
            $textWidth = $bbox[2] - $bbox[0];
            $textHeight = $bbox[1] - $bbox[7];
            $x = ($width - $textWidth) / 2;
            $y = ($height + $textHeight) / 2;
            imagettftext($im, $fontSize, rand(-5,5), $x, $y, $textColor, $fontFile, $question);
        } else {
            // Fallback to built-in font if TTF not available
            imagestring($im, 5, 20, ($height/2)-10, $question, $textColor);
        }

        // Output PNG to buffer
        ob_start();
        imagepng($im);
        $imageData = ob_get_clean();
        imagedestroy($im);

        // Return as data URL
        return 'data:image/png;base64,' . base64_encode($imageData);
    }

    public static function refresh($difficulty = 'medium')
    {
        // Clear existing captcha
        Session::forget(['captcha_answer', 'captcha_id']);
        
        // Generate new captcha with specified difficulty
        return self::generate($difficulty);
    }
}
