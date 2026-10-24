<?php

use Adminer\AdminerColumnHider;
use Adminer\AdminerDynamicSidebar;
use Adminer\AdminerSavedConnections;
use Adminer\AdminerTableGroups;
use Adminer\CopyStructureExport;
use Adminer\Plugin;

require_once __DIR__ . '/adminer-plugins/column-hider/column-hider.php';
require_once __DIR__ . '/adminer-plugins/dynamic-sidebar/dynamic-sidebar.php';
require_once __DIR__ . '/adminer-plugins/saved-connections/saved-connections.php';

$config = require __DIR__ . '/config.local.php';

class AdminerLocalLogin extends Plugin
{
    public function __construct(private string $permanentLoginKey)
    {
    }

    public function permanentLogin($create = false)
    {
        return $this->permanentLoginKey;
    }
}

return [
    new AdminerLocalLogin(permanentLoginKey: $config['permanent_login_key']),
    new AdminerLoginPasswordLess(password_hash('password', PASSWORD_DEFAULT)),
    new AdminerFileUpload($config['file_upload_path'], '?adminer-file=', 'pdf|txt|csv|json|png|jpe?g|gif|webp'),
    new AdminerUUIDGenerator(),
    new AdminerEditForeignSearch(),
    new AdminerForeignTooltip(),
    new AdminerShowColumnComment(),
    new AdminerAddButtonColumnHeader(),
    new searchAutocomplete(),
    new AdminerReadableDates(),
    new AdminerTreeViewer('adminer-plugins/tree-view.js'),
    new AdminerTinymce('tinymce/tinymce.min.js'),
    new AdminerFavoriteQuery(),
    new AdminerFormatSql(),
    new AdminerJsonPreview(),
    new AdminerPrettyJsonColumn(),
    new AdminerEnumOption(),
    new AdminerEditTextarea(),
    new AdminerCopy(),
    new AdminerColumnHider(assetsPath: 'adminer-plugins/column-hider/assets'),
    new AdminerTablesFilter(),
    new AdminerTableGroups(),
    new AdminerDynamicSidebar(
        cssFile: 'adminer-plugins/dynamic-sidebar/assets/dynamic-sidebar.css',
        jsFile: 'adminer-plugins/dynamic-sidebar/assets/dynamic-sidebar.js',
    ),
    new AdminerSavedConnections(
        cssFile: 'adminer-plugins/saved-connections/assets/saved-connections.css',
        jsFile: 'adminer-plugins/saved-connections/assets/saved-connections.js',
    ),
    new AdminerHumanReadableSize(),
    new CopyStructureExport(),
    new DbStructure(),
    new AdminerAiMdExport(),
    new AdminerDumpMarkdown(),
];
