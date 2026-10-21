<?php

/** Pretty print JSON values in edit
* @link https://www.adminer.org/plugins/#use
* @author Christopher Chen
* @license https://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
* @license https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2 (one or other)
*/
class AdminerPrettyJsonColumn extends Adminer\Plugin
{
    public function head($dark = null)
    {
        echo Adminer\script_src('adminer-plugins/assets/json-editor.js', true);
    }

    public function editInput($table, $field, $attrs, $value)
    {
        $native = preg_match('~^jsonb?$~i', $field['type']);

        if (!is_string($value) && $value !== null) {
            return;
        }

        $text  = trim((string) $value);
        $json  = json_decode($text);
        $valid = json_last_error() === JSON_ERROR_NONE;

        if (!$native && (!$valid || !in_array(substr($text, 0, 1), ['{', '['], true))) {
            return;
        }

        if ($valid) {
            $formatted = json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION);

            // Keep number spelling and precision unchanged when formatting.
            $numberPattern = '~"(?:[^"\\\\]|\\\\.)*"(*SKIP)(*F)|-?\d+(?:\.\d+)?(?:[eE][+-]?\d+)?~s';
            preg_match_all($numberPattern, $text, $originalNumbers);
            preg_match_all($numberPattern, (string) $formatted, $formattedNumbers);

            if ($formatted !== false && $originalNumbers[0] === $formattedNumbers[0]) {
                $value = $formatted;
            }
        }

        return "<textarea$attrs cols='60' rows='16' class='jush-json adminer-json-editor'>" . Adminer\h($value) . '</textarea>';
    }

	protected $translations = array(
		'cs' => array('' => 'V editaci zobrazí syntaxi u JSONu'),
		'de' => array('' => 'JSON-Werte in der Bearbeitung hübsch drucken'),
		'pl' => array('' => 'Ładnie drukuj wartości JSON w edycji'),
		'ro' => array('' => 'Afisare frumoasa a valorilor JSON în editare'),
		'ja' => array('' => '編集時に JSON 文字列を見易く表示'),
	);
}
