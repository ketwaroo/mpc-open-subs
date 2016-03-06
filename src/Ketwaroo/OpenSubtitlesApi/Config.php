<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Ketwaroo\OpenSubtitlesApi;

/**
 * Description of Config
 *
 * @author Yaasir Ketwaroo<ketwaroo.yaasir@gmail.com>
 */
class Config {

    protected $username;
    protected $password;
    protected $useragent = 'OSTestUserAgent';
    protected $language = 'eng';
    protected $url = 'http://api.opensubtitles.org:80/xml-rpc';
    protected $tokenCacheFile = './token-cache';

    public function getUsername() {
        return $this->username;
    }

    public function getPassword() {
        return $this->password;
    }

    public function getLanguage() {
        return $this->language;
    }

    public function getUseragent() {
        return $this->useragent;
    }

    public function setUsername($username) {
        $this->username = $username;
        return $this;
    }

    public function setPassword($password) {
        $this->password = $password;
        return $this;
    }

    public function setLanguage($language) {
        $this->language = $language;
        return $this;
    }

    public function setUseragent($useragent = 'OSTestUserAgent') {
        $this->useragent = $useragent;
        return $this;
    }

    public function getTokenCacheFile() {
        return $this->tokenCacheFile;
    }

    public function setTokenCacheFile($tokenCacheFile) {
        $this->tokenCacheFile = $tokenCacheFile;
        return $this;
    }

    function getUrl() {
        return $this->url;
    }

    function setUrl($url='http://api.opensubtitles.org/xml-rpc') {
        $this->url = $url;
    }
}
