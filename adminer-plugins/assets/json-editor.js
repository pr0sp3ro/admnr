(() => {
    'use strict';

    document.querySelectorAll('textarea.adminer-json-editor').forEach(input => {
        const form = input.form;
        if (!form) return;

        function validate() {
            const operation = form.elements.namedItem('function' + input.name.slice('fields'.length));
            input.setCustomValidity('');

            if (input.disabled || (operation && !['', 'json'].includes(operation.value))) return;

            try {
                JSON.parse(input.value);
            } catch (error) {
                input.setCustomValidity('Invalid JSON: ' + error.message);
            }
        }

        form.addEventListener('input', validate);
        form.addEventListener('change', validate);
        form.addEventListener('submit', event => {
            validate();

            if (!input.reportValidity()) event.preventDefault();
        });
    });
})();
