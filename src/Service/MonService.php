<?php

namespace App\Service;


class MonService
{
    public function reverseString(string $message): string
    {
        $r = '';
        for ($i = mb_strlen($message); $i >= 0; $i--) {
            $r .= mb_substr($message, $i, 1);
        }
        return $r;
    }
}