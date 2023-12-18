<?php
namespace Markdown2Html;

class ExtendedParsedown extends \ParsedownExtra
{
    protected function inlineLink($Excerpt)
    {
        $link = parent::inlineLink($Excerpt);

        if (!isset($link['element']['attributes']['href'])) {
            return $link;
        }

        $href = $link['element']['attributes']['href'];

        if (preg_match('/^https?:\/\//', $href)) {
            return $link;
        }

        $ext = pathinfo($href, PATHINFO_EXTENSION);
        if (!in_array($ext, ['md', 'markdown'], true)) {
            return $link;
        }

        $href = preg_replace('/(\/|^)\d+\./', '$1', $href);
        $href = substr($href, 0, -(strlen($ext) + 1)) . '.html';

        $link['element']['attributes']['href'] = $href;

        return $link;
    }

}
