<?php

namespace Ketwaroo\OpenSubtitlesApi;

use Ketwaroo\OpenSubtitlesApi\Response;

/**
 * @author Yaasir Ketwaroo<ketwaroo.yaasir@gmail.com>
 */
interface InterfaceOutput {

    public function renderSubtitleList(Response $response);
}
