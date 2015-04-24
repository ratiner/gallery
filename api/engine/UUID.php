<?php
class UUID {

    public static $alphabet = "123456789abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ";
    public static $ID_LENGTH = 17;
    public static $UGLYID_LENGTH = 10;
    public static function Generate() {

        $min = 10000000000000000;
        $max = 99999999999999999;

        //$rand = $this->crypto_rand_secure(0, 2);
        //if($rand == 0)
        //    $rand =  $this->crypto_rand_secure(1000000000, 4294967295);
        //else {
        //     $rand = $this->crypto_rand_secure(100000000, 999999999);
        //}

        $range = $max - $min;
        if ($range < 0) return $min; // not so random...
        $log = log($range, 2);
        $bytes = (int) ($log / 8) + 1; // length in bytes
        $bits = (int) $log + 1; // length in bits
        $filter = (int) (1 << $bits) - 1; // set all lower bits to 1
        do {
            $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
            $rnd = $rnd & $filter; // discard irrelevant bits
        } while ($rnd >= $range);
        return $min + $rnd;
    }


    public static function Encode($id) {
        $num = new Math_BigInteger($id);
        $base_count = new Math_BigInteger(strlen(UUID::$alphabet));
        $encoded = '';
        while ($num->compare($base_count) >= 0) {
            list($div, $mod) = $num->divide($base_count);
            $mod = intval($mod->toString());
            $encoded = UUID::$alphabet[$mod] . $encoded;
            $num = $div;
        }

        if($num->compare(new Math_BigInteger(0)) >0) {
            $num =  intval($num->toString());
            $encoded = UUID::$alphabet[$num] . $encoded;
        }
        return $encoded;

        /*
         *
         $base_count = strlen(UUID::$alphabet);
        $encoded = '';
        while ($num >= $base_count) {
            $div = $num / $base_count;
            $mod = ($num - ($base_count * intval($div)));
            $encoded = UUID::$alphabet[$mod] . $encoded;
            $num = intval($div);
        }
        if ($num) {
            $encoded = UUID::$alphabet[$num] . $encoded;
        }
        return $encoded;
         * */
    }

    public static function Decode($encId) {
        $decoded = new Math_BigInteger(0);
        $multi = new Math_BigInteger(1);
        $alphabetLength = new Math_BigInteger(strlen(UUID::$alphabet));

        while (strlen($encId) > 0) {
            $digit = $encId[strlen($encId)-1];
            $pos = new Math_BigInteger(strpos(UUID::$alphabet, $digit));
            $decoded = $decoded->add($multi->multiply($pos));
            $multi = $multi->multiply($alphabetLength);
            $encId = substr($encId, 0, -1);
        }
        return $decoded->toString();
        /*
        $decoded = 0;
        $multi = 1;
        while (strlen($encId) > 0) {
            $digit = $encId[strlen($encId)-1];
            $decoded += $multi * strpos(UUID::$alphabet, $digit);
            $multi = $multi * strlen(UUID::$alphabet);
            $encId = substr($encId, 0, -1);
        }

        return $decoded;
        */
    }



    public static function v3($namespace, $name) {
        if(!self::is_valid($namespace)) return false;

        // Get hexadecimal components of namespace
        $nhex = str_replace(array('-','{','}'), '', $namespace);

        // Binary Value
        $nstr = '';

        // Convert Namespace UUID to bits
        for($i = 0; $i < strlen($nhex); $i+=2) {
            $nstr .= chr(hexdec($nhex[$i].$nhex[$i+1]));
        }

        // Calculate hash value
        $hash = md5($nstr . $name);

        return sprintf('%08s-%04s-%04x-%04x-%12s',

            // 32 bits for "time_low"
            substr($hash, 0, 8),

            // 16 bits for "time_mid"
            substr($hash, 8, 4),

            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 3
            (hexdec(substr($hash, 12, 4)) & 0x0fff) | 0x3000,

            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            (hexdec(substr($hash, 16, 4)) & 0x3fff) | 0x8000,

            // 48 bits for "node"
            substr($hash, 20, 12)
        );
    }

    public static function v4() {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',

            // 32 bits for "time_low"
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),

            // 16 bits for "time_mid"
            mt_rand(0, 0xffff),

            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 4
            mt_rand(0, 0x0fff) | 0x4000,

            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            mt_rand(0, 0x3fff) | 0x8000,

            // 48 bits for "node"
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }

    public static function v5($namespace, $name) {
        if(!self::is_valid($namespace)) return false;

        // Get hexadecimal components of namespace
        $nhex = str_replace(array('-','{','}'), '', $namespace);

        // Binary Value
        $nstr = '';

        // Convert Namespace UUID to bits
        for($i = 0; $i < strlen($nhex); $i+=2) {
            $nstr .= chr(hexdec($nhex[$i].$nhex[$i+1]));
        }

        // Calculate hash value
        $hash = sha1($nstr . $name);

        return sprintf('%08s-%04s-%04x-%04x-%12s',

            // 32 bits for "time_low"
            substr($hash, 0, 8),

            // 16 bits for "time_mid"
            substr($hash, 8, 4),

            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 5
            (hexdec(substr($hash, 12, 4)) & 0x0fff) | 0x5000,

            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            (hexdec(substr($hash, 16, 4)) & 0x3fff) | 0x8000,

            // 48 bits for "node"
            substr($hash, 20, 12)
        );
    }

    public static function is_valid($uuid) {
        return preg_match('/^\{?[0-9a-f]{8}\-?[0-9a-f]{4}\-?[0-9a-f]{4}\-?'.
            '[0-9a-f]{4}\-?[0-9a-f]{12}\}?$/i', $uuid) === 1;
    }

    private function PermuteId($id) {
        $l1 = ($id >> 16)&65535;
        $r1 = $id&65535;

        $l2 = 0;
        $r2 = 0;
        for($i=0; $i<3; $i++) {
            $l2 = $r1;
            $r2 = $l1 ^ ($this->RoundFunction($r1) * 65535);
            $l1 = $l2;
            $r1 = $r2;
        }
        return (($r1 << 16) + $l1);

    }

    private function RoundFunction($input) {
        return ((1369 * $input + 150889) % 714025) / 714025.0;
    }



}

// Usage
// Named-based UUID.

$v3uuid = UUID::v3('1546058f-5a25-4334-85ae-e68f2a44bbaf', 'SomeRandomString');
$v5uuid = UUID::v5('1546058f-5a25-4334-85ae-e68f2a44bbaf', 'SomeRandomString');

// Pseudo-random UUID

$v4uuid = UUID::v4();