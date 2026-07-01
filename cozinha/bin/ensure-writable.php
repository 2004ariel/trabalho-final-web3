<?php

/**
 * Garante que as subpastas de writable/ existam após um `composer install`
 * em uma máquina nova (writable/ é ignorado no git para não versionar
 * cache, logs e sessões). Sem isso o CodeIgniter lança CacheException
 * no filtro global "pagecache" assim que writable/cache/ não existe.
 */

$base = dirname(__DIR__) . '/writable';

foreach (['cache', 'logs', 'session', 'uploads', 'debugbar'] as $dir) {
    $path = $base . '/' . $dir;
    if (! is_dir($path)) {
        mkdir($path, 0775, true);
    }
}

echo "writable/ pronta.\n";
