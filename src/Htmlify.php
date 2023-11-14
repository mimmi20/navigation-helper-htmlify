<?php
/**
 * This file is part of the mimmi20/navigation-helper-htmlify package.
 *
 * Copyright (c) 2021-2023, Thomas Mueller <mimmi20@live.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);

namespace Mimmi20\NavigationHelper\Htmlify;

use Laminas\I18n\Exception\RuntimeException;
use Laminas\I18n\View\Helper\Translate;
use Laminas\Navigation\Page\AbstractPage;
use Laminas\View\Exception\InvalidArgumentException;
use Laminas\View\Helper\EscapeHtml;
use Mezzio\Navigation\Page\PageInterface;
use Mimmi20\LaminasView\Helper\HtmlElement\Helper\HtmlElementInterface;

use function array_diff_key;
use function array_flip;
use function array_key_exists;
use function array_merge;
use function assert;
use function is_string;
use function mb_strrpos;
use function mb_strtolower;
use function mb_substr;
use function trim;

final class Htmlify implements HtmlifyInterface
{
    /** @throws void */
    public function __construct(
        private readonly EscapeHtml $escaper,
        private readonly HtmlElementInterface $htmlElement,
        private readonly Translate | null $translator = null,
    ) {
        // nothing to do
    }

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
    ): string {
        $label = (string) $page->getLabel();
        $title = $page->getTitle();

        if ($this->translator !== null) {
            $textDomain = $page->getTextDomain();
            assert($textDomain === null || is_string($textDomain));

            if (!empty($label)) {
                $label = ($this->translator)($label, $textDomain);
            }

            if (!empty($title)) {
                $title = ($this->translator)($title, $textDomain);
            }
        }

        // get attribs for element

        $attributes['id']    = $page->getId();
        $attributes['title'] = $title;

        if (!$addClassToListItem) {
            $attributes['class'] = $page->getClass();
        }

        if ($convertToButton) {
            $element = 'button';
        } elseif ($page->getHref() !== '') {
            $element              = 'a';
            $attributes['href']   = $page->getHref();
            $attributes['target'] = $page->getTarget();
        } else {
            $element = 'span';
        }

        // remove sitemap specific attributes
        $attributes = array_diff_key(
            array_merge($attributes, $page->getCustomProperties()),
            array_flip(['lastmod', 'changefreq', 'priority']),
        );

        if (!empty($label) && $escapeLabel) {
            $label = ($this->escaper)($label);

            assert(is_string($label));
        }

        if (array_key_exists('id', $attributes) && is_string($attributes['id'])) {
            $attributes['id'] = $this->normalizeId($prefix, $attributes['id']);
        }

        return $this->htmlElement->toHtml($element, $attributes, $label);
    }

    /**
     * Normalize an ID
     *
     * @throws void
     */
    private function normalizeId(string $prefix, string $value): string
    {
        $prefix = mb_strtolower(trim(mb_substr($prefix, (int) mb_strrpos($prefix, '\\')), '\\'));

        return $prefix . '-' . $value;
    }
}
