<?php

function generateRandomString($length = 10)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function array_usearch(array $array, callable $comparitor)
{
    return array_filter(
        $array,
        function ($element) use ($comparitor) {
            if ($comparitor($element)) {
                return $element;
            }
        }
    );
}

function groupBy($array, $condition)
{
    $dictionary = array();

    foreach ($array as $value) {

        $decodedValue = json_decode($value);
        $key = $condition($decodedValue);
        $contains = false;

        foreach ($dictionary as $index => $kvp) {
            foreach ($kvp['value'] as $v) {
                $newkey = $condition($v);
                if ($key === $newkey) {
                    $contains = true;
                    array_push($dictionary[$index]['value'], $decodedValue);
                    break;
                }
            }
        }

        if (!$contains) {
            array_push($dictionary, [
                'key' => $key,
                'value' => [$decodedValue]
            ]);
        }
    }

    return $dictionary;
}
