<?php

function ssl_decrypt($data){
    $open_ssl = openssl_decrypt(
        base64_decode($data),
        'AES-128-CBC',
        SECRET,
        0,
        SECRET_IV
    );
    return $open_ssl;
}

function ssl_crypt($data){
    $open_ssl = openssl_encrypt(
        json_encode($data),
        'AES-128-CBC',
        SECRET,
        0,
        SECRET_IV
    );
    return base64_encode($open_ssl);
}