<?php

/**
 * Equivalent to zlib_encode($data, ZLIB_ENCODING_RAW, $level)
 *
 * @param string $data
 * @param int $level
 *
 * @return string
 */
function libdeflate_deflate_compress(string $data, int $level = 6) : string{}

/**
 * Equivalent to zlib_encode($data, ZLIB_ENCODING_DEFLATE, $level)
 *
 * @param string $data
 * @param int $level
 *
 * @return string
 */
function libdeflate_zlib_compress(string $data, int $level = 6) : string{}

/**
 * Equivalent to zlib_encode($data, ZLIB_ENCODING_GZIP, $level)
 *
 * @param string $data
 * @param int $level
 *
 * @return string
 */
function libdeflate_gzip_compress(string $data, int $level = 6) : string{}