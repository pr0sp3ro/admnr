<?php

/** Add a quick link under each header column
* @author Andrea Mariani, https://fasys.it
*/
class AdminerAddButtonColumnHeader extends Adminer\Plugin {

    const DISTINCT = "DISTINCT";
    private $tableName;
    private $buttons;

    function __construct($buttons = [self::DISTINCT]) {
        $this->buttons = $buttons;
    }

    function selectColumnsPrint($select, $columns)
    {
        $queries = [];

        foreach ($columns as $name => $field) {
            $column = Adminer\idf_escape($name);
            $query  = 'SELECT ' . $column . ', COUNT(*) AS total FROM ' . Adminer\table($_GET['select']) . ' GROUP BY ' . $column;
            $queries[$name] = Adminer\ME . 'sql=' . rawurlencode($query);
        }

        echo Adminer\script('document.addEventListener("DOMContentLoaded", () => {
            const queries = ' . json_encode($queries, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) . ';
            document.querySelectorAll("#table thead th .column").forEach(el => {
                const field = el.textContent.trim();
                if (!queries[field]) return;
                const container = document.createElement("div");
                const link = document.createElement("a");
                link.href = queries[field];
                link.textContent = "Distinct";
                container.appendChild(link);
                el.parentElement.appendChild(container);
            });
        });');
    }
}
