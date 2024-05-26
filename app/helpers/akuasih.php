<?php

if (!function_exists('phAsam')) {
    function phAsam($value)
    {
        if ($value >= 7) return 0;
        if (6.5 < $value && $value < 7) return (7 - $value) / (7 - 6.5);
        if ($value <= 6.5) return 1;
        throw new Exception('Wrong phAsam argument');
    }
}

if (!function_exists('phBaik')) {
    function phBaik($value)
    {
        if ($value <= 6.5 || $value >= 8.5) return 0;
        if (6.5 < $value && $value < 7) return ($value - 6.5) / (7 - 6.5);
        if (7 <= $value && $value < 8.5) return (8.5 - $value) / (8.5 - 7);
        throw new Exception('Wrong phBaik argument');
    }
}

if (!function_exists('phBasa')) {
    function phBasa($value)
    {
        if ($value <= 7) return 0;
        if (7 < $value && $value < 8.5) return ($value - 7) / (8.5 - 7);
        if ($value >= 8.5) return 1;
        throw new Exception('Wrong phBasa argument');
    }
}

if (!function_exists('metalBaik')) {
    function metalBaik($value)
    {
        if ($value >= 1500) return 0;
        if (750 < $value && $value < 1500) return (1500 - $value) / (1500 - 750);
        if ($value <= 750) return 1;
        throw new Exception('Wrong metalBaik argument');
    }
}

if (!function_exists('metalSedang')) {
    function metalSedang($value)
    {
        if ($value <= 750 || $value >= 2250) return 0;
        if (750 < $value && $value < 1500) return ($value - 750) / (1500 - 750);
        if (1500 <= $value && $value < 2250) return (2250 - $value) / (2250 - 1500);
        throw new Exception('Wrong metalSedang argument');
    }
}

if (!function_exists('metalBuruk')) {
    function metalBuruk($value)
    {
        if ($value <= 1500) return 0;
        if (1500 < $value && $value < 2250) return ($value - 1500) / (2250 - 1500);
        if ($value >= 2250) return 1;
        throw new Exception('Wrong metalSedang argument');
    }
}

if (!function_exists('oxygenBuruk')) {
    function oxygenBuruk($value)
    {
        if ($value >= 2) return 0;
        if (1.9 < $value && $value < 2) return (2 - $value) / (2 - 1.9);
        if ($value <= 1.9) return 1;
        throw new Exception('Wrong oxygenBuruk argument');
    }
}

if (!function_exists('oxygenCukup')) {
    function oxygenCukup($value)
    {
        if ($value <= 1.9 || $value >= 2.1) return 0;
        if (1.9 < $value && $value < 2) return ($value - 1.9) / (2 - 1.9);
        if (2 <= $value && $value < 2.1) return (2.1 - $value) / (2.1 - 2);
        throw new Exception('Wrong oxygenCukup argument');
    }
}

if (!function_exists('oxygenBaik')) {
    function oxygenBaik($value)
    {
        if ($value <= 2) return 0;
        if (2 < $value && $value < 2.1) return ($value - 2) / (2.1 - 2);
        if ($value >= 2.1) return 1;
        throw new Exception('Wrong oxygenBaik argument');
    }
}

if (!function_exists('tdsBaik')) {
    function tdsBaik($value)
    {
        if ($value >= 1750) return 0;
        if (1500 < $value && $value < 1750) return (1750 - $value) / (1750 - 1500);
        if ($value <= 1500) return 1;
        throw new Exception('Wrong tdsBaik argument');
    }
}

if (!function_exists('tdsSedang')) {
    function tdsSedang($value)
    {
        if ($value <= 1500 || $value >= 2000) return 0;
        if (1500 < $value && $value < 1750) return ($value - 1500) / (1750 - 1500);
        if (1750 <= $value && $value < 2000) return (2000 - $value) / (2000 - 1750);
        throw new Exception('Wrong tdsSedang argument');
    }
}

if (!function_exists('tdsBuruk')) {
    function tdsBuruk($value)
    {
        if ($value <= 1750) return 0;
        if (1750 < $value && $value < 2000) return ($value - 1750) / (2000 - 1750);
        if ($value >= 2000) return 1;
        throw new Exception('Wrong tdsBuruk argument');
    }
}

