<?php
namespace app\modules\efatura\services;

/**
 * Luhn algorithm utility.
 *
 * @author    Tobias Dély (tdely) <cleverhatcamouflage@gmail.com>
 * @copyright 2017-present Tobias Dély
 * @license   https://unlicense.org/ Unlicense
 */
class LuhnAlgorithm
{
    /**
     * Luhn core algorithm.
     *
     * @param int $number
     * @return int
     */
    private static function algorithm( $number)
    {
        $sum = 0;
        $parity = 1;
        settype($number, 'string');
        for ($i = strlen($number) - 1; $i >= 0; $i--) {
            $factor = $parity ? 2 : 1;
            $parity = $parity ? 0 : 1;
            $sum += array_sum(str_split($number[$i] * $factor));
        }

        return $sum;
    }

    /**
     * Compute check digit.
     *
     * @param int $number Number to compute.
     * @return int
     */
    public static function checksum( $number)
    {
        return ((self::algorithm($number) * 9) % 10);
    }

    /**
     * Validate number containing check digit.
     *
     * @param int $number Number to validate.
     * @return bool
     */
    public static function isValid( $number):bool
    {
        return (self::algorithm($number . '0') % 10) === 0 ? true : false;
    }

    /**
     * Add check digit to number.
     *
     * @param int $number Number to checksum.
     * @param bool $soft Do not add check digit if number already validates.
     * @return int
     */
    public static function create($number, bool $soft = false)
    {
        return !self::isValid($number) || !$soft
            ? $number . self::checksum($number)
            : $number;
    }
}