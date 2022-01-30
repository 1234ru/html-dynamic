/**
 * $_GET[v] => window.variants
 * Пока поддерживается только плоский массив без поддержки вложенных ключей.
 */
(function() {
    var variants = {};
    var queryString = window.location.search.substr(1);
    // var regexp = /\b(\w[^=]*)=[^&]+/g;
    // var matches = queryString.matchAll(regexp); // не работает в Firefox ниже 67
    // console.dir(matches);
    var parts = queryString.split('&');
    var i, matches, key_string, keys, key, value;
    // var regexp = /^(\w[^=]*)=([^&]+)$/; // тут тоже нужна поддержка matchAll, иначе трудно
    var regexp = /^v((?:\[\w*\])+)=([^&]+)$/;
    // var j, k, v, m2;
    for (i = 0; i < parts.length; i++) {
        matches = regexp.exec(decodeURIComponent(parts[i]));
        if (!matches)
            continue;
        key_string = matches[1];
        value = matches[2];

        // Вложенные массивы пока заменяем нотацией {...}
        keys = key_string
            .substr(1, key_string.length - 2)
            .split('][');
        key = keys[0];

        variants[key] = (keys.length === 1)
            ? value
            : '{some object}';
    }
    window.variants = variants;
})();