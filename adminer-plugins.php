<?php

use Adminer\AdminerDynamicSidebar;
use Adminer\AdminerTableGroups;
use Adminer\CopyStructureExport;
use Adminer\Plugin;

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
    new Adminer\AdminerColumnHider(),
    new AdminerTablesFilter(),
    new AdminerTableGroups(),
    new AdminerDynamicSidebar(),
    new AdminerHumanReadableSize(),
    new CopyStructureExport(),
    new DbStructure(),
    new AdminerAiMdExport(),
    new AdminerDumpMarkdown(),
];
