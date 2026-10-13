/* Shows the icon next to each option in the IconSans select fields. */
(function () {
    'use strict';

    if (typeof acf === 'undefined' || typeof wheellabIconsans === 'undefined') return;

    function render(state) {
        if (!state.id) return state.text;
        var wrap = document.createElement('span');
        wrap.className = 'wl-iconsans-opt';
        var img = document.createElement('img');
        img.src = wheellabIconsans.base + state.id + '.svg';
        img.alt = '';
        img.loading = 'lazy';
        var label = document.createElement('span');
        label.textContent = state.text;
        wrap.appendChild(img);
        wrap.appendChild(label);
        return wrap;
    }

    acf.addFilter('select2_args', function (args, $select, settings, field) {
        var key = field && field.get ? field.get('key') : '';
        if (/iconsans$/.test(key)) {
            args.templateResult = render;
            args.templateSelection = render;
        }
        return args;
    });
})();
