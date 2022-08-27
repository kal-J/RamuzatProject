<?php

/**
 * Description of Crypto
 *
 * @author allan_jes
 */
class Crypto {

    protected $mcrypt_cipher = MCRYPT_RIJNDAEL_128;
    protected $mcrypt_mode = MCRYPT_MODE_CBC;

    private function decrypt($key, $iv, $encrypted) {
        $iv_utf = mb_convert_encoding($iv, 'UTF-8');
        return mcrypt_decrypt($this->mcrypt_cipher, $key, base64_decode($encrypted), $this->mcrypt_mode, $iv_utf);
    }

    public function encrypt($key, $iv, $password) {
        $block = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB);
        $padding = $block - (strlen($password) % $block);
        $password .= str_repeat(chr($padding), $padding);
        return mcrypt_encrypt($this->mcrypt_cipher, $key, $password, $this->mcrypt_mode, $iv);
    }

    public function compare($encrypted, $password) {
        $key = "25011jabarajab2322130";
        $iv = "1234567890123456";

        $decrypted_pass =  $this->decrypt($key, $iv, $encrypted);
        return $decrypted_pass === $password;
    }

    public function PBKDF1($pass, $salt, $count, $cb) {
        static $base;
        static $extra;
        static $extracount = 0;
        static $hashno;
        static $state = 0;

        if ($state == 0) {
            $hashno = 0;
            $state = 1;

            $key = $pass . $salt;
            $base = sha1($key, true);
            for ($i = 2; $i < $count; $i++) {
                $base = sha1($base, true);
            }
        }

        $result = "";

        if ($extracount > 0) {
            $rlen = strlen($extra) - $extracount;
            if ($rlen >= $cb) {
                $result = substr($extra, $extracount, $cb);
                if ($rlen > $cb) {
                    $extracount += $cb;
                } else {
                    $extra = null;
                    $extracount = 0;
                }
                return $result;
            }
            $result = substr($extra, $rlen, $rlen);
        }

        $current = "";
        $clen = 0;
        $remain = $cb - strlen($result);
        while ($remain > $clen) {
            if ($hashno == 0) {
                $current = sha1($base, true);
            } else if ($hashno < 1000) {
                $n = sprintf("%d", $hashno);
                $tmp = $n . $base;
                $current .= sha1($tmp, true);
            }
            $hashno++;
            $clen = strlen($current);
        }

        // $current now holds at least as many bytes as we need
        $result .= substr($current, 0, $remain);

        // Save any left over bytes for any future requests
        if ($clen > $remain) {
            $extra = $current;
            $extracount = $remain;
        }

        return $result;
    }

}
