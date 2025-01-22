----------------------------------------------------------------------------------
Минифицированный класс для AJAX обновления контента
----------------------------------------------------------------------------------
Поддерживаются различные события для ссылок и веб-форм.  
Для срабатывания логики нужно повесить атрибуты на необходимые элементы.  

Элемент       | Событие | Атрибут
--------------|---------|-------------------
Элемент       | click   | data-ajax-link-to
Форма         | submit  | data-ajax-form-to
Форма         | reset   | data-ajax-form-to
Элемент формы | change  | data-submit-form
Элемент формы | click   | data-submit-form-on-click

----------------------------------------------------------------------------------
Пример
----------------------------------------------------------------------------------
```
<div id="wrapper">
    <?php \Local\Helper::ajaxBuffer(); ?>
        <form data-ajax-form-to="#wrapper">
            <select data-submit-form="y"></select>
        </form>
        ...
        <список_новостей>
        ...
        <a data-ajax-link-to="#wrapper" data-href="./PAGEN_1=2">Страница 2</a>
    <?php \Local\Helper::ajaxBuffer(false); ?>
</div>
```
----------------------------------------------------------------------------------
Основные атрибуты
----------------------------------------------------------------------------------
data-ajax-link-to  
Значение: jQuery селектор обертки  
При событии click элемента с атрибутом произойдет обновление контента в обертке контентом, обернутым в \Local\Helper::ajaxBuffer()...\Local\Helper::ajaxBuffer(false) на back-end.  
Данные будут получены по адресу в атрибуте data-href.  
     
data-ajax-form-to  
Значение: jQuery селектор обертки  
При событии submit формы с атрибутом произойдет обновление контента в обертке контентом, обернутым в \Local\Helper::ajaxBuffer()...\Local\Helper::ajaxBuffer(false) на back-end.  
Данные формы будут сериализованны и отправлены.  
Данные будут получены по адресу в атрибуте data-href.  
     
data-submit-form  
Значение: не пустая строка  
При изменении элемента с атрибутом будет вызвано событие submit формы-родителя.  
Для формы дополнительно нужно указать атрибут data-ajax-form-to.  

----------------------------------------------------------------------------------
Дополнительные атрибуты
----------------------------------------------------------------------------------
Дополнительные атрибуты нужно задавать элементам с основными атрибутами.  
  
data-append-content  
Значение: не пустая строка  
Не обновлять контент в обертке, а добавлять новый к старому.  
  
data-keep-history  
Значение: не пустая строка  
Не изменять адресную строку браузера  
  
data-scroll-on-load  
Значение: не пустая строка  
При обновлении контента пролистывать к началу обертки.  

----------------------------------------------------------------------------------
Дополнительное обновление контента
----------------------------------------------------------------------------------
data-parse-selector  
Значение: не пустая строка  
  
Если указать данный атрибут, то контент обертки будет обновлен не контентом,  
обернутым в \Local\Helper::ajaxBuffer()...\Local\Helper::ajaxBuffer(false) на back-end,  
а контентом обертки из ответа.  
  
Дополнительно будет обновлены области содержащие атрибут  
data-ajax-replace-content (значение: уникальная строка).  
Обязательно наличие хотя бы одного атрибута data-ajax-replace-content.  
  
В этом случае в \Local\Helper::ajaxBuffer()...\Local\Helper::ajaxBuffer(false) на back-end нужно оборачивать область, содержащую все обновляемые данные.  

