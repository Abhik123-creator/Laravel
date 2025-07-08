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
        // Create a simple SVG image with the math question
        $width = 300;
        $height = 80;
        $fontSize = 18;
        
        // Add some visual noise and styling
        $backgroundColor = sprintf('#%06X', mt_rand(0xF0F0F0, 0xFFFFFF));
        $textColor = sprintf('#%06X', mt_rand(0x000000, 0x333333));
        $noiseColor = sprintf('#%06X', mt_rand(0xCCCCCC, 0xEEEEEE));
        
        $svg = "<svg width='{$width}' height='{$height}' xmlns='http://www.w3.org/2000/svg'>";
        $svg .= "<rect width='100%' height='100%' fill='{$backgroundColor}' stroke='#ccc' stroke-width='2' rx='8'/>";
        
        // Add some noise lines
        for ($i = 0; $i < 15; $i++) {
            $x1 = rand(0, $width);
            $y1 = rand(0, $height);
            $x2 = rand(0, $width);
            $y2 = rand(0, $height);
            $svg .= "<line x1='{$x1}' y1='{$y1}' x2='{$x2}' y2='{$y2}' stroke='{$noiseColor}' stroke-width='1' opacity='0.3'/>";
        }
        
        // Add some noise dots
        for ($i = 0; $i < 30; $i++) {
            $x = rand(10, $width - 10);
            $y = rand(10, $height - 10);
            $r = rand(1, 3);
            $svg .= "<circle cx='{$x}' cy='{$y}' r='{$r}' fill='{$noiseColor}' opacity='0.5'/>";
        }
        
        // Add the text with slight rotation and positioning variations
        $textX = $width / 2;
        $textY = $height / 2 + 6;
        $rotation = rand(-5, 5);
        
        $svg .= "<text x='{$textX}' y='{$textY}' font-family='Arial, sans-serif' font-size='{$fontSize}' font-weight='bold' fill='{$textColor}' text-anchor='middle' transform='rotate({$rotation} {$textX} {$textY})'>";
        $svg .= htmlspecialchars($question);
        $svg .= "</text>";
        
        $svg .= "</svg>";
        
        // Convert SVG to base64 data URL
        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }

    public static function refresh($difficulty = 'medium')
    {
        // Clear existing captcha
        Session::forget(['captcha_answer', 'captcha_id']);
        
        // Generate new captcha with specified difficulty
        return self::generate($difficulty);
    }
}
