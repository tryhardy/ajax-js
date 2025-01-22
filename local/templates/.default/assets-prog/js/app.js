window.UPLAB_LOAD_LIBS = true;
__webpack_public_path__ = __webpack_public_path__ || '/';

// Файлы начинающиеся с '_' можно импортировать, они не компилируются сами по себе
const loadModules = () => {
    require('./_ajaxLoadHandler.class');
};

// TODO переделать на промисы
const asyncLoadJs = (url, onload) => {
    let script = document.createElement('script');
    script.onload = onload;
    script.src = __webpack_public_path__ + url;
    const parentNode = document.querySelector('head');
    parentNode && parentNode.appendChild(script);
};

const checkLoadedLibs = () => {
    if (typeof jQuery === 'undefined') {
        console.error('jQuery undefined');
    } else {
        loadModules();
    }
};

document.addEventListener('DOMContentLoaded', checkLoadedLibs);