if (!function_exists('output')) {
    function output($value, $criteria)
    {
        $points = [];

        switch ($criteria) {
            case 'Sangat Buruk':
                array_push($points, [0, $value]);
                if ($value <= 0.5) array_push($points, [rules2($value), $value]);
                array_push($points, [rules1($value), $value]);
                array_push($points, [35, 0]);
                array_push($points, [100, 0]);
                break;
            case 'Buruk':
                array_push($points, [0, 0]);
                array_push($points, [25, 0]);
                array_push($points, [rules2($value), $value]);
                if ($value <= 0.5) array_push($points, [rules1($value), $value]);
                if ($value <= 0.5) array_push($points, [rules4($value), $value]);
                array_push($points, [rules3($value), $value]);
                array_push($points, [50, 0]);
                array_push($points, [100, 0]);
                break;
            case 'Sedang':
                array_push($points, [0, 0]);
                array_push($points, [35, 0]);
                array_push($points, [rules4($value), $value]);
                if ($value <= 0.5) array_push($points, [rules3($value), $value]);
                if ($value <= 0.5) array_push($points, [rules6($value), $value]);
                array_push($points, [rules5($value), $value]);
                array_push($points, [70, 0]);
                array_push($points, [100, 0]);
                break;
            case 'Baik':
                array_push($points, [0, 0]);
                array_push($points, [50, 0]);
                array_push($points, [rules6($value), $value]);
                if ($value <= 0.5)  array_push($points, [rules5($value), $value]);
                if ($value <= 0.5)  array_push($points, [rules8($value), $value]);
                array_push($points, [rules7($value), $value]);
                array_push($points, [90, 0]);
                array_push($points, [100, 0]);
                break;
            case 'Sangat Baik':
                array_push($points, [0, 0]);
                array_push($points, [70, 0]);
                array_push($points, [rules8($value), $value]);
                if ($value <= 0.5) array_push($points, [rules7($value), $value]);
                array_push($points, [100, $value]);
                break;
            default:
                throw new Exception("Wrong Criteria");
                break;
        }

        return $points;
    }
}

function rules1($value)
{
    return abs(((35 - 25) * $value) - 35);
}

function rules2($value)
{
    return abs(((35 - 25) * $value) + 25);
}

function rules3($value)
{
    return abs(((50 - 35) * $value) - 50);
}

function rules4($value)
{
    return abs(((50 - 35) * $value) + 35);
}

function rules5($value)
{
    return abs(((70 - 50) * $value) - 70);
}

function rules6($value)
{
    return abs(((70 - 50) * $value) + 50);
}

function rules7($value)
{
    return abs(((90 - 70) * $value) - 90);
}

function rules8($value)
{
    return abs(((90 - 70) * $value) + 70);
}

function generateWholeArea(
    $sangatBuruk,
    $buruk,
    $sedang,
    $baik,
    $sangatBaik,
) {
    $all = [];
    // Calculate Sangat Buruk
    array_push($all, [0, $sangatBuruk]);
    array_push($all, [35, $buruk]);

    // Calculate Buruk
    if ($sangatBuruk > $buruk) {
        array_pop($all);
        array_push($all, [rules1($sangatBuruk), $sangatBuruk]);
        array_push($all, [rules1($buruk), $buruk]);
    } else if ($sangatBuruk < $buruk) {
        array_pop($all);
        array_push($all, [rules2($sangatBuruk), $sangatBuruk]);
        array_push($all, [rules2($buruk), $buruk]);
    } else {
        array_push($all, [25, $buruk]);
    }

    array_push($all, [50, $buruk]);

    // Calculate Sedang
    if ($buruk > $sedang) {
        array_pop($all);
        array_push($all, [rules3($buruk), $buruk]);
        array_push($all, [rules3($sedang), $sedang]);
    } else if ($buruk < $sedang) {
        array_pop($all);
        array_push($all, [rules4($buruk), $buruk]);
        array_push($all, [rules4($sedang), $sedang]);
    } else {
        array_push($all, [35, $sedang]);
    }

    array_push($all, [70, $sedang]);

    // Calculate Baik
    if ($sedang > $baik) {
        array_pop($all);
        array_push($all, [rules5($sedang), $sedang]);
        array_push($all, [rules5($baik), $baik]);
    } else if ($sedang < $baik) {
        array_pop($all);
        array_push($all, [rules6($sedang), $sedang]);
        array_push($all, [rules6($baik), $baik]);
    } else {
        array_push($all, [50, $baik]);
    }

    array_push($all, [90, $baik]);

    // Calculate Sangat Baik
    if ($baik > $sangatBaik) {
        array_pop($all);
        array_push($all, [rules7($baik), $baik]);
        array_push($all, [rules7($sangatBaik), $sangatBaik]);
    } else if ($baik < $sangatBaik) {
        array_pop($all);
        array_push($all, [rules8($baik), $baik]);
        array_push($all, [rules8($sangatBaik), $sangatBaik]);
    } else {
        array_push($all, [70, $sangatBaik]);
    }

    array_push($all, [100, $sangatBaik]);
    return $all;
}

function moment1($x)
{
    return (1 / 10) * ((35 * $x * $x / 2) - ($x * $x * $x / 3));
}

function moment2($x)
{
    return (1 / 10) * (($x * $x * $x / 3) - (25 * $x * $x / 2));
}

function moment3($x)
{
    return (1 / 15) * ((25 * $x * $x) - ($x * $x * $x / 3));
}

function moment4($x)
{
    return (1 / 15) * (($x * $x * $x / 3) - (35 * $x * $x / 2));
}

function moment5($x)
{
    return (1 / 20) * ((35 * $x * $x) - ($x * $x * $x / 3));
}

function moment6($x)
{
    return (1 / 20) * (($x * $x * $x / 3) - (25 * $x * $x));
}

function moment7($x)
{
    return (1 / 20) * ((45 * $x * $x) - ($x * $x * $x / 3));
}

function moment8($x)
{
    return (1 / 20) * (($x * $x * $x / 3) - (35 * $x * $x));
}
