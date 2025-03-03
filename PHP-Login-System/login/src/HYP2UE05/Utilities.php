<?php

namespace HYP2UE05;

/**
 * Offers static helper methods for often used tasks.
 * This class offers methods for sanitizing form input, checks for valid e-mail addresses, phone numbers and other kinds
 * of data. This code can be used in different classes.
 * @package HYP2UE05
 * @author Wolfgang Hochleitner <wolfgang.hochleitner@fh-hagenberg.at>
 * @version 2024
 */
class Utilities
{
    /**
     * Filters unwanted HTML tags from an input string and returns the filtered (a.k.a. sanitized) string.
     * @param string $input The input string containing possible unwanted HTML tags.
     * @return string The sanitized string that can be safely used.
     */
    public static function sanitizeFilter(string $input): string
    {
        return htmlspecialchars($input, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5);
    }

    /**
     * Checks if a given string is a valid e-mail address according to the employed pattern.
     * @see https://www.regular-expressions.info/email.html More information about e-mail validation using regex.
     * @param string $string The input string that is to be checked.
     * @return bool Returns true if the string is a valid e-mail address, otherwise false.
     */
    public static function isEmail(string $string): bool
    {
        // $emailPattern = "/^[\w.-]+@[\w.-]+\.[A-Za-z]{2,6}$/"; // easy pattern
        $emailPattern = "/^[a-zA-Z0-9._-]+@[a-zA-Z0-9-]+\.[a-zA-Z.]{2,5}$/"; // more complicate pattern

        return preg_match($emailPattern, $string) === 1;
    }

    /**
     * Checks if a given string is a valid phone number. Du to the vast number of phone number formats, not everything
     * is covered by this regular expression. Strings such as +43 732 1234-1234 should work though.
     * @see https://github.com/googlei18n/libphonenumber Project for validating phone numbers.
     * @param string $string The input string that is to be checked.
     * @return bool Returns true if the string is a valid phone number, otherwise false.
     */
    public static function isPhone(string $string): bool
    {
        // Regex explanation:
        // ^\+? - Starts with an optional plus sign
        // \d{1,3} - Followed by 1 to 3 digits (country code)
        // [-\s]? - Optional dash or space
        // ((\d{3}[-\s]?){2}\d{4}) - Follows a pattern of three digits, an optional dash or space,
        // repeated twice, then four digits (typical format)
        // | - OR
        // (\d{2,4}[-\s]?){2,3}\d+ - Allows for 2 to 4 digits followed by an optional dash or space,
        // repeating 2 to 3 times, ending in digits
        // $ - End of the string
        $phonePattern = "/^\+?\d{1,3}[-\s]?((\d{3}[-\s]?){2}\d{4}|(\d{2,4}[-\s]?){2,3}\d+)$/";

        return preg_match($phonePattern, $string) === 1;
    }

    /**
     * Checks if a given string is a valid price. A price is a number with no leading zeroes, a comma for separating
     * the two decimal places. Other formats (as used by most databases) will use the period for separating decimal
     * points.
     * @param string $string The input string that is to be checked.
     * @return bool Returns true if the string is a valid price, otherwise false.
     */
    public static function isPrice(string $string): bool
    {
        $pricePattern = "/(?!0,00)((^[1-9])(\d)*|^0)(([,.])\d{2})?$/";

        return preg_match($pricePattern, $string) === 1;
    }

    /**
     * Checks if a string contains a positive integer that does not equal 0.
     * @param string $string The input string that is to be checked.
     * @return bool Returns true if the string is a valid integer, otherwise false.
     * @see filter_var The PHP function filter_var using the FILTER_VALIDATE_INT filter can achieve the same.
     */
    public static function isInt(string $string): bool
    {
        $intPattern = "/(^[1-9][0-9]*|0$)/";

        return preg_match($intPattern, $string) === 1;
    }

    /**-+
     * Performs a white listing of the supplied characters and checks for minimum and maximum length. White spaces are
     * excluded, therefore only one search term can be entered. This is tailored for DAB, where LIKE is employed instead
     * of a full text search. Production environment will more likely use ElasticSearch or Google crawler.
     * @param string $string The input string that is to be checked.
     * @param int $min The string's minimum length. Default value is 0.
     * @param int $max The string's maximum length. Default value is 50.
     * @return bool Returns true if the string is a single word, otherwise false.
     */
    public static function isSingleWord(string $string, int $min = 0, int $max = 50): bool
    {
        $stringPattern = "/^[a-zäöüA-ZÄÖÜ0-9]{" . $min . "," . $max . "}$/i";

        return preg_match($stringPattern, $string) === 1;
    }

    /**
     * Checks if a given string is a valid password. Only certain characters are allowed, a minimum and maximum length
     * is enforced.
     * Use it like this: Utilities::isPassword("mySecurePassword", 12, 50).
     * @param string $string The input string that is to be checked.
     * @param int $min The password's minimum length.
     * @param int $max The password's maximum length.
     * @return bool Returns true if the string is a valid password, otherwise false.
     */
    public static function isPassword(string $string, int $min, int $max): bool
    {
        $passwordPattern = "/^[a-zA-Z0-9_]{" . $min . "," . $max . "}$/";

        return preg_match($passwordPattern, $string) === 1;
    }

    /**
     * Checks if a given string is empty after leading and trailing whitespaces were trimmed.
     * @param string $string The input string that is to be checked.
     * @return bool Returns true if the string is empty, otherwise false.
     */
    public static function isEmptyString(string $string): bool
    {
        return (mb_strlen(trim($string)) === 0);
    }

    /**
     * Quick and dirty method for replacing the most common umlauts in a string with regular ASCII characters.
     * Useful when dealing with file names that are provided by the file system. Windows actually delivers e.g. an "ä",
     * whereas macOS does a diaeresis of a and two dots, which is seen as e.g. \x61\xcc\x88
     * @param string $string The input string where replacements should be performed.
     * @return string A string without umlauts.
     */
    public static function replaceUmlauts(string $string): string
    {
        $charReplace = [
            "ä" => "ae",
            "\x61\xcc\x88" => "ae",
            "Ä" => "Ae",
            "\x41\xcc\x88" => "Ae",
            "ö" => "oe",
            "\x6f\xcc\x88" => "oe",
            "Ö" => "Oe",
            "\x4f\xcc\x88" => "Oe",
            "ü" => "ue",
            "\x75\xcc\x88" => "ue",
            "Ü" => "Ue",
            "\x55\xcc\x88" => "Ue",
            "ß" => "ss",
            " " => "_"
        ];
        return strtr($string, $charReplace);
    }

    /**
     * Generates a 128 character hash value using the SHA-512 algorithm. The user's IP address as well as the user agent
     * string are hashed. This hash can then be stored in the $_SESSION array to act as a token for a logged-in user.
     * @return string The login hash value.
     */
    public static function generateLoginHash(): string
    {
        return hash("sha512", $_SERVER["REMOTE_ADDR"] . $_SERVER["HTTP_USER_AGENT"]);
    }
}
