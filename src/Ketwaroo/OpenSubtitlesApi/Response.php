<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Ketwaroo\OpenSubtitlesApi;

/**
 * Successful 2xx
 * 
 *     200 OK
 *     206 Partial content; message 
 * 
 * Moved 3xx
 * 
 *     301 Moved (host) 
 * 
 * Errors 4xx
 * 
 *     401 Unauthorized
 *     402 Subtitles has invalid format
 *     403 SubHashes (content and sent subhash) are not same!
 *     404 Subtitles has invalid language!
 *     405 Not all mandatory parameters was specified
 *     406 No session
 *     407 Download limit reached
 *     408 Invalid parameters
 *     409 Method not found
 *     410 Other or unknown error
 *     411 Empty or invalid useragent
 *     412 %s has invalid format (reason)
 *     413 Invalid ImdbID
 *     414 Unknown User Agent
 *     415 Disabled user agent
 *     416 Internal subtitle validation failed 
 * 
 * Server Error 5xx
 * 
 *     503 Service Unavailable
 *     506 Server under maintenance 
 *
 * @author Yaasir Ketwaroo<ketwaroo.yaasir@gmail.com>
 */
class Response implements \ArrayAccess, \Serializable
{

    protected $code;
    protected $message       = '';
    protected $isSucess      = false;
    protected $isError       = false;
    protected $isMoved       = false;
    protected $isServerError = false;
    protected $rawResponse   = [];
    protected $timeTaken     = 0;

    public function __construct(array $rawResonse)
    {
        $this->rawResponse = $rawResonse;

        $this->proccessRawResponse();
    }

    protected function proccessRawResponse()
    {

        list($code, $message) = explode(' ', $this->rawResponse['status'], 2);
        $this->code    = intval($code);
        $this->message = $message;

        switch (intval($code / 100))
        {
            case 2:
                $this->isSucess      = true;
                break;
            case 3:
                $this->isMoved       = true;
                break;
            case 4:
                $this->isError       = true;
                break;
            case 5:
                $this->isServerError = true;
                break;
        }

        $this->timeTaken = floatval($this->rawResponse['seconds']);
    }

    /**
     * 
     * @return boolean
     */
    public function isSucess()
    {
        return $this->isSucess;
    }

    public function getData()
    {
        return isset($this->rawResponse['data']) ? $this->rawResponse['data'] : [];
    }

    public function getCode()
    {
        return $this->code;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function isError()
    {
        return $this->isError;
    }

    public function isMoved()
    {
        return $this->isMoved;
    }

    public function isServerError()
    {
        return $this->isServerError;
    }

    public function getRawResponse()
    {
        return $this->rawResponse;
    }

    public function getTimeTaken()
    {
        return $this->timeTaken;
    }

    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->rawResponse);
    }

    public function offsetGet($offset)
    {
        return $this->rawResponse[$offset];
    }

    public function offsetSet($offset, $value)
    {
        throw new Exception('set: readonly response');
    }

    public function offsetUnset($offset)
    {
        throw new Exception('unset: eadonly response');
    }

    public function __get($name)
    {
        return isset($this->rawResponse[$name]) ? $this->rawResponse[$name] : null;
    }

    public function serialize()
    {
        $reflect = new \ReflectionClass($this);
        $data    = [];
        foreach ($reflect->getProperties() as $prop)
        {
            /* @var $prop \ReflectionProperty */
            $propName        = $prop->getName();
            $data[$propName] = $this->{$propName};
        }
        return serialize($data);
    }

    public function unserialize($serialized)
    {
        $data = unserialize($serialized);

        foreach ($data as $k => $v)
        {
            $this->{$k} = $v;
        }
    }

}
