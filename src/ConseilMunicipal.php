<?php

namespace VincentTrotot\ConseilMunicipal;

use Timber\Post;

class ConseilMunicipal extends Post
{
    public $ordre_du_jour;
    public $audio;
    public $pdf;

    public function __construct($pid = null)
    {
        parent::__construct($pid);
        $this->ordre_du_jour['name'] = $this->meta('name-ordre-du-jour');
        $this->ordre_du_jour['url'] = $this->meta('url-ordre-du-jour');
        $this->audio['name'] = $this->meta('name-audio');
        $this->audio['url'] = $this->meta('url-audio');
        $this->pdf['name'] = $this->meta('name-pdf');
        $this->pdf['url'] = $this->meta('url-pdf');
    }
}
