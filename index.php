<?php

error_reporting(E_ALL);
require './vendor/autoload.php';

if (!empty($_GET['name']))
{
    $cacheFile = __DIR__ . '/cache/Response' . sha1($_SERVER['QUERY_STRING']);

    if (is_file($cacheFile) && filemtime($cacheFile) > (time() - 3600))
    {
        $response = unserialize(file_get_contents($cacheFile));
    }
    else
    {

        $configSrc = json_decode(file_get_contents(__DIR__ . '/config.json'));
        $config    = new \Ketwaroo\OpenSubtitlesApi\Config();
        $config->setUsername($configSrc->username)
                ->setPassword($configSrc->password)
                ->setTokenCacheFile(__DIR__ . '/token-cache')
                ->setLanguage($configSrc->language)
                ->setUseragent($configSrc->userAgent);

        $name = pathinfo($_GET['name'][0], PATHINFO_FILENAME);

        // match season ep
        $seasonEp = [];
        preg_match('~s(\d+)e(\d+)~i', $name, $seasonEp);
        list($seasonEpString, $season, $episode) = array_pad($seasonEp, 3, '');

        // unnecesary words list.
        $cruft = array_filter(file(__DIR__ . '/cruft-words.txt'));

        $rep = [
            '~\[[^\]]+\]~'               => ' ', // remove "[tags]"
            '~[^a-z0-9\' ]+~i'           => ' ', // none regular characters to string
            '~' . implode('|', $cruft) . '~i' => ' ', // remove unrelated words.
            '~ +~i'                      => ' ', //cleanup.
        ];

        $filteredName = trim(preg_replace(array_keys($rep), array_values($rep), str_replace($seasonEpString, '', $name)));

        $query = array_filter([
            'query'         => $filteredName,
            'season'        => '' . intval($season),
            'episode'       => '' . intval($episode),
            'sublanguageid' => $config->getLanguage(),
        ]);

        if (empty($query) || empty($query['query']))
        {
            exit;
        }



        $op = new \Ketwaroo\OpenSubtitlesApi($config);


        $response = $op->searchSubtitles([
            $query
        ]);
        file_put_contents($cacheFile, serialize($response));
    }
    $mpcOut = new Ketwaroo\OpenSubtitlesApi\Output\MediaPlayerClassic;

    echo $mpcOut->renderSubtitleList($response);
}
