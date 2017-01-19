<?php

/**
 * A PHP port of URLify.js from the Django project
 * (https://github.com/django/django/blob/master/django/contrib/admin/static/admin/js/urlify.js).
 * Handles symbols from Latin languages, Greek, Turkish, Bulgarian, Russian,
 * Ukrainian, Czech, Polish, Romanian, Latvian, Lithuanian, Vietnamese, Arabic,
 * Serbian, Azerbaijani, and Slovakian. Symbols it cannot transliterate
 * it will simply omit.
 *
 * Usage:
 *
 *     echo URLify::filter (' J\'étudie le français ');
 *     // "jetudie-le-francais"
 *
 *     echo URLify::filter ('Lo siento, no hablo español.');
 *     // "lo-siento-no-hablo-espanol"
 */
class URLify {
    public static $maps = array (
        'de' => array ( /* German */
            'Ä' => 'Ae', 'Ö' => 'Oe', 'Ü' => 'Ue', 'ä' => 'ae', 'ö' => 'oe', 'ü' => 'ue', 'ß' => 'ss',
            'ẞ' => 'SS'
        ),
        'latin' => array (
            'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A','Ă' => 'A', 'Æ' => 'AE', 'Ç' =>
            'C', 'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I',
            'Ï' => 'I', 'Ð' => 'D', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' =>
            'O', 'Ő' => 'O', 'Ø' => 'O','Ș' => 'S','Ț' => 'T', 'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ű' => 'U',
            'Ý' => 'Y', 'Þ' => 'TH', 'ß' => 'ss', 'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' =>
            'a', 'å' => 'a', 'ă' => 'a', 'æ' => 'ae', 'ç' => 'c', 'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e',
            'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i', 'ð' => 'd', 'ñ' => 'n', 'ò' => 'o', 'ó' =>
            'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ő' => 'o', 'ø' => 'o', 'ș' => 's', 'ț' => 't', 'ù' => 'u', 'ú' => 'u',
            'û' => 'u', 'ü' => 'u', 'ű' => 'u', 'ý' => 'y', 'þ' => 'th', 'ÿ' => 'y'
        ),
        'latin_symbols' => array (
            '©' => '(c)'
        ),
        'el' => array ( /* Greek */
            'α' => 'a', 'β' => 'b', 'γ' => 'g', 'δ' => 'd', 'ε' => 'e', 'ζ' => 'z', 'η' => 'h', 'θ' => '8',
            'ι' => 'i', 'κ' => 'k', 'λ' => 'l', 'μ' => 'm', 'ν' => 'n', 'ξ' => '3', 'ο' => 'o', 'π' => 'p',
            'ρ' => 'r', 'σ' => 's', 'τ' => 't', 'υ' => 'y', 'φ' => 'f', 'χ' => 'x', 'ψ' => 'ps', 'ω' => 'w',
            'ά' => 'a', 'έ' => 'e', 'ί' => 'i', 'ό' => 'o', 'ύ' => 'y', 'ή' => 'h', 'ώ' => 'w', 'ς' => 's',
            'ϊ' => 'i', 'ΰ' => 'y', 'ϋ' => 'y', 'ΐ' => 'i',
            'Α' => 'A', 'Β' => 'B', 'Γ' => 'G', 'Δ' => 'D', 'Ε' => 'E', 'Ζ' => 'Z', 'Η' => 'H', 'Θ' => '8',
            'Ι' => 'I', 'Κ' => 'K', 'Λ' => 'L', 'Μ' => 'M', 'Ν' => 'N', 'Ξ' => '3', 'Ο' => 'O', 'Π' => 'P',
            'Ρ' => 'R', 'Σ' => 'S', 'Τ' => 'T', 'Υ' => 'Y', 'Φ' => 'F', 'Χ' => 'X', 'Ψ' => 'PS', 'Ω' => 'W',
            'Ά' => 'A', 'Έ' => 'E', 'Ί' => 'I', 'Ό' => 'O', 'Ύ' => 'Y', 'Ή' => 'H', 'Ώ' => 'W', 'Ϊ' => 'I',
            'Ϋ' => 'Y'
        ),
        'tr' => array ( /* Turkish */
            'ş' => 's', 'Ş' => 'S', 'ı' => 'i', 'İ' => 'I', 'ç' => 'c', 'Ç' => 'C', 'ü' => 'u', 'Ü' => 'U',
            'ö' => 'o', 'Ö' => 'O', 'ğ' => 'g', 'Ğ' => 'G'
        ),
        'bg' => array( /* Bulgarian */
            "Щ" => 'Sht', "Ш" => 'Sh', "Ч" => 'Ch', "Ц" => 'C', "Ю" => 'Yu', "Я" => 'Ya',
            "Ж" => 'J',   "А" => 'A',  "Б" => 'B',  "В" => 'V', "Г" => 'G',  "Д" => 'D',
            "Е" => 'E',   "З" => 'Z',  "И" => 'I',  "Й" => 'Y', "К" => 'K',  "Л" => 'L',
            "М" => 'M',   "Н" => 'N',  "О" => 'O',  "П" => 'P', "Р" => 'R',  "С" => 'S',
            "Т" => 'T',   "У" => 'U',  "Ф" => 'F',  "Х" => 'H', "Ь" => '',   "Ъ" => 'A',
            "щ" => 'sht', "ш" => 'sh', "ч" => 'ch', "ц" => 'c', "ю" => 'yu', "я" => 'ya',
            "ж" => 'j',   "а" => 'a',  "б" => 'b',  "в" => 'v', "г" => 'g',  "д" => 'd',
            "е" => 'e',   "з" => 'z',  "и" => 'i',  "й" => 'y', "к" => 'k',  "л" => 'l',
            "м" => 'm',   "н" => 'n',  "о" => 'o',  "п" => 'p', "р" => 'r',  "с" => 's',
            "т" => 't',   "у" => 'u',  "ф" => 'f',  "х" => 'h', "ь" => '',   "ъ" => 'a'
        ),
        'ru' => array ( /* Russian */
            'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'yo', 'ж' => 'zh',
            'з' => 'z', 'и' => 'i', 'й' => 'j', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o',
            'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c',
            'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sh', 'ъ' => '', 'ы' => 'y', 'ь' => '', 'э' => 'e', 'ю' => 'yu',
            'я' => 'ya',
            'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Д' => 'D', 'Е' => 'E', 'Ё' => 'Yo', 'Ж' => 'Zh',
            'З' => 'Z', 'И' => 'I', 'Й' => 'J', 'К' => 'K', 'Л' => 'L', 'М' => 'M', 'Н' => 'N', 'О' => 'O',
            'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T', 'У' => 'U', 'Ф' => 'F', 'Х' => 'H', 'Ц' => 'C',
            'Ч' => 'Ch', 'Ш' => 'Sh', 'Щ' => 'Sh', 'Ъ' => '', 'Ы' => 'Y', 'Ь' => '', 'Э' => 'E', 'Ю' => 'Yu',
            'Я' => 'Ya',
            '№' => 'No'
        ),
        'uk' => array ( /* Ukrainian */
            'Є' => 'Ye', 'І' => 'I', 'Ї' => 'Yi', 'Ґ' => 'G', 'є' => 'ye', 'і' => 'i', 'ї' => 'yi', 'ґ' => 'g'
        ),
        'cs' => array ( /* Czech */
            'č' => 'c', 'ď' => 'd', 'ě' => 'e', 'ň' => 'n', 'ř' => 'r', 'š' => 's', 'ť' => 't', 'ů' => 'u',
            'ž' => 'z', 'Č' => 'C', 'Ď' => 'D', 'Ě' => 'E', 'Ň' => 'N', 'Ř' => 'R', 'Š' => 'S', 'Ť' => 'T',
            'Ů' => 'U', 'Ž' => 'Z'
        ),
        'pl' => array ( /* Polish */
            'ą' => 'a', 'ć' => 'c', 'ę' => 'e', 'ł' => 'l', 'ń' => 'n', 'ó' => 'o', 'ś' => 's', 'ź' => 'z',
            'ż' => 'z', 'Ą' => 'A', 'Ć' => 'C', 'Ę' => 'e', 'Ł' => 'L', 'Ń' => 'N', 'Ó' => 'O', 'Ś' => 'S',
            'Ź' => 'Z', 'Ż' => 'Z'
        ),
        'ro' => array ( /* Romanian */
            'ă' => 'a', 'â' => 'a', 'î' => 'i', 'ș' => 's', 'ț' => 't', 'Ţ' => 'T', 'ţ' => 't'
        ),
        'lv' => array ( /* Latvian */
            'ā' => 'a', 'č' => 'c', 'ē' => 'e', 'ģ' => 'g', 'ī' => 'i', 'ķ' => 'k', 'ļ' => 'l', 'ņ' => 'n',
            'š' => 's', 'ū' => 'u', 'ž' => 'z', 'Ā' => 'A', 'Č' => 'C', 'Ē' => 'E', 'Ģ' => 'G', 'Ī' => 'i',
            'Ķ' => 'k', 'Ļ' => 'L', 'Ņ' => 'N', 'Š' => 'S', 'Ū' => 'u', 'Ž' => 'Z'
        ),
        'lt' => array ( /* Lithuanian */
            'ą' => 'a', 'č' => 'c', 'ę' => 'e', 'ė' => 'e', 'į' => 'i', 'š' => 's', 'ų' => 'u', 'ū' => 'u', 'ž' => 'z',
            'Ą' => 'A', 'Č' => 'C', 'Ę' => 'E', 'Ė' => 'E', 'Į' => 'I', 'Š' => 'S', 'Ų' => 'U', 'Ū' => 'U', 'Ž' => 'Z'
        ),
        'vn' => array ( /* Vietnamese */
            'Á' => 'A', 'À' => 'A', 'Ả' => 'A', 'Ã' => 'A', 'Ạ' => 'A', 'Ă' => 'A', 'Ắ' => 'A', 'Ằ' => 'A', 'Ẳ' => 'A', 'Ẵ' => 'A', 'Ặ' => 'A', 'Â' => 'A', 'Ấ' => 'A', 'Ầ' => 'A', 'Ẩ' => 'A', 'Ẫ' => 'A', 'Ậ' => 'A',
            'á' => 'a', 'à' => 'a', 'ả' => 'a', 'ã' => 'a', 'ạ' => 'a', 'ă' => 'a', 'ắ' => 'a', 'ằ' => 'a', 'ẳ' => 'a', 'ẵ' => 'a', 'ặ' => 'a', 'â' => 'a', 'ấ' => 'a', 'ầ' => 'a', 'ẩ' => 'a', 'ẫ' => 'a', 'ậ' => 'a',
            'É' => 'E', 'È' => 'E', 'Ẻ' => 'E', 'Ẽ' => 'E', 'Ẹ' => 'E', 'Ê' => 'E', 'Ế' => 'E', 'Ề' => 'E', 'Ể' => 'E', 'Ễ' => 'E', 'Ệ' => 'E',
            'é' => 'e', 'è' => 'e', 'ẻ' => 'e', 'ẽ' => 'e', 'ẹ' => 'e', 'ê' => 'e', 'ế' => 'e', 'ề' => 'e', 'ể' => 'e', 'ễ' => 'e', 'ệ' => 'e',
            'Í' => 'I', 'Ì' => 'I', 'Ỉ' => 'I', 'Ĩ' => 'I', 'Ị' => 'I', 'í' => 'i', 'ì' => 'i', 'ỉ' => 'i', 'ĩ' => 'i', 'ị' => 'i',
            'Ó' => 'O', 'Ò' => 'O', 'Ỏ' => 'O', 'Õ' => 'O', 'Ọ' => 'O', 'Ô' => 'O', 'Ố' => 'O', 'Ồ' => 'O', 'Ổ' => 'O', 'Ỗ' => 'O', 'Ộ' => 'O', 'Ơ' => 'O', 'Ớ' => 'O', 'Ờ' => 'O', 'Ở' => 'O', 'Ỡ' => 'O', 'Ợ' => 'O',
            'ó' => 'o', 'ò' => 'o', 'ỏ' => 'o', 'õ' => 'o', 'ọ' => 'o', 'ô' => 'o', 'ố' => 'o', 'ồ' => 'o', 'ổ' => 'o', 'ỗ' => 'o', 'ộ' => 'o', 'ơ' => 'o', 'ớ' => 'o', 'ờ' => 'o', 'ở' => 'o', 'ỡ' => 'o', 'ợ' => 'o',
            'Ú' => 'U', 'Ù' => 'U', 'Ủ' => 'U', 'Ũ' => 'U', 'Ụ' => 'U', 'Ư' => 'U', 'Ứ' => 'U', 'Ừ' => 'U', 'Ử' => 'U', 'Ữ' => 'U', 'Ự' => 'U',
            'ú' => 'u', 'ù' => 'u', 'ủ' => 'u', 'ũ' => 'u', 'ụ' => 'u', 'ư' => 'u', 'ứ' => 'u', 'ừ' => 'u', 'ử' => 'u', 'ữ' => 'u', 'ự' => 'u',
            'Ý' => 'Y', 'Ỳ' => 'Y', 'Ỷ' => 'Y', 'Ỹ' => 'Y', 'Ỵ' => 'Y', 'ý' => 'y', 'ỳ' => 'y', 'ỷ' => 'y', 'ỹ' => 'y', 'ỵ' => 'y',
            'Đ' => 'D', 'đ' => 'd'
        ),
        'sr' => array ( /* Serbian */
            'ђ' => 'dj', 'ј' => 'j', 'љ' => 'lj', 'њ' => 'nj', 'ћ' => 'c', 'џ' => 'dz', 'đ' => 'd',
            'Ђ' => 'Dj', 'Ј' => 'j', 'Љ' => 'Lj', 'Њ' => 'Nj', 'Ћ' => 'C', 'Џ' => 'Dz', 'Đ' => 'D'
        ),
        'az' => array ( /* Azerbaijani */
            'ç' => 'c', 'ə' => 'e', 'ğ' => 'g', 'ı' => 'i', 'ö' => 'o', 'ş' => 's', 'ü' => 'u',
            'Ç' => 'C', 'Ə' => 'E', 'Ğ' => 'G', 'İ' => 'I', 'Ö' => 'O', 'Ş' => 'S', 'Ü' => 'U'
        ),
        'slo' => array ( /* Slovakian */
            'DZ' => 'DZ', 'dz' => 'dz', 'DŽ' => 'DZ', 'dž' => 'dz', 'CH' => 'CH', 'Ĺ' => 'L', 'ĺ' => 'l', 'Ľ' => 'L',
            'ľ' => 'l', 'Ŕ' => 'R', 'ŕ' => 'r'
        ),
        'cus' => array ( /* Custom */
            'Ĉ' => 'C', 'ĉ' => 'c', 'Ċ' => 'C', 'ċ' => 'c', 'Ď' => 'D', 'ď' => 'd', 'Ĕ' => 'E', 'ĕ' => 'e', 'Ĝ' => 'G', 'ĝ' => 'g',
            'Ġ' => 'G', 'ġ' => 'g', 'Ĥ' => 'H', 'ĥ' => 'h', 'Ħ' => 'H', 'ħ' => 'h', 'Ĭ' => 'I', 'ĭ' => 'i', 'Ĳ' => 'J', 'ĳ' => 'j',
            'Ĵ' => 'J', 'ĵ' => 'j', 'Ŀ' => 'L', 'ŀ' => 'l', 'ŉ' => 'n', 'Ō' => 'O', 'ō' => 'o', 'Ŏ' => 'O', 'ŏ' => 'o', 'Œ' => 'OE',
            'œ' => 'oe', 'Ŗ' => 'R', 'ŗ' => 'r', 'Ŝ' => 'S', 'ŝ' => 's', 'Ŧ' => 'T', 'ŧ' => 't', 'Ŭ' => 'U', 'ŭ' => 'u', 'Ŵ' => 'W',
            'ŵ' => 'w', 'Ŷ' => 'Y', 'ŷ' => 'y', 'Ÿ' => 'Y', 'ſ' => 'S', 'Ƒ' => 'F', 'ƒ' => 'f', 'Ǎ' => 'A', 'ǎ' => 'a', 'Ǐ' => 'I',
            'ǐ' => 'i', 'Ǒ' => 'O', 'ǒ' => 'o', 'Ǔ' => 'U', 'ǔ' => 'u', 'Ǖ' => 'U', 'ǖ' => 'u', 'Ǘ' => 'U', 'ǘ' => 'u', 'Ǚ' => 'U',
            'ǚ' => 'u', 'Ǜ' => 'U', 'ǜ' => 'u', 'Ǻ' => 'A', 'ǻ' => 'a', 'Ǽ' => 'AE', 'ǽ' => 'ae', 'Ǿ' => 'O', 'ǿ' => 'o'
        )
    );

