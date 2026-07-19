(function () {
    'use strict';

    // Automatyczne ukrywanie komunikatu
    setTimeout(function () {
        document.querySelectorAll('.admin-flash').forEach(function (el) {
            el.style.transition = 'opacity 0.5s ease';
            el.style.opacity = '0';
            setTimeout(function () { el.remove(); }, 600);
        });
    }, 4000);
})();
