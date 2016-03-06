<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Ketwaroo;

use Ketwaroo\OpenSubtitlesApi\Response;
use Ketwaroo\OpenSubtitlesApi\Config;

/**
 * Description of OpenSubtitlesApi
 *
 * @author Yaasir Ketwaroo<ketwaroo.yaasir@gmail.com>
 */
class OpenSubtitlesApi
{

    const TOKEN_TIMEOUT = 600;

    /**
     *
     * @var Config 
     */
    protected $config;
    protected $token = '';

    public function __construct(Config $config)
    {
        $this->setConfig($config);
    }

    /**
     * 
     * @return Response
     */
    public function noOperation()
    {

        return $this->processRequest(ucfirst(__FUNCTION__), $this->getToken());
    }

    /**
     * <pre><code>$query = 
     * array(
     *     array(
     *         'sublanguageid' => $sublanguageid,
     *         'moviehash'     => $moviehash,
     *         'moviebytesize' => $moviesize,
     *         'imdbid'        => $imdbid,
     *         'query'         => 'movie name',
     *         "season"        => 'season number',
     *         "episode"       => 'episode number',
     *         'tag'           => 'tag'),
     *     array('...')
     * );
     * </code></pre>
     * @param array $query
     * @param int $limit
     * @return Response
     */
    public function searchSubtitles(array $query, $limit = 100)
    {
        return $this->processRequest(ucfirst(__FUNCTION__), $this->getToken(), $query, ['limit' => $limit]);
    }
    
    /**
     * 
     * @param array $subtitleIds ints
     * @return Response
     */
    public function downloadSubtitles(array $subtitleIds)
    {
        return $this->processRequest(ucfirst(__FUNCTION__), $this->getToken(), $subtitleIds);
    }

    /**
     * 
     * @return Config
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * 
     * @param Config $config
     * @return \Ketwaroo\OpenSubtitlesApi
     */
    public function setConfig(Config $config)
    {
        $this->config = $config;
        return $this;
    }

    /**
     * 
     * @param type $username
     * @param type $password
     * @param type $language
     * @param type $useragent
     * 
     * @return Response
     */
    protected function logIn($username, $password, $language, $useragent = 'OSTestUserAgent')
    {
        return $this->processRequest(ucfirst(__FUNCTION__), $username, $password, $language, $useragent);
    }

    protected function getToken()
    {

        $tokenFile = $this->getConfig()->getTokenCacheFile();

        if (is_file($tokenFile) && filemtime($tokenFile) > (time() - static::TOKEN_TIMEOUT))
        {
            $this->setToken(file_get_contents($tokenFile));
        }
        else
        {

            $config = $this->getConfig();

            $loginResponse = $this->logIn($config->getUsername(), $config->getPassword(), $config->getLanguage(), $config->getUseragent());

            if ($loginResponse->isError())
            {
                throw new Exception($loginResponse->getMessage(), $loginResponse->getCode());
            }

            if (!isset($loginResponse['token']))
            {
                throw new Exception('Could not fetch token');
            }

            file_put_contents($tokenFile, $loginResponse['token']);

            $this->setToken($loginResponse['token']);
        }

        return $this->token;
    }

    protected function setToken($token)
    {
        $this->token = $token;
        return $this;
    }

    /**
     * 
     * @param type $method
     * @param mixed $args method arguments.
     * 
     * @return Response
     */
    protected function processRequest($method)
    {

        $args = array_slice(func_get_args(), 1);

        $request = xmlrpc_encode_request($method, $args);

        $context = stream_context_create([
            'http' => [
                'method'  => "POST",
                'header'  => "Content-Type: text/xml",
                'content' => $request
            ]
        ]);

        $file     = file_get_contents($this->getConfig()->getUrl(), false, $context);
        $response = xmlrpc_decode($file);

        if (xmlrpc_is_fault($response))
        {
            throw new Exception($response['faultString'], $response['faultCode']);
        }
        return new Response($response);
    }

}
