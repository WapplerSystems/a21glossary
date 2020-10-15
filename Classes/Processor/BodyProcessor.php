<?php

namespace WapplerSystems\A21glossary\Processor;


class BodyProcessor extends AbstractProcessor {


    /**
     * replace
     * <body ....> to <body ...>|start_marker|
     * </body> to |end_marker|</body>
     *
     * @param $content
     * @return string
     */
    public function prepareContent($content)
    {

        if ($this->config['workIn'] === 'body') {
            preg_match("/(.*)(<body[^>]*>)(.*?)(<\/body>)(.*)/is", $content, $matches);
            return $matches[1].$matches[2]. $this->startMarker .$matches[3].$this->endMarker.$matches[4].$matches[5];
        }

        return $content;


    }


}