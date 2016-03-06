<?php

/**
 * 
 */
echo 'ISDb v1';

// some setup while we're at it.
if (!is_dir(__DIR__ . '/cache'))
{
    mkdir(__DIR__ . '/cache', 0766);
}
