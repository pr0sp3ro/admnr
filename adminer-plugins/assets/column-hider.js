(() => {
    'use strict';

    const table = document.getElementById('table');
    const config = document.querySelector('meta[name="adminer-column-hider"]');

    if (!table || !config || !table.tHead) return;

    const key = 'adminer-column-hider:' + JSON.stringify([
        location.pathname,
        ...JSON.parse(config.content),
    ]);
    let hidden = new Set();

    try {
        const saved = JSON.parse(localStorage.getItem(key));
        if (Array.isArray(saved)) hidden = new Set(saved.filter(name => typeof name === 'string'));
    } catch (_) {
        // Column toggles also work when browser storage is unavailable.
    }

    const columns = Array.from(table.tHead.rows[0].cells)
        .filter(cell => cell.tagName === 'TH' && cell.id.startsWith('th['))
        .map(cell => {
            const label = cell.cloneNode(true);
            label.querySelectorAll('.column').forEach(element => element.remove());

            return { name: cell.id, index: cell.cellIndex, label: label.textContent.trim() };
        });

    if (!columns.length) return;

    const menu = document.createElement('details');
    menu.className = 'column-hider';
    const summary = document.createElement('summary');
    summary.textContent = 'Columns';
    menu.appendChild(summary);
    const list = document.createElement('div');
    list.className = 'column-hider-list';
    menu.appendChild(list);

    function apply() {
        for (const row of table.rows) {
            for (const column of columns) {
                row.cells[column.index]?.classList.toggle('column-hider-hidden', hidden.has(column.name));
            }
        }
    }

    function save() {
        try {
            localStorage.setItem(key, JSON.stringify([...hidden]));
        } catch (_) {
            // Keep the current page usable without persistent storage.
        }
        apply();
    }

    for (const column of columns) {
        const label = document.createElement('label');
        const checkbox = document.createElement('input');
        checkbox.type = 'checkbox';
        checkbox.checked = !hidden.has(column.name);
        checkbox.addEventListener('change', () => {
            if (checkbox.checked) hidden.delete(column.name);
            else hidden.add(column.name);
            save();
        });
        label.append(checkbox, document.createTextNode(' ' + column.label));
        list.appendChild(label);
    }

    const reset = document.createElement('button');
    reset.type = 'button';
    reset.textContent = 'Show all';
    reset.addEventListener('click', () => {
        hidden.clear();
        list.querySelectorAll('input').forEach(checkbox => { checkbox.checked = true; });
        save();
    });
    list.appendChild(reset);
    const container = table.closest('.scrollable') || table;
    container.before(menu);
    apply();

    new MutationObserver(apply).observe(table, { childList: true, subtree: true });
})();
