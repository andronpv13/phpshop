<?php

function hmac($key, $data) {
    // Вычисление подписи методом HMAC
    $b = 64; // byte length for md5

    if (strlen($key) > $b) {
        $key = pack("H*", md5($key));
    }

    $key = str_pad($key, $b, chr(0x00));
    $k_ipad = $key ^ str_pad(null, $b, chr(0x36));
    $k_opad = $key ^ str_pad(null, $b, chr(0x5c));

    return md5($k_opad . pack("H*", md5($k_ipad . $data)));
}

?>