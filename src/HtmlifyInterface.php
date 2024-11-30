<?php

/**
 * This file is part of the mimmi20/navigation-helper-htmlify package.
 *
 * Copyright (c) 2021-2024, Thomas Mueller <mimmi20@live.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);

namespace Mimmi20\NavigationHelper\Htmlify;

use Laminas\I18n\Exception\RuntimeException;
use Laminas\Navigation\Page\AbstractPage;
use Laminas\View\Exception\InvalidArgumentException;
use Mimmi20\Mezzio\Navigation\Page\PageInterface;

interface HtmlifyInterface
{
    /**
     * Returns an HTML string for the given page
     *
     * @param string                     $prefix             prefix to normalize the id attribute
     * @param AbstractPage|PageInterface $page               page to generate HTML for
     * @param bool                       $escapeLabel        Whether to escape the label
     * @param bool                       $addClassToListItem Whether to add the page class to the list item
     * @param array<string, string>      $attributes
     * @param bool                       $convertToButton    Whether to convert a link to a button
     *
     * @return string HTML string
     *
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public function toHtml(
        string $prefix,
        AbstractPage | PageInterface $page,
        bool $escapeLabel = true,
        bool $addClassToListItem = false,
        array $attributes = [],
        bool $convertToButton = false,
    ): string;
}
