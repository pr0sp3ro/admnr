<?php
declare(strict_types=1);

namespace Adminer;

class AdminerColumnHider extends Plugin
{
    public function __construct(private string $assetsPath = 'adminer-plugins/assets')
    {
    }

    public function head(?bool $dark = null): void
    {
        if (!isset($_GET['select']) || !is_string($_GET['select'])) {
            return;
        }

        $context = [
            DRIVER,
            SERVER,
            (string) ($_GET['username'] ?? ''),
            DB,
            (string) ($_GET['ns'] ?? ''),
            $_GET['select'],
        ];

        echo '<meta name="adminer-column-hider" content="' . h(json_encode($context)) . '">' . "\n";
        echo '<link rel="stylesheet" href="' . h($this->assetsPath . '/column-hider.css') . '">' . "\n";
        echo script_src($this->assetsPath . '/column-hider.js', true);
    }
}
