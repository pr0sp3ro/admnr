(() => {
    'use strict';

    let dialog;
    let content;

    document.addEventListener('click', event => {
        const button = event.target.closest('.adminer-json-open');
        if (!button) return;

        const template = document.getElementById(button.dataset.preview);
        if (!template) return;

        event.preventDefault();
        event.stopPropagation();

        if (!dialog) {
            dialog = document.createElement('dialog');
            dialog.className = 'adminer-json-dialog';
            const header = document.createElement('div');
            header.className = 'adminer-json-header';
            const title = document.createElement('strong');
            title.id = 'adminer-json-title';
            title.textContent = 'JSON';
            dialog.setAttribute('aria-labelledby', title.id);
            const close = document.createElement('button');
            close.type = 'button';
            close.textContent = 'Close';
            close.addEventListener('click', () => dialog.close());
            header.append(title, close);
            content = document.createElement('pre');
            dialog.append(header, content);
            document.body.appendChild(dialog);
        }

        content.textContent = template.content.textContent;
        dialog.showModal();
        dialog.scrollTop = 0;
    }, true);
})();
