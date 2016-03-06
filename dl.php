<?php

error_reporting(E_ALL);
require './vendor/autoload.php';

if (!empty($_GET['id']))
{

    $subId = $_GET['id'];

    $cacheFile = __DIR__ . '/cache/' . $subId;

    if (!is_file($cacheFile))
    {

        $config = new \Ketwaroo\OpenSubtitlesApi\Config();

        $configSrc = json_decode(file_get_contents(__DIR__ . '/config.json'));

        $config->setUsername($configSrc->username)
                ->setPassword($configSrc->password)
                ->setTokenCacheFile(__DIR__ . '/token-cache')
                ->setLanguage($configSrc->language);

        $op = new \Ketwaroo\OpenSubtitlesApi($config);

        $response = $op->downloadSubtitles([$subId]);
        
        if ($response->isSucess())
        {
            $fileData = base64_decode($response->data[0]['data']);
            file_put_contents($cacheFile, $fileData);
        }
        else
        {
            exit; // nothing can be done.
        }
    }

    echo gzdecode(file_get_contents($cacheFile));
}


