<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Ketwaroo\OpenSubtitlesApi\Output;

use Ketwaroo\OpenSubtitlesApi\InterfaceOutput;
use Ketwaroo\OpenSubtitlesApi\Response;

/**
 * Description of MediaPlayerClassic
 *
 * @author Yaasir Ketwaroo<ketwaroo.yaasir@gmail.com>
 */
class MediaPlayerClassic implements InterfaceOutput
{

    public function renderSubtitleList(Response $response)
    {

        $out = [];

        $out[] = 'ticket=' . time();

        $fieldMap = [
            'movie'    => 'MovieName',
            'subtitle' => 'IDSubtitleFile',
            'name'     => 'SubFileName',
            'discs'    => 'SubSumCD',
            'disc_no'  => 'SubActualCD',
            'format'   => 'SubFormat',
            'iso639_2' => 'ISO639',
            'language' => 'LanguageName',
            'nick'     => 'UserNickName',
            'email'    => 'MAILS_ARE_PROTECTED:)',
        ];

        foreach ($response->getData() as $sub)
        {

            foreach ($fieldMap as $local => $remote)
            {

                $out[] = $local . '=' . (isset($sub[$remote]) ? $sub[$remote] : $remote);
            }
            $out[] = 'endsubtitle';
            $out[] = 'endmovie';
        }
        return implode(PHP_EOL, $out);
    }

}
