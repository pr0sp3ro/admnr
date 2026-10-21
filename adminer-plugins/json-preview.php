<?php

/**
 * Displays JSON in a modal preview.
 *
 * @link https://github.com/pematon/adminer-plugins
 * @author Peter Knut
 * @copyright 2014-2018 Pematon, s.r.o. (http://www.pematon.com/)
 */
class AdminerJsonPreview
{
    public function __construct(private bool $inTable = true, private bool $inEdit = true)
    {
    }

    public function head($dark = null)
    {
        echo '<link rel="stylesheet" href="adminer-plugins/assets/json-preview.css">' . "\n";
        echo Adminer\script_src('adminer-plugins/assets/json-preview.js', true);
    }

    public function selectVal(&$val, $link, array $field, $original)
    {
        if ($this->inTable) {
            return $this->previewButton(field: $field, value: $original);
        }
    }

    public function editInput($table, array $field, $attrs, $value)
    {
        if ($this->inEdit) {
            echo $this->previewButton(field: $field, value: $value);
        }
    }

    private function previewButton(array $field, $value): ?string
    {
        static $counter = 0;

        if (!is_string($value) || (!in_array($field['type'], ['json', 'jsonb'], true)
            && !in_array(substr(ltrim($value), 0, 1), ['{', '['], true))) {
            return null;
        }

        $json = Adminer\json_decode_exact($value);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return null;
        }

        $formatted = Adminer\json_encode_exact($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $id        = 'adminer-json-preview-' . ++$counter;

        return '<button type="button" class="adminer-json-open" data-preview="' . $id . '">JSON</button>'
            . '<template id="' . $id . '">' . Adminer\h($formatted) . '</template>';
    }
}
