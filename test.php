<?php
/**
 * 
 */
echo 'ISDb v1';
file_put_contents(__FILE__.'.log', print_r($_REQUEST,1));
file_put_contents(__FILE__.'.log', print_r($_SERVER,1), FILE_APPEND);
