<?php

class RandomString
{
    /**
     * Generate Random String
     */
    public function generateRandomString()
    {
        $length = rand(3, 12);
        return substr(
            str_shuffle(str_repeat($x = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
                ceil($length / strlen($x)))), 1, $length
        );
    }
}