<?php
/**
 * This file is part of the mimmi20/navigation-helper-htmlify package.
 *
 * Copyright (c) 2021, Thomas Mueller <mimmi20@live.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);

namespace Mimmi20\NavigationHelper\Htmlify;

use Interop\Container\ContainerInterface;
use Laminas\I18n\View\Helper\Translate;
use Laminas\View\Helper\EscapeHtml;
use Laminas\View\HelperPluginManager as ViewHelperPluginManager;
use Mimmi20\LaminasView\Helper\HtmlElement\Helper\HtmlElementInterface;
use Psr\Container\ContainerExceptionInterface;

use function assert;

final class HtmlifyFactory
{
    /**
     * Create and return a navigation view helper instance.
     *
     * @throws ContainerExceptionInterface
     */
    public function __invoke(ContainerInterface $container): Htmlify
    {
        $plugin     = $container->get(ViewHelperPluginManager::class);
        $translator = null;

        assert($plugin instanceof ViewHelperPluginManager);

        if ($plugin->has(Translate::class)) {
            $translator = $plugin->get(Translate::class);

            assert($translator instanceof Translate);
        }

        $escaper = $plugin->get(EscapeHtml::class);
        $element = $container->get(HtmlElementInterface::class);

        assert($escaper instanceof EscapeHtml);
        assert($element instanceof HtmlElementInterface);

        return new Htmlify($escaper, $element, $translator);
    }
}