    /**
     * List of words to remove from URLs.
     */
    public static $remove_list = array (
        'a', 'an', 'as', 'at', 'before', 'but', 'by', 'for', 'from',
        'is', 'in', 'into', 'like', 'of', 'off', 'on', 'onto', 'per',
        'since', 'than', 'the', 'this', 'that', 'to', 'up', 'via',
        'with'
    );

    /**
     * The character map.
     */
    private static $map = array ();

    /**
     * The character list as a string.
     */
    private static $chars = '';

    /**
     * The character list as a regular expression.
     */
    private static $regex = '';

    /**
     * The current language
     */
    private static $language = '';

    /**
     * Initializes the character map.
     */
    private static function init ($language = "") {
        if (count (self::$map) > 0 && (($language == "") || ($language == self::$language))) {
            return;
        }

        /* Is a specific map associated with $language ? */
        if (isset(self::$maps[$language]) && is_array(self::$maps[$language])) {
            /* Move this map to end. This means it will have priority over others */
            $m = self::$maps[$language];
            unset(self::$maps[$language]);
            self::$maps[$language] = $m;
        }
        /* Reset static vars */
        self::$language = $language;
        self::$map = array();
        self::$chars = '';

        foreach (self::$maps as $map) {
            foreach ($map as $orig => $conv) {
                self::$map[$orig] = $conv;
                self::$chars .= $orig;
            }
        }

        self::$regex = '/[' . self::$chars . ']/u';
    }