----------------------------------------------------------------------------------
Пример дополнительного обновления контента
----------------------------------------------------------------------------------
```
<?php \Local\Helper::ajaxBuffer(); ?>
    ...
    <form
        data-ajax-form-to="#wrapper"
        data-parse-selector="y">
        <select data-submit-form="y"
        data-ajax-replace-content="filter"></select>
    </form>
    ...
    <div id="wrapper" data-ajax-replace-content="news">
        список новостей
    </div>
    ...
    <a
        data-ajax-link-to="#wrapper"
        data-href="./?PAGEN_1=2"
        data-parse-selector="y"
        data-ajax-replace-content="pagination">Страница 2</a>
    ...
<?php \Local\Helper::ajaxBuffer(false); ?>
```
----------------------------------------------------------------------------------
Содержимое класса (до минификации)
----------------------------------------------------------------------------------
```
class AjaxLoadHandler {
    constructor() {
        this.init();
    }

    ajaxReplaceContent(params) {
        params = params || {};
        const dataAttribute = params.dataAttribute;
        const $html = params.html;
        const res = params.res || {};
        const isParseSelector = params.isParseSelector === true;
        let isAppend;

        $(`[data-${dataAttribute}]`).each((key, item) => {
            const $item = $(item);
            const keyValue = $item.data(dataAttribute);

            let newHtml = false;
            isAppend = params.isAppend === true && $item.is('[data-ajax-append="true"]');

            if (isParseSelector) {
                const $replaceEl = $html.find(`[data-${dataAttribute}='${keyValue}']`);
                if ($replaceEl.length) {
                    newHtml = isAppend ? $replaceEl.html() : $replaceEl.prop('outerHTML');
                }
            } else {
                if (res.hasOwnProperty(keyValue)) {
                    newHtml = res[keyValue];
                }
            }

            if (newHtml) {
                if (isAppend) {
                    $item.append($(newHtml));
                } else {
                    $item.replaceWith(newHtml);
                }
            }
        });
    }

    loadContent(params) {
        const self = this;

        params = params || {};
        params.selector = params.selector || this.AJAX_CONTENT_SEL;

        const $loadTo = $(params.selector);

        if (!$loadTo.length) {
            console.error('no-ajax-container');
            return;
        }

        params.isJson = params.isJson !== false;
        params.isParseSelector = params.isParseSelector === true;
        params.url = params.url || location.href;
        params.query = params.query || '';
        params.method = params.method || 'get';
        params.onSuccess = typeof params.onSuccess === 'function' ? params.onSuccess : false;

        params.hasOwnProperty("isAppend") || (params.isAppend = false);
        params.hasOwnProperty("isPrepend") || (params.isPrepend = false);
        params.hasOwnProperty("isKeepHistory") || (params.isKeepHistory = false);
        params.hasOwnProperty("isKeepTitle") || (params.isKeepTitle = false);
        params.hasOwnProperty("isKeepH1") || (params.isKeepH1 = false);
        params.hasOwnProperty("isScrollOnLoad") || (params.isScrollOnLoad = false);

        $(window).trigger('preloader:show', $loadTo);

        $.ajax({
            url: params.url,
            data: params.query,
            dataType: params.isJson ? 'json' : 'html',
            method: params.method,
            success: (res) => {
                $(window).trigger('preloader:hide', $loadTo);
                let html = params.isJson ? res.html : res;
                let $html;

                if (params.isParseSelector) {
                    $html = $('<div>').append(html);
                    html = $html.find(params.selector).html();
                }

                if (!html) return;

                if (params.isPrepend) {
                    $loadTo.prepend(html);
                } else if (params.isAppend) {
                    $loadTo.append(html);
                } else {
                    $loadTo.html(html);
                }



                if (params.isJson) {

                    self.ajaxReplaceContent({
                        dataAttribute: 'ajax-replace-content',
                        html: $html || $(html),
                        res: res,
                        isParseSelector: params.isParseSelector,
                        isAppend: params.isAppend,
                    });

                    if (!params.isKeepHistory) {
                        history.replaceState({}, '', res.url);
                    }

                    if (!params.isKeepH1 && res.hasOwnProperty('h1') && res.h1) {
                        $('h1').html(res.h1);
                    }

                    if (!params.isKeepTitle && res.hasOwnProperty('title') && res.title) {
                        document.title = res.title;
                    }
                }

                $(window).trigger('init');
                window.dispatchEvent(new CustomEvent('reinit'));

                if (params.isScrollOnLoad) {
                    $loadTo.scrollTo();
                }

                if (params.onSuccess) {
                    params.onSuccess(params, res);
                }
            }
        });
    }

    initHandlers() {
        const self = this;

        $(document).on('reset', '[data-ajax-form-to]', function (event) {
            const $this = $(this);

            self.loadContent({
                url: $this.prop('action') || location.href,
                query: '',
                method: $this.prop('method') || 'post',
                selector: $this.data('ajax-form-to'),
                isJson: $this.data('json-content'),
                isParseSelector: $this.data('parse-selector'),
                isAppend: $this.data('append-content') || false,
                isKeepHistory: $this.data('keep-history') || false,
                isScrollOnLoad: $this.data('scroll-on-load') || false
            });
        });

        $(document).on('submit', '[data-ajax-form-to]', function (event) {
            event.preventDefault();
            const $this = $(this);
            $this.prop('disabled', false);

            self.loadContent({
                url: $this.data('action') || $this.prop('action'),
                query: $this.serializeArray(),
                method: $this.prop('method') || 'post',
                selector: $this.data('ajax-form-to'),
                isJson: $this.data('json-content'),
                isParseSelector: $this.data('parse-selector'),
                isAppend: $this.data('append-content') || false,
                isKeepHistory: $this.data('keep-history') || false,
                isScrollOnLoad: $this.data('scroll-on-load') || false
            });

        });

        $(document).on('click', '[data-ajax-link-to]', function (event) {
            event.preventDefault();
            const $this = $(this);

            $this.trigger('button:loadingStart');

            self.loadContent({
                url: $this.data('href') || this.href,
                query: $this.data('query') || '',
                selector: $this.data('ajax-link-to'),
                isJson: $this.data('json-content'),
                isParseSelector: $this.data('parse-selector'),
                isAppend: $this.data('append-content') || false,
                isKeepHistory: $this.data('keep-history') || false,
                isScrollOnLoad: $this.data('scroll-on-load') || false,
                onSuccess: () => {
                    $this.trigger('button:loadingEnd');
                }
            });
        });
/*
$(document).on('change', '[data-submit-form]', function () {
$(this).closest('form').trigger('submit');
});*/

        $(document).on('click', '[data-submit-form-on-click]', function (event) {
            event.preventDefault();
            const $this = $(this);

            $($this.data('submit-form-on-click')).trigger('submit', {
                query: $this.data('query')
            });
        });

        $(window).on('init.ajax', () => {
            $(window).trigger('scroll');
        });
    }

    init() {
        this.initHandlers();
    }
}

$(() => {
window.__ajaxLoaderHandler = new AjaxLoadHandler();
});
```
