<?php

/** This plugin replaces UNIX timestamps with human-readable dates in your local format.
* Mouse click on the date field reveals timestamp back.
*
* @link https://www.adminer.org/plugins/#use
* @author Anonymous
* @license http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2 (one or other)
*/
class AdminerReadableDates extends Adminer\Plugin
{
    public function selectVal(&$val, $link, $field, $original)
    {
        if ($link || !preg_match('~int~i', $field['type'])
            || !preg_match('~(?:^|_)(?:at|time|timestamp)$~i', $field['field'])
            || !is_scalar($original) || !preg_match('~^\d{10}$~', (string) $original)) {
            return;
        }

        $val = '<span class="adminer-timestamp" data-timestamp="' . Adminer\h($original) . '">' . $val . '</span>';
    }

    public function head($dark = null)
    {
        echo Adminer\script(<<<'JS'
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.adminer-timestamp').forEach(element => {
        const timestamp = element.dataset.timestamp;
        const date = new Date(Number(timestamp) * 1000).toLocaleString();
        let readable = true;
        element.textContent = date;
        element.title = timestamp;
        element.addEventListener('click', () => {
            readable = !readable;
            element.textContent = readable ? date : timestamp;
            element.title = readable ? timestamp : date;
        });
    });
});
JS);
    }
}
