<?php
declare(strict_types=1);

namespace Adminer;

/** Resize the sidebar and remember its width in the browser. */
class AdminerDynamicSidebar extends Plugin
{
    public function __construct(
        private string $cssFile = 'adminer-plugins/assets/dynamic-sidebar.css',
        private string $jsFile = 'adminer-plugins/assets/dynamic-sidebar.js',
    ) {
    }

    public function head(?bool $dark = null): void
    {
        $cssVersion = crc32((string) file_get_contents(__DIR__ . '/assets/dynamic-sidebar.css'));
        $jsVersion  = crc32((string) file_get_contents(__DIR__ . '/assets/dynamic-sidebar.js'));

        echo '<link rel="stylesheet" href="' . h($this->cssFile . '?v=' . $cssVersion) . '">' . "\n";
        echo script_src($this->jsFile . '?v=' . $jsVersion, true);
    }
}
