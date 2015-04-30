<?php

namespace Application;

class Markdown extends \cebe\markdown\GithubMarkdown
{
    // We don't need headline in this case...
    protected function identifyHeadline($line, $lines, $current)
    {
        return false;
    }
}