    /**
     * Add new characters to the list. `$map` should be a hash.
     */
    public static function add_chars ($map) {
        if (! is_array ($map)) {
            throw new LogicException ('$map must be an associative array.');
        }
        self::$maps[] = $map;
        self::$map = array ();
        self::$chars = '';
    }

    /**
     * Append words to the remove list. Accepts either single words
     * or an array of words.
     */
    public static function remove_words ($words) {
        $words = is_array ($words) ? $words : array ($words);
        self::$remove_list = array_merge (self::$remove_list, $words);
    }

    /**
     * Transliterates characters to their ASCII equivalents.
     * $language specifies a priority for a specific language.
     * The latter is useful if languages have different rules for the same character.
     */
    public static function downcode ($text, $language = "") {
        self::init ($language);

        if (preg_match_all (self::$regex, $text, $matches)) {
            for ($i = 0; $i < count ($matches[0]); $i++) {
                $char = $matches[0][$i];
                if (isset (self::$map[$char])) {
                    $text = str_replace ($char, self::$map[$char], $text);
                }
            }
        }
        return $text;
    }

    /**
     * Filters a string, e.g., "Petty theft" to "petty-theft"
     */
    public static function filter ($text, $length = 60, $language = "", $file_name = false, $use_remove_list = true) {
        $text = self::downcode ($text,$language);

        if ($use_remove_list) {
            // remove all these words from the string before urlifying
            $text = preg_replace ('/\b(' . join ('|', self::$remove_list) . ')\b/i', '', $text);
        }

        // if downcode doesn't hit, the char will be stripped here
        $remove_pattern = ($file_name) ? '/[^_\-.\-a-zA-Z0-9\s]/u' : '/[^\s_\-a-zA-Z0-9]/u';
        $text = preg_replace ($remove_pattern, '', $text); // remove unneeded chars
        $text = str_replace ('_', ' ', $text);             // treat underscores as spaces
        $text = preg_replace ('/^\s+|\s+$/u', '', $text);  // trim leading/trailing spaces
        $text = preg_replace ('/[-\s]+/u', '-', $text);    // convert spaces to hyphens
        $text = strtolower ($text);                        // convert to lowercase
        return trim (substr ($text, 0, $length), '-');     // trim to first $length chars
    }

    /**
     * Alias of `URLify::downcode()`.
     */
    public static function transliterate ($text) {
        return self::downcode ($text);
    }
}
