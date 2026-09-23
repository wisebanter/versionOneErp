/* ============================================================================
 *  api.js — API Reference Playground
 *  ---------------------------------------------------------------------------
 *  Requires: jQuery 3.x+, and an `Api` class exposing:
 *      new Api({ apiVersion, basefile })
 *      api.formulated.baseUrlPath
 *      api.getEndpoint(endpointPath)
 *
 *  Debug: set `window.API_PLAYGROUND_DEBUG = true` in the console to log
 *  token / URL / content-type decisions.
 * ==========================================================================*/
$(function () {
    'use strict';

    /* ========================================================================
       1. CONFIG + STATE
       ======================================================================== */
    var api = new Api({ apiVersion: 'v1', basefile: '' });

    var MONTH_KEY = 'apiRequestCount_' + new Date().toISOString().slice(0, 7);
    var BASE_TITLE = document.title;

    var currentEndpoint = '';
    var currentRequireToken = true;

    /* --- Token -------------------------------------------------------- */
    var TOKEN_KEY = 'apiPlaygroundToken';
    var storedToken = '';
    try { storedToken = localStorage.getItem(TOKEN_KEY) || ''; } catch (e) { storedToken = ''; }

    function setStoredToken(v) {
        storedToken = (v == null) ? '' : String(v);
        try { localStorage.setItem(TOKEN_KEY, storedToken); } catch (e) { }
    }

    /* --- Auth scheme -------------------------------------------------- */
    var AUTH_SCHEME_KEY = 'apiPlaygroundAuthScheme';
    var currentAuthScheme = 'Token';
    try {
        var s = localStorage.getItem(AUTH_SCHEME_KEY);
        if (s === 'Token' || s === 'Bearer') currentAuthScheme = s;
    } catch (e) { }

    function setAuthScheme(v) {
        if (v !== 'Token' && v !== 'Bearer') v = 'Token';
        currentAuthScheme = v;
        try { localStorage.setItem(AUTH_SCHEME_KEY, v); } catch (e) { }
    }

    /* --- Content-Type ------------------------------------------------- */
    var CONTENT_TYPE_KEY = 'apiPlaygroundContentType';
    var currentContentType = 'application/json';
    try {
        var ct = localStorage.getItem(CONTENT_TYPE_KEY);
        if (ct) currentContentType = ct;
    } catch (e) { }

    function setContentType(v) {
        currentContentType = String(v || 'application/json');
        try { localStorage.setItem(CONTENT_TYPE_KEY, currentContentType); } catch (e) { }
    }

    function debug() {
        if (window.API_PLAYGROUND_DEBUG && window.console && console.log) {
            console.log.apply(console, ['[api.js]'].concat(Array.prototype.slice.call(arguments)));
        }
    }

    function getApiBase() {
        return api.getEndpoint(currentEndpoint);
    }

    /* ========================================================================
       2. CONFIG FLAGS
       ======================================================================== */
    window.ENDPOINT_NAME_STRIP_WHITESPACE =
        window.ENDPOINT_NAME_STRIP_WHITESPACE !== false;

    window.SIDEBAR_CHILD_ALERT_DISABLED =
        window.SIDEBAR_CHILD_ALERT_DISABLED === true;

    /* ========================================================================
       3. LINK INTROSPECTION
       ======================================================================== */
    function getLinkInfo($link) {
        if (!$link || !$link.length) {
            return { text: '', raw: {}, camel: {}, method: undefined };
        }

        var t = $link.find('.sidebar-link-text').first().text();
        if (!t) t = $link.text();
        t = String(t == null ? '' : t);
        var text = window.ENDPOINT_NAME_STRIP_WHITESPACE ? t.trim() : t;

        var raw = {};
        var el = $link[0];
        var attrs = el.attributes || [];
        for (var i = 0; i < attrs.length; i++) {
            var a = attrs[i];
            if (a && a.name && a.name.indexOf('data-') === 0) {
                raw[a.name] = a.value;
            }
        }

        var camel = $link.data() || {};
        var method = camel.methodLink || raw['data-method-link'] || undefined;

        return { text: text, raw: raw, camel: camel, method: method };
    }

    /* ========================================================================
       4. HELPERS
       ======================================================================== */
    function escapeHtml(s) {
        if (s == null) return '';
        return String(s).replace(/[&<>"']/g, function (c) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
        });
    }

    var FALSE_VALUES = { 'false': 1, '0': 1, 'no': 1, 'off': 1, 'null': 1, 'none': 1, 'undefined': 1 };
    var TRUE_VALUES = { 'true': 1, '1': 1, 'yes': 1, 'on': 1 };

    function toBool(v, dflt) {
        if (v === true) return true;
        if (v === false) return false;
        if (v === 0) return false;
        if (v === 1) return true;
        if (v == null) return !!dflt;

        var s = String(v).trim().toLowerCase();
        if (s === '') return !!dflt;
        if (TRUE_VALUES[s]) return true;
        if (FALSE_VALUES[s]) return false;

        return !!dflt;
    }

    function normalizeType(t) {
        t = String(t || 'string').toLowerCase().trim();
        var map = {
            int: 'integer', integer: 'integer', long: 'integer',
            float: 'integer', number: 'integer', decimal: 'integer',
            str: 'string', string: 'string', text: 'string',
            email: 'email',
            date: 'date', datetime: 'date',
            bool: 'boolean', boolean: 'boolean',
            phone: 'string', phone_number: 'string', url: 'string'
        };
        return map[t] || 'string';
    }

    function typeIcon(type) {
        return {
            integer: 'fa-hashtag',
            string: 'fa-font',
            email: 'fa-envelope',
            date: 'fa-calendar',
            boolean: 'fa-toggle-on'
        }[type] || 'fa-font';
    }

    /* ========================================================================
       5. PARAM NORMALISATION
       ======================================================================== */
    function normalizeParams(rawParams) {
        if (rawParams == null || rawParams === '') return [];

        var data = rawParams;

        if (typeof data === 'string') {
            var str = data.trim();
            try {
                data = JSON.parse(str);
            } catch (e) {
                try {
                    data = JSON.parse(
                        str
                            .replace(/\bNone\b/g, 'null')
                            .replace(/\bTrue\b/g, 'true')
                            .replace(/\bFalse\b/g, 'false')
                            .replace(/'/g, '"')
                    );
                } catch (e2) {
                    console.warn('[params] could not parse data-parameters:', rawParams);
                    return [];
                }
            }
        }

        var out = [];

        function pushOne(name, cfg) {
            cfg = cfg || {};
            var schema = cfg.schema || {};
            var rawType = cfg.type || schema.type || cfg.dataType || 'string';
            var type = normalizeType(rawType);

            var location = String(
                cfg.location || cfg.in || cfg.locationType || 'form'
            ).toLowerCase().trim();
            if (location === 'path') location = 'url';
            if (location !== 'url') location = 'form';

            out.push({
                name: String(name),
                type: type,
                description: cfg.description || cfg.desc || schema.description || '',
                required: !!cfg.required,
                placeholder: cfg.placeholder || cfg.example ||
                    (type === 'date' ? 'YYYY-MM-DD' : ''),
                location: location
            });
        }

        if (Array.isArray(data)) {
            data.forEach(function (item) {
                if (typeof item === 'string') { pushOne(item, {}); return; }
                if (item && typeof item === 'object' && item.name) pushOne(item.name, item);
            });
            return out;
        }

        if (data && typeof data === 'object') {
            Object.keys(data).forEach(function (key) { pushOne(key, data[key]); });
        }

        return out;
    }

    var URL_TOKEN_RE = /\{([^}:]+)(?::([^}]+))?\}/g;

    function applyUrlLocationRules(params, endpointPath) {
        var path = String(endpointPath || '');
        var slots = {};
        var m;
        URL_TOKEN_RE.lastIndex = 0;
        while ((m = URL_TOKEN_RE.exec(path)) !== null) {
            slots[m[1]] = m[2] || 'string';
        }

        params.forEach(function (p) {
            if (p.location === 'url' && !slots[p.name]) p.location = 'form';
        });

        var declared = {};
        params.forEach(function (p) { declared[p.name] = true; });

        Object.keys(slots).forEach(function (name) {
            if (declared[name]) return;
            params.push({
                name: name,
                type: normalizeType(slots[name]),
                description: '',
                required: true,
                placeholder: '',
                location: 'url'
            });
        });

        return params;
    }

    function collectParamValues(location) {
        var out = {};
        $('#queryParamsForm .param-input').each(function () {
            var $i = $(this);
            var loc = $i.data('location') || 'form';
            if (loc !== location) return;
            var v = ($i.val() || '').trim();
            if (v) out[$i.attr('name')] = v;
        });
        return out;
    }

    function resolveEndpointPath(endpointPath, urlValues) {
        if (!endpointPath) return '';
        return String(endpointPath).replace(URL_TOKEN_RE, function (full, name) {
            var v = urlValues[name];
            return (v == null || v === '') ? full : encodeURIComponent(v);
        });
    }

    function missingUrlParams() {
        var names = [];
        var re = new RegExp(URL_TOKEN_RE.source, 'g');
        var m;
        while ((m = re.exec(String(currentEndpoint || ''))) !== null) names.push(m[1]);

        var filled = collectParamValues('url');
        return names.filter(function (n) { return !filled[n]; });
    }

    /* ========================================================================
       6. DYNAMIC PARAMS FORM
       ======================================================================== */
    var PARAM_TO_BODY = {};

    var EXCLUDED_BODY_SUFFIX =
        /__(gte|lte|gt|lt|in|contains|icontains|startswith|istartswith|endswith|iendswith|isnull|range|year|month|day|week_day|hour|minute|second|regex|iregex)$/i;

    function rebuildParamToBody(params) {
        PARAM_TO_BODY = {};
        (params || []).forEach(function (p) {
            if (!p || !p.name) return;
            if (p.location === 'url') return;
            if (EXCLUDED_BODY_SUFFIX.test(p.name)) return;
            PARAM_TO_BODY[p.name] = p.name;
        });
    }

    function renderParamsForm(rawParams) {
        var $form = $('#queryParamsForm');
        if (!$form.length) return;

        var params = applyUrlLocationRules(
            normalizeParams(rawParams),
            currentEndpoint
        );

        $form.empty();

        if (!params.length) {
            $form.html(
                '<div class="params-empty">' +
                '<i class="fa-solid fa-sliders"></i>' +
                'No query parameters for this endpoint' +
                '</div>'
            );
            rebuildParamToBody([]);
            return;
        }

        var html = '';
        params.forEach(function (p) {
            var isUrl = p.location === 'url';

            html += '<div class="param' + (isUrl ? ' param-url' : '') + '">';
            html += '<div class="param-info">';
            html += '<div class="param-header">';
            html += '<span class="param-name">' + escapeHtml(p.name) + '</span>';
            html += '<span class="param-type">' +
                '<i class="fa-solid ' + typeIcon(p.type) + '"></i>' +
                escapeHtml(p.type) +
                '</span>';
            if (isUrl) {
                html += '<span class="param-location" title="Substituted into the endpoint path">' +
                    '<i class="fa-solid fa-link"></i>in path' +
                    '</span>';
            }
            if (p.required) html += '<span class="param-required">required</span>';
            html += '</div>';
            if (p.description) {
                html += '<div class="param-desc">' + escapeHtml(p.description) + '</div>';
            }
            html += '</div>';
            html += '<div>';
            html += '<input class="param-input" type="text" ' +
                'name="' + escapeHtml(p.name) + '" ' +
                'data-type="' + escapeHtml(p.type) + '" ' +
                'data-location="' + escapeHtml(p.location) + '" ' +
                'data-required="' + (p.required ? 'true' : 'false') + '" ' +
                'placeholder="' + escapeHtml(p.placeholder || '') + '" />';
            html += '<div class="param-error" data-for="' + escapeHtml(p.name) + '">' +
                '<i class="fa-solid fa-circle-exclamation"></i><span></span>' +
                '</div>';
            html += '</div>';
            html += '</div>';
        });
        $form.html(html);

        rebuildParamToBody(params);
    }

    /* ========================================================================
       7. TOKEN — PERSISTENCE + UI + HEADER
       ======================================================================== */
    function persistToken() {
        var $i = $('#authInput');
        if (!$i.length) return;
        setStoredToken($i.val());
        debug('persistToken — stored length =', storedToken.length);
    }

    function applyTokenRequirementUI() {
        var $input = $('#authInput');
        var $eye = $('#authToggle');
        var $scheme = $('#authSchemePicker');
        var $req = $('#credTagRequired');
        var $opt = $('#credTagOptional');
        var $err = $('#credError');

        if (currentRequireToken) {
            $input.prop('disabled', false).show().attr('placeholder', 'Enter token');
            $eye.show();
            $scheme.prop('disabled', false).show();
            $req.show();
            $opt.hide();
        } else {
            $input.prop('disabled', true).show().attr('placeholder', 'Not required for this endpoint');
            $eye.hide();
            $scheme.prop('disabled', true).show();
            $req.hide();
            $opt.show();
            $err.removeClass('show');
            $input.removeClass('invalid');
        }

        debug('applyTokenRequirementUI — requireToken =', currentRequireToken);
    }

    function applyAuthSchemeUI() {
        var $picker = $('#authSchemePicker');
        if (!$picker.length) return;
        $picker.val(currentAuthScheme);
    }

    function stripSchemePrefix(v) {
        return String(v || '').replace(/^\s*(Token|Bearer)\s+/i, '').trim();
    }

    function getAuthToken() {
        if (!currentRequireToken) return '';
        var raw = stripSchemePrefix(storedToken);
        if (!raw) return currentAuthScheme + ' ••••';
        return currentAuthScheme + ' ' + raw;
    }

    function getAuthTokenRaw() {
        if (!currentRequireToken) return '';
        var raw = stripSchemePrefix(storedToken);
        if (!raw) return '';
        return raw;
    }

    function getAuthorizationHeader() {
        var v = getAuthTokenRaw();
        if (!v) return '';
        return currentAuthScheme + ' ' + v;
    }

    /* ========================================================================
       8. ENDPOINT UPDATERS
       ======================================================================== */
    function updateEndpointName(info) {
        if (!info) return;

        var camel = info.camel || {};
        var raw = info.raw || {};

        var endpointPath = camel.endpoint || raw['data-endpoint'] || '';
        var description = camel.description || raw['data-description'] || '';
        var rawParams = (camel.parameters !== undefined && camel.parameters !== null)
            ? camel.parameters
            : raw['data-parameters'];

        var requireTokenRaw;
        if (camel.requireToken !== undefined && camel.requireToken !== null) {
            requireTokenRaw = camel.requireToken;
        } else if (raw['data-require-token'] !== undefined) {
            requireTokenRaw = raw['data-require-token'];
        } else {
            requireTokenRaw = undefined;
        }
        currentRequireToken = toBool(requireTokenRaw, true);

        var changed = endpointPath !== currentEndpoint;
        currentEndpoint = endpointPath;

        var title = info.text || endpointPath;
        if (title) $('.endpoint-name').text(title);
        $('.endpoint-desc').text(description);

        applyTokenRequirementUI();
        renderParamsForm(rawParams);

        if (changed) {
            $('#bodyInput').removeData();
            $('#bodyInput').val('');
            $('#bodyError').removeClass('show');
            $('#bodyInput').removeClass('invalid');
            $('.param-error').removeClass('show');
            $('.param-input').removeClass('invalid valid');
        }

        renderSnippet();
    }

    function updatePageTitle(text) {
        if (!text) { document.title = BASE_TITLE; return; }
        var cleanBase = BASE_TITLE.replace(/\s*·\s*.*$/, '').trim();
        document.title = text + ' · ' + cleanBase;
    }

    function resetPlaygroundState() {
        $('#recentTableBody').empty();
        $('#responseResult').addClass('hidden');
        $('#responseEmpty').removeClass('hidden');
        $('#respStatus').html('<i class="fa-solid fa-circle"></i>---');
        $('#respTime').html('<i class="fa-solid fa-clock"></i>--- ms');
        $('#respSize').html('<i class="fa-solid fa-database"></i>--- B');
        $('#respHead').text('application/json');
        $('#respBody').text('');
    }

    function handleSidebarLinkClick($link) {
        var info = getLinkInfo($link);

        if (info.text || info.camel.endpoint) {
            updateEndpointName(info);
            updatePageTitle(info.text);
        }

        if (info.method && typeof window.setMethod === 'function') {
            window.setMethod(info.method);
        }

        resetPlaygroundState();
        return info;
    }

    /* ========================================================================
       9. DEFAULT CHILD-LINK HANDLER (user hook)
       ======================================================================== */
    window.onSidebarChildClick = window.onSidebarChildClick || function ($link, evt, info) {
        if (!info) info = getLinkInfo($link);

        var camel = info.camel || {};
        var raw = info.raw || {};

        var reqToken = (camel.requireToken !== undefined && camel.requireToken !== null)
            ? camel.requireToken
            : raw['data-require-token'];

        return {
            text: info.text,
            method: camel.methodLink || raw['data-method-link'] || '',
            requireToken: toBool(reqToken, true),
            parameters: normalizeParams(
                (camel.parameters !== undefined && camel.parameters !== null)
                    ? camel.parameters : raw['data-parameters']
            ),
            endpoint: camel.endpoint || raw['data-endpoint'] || '',
            description: camel.description || raw['data-description'] || ''
        };
    };

    /* ========================================================================
       10. TOASTS
       ======================================================================== */
    function toast(msg, type) {
        var icon = type === 'success' ? 'fa-circle-check'
            : type === 'error' ? 'fa-circle-xmark'
                : 'fa-circle-info';
        var $t = $('<div class="toast ' + (type || '') + '"><i class="fa-solid ' + icon + '"></i><span></span></div>');
        $t.find('span').text(msg);
        $('#toastStack').append($t);
        setTimeout(function () {
            $t.addClass('out');
            setTimeout(function () { $t.remove(); }, 320);
        }, type === 'error' ? 5000 : 2600);
    }

    /* ========================================================================
       11. THEME
       ======================================================================== */
    var $html = $('html');
    var THEME_KEY = 'theme';
    var systemTheme = window.matchMedia('(prefers-color-scheme: dark)');

    function systemThemeValue() { return systemTheme.matches ? 'dark' : 'light'; }

    function getPreferredTheme() {
        var stored = null;
        try { stored = localStorage.getItem(THEME_KEY); } catch (e) { }
        if (stored === 'light' || stored === 'dark') return stored;
        return systemThemeValue();
    }

    function applyTheme(mode) {
        $html.attr('data-color-mode', mode);
        $('#themeIcon')
            .removeClass('fa-moon fa-sun')
            .addClass(mode === 'dark' ? 'fa-sun' : 'fa-moon');
    }

    applyTheme(getPreferredTheme());

    function onSystemThemeChange(e) {
        var manual = null;
        try { manual = localStorage.getItem(THEME_KEY); } catch (err) { }
        if (manual) return;
        applyTheme(e.matches ? 'dark' : 'light');
    }
    if (systemTheme.addEventListener) {
        systemTheme.addEventListener('change', onSystemThemeChange);
    } else if (systemTheme.addListener) {
        systemTheme.addListener(onSystemThemeChange);
    }

    $('#themeToggle').on('click', function () {
        var next = $html.attr('data-color-mode') === 'dark' ? 'light' : 'dark';
        applyTheme(next);
        try { localStorage.setItem(THEME_KEY, next); } catch (e) { }
        toast('Switched to ' + next + ' mode', 'success');
    });

    /* ========================================================================
       12. DESKTOP SIDEBAR COLLAPSE
       ======================================================================== */
    var $app = $('#appRoot');
    if (localStorage.getItem('sidebar-collapsed') === 'true') $app.addClass('sidebar-collapsed');
    $('#sidebarToggle').on('click', function () {
        $app.toggleClass('sidebar-collapsed');
        var c = $app.hasClass('sidebar-collapsed');
        localStorage.setItem('sidebar-collapsed', c);
        $(this).attr('title', c ? 'Expand sidebar' : 'Collapse sidebar');
    });

    /* ========================================================================
       13. MOBILE SIDEBAR
       ======================================================================== */
    function openSidebar() { $('#sidebar').addClass('open'); $('#mobileOverlay').addClass('show'); $('body').css('overflow', 'hidden'); }
    function closeSidebar() { $('#sidebar').removeClass('open'); $('#mobileOverlay').removeClass('show'); $('body').css('overflow', ''); }
    $('#menuToggle').on('click', function () { $('#sidebar').hasClass('open') ? closeSidebar() : openSidebar(); });
    $('#mobileOverlay').on('click', closeSidebar);

    /* ========================================================================
       14. SIDEBAR — EXCLUSIVE ACCORDION
       ======================================================================== */
    $('.sidebar-item[data-parent] > .sidebar-link').on('click', function (e) {
        e.preventDefault();
        var $item = $(this).closest('.sidebar-item');
        var isOpen = $item.hasClass('open');
        if (!isOpen) $('.sidebar-item[data-parent].open').not($item).removeClass('open');
        $item.toggleClass('open');
    });

    /* ========================================================================
       15. SIDEBAR — ACTIVE LINK (parent/plain only)
       ======================================================================== */
    $(document).on('click',
        '.sidebar-link:not(.sidebar-link-parent):not(.sidebar-link-child)',
        function (e) {
            e.preventDefault();
            $('.sidebar-link').removeClass('active');
            $(this).addClass('active');
            handleSidebarLinkClick($(this));
            if (window.matchMedia('(max-width: 860px)').matches) closeSidebar();
        });

    /* ========================================================================
       16. SIDEBAR CHILD LINK — SINGLE ENTRY POINT
       ======================================================================== */
    $(document).on('click', '.sidebar-link-child', function (e) {
        if ($(e.target).closest('.sidebar-method').length) return;

        e.preventDefault();

        var $link = $(this);

        $('.sidebar-link').removeClass('active');
        $link.addClass('active');

        var info = handleSidebarLinkClick($link);

        try {
            if (typeof window.onSidebarChildClick === 'function' && !window.SIDEBAR_CHILD_ALERT_DISABLED) {
                window.onSidebarChildClick($link, e, info);
            }
        } catch (err) {
            console.error('[sidebar-link-child] handler threw:', err);
        }

        if (window.matchMedia('(max-width: 860px)').matches) closeSidebar();
    });

    /* ========================================================================
       17. SIDEBAR FILTER
       ======================================================================== */
    var $filterInput = $('#filterInput');
    var $filterWrap = $('#filterWrap');
    var $filterClear = $('#filterClear');
    var $sidebar = $('#sidebar');

    function highlight(el, q) {
        var $el = $(el);
        var raw = $el.data('original');
        if (typeof raw !== 'string') { raw = $el.text(); $el.data('original', raw); }
        if (!q) { $el.text(raw); return; }
        var re = new RegExp('(' + q.replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + ')', 'ig');
        $el.html(escapeHtml(raw).replace(re, '<mark class="hl">$1</mark>'));
    }

    function runFilter() {
        var q = ($filterInput.val() || '').trim();
        var lq = q.toLowerCase();
        $filterWrap.toggleClass('has-value', !!q);
        $filterClear.toggleClass('show', !!q);

        if (!q) {
            $('.sidebar-item, .sidebar-link').show();
            $('.sidebar-link-text').each(function () { highlight(this, ''); });
            $('#filterCount').text('0');
            $sidebar.removeClass('filtering-empty');
            return;
        }

        var count = 0;
        $('.sidebar-item, .sidebar-link').hide();
        $('.sidebar-link-text').each(function () { highlight(this, ''); });

        $('.sidebar-link').each(function () {
            var $l = $(this);
            if ($l.text().toLowerCase().indexOf(lq) !== -1) {
                count++;
                $l.show();
                $l.closest('.sidebar-item').show();
                $l.parents('.sidebar-item').show();
                $l.find('.sidebar-link-text').each(function () { highlight(this, q); });
            }
        });

        $('.sidebar-item[data-parent]').each(function () {
            if ($(this).find('.sidebar-link:visible').length) $(this).addClass('open');
        });

        $('#filterCount').text(count);
        $sidebar.toggleClass('filtering-empty', count === 0);
    }

    $filterInput.on('input', runFilter);
    $filterClear.on('click', function () { $filterInput.val('').focus(); runFilter(); });
    $('#filterClearAll').on('click', function () { $filterInput.val('').focus(); runFilter(); });

    /* ========================================================================
       18. RESPONSE ACCORDION
       ======================================================================== */
    $('[data-acc] .acc-header').on('click', function () {
        $(this).closest('[data-acc]').toggleClass('open');
    });

    /* ========================================================================
       19. STATE
       ======================================================================== */
    var currentMethod = 'get';
    var currentLang = 'python';
    var METHOD_HAS_BODY = { post: true, put: true, patch: true, get: false, del: false };

    function methodUpper(m) { return m === 'del' ? 'DELETE' : String(m || '').toUpperCase(); }

    /* ========================================================================
       20. URL / BODY HELPERS
       ======================================================================== */
    function buildUrl() {
        var urlValues = collectParamValues('url');
        var resolvedPath = resolveEndpointPath(currentEndpoint, urlValues);
        var base = api.getEndpoint(resolvedPath);

        if (currentMethod !== 'get' && currentMethod !== 'del') return base;

        var formValues = collectParamValues('form');
        var pairs = Object.keys(formValues).map(function (k) {
            return encodeURIComponent(k) + '=' + encodeURIComponent(formValues[k]);
        });

        return pairs.length ? base + '?' + pairs.join('&') : base;
    }

    /* ----------------------------------------------------------------------
       Seed body values from the form inputs (form-location params only).
       ---------------------------------------------------------------------- */
    function seedBodyFromParams() {
        var seed = {};
        Object.keys(PARAM_TO_BODY).forEach(function (paramName) {
            var $input = $('#queryParamsForm .param-input[name="' + paramName + '"]');
            if (!$input.length) return;
            var loc = $input.data('location') || 'form';
            if (loc !== 'form') return;
            var v = ($input.val() || '').trim();
            if (v) seed[PARAM_TO_BODY[paramName]] = v;
        });
        return seed;
    }

    /* ----------------------------------------------------------------------
       ✅ Convert form data (a plain object) into the selected content type.
       Returns a STRING ready for display in the textarea / snippet.
       ---------------------------------------------------------------------- */
    function serializeParams(ct, obj) {
        obj = obj || {};
        var keys = Object.keys(obj);

        if (ct === 'application/x-www-form-urlencoded') {
            return keys.map(function (k) {
                return encodeURIComponent(k) + '=' + encodeURIComponent(obj[k]);
            }).join('&');
        }

        if (ct === 'application/xml') {
            var xml = '<?xml version="1.0" encoding="UTF-8"?>\n<request>\n';
            keys.forEach(function (k) {
                xml += '  <' + k + '>' + escapeHtml(obj[k]) + '</' + k + '>\n';
            });
            xml += '</request>';
            return xml;
        }

        if (ct === 'text/plain') {
            return keys.map(function (k) { return k + '=' + obj[k]; }).join('\n');
        }

        /* application/json AND multipart/form-data both show as JSON in the
           textarea — multipart is converted to FormData only when sending. */
        return JSON.stringify(obj, null, 2);
    }

    /* ----------------------------------------------------------------------
       buildDefaultBody — CT-aware.
       ---------------------------------------------------------------------- */
    function buildDefaultBody(method) {
        var base = { post: {}, put: {}, patch: {} }[method] || {};
        var merged = Object.assign({}, base, seedBodyFromParams());
        return serializeParams(currentContentType, merged);
    }

    /* ----------------------------------------------------------------------
       The body string shown in the snippet. It matches the textarea for
       non-JSON types and pretty-prints JSON for readability.
       ---------------------------------------------------------------------- */
    function getBodyPretty() {
        if (!METHOD_HAS_BODY[currentMethod]) return '';
        var raw = ($('#bodyInput').val() || '').trim();
        if (!raw) return '';
        if (currentContentType === 'application/json' ||
            currentContentType === 'multipart/form-data') {
            try { return JSON.stringify(JSON.parse(raw), null, 2); }
            catch (e) { return raw; }
        }
        return raw;
    }

    /* ========================================================================
       21. VALIDATION
       ======================================================================== */
    function validateParam($input) {
        var val = ($input.val() || '').trim();
        var type = $input.data('type') || 'string';
        var loc = $input.data('location') || 'form';
        var name = $input.attr('name') || '';

        var required = ($input.data('required') === true)
                    || ($input.attr('data-required') === 'true');

        if (loc === 'url' && !val) return 'Required in path';
        if (required && !val) return 'Required';
        if (!val) return null;

        if (type === 'integer') {
            if (!/^-?\d+$/.test(val)) return 'Must be a whole number';
            if (parseInt(val, 10) < 1 && name !== 'id') return 'Must be 1 or greater';
        }
        if (type === 'date') {
            if (!/^\d{4}-\d{2}-\d{2}$/.test(val)) return 'Use YYYY-MM-DD format';
            if (isNaN(new Date(val).getTime())) return 'Invalid date';
        }
        if (type === 'email') {
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val)) return 'Invalid email address';
        }
        if (type === 'string' && val.length > 255) return 'Max 255 characters';

        return null;
    }

    function setParamError($input, msg) {
        if (!$input || !$input.length) return;
        var name = $input.attr('name');
        var $err = $('.param-error[data-for="' + name + '"]');
        if (msg) {
            $input.addClass('invalid').removeClass('valid');
            $err.find('span').text(msg);
            $err.addClass('show');
        } else {
            $input.removeClass('invalid');
            if (($input.val() || '').trim()) $input.addClass('valid');
            else $input.removeClass('valid');
            $err.removeClass('show');
        }
    }

    /* ----------------------------------------------------------------------
       Validate the body according to the CURRENT content type.
       ---------------------------------------------------------------------- */
    function validateBody() {
        var raw = ($('#bodyInput').val() || '').trim();
        if (!raw) return 'Request body is required';

        if (currentContentType === 'application/json' ||
            currentContentType === 'multipart/form-data') {
            try { JSON.parse(raw); }
            catch (e) { return 'Invalid JSON: ' + e.message; }
            return null;
        }

        if (currentContentType === 'application/x-www-form-urlencoded') {
            /* Loosely check: `k=v` pairs separated by `&` */
            var ok = raw.split('&').every(function (p) {
                return /^[^=]+=.*$/.test(p);
            });
            return ok ? null : 'Use key=value&key2=value2 format';
        }

        if (currentContentType === 'application/xml') {
            /* Very loose — a real XML parser would be overkill here. */
            return /^\s*<\?xml/.test(raw) || /^\s*<[^>]+>/.test(raw)
                ? null : 'Body does not look like XML';
        }

        /* text/plain — anything goes */
        return null;
    }

    function validateForm() {
        var errors = [];

        if (currentRequireToken) {
            var token = stripSchemePrefix(storedToken);
            if (!token) {
                $('#authInput').addClass('invalid');
                $('#credError').addClass('show').find('span').text('Authorization token is required');
                errors.push('Authorization token is required');
            } else {
                $('#authInput').removeClass('invalid');
                $('#credError').removeClass('show');
            }
        } else {
            $('#authInput').removeClass('invalid');
            $('#credError').removeClass('show');
        }

        $('#queryParamsForm .param-input').each(function () {
            var $i = $(this);
            var err = validateParam($i);
            setParamError($i, err);
            if (err) errors.push($i.attr('name') + ': ' + err);
        });

        var missing = missingUrlParams();
        if (missing.length) {
            missing.forEach(function (name) {
                setParamError(
                    $('#queryParamsForm .param-input[name="' + name + '"]'),
                    'Required in path'
                );
            });
            errors.push('Missing URL parameter(s): ' + missing.join(', '));
        }

        if (METHOD_HAS_BODY[currentMethod]) {
            var bodyErr = validateBody();
            if (bodyErr) {
                $('#bodyInput').addClass('invalid');
                $('#bodyError').addClass('show').find('span').text(bodyErr);
                errors.push(bodyErr);
            } else {
                $('#bodyInput').removeClass('invalid');
                $('#bodyError').removeClass('show');
            }
        } else {
            $('#bodyInput').removeClass('invalid');
            $('#bodyError').removeClass('show');
        }

        return errors;
    }

    /* ========================================================================
       22. INPUT HANDLERS
       ======================================================================== */

    /* Form inputs → validate + (re)seed body if untouched */
    $('#queryParamsForm').on('input', '.param-input', function () {
        var $i = $(this);
        setParamError($i, validateParam($i));

        if (METHOD_HAS_BODY[currentMethod] && !$('#bodyInput').data('touched-' + currentMethod)) {
            $('#bodyInput').val(buildDefaultBody(currentMethod));
            $('#bodyInput').removeClass('invalid');
            $('#bodyError').removeClass('show');
        }

        scheduleSnippetUpdate();
    });

    $('#queryParamsForm').on('blur', '.param-input', function () {
        var $i = $(this);
        setParamError($i, validateParam($i));
    });

    /* Token input */
    $('#authInput').on('input change', function () {
        persistToken();
        var v = stripSchemePrefix($(this).val());
        if (v) {
            $(this).removeClass('invalid');
            $('#credError').removeClass('show');
        }
        scheduleSnippetUpdate();
    });

    /* Auth scheme */
    $('#authSchemePicker').on('change', function () {
        setAuthScheme($(this).val());
        debug('auth scheme changed →', currentAuthScheme);
        scheduleSnippetUpdate();
    });

    /* Body textarea — remember it was touched for the current method */
    $('#bodyInput').on('input', function () {
        var $b = $(this);
        var v = $b.val();
        $b.data('touched-' + currentMethod, true);
        $b.data('stash-' + currentMethod, v);

        if (METHOD_HAS_BODY[currentMethod]) {
            var err = validateBody();
            if (err) {
                $b.addClass('invalid');
                $('#bodyError').addClass('show').find('span').text(err);
            } else {
                $b.removeClass('invalid');
                $('#bodyError').removeClass('show');
            }
        }
        scheduleSnippetUpdate();
    });

    /* ========================================================================
       22b. CONTENT-TYPE PICKER
       ------------------------------------------------------------------------
       On change, if the body hasn't been manually edited for this method,
       re-serialize it from the form inputs into the newly-picked format.
       ======================================================================== */
    function applyContentTypeUI() {
        var $picker = $('#contentTypePicker');
        if (!$picker.length) return;
        $picker.val(currentContentType);
        if ($picker.val() !== currentContentType) {
            currentContentType = 'application/json';
            $picker.val(currentContentType);
        }
        debug('applyContentTypeUI — content type =', currentContentType);
    }

    $('#contentTypePicker').on('change', function () {
        setContentType($(this).val());
        debug('content-type changed →', currentContentType);

        /* ✅ Convert form data into the picked content type. */
        if (METHOD_HAS_BODY[currentMethod]
            && !$('#bodyInput').data('touched-' + currentMethod)) {
            $('#bodyInput').val(buildDefaultBody(currentMethod));
            $('#bodyInput').removeClass('invalid');
            $('#bodyError').removeClass('show');
        } else if (METHOD_HAS_BODY[currentMethod]) {
            /* Body was hand-edited. If it's parseable JSON, try to convert it
               into the new format so the user's values survive the switch. */
            var raw = ($('#bodyInput').val() || '').trim();
            if (raw) {
                try {
                    var parsed = JSON.parse(raw);
                    if (parsed && typeof parsed === 'object') {
                        $('#bodyInput').val(serializeParams(currentContentType, parsed));
                        $('#bodyInput').removeClass('invalid');
                        $('#bodyError').removeClass('show');
                    }
                } catch (e) { /* leave the body as-is */ }
            }
        }

        scheduleSnippetUpdate();
    });

    /* ========================================================================
       23. SNIPPET BUILDERS
       ======================================================================== */
    var SNIPPETS = {
        python: function (url, tok, body, ct) {
            var hasBody = body.length > 0;
            var hasAuth = !!tok;
            var verb = currentMethod === 'del' ? 'delete' : currentMethod;

            var out = '<span class="kw">import</span> <span class="var">requests</span>\n';
            if (hasBody && ct === 'application/json') out += '<span class="kw">import</span> <span class="var">json</span>\n';
            out += '\n<span class="var">url</span> = <span class="str">"' + escapeHtml(url) + '"</span>\n\n';
            if (hasBody && ct === 'application/json') {
                out += '<span class="var">payload</span> = <span class="var">json</span>.<span class="fn">loads</span>(<span class="str">r"""' + escapeHtml(body) + '"""</span>)\n\n';
            } else if (hasBody) {
                out += '<span class="var">payload</span> = <span class="str">r"""' + escapeHtml(body) + '"""</span>\n\n';
            }

            var hdrs = [];
            if (hasBody) hdrs.push('    <span class="str">"content-type"</span>: <span class="str">"' + escapeHtml(ct) + '"</span>');
            if (hasAuth) hdrs.push('    <span class="str">"Authorization"</span>: <span class="str">"' + escapeHtml(tok) + '"</span>');
            out += '<span class="var">headers</span> = {\n' + hdrs.join(',\n') + '\n}\n\n';

            var call = '<span class="var">requests</span>.<span class="fn">' + verb + '</span>(<span class="var">url</span>, <span class="var">headers</span>=<span class="var">headers</span>';
            if (hasBody) call += ', <span class="var">data</span>=<span class="var">payload</span>';
            call += ')';
            out += '<span class="var">response</span> = ' + call + '\n\n';
            out += '<span class="fn">print</span>(<span class="var">response</span>.<span class="fn">text</span>)';
            return out;
        },

        shell: function (url, tok, body, ct) {
            var hasBody = body.length > 0;
            var hasAuth = !!tok;
            var method = methodUpper(currentMethod);

            var parts = [];
            parts.push('--request ' + method);
            parts.push('--url <span class="str">"' + escapeHtml(url) + '"</span>');

            if (hasBody && ct === 'multipart/form-data') {
                /* multipart: use --form flags parsed from the JSON body */
                parts.push('--header <span class="str">"content-type: multipart/form-data"</span>');
                if (hasAuth) parts.push('--header <span class="str">"Authorization: ' + escapeHtml(tok) + '"</span>');
                try {
                    var obj = JSON.parse(body);
                    Object.keys(obj).forEach(function (k) {
                        parts.push('--form <span class="str">\'' + escapeHtml(k) + '=' + escapeHtml(String(obj[k])) + '\'</span>');
                    });
                } catch (e) {
                    parts.push('--data <span class="str">\'' + escapeHtml(body) + '\'</span>');
                }
            } else {
                if (hasBody) parts.push('--header <span class="str">"content-type: ' + escapeHtml(ct) + '"</span>');
                if (hasAuth) parts.push('--header <span class="str">"Authorization: ' + escapeHtml(tok) + '"</span>');
                if (hasBody) {
                    var single = body.replace(/'/g, "'\\''");
                    parts.push('--data <span class="str">\'' + escapeHtml(single) + '\'</span>');
                }
            }
            return '<span class="var">curl</span> ' + parts.join(' \\\n  ');
        },

        node: function (url, tok, body, ct) {
            var hasBody = body.length > 0;
            var hasAuth = !!tok;
            var method = methodUpper(currentMethod);

            var hdrs = [];
            if (hasAuth) hdrs.push('    <span class="str">"Authorization"</span>: <span class="str">"' + escapeHtml(tok) + '"</span>');
            if (hasBody && ct !== 'multipart/form-data') {
                hdrs.push('    <span class="str">"content-type"</span>: <span class="str">"' + escapeHtml(ct) + '"</span>');
            }

            var out = '<span class="kw">const</span> <span class="var">fetch</span> = <span class="fn">require</span>(<span class="str">"node-fetch"</span>);\n\n';

            if (hasBody && ct === 'multipart/form-data') {
                out += '<span class="kw">const</span> <span class="var">FormData</span> = <span class="fn">require</span>(<span class="str">"form-data"</span>);\n';
                out += '<span class="kw">const</span> <span class="var">form</span> = <span class="kw">new</span> <span class="var">FormData</span>();\n';
                try {
                    var obj = JSON.parse(body);
                    Object.keys(obj).forEach(function (k) {
                        out += '<span class="var">form</span>.<span class="fn">append</span>(<span class="str">"' + escapeHtml(k) + '"</span>, <span class="str">"' + escapeHtml(String(obj[k])) + '"</span>);\n';
                    });
                } catch (e) {
                    out += '<span class="var">form</span>.<span class="fn">append</span>(<span class="str">"body"</span>, <span class="str">"' + escapeHtml(body) + '"</span>);\n';
                }
                out += '\n';
            }

            out += '<span class="kw">const</span> <span class="var">options</span> = {\n';
            out += '  <span class="var">method</span>: <span class="str">"' + method + '"</span>,\n';
            if (hdrs.length) {
                out += '  <span class="var">headers</span>: {\n' + hdrs.join(',\n') + '\n  }';
            } else {
                out += '  <span class="var">headers</span>: {}';
            }
            if (hasBody) {
                if (ct === 'multipart/form-data') {
                    out += ',\n  <span class="var">body</span>: <span class="var">form</span>';
                } else if (ct === 'application/json') {
                    out += ',\n  <span class="var">body</span>: <span class="var">JSON</span>.<span class="fn">stringify</span>(' + escapeHtml(body) + ')';
                } else {
                    out += ',\n  <span class="var">body</span>: <span class="str">' + JSON.stringify(body) + '</span>';
                }
            }
            out += '\n};\n\n';
            out += '<span class="fn">fetch</span>(<span class="str">"' + escapeHtml(url) + '"</span>, <span class="var">options</span>)\n';
            out += '  .<span class="fn">then</span>(<span class="var">res</span> =&gt; <span class="var">res</span>.<span class="fn">json</span>())\n';
            out += '  .<span class="fn">then</span>(<span class="var">json</span> =&gt; <span class="fn">console</span>.<span class="fn">log</span>(<span class="var">json</span>))\n';
            out += '  .<span class="fn">catch</span>(<span class="var">err</span> =&gt; <span class="fn">console</span>.<span class="fn">error</span>(<span class="var">err</span>));';
            return out;
        },

        ruby: function (url, tok, body, ct) {
            var hasBody = body.length > 0;
            var hasAuth = !!tok;
            var klass = { get: 'Get', post: 'Post', put: 'Put', patch: 'Patch', del: 'Delete' }[currentMethod] || 'Get';

            var out = '<span class="kw">require</span> <span class="str">"uri"</span>\n';
            out += '<span class="kw">require</span> <span class="str">"net/http"</span>\n\n';
            out += '<span class="var">url</span> = <span class="var">URI</span>(<span class="str">"' + escapeHtml(url) + '"</span>)\n\n';
            out += '<span class="var">http</span> = <span class="var">Net</span>::<span class="var">HTTP</span>.<span class="fn">new</span>(<span class="var">url</span>.<span class="var">host</span>, <span class="var">url</span>.<span class="var">port</span>)\n';
            out += '<span class="var">request</span> = <span class="var">Net</span>::<span class="var">HTTP</span>::<span class="var">' + klass + '</span>.<span class="fn">new</span>(<span class="var">url</span>)\n';
            if (hasBody) out += '<span class="var">request</span>[<span class="str">"content-type"</span>] = <span class="str">"' + escapeHtml(ct) + '"</span>\n';
            if (hasAuth) out += '<span class="var">request</span>[<span class="str">"Authorization"</span>] = <span class="str">"' + escapeHtml(tok) + '"</span>\n';
            if (hasBody) out += '<span class="var">request</span>.<span class="var">body</span> = <span class="str">r"""' + escapeHtml(body) + '"""</span>\n';
            out += '\n<span class="var">response</span> = <span class="var">http</span>.<span class="fn">request</span>(<span class="var">request</span>)\n';
            out += '<span class="fn">puts</span> <span class="var">response</span>.<span class="fn">read_body</span>';
            return out;
        },

        php: function (url, tok, body, ct) {
            var hasBody = body.length > 0;
            var hasAuth = !!tok;
            var method = methodUpper(currentMethod);

            var hdrs = [];
            if (hasBody) hdrs.push('    <span class="str">"content-type: ' + escapeHtml(ct) + '"</span>');
            if (hasAuth) hdrs.push('    <span class="str">"Authorization: ' + escapeHtml(tok) + '"</span>');

            var out = '<span class="var">$curl</span> = <span class="fn">curl_init</span>();\n\n';
            out += '<span class="fn">curl_setopt_array</span>(<span class="var">$curl</span>, [\n';
            out += '  <span class="var">CURLOPT_URL</span> =&gt; <span class="str">"' + escapeHtml(url) + '"</span>,\n';
            out += '  <span class="var">CURLOPT_RETURNTRANSFER</span> =&gt; <span class="kw">true</span>,\n';
            out += '  <span class="var">CURLOPT_CUSTOMREQUEST</span> =&gt; <span class="str">"' + method + '"</span>,\n';
            if (hasBody) {
                var bodyEsc = escapeHtml(body).replace(/\\/g, '\\\\').replace(/'/g, "\\'");
                out += '  <span class="var">CURLOPT_POSTFIELDS</span> =&gt; <span class="str">\'' + bodyEsc + '\'</span>,\n';
            }
            out += '  <span class="var">CURLOPT_HTTPHEADER</span> =&gt; [\n' + hdrs.join(',\n') + '\n  ]\n]);\n\n';
            out += '<span class="var">$response</span> = <span class="fn">curl_exec</span>(<span class="var">$curl</span>);\n';
            out += '<span class="fn">curl_close</span>(<span class="var">$curl</span>);\n\n';
            out += '<span class="fn">echo</span> <span class="var">$response</span>;';
            return out;
        }
    };

    var LANG_HEADERS = {
        python: 'python -m pip install requests',
        shell: 'bash',
        node: 'npm install node-fetch',
        ruby: 'ruby (net/http built-in)',
        php: 'php (curl extension)'
    };

    function renderSnippet() {
        var builder = SNIPPETS[currentLang];
        if (!builder) return;

        var $body = $('#codeBody');
        if (!$body.length) return;

        var url = buildUrl();
        var tok = getAuthToken();
        var ct = currentContentType;

        $body.html(builder(url, tok, getBodyPretty(), ct));
        $('#codeHead').text(LANG_HEADERS[currentLang] || currentLang);

        $('.endpoint-url, #endpointUrlText').text(url);
    }

    var snippetTimer;
    function scheduleSnippetUpdate() {
        clearTimeout(snippetTimer);
        snippetTimer = setTimeout(renderSnippet, 150);
    }

    /* ========================================================================
       24. METHOD PICKER
       ======================================================================== */
    function setMethod(m) {
        if (['get', 'post', 'put', 'patch', 'del'].indexOf(m) === -1) return;
        currentMethod = m;

        $('#methodPicker .method-pick').removeClass('active');
        $('#methodPicker .method-pick[data-method="' + m + '"]').addClass('active');

        var $badge = $('.endpoint-bar .method');
        $badge.removeClass('get post put patch del').addClass(m);
        var iconMap = { get: 'fa-download', post: 'fa-plus', put: 'fa-pen', patch: 'fa-eraser', del: 'fa-trash' };
        $badge.find('i').removeClass().addClass('fa-solid ' + iconMap[m]);
        $badge.contents().filter(function () { return this.nodeType === 3; }).remove();
        $badge.append(m === 'del' ? 'delete' : m);

        if (METHOD_HAS_BODY[m]) {
            $('#bodySection').slideDown(200);
            $('#contentTypeSection').show();
            var touched = $('#bodyInput').data('touched-' + m);
            var stash = $('#bodyInput').data('stash-' + m);
            if (touched && typeof stash === 'string') {
                $('#bodyInput').val(stash);
            } else if (!touched) {
                /* ✅ Rebuild in the current content type. */
                $('#bodyInput').val(buildDefaultBody(m));
            }
            $('#bodyInput').removeClass('invalid');
            $('#bodyError').removeClass('show');
        } else {
            $('#bodySection').slideUp(200);
            $('#contentTypeSection').hide();
        }
        $('#bodyInput').data('lastMethod', m);

        renderSnippet();
    }
    window.setMethod = setMethod;

    $('#methodPicker').on('click', '.method-pick', function () {
        setMethod($(this).data('method'));
    });

    /* ========================================================================
       25. SIDEBAR METHOD BADGE
       ======================================================================== */
    $(document).on('click', '.sidebar-link .sidebar-method', function (e) {
        e.preventDefault();
        e.stopPropagation();
        var m = $(this).hasClass('del') ? 'del' : $(this).text().trim().toLowerCase();
        if (m === 'delete') m = 'del';
        setMethod(m);
    });

    /* ========================================================================
       26. LANGUAGE PICKER
       ======================================================================== */
    $('#langPicker').on('mousemove', '.lang-btn', function (e) {
        var r = this.getBoundingClientRect();
        this.style.setProperty('--mx', ((e.clientX - r.left) / r.width * 100) + '%');
        this.style.setProperty('--my', ((e.clientY - r.top) / r.height * 100) + '%');
    });

    $('#langPicker').on('click', '.lang-btn', function () {
        $('#langPicker .lang-btn').removeClass('active');
        $(this).addClass('active');
        currentLang = $(this).data('lang');
        renderSnippet();
        var bodyEl = $('#codeBody')[0];
        if (bodyEl) {
            $('#codeBody').css('animation', 'none');
            void bodyEl.offsetWidth;
            $('#codeBody').css('animation', 'fadeIn .3s var(--ease-out)');
        }
    });

    /* ========================================================================
       27. AUTH TOGGLE
       ======================================================================== */
    $('#authToggle').on('click', function () {
        var $i = $('#authInput');
        if ($i.prop('disabled')) return;
        var $ic = $(this).find('i');
        if ($i.attr('type') === 'password') {
            $i.attr('type', 'text');
            $ic.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            $i.attr('type', 'password');
            $ic.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });

    /* ========================================================================
       28. TRY IT — REAL API SUBMISSION
       ======================================================================== */
    function updateRequestCount() {
        var n = parseInt(localStorage.getItem(MONTH_KEY) || '0', 10);
        if (isNaN(n) || n < 0) n = 0;
        n += 1;
        localStorage.setItem(MONTH_KEY, n);
        $('#requestCount').text(n);
        $('#requestPlural').text(n === 1 ? '' : 's');
    }
    function initRequestCount() {
        var n = parseInt(localStorage.getItem(MONTH_KEY) || '0', 10);
        if (isNaN(n) || n < 0) n = 0;
        $('#requestCount').text(n);
        $('#requestPlural').text(n === 1 ? '' : 's');
    }
    initRequestCount();

    function addRecentRow(status, method, timeText) {
        var statusClass = (status >= 200 && status < 300) ? 'status-2xx' : 'status-4xx';
        var label = methodUpper(method);
        var $row = $('<tr>'
            + '<td>' + escapeHtml(timeText) + '</td>'
            + '<td><span class="status-code ' + statusClass + '"><i class="fa-solid fa-circle"></i>' + (status || '—') + '</span></td>'
            + '<td><span class="badge-try"><i class="fa-solid fa-play"></i>' + label + '</span></td>'
            + '<td><i class="fa-regular fa-eye" style="color:var(--text-subtle)"></i></td>'
            + '</tr>');
        $('#recentTableBody').prepend($row);
        if ($('#recentTableBody tr').length > 5) $('#recentTableBody tr').last().remove();
    }

    function humanBytes(b) {
        b = Number(b);
        if (!isFinite(b) || b < 0) b = 0;
        if (b < 1024) return b + ' B';
        if (b < 1024 * 1024) return (b / 1024).toFixed(1) + ' KB';
        return (b / (1024 * 1024)).toFixed(2) + ' MB';
    }

    function showResponse(status, statusText, body, ms, bytes, contentType) {
        $('#responseEmpty').addClass('hidden');
        $('#responseResult').removeClass('hidden');
        var cls = status >= 200 && status < 300 ? 'status-ok'
            : status >= 500 ? 'status-err'
                : status >= 400 ? 'status-warn' : '';
        $('#respStatus').html(
            '<i class="fa-solid fa-circle ' + cls + '"></i>' +
            escapeHtml(String(status)) + ' ' + escapeHtml(statusText || '')
        );
        $('#respTime').html('<i class="fa-solid fa-clock"></i>' + (Number(ms) || 0) + ' ms');
        $('#respSize').html('<i class="fa-solid fa-database"></i>' + humanBytes(bytes));
        var pretty = body;
        try { pretty = JSON.stringify(JSON.parse(body), null, 2); } catch (e) { }
        $('#respBody').text(pretty);
        $('#respHead').text(contentType || 'application/json');
    }

    function showErrorResponse(title, details) {
        $('#responseEmpty').addClass('hidden');
        $('#responseResult').removeClass('hidden');
        $('#respStatus').html('<i class="fa-solid fa-circle status-err"></i>' + escapeHtml(title));
        $('#respTime').html('<i class="fa-solid fa-clock"></i>—');
        $('#respSize').html('<i class="fa-solid fa-database"></i>—');
        $('#respBody').text(details || '');
        $('#respHead').text('error');
    }

    /* ----------------------------------------------------------------------
       Build the actual $.ajax payload from the textarea contents, matching
       the selected content type.
       Returns null when there's no body to send.
       ---------------------------------------------------------------------- */
    function buildAjaxPayload() {
        if (!METHOD_HAS_BODY[currentMethod]) return null;

        var raw = ($('#bodyInput').val() || '').trim();
        if (!raw) return null;

        var ct = currentContentType;

        if (ct === 'application/json') {
            /* Send the raw string — jQuery would re-serialize anyway. */
            return { contentType: 'application/json; charset=UTF-8', data: raw };

        } else if (ct === 'application/x-www-form-urlencoded') {
            return { contentType: 'application/x-www-form-urlencoded; charset=UTF-8', data: raw };

        } else if (ct === 'multipart/form-data') {
            /* Parse the JSON in the textarea, convert to FormData.
               jQuery sets its own boundary → contentType: false. */
            var obj = null;
            try { obj = JSON.parse(raw); } catch (e) { obj = null; }
            var fd = new FormData();
            if (obj && typeof obj === 'object') {
                Object.keys(obj).forEach(function (k) {
                    var v = obj[k];
                    fd.append(k, (typeof v === 'object' && v !== null) ? JSON.stringify(v) : v);
                });
            } else {
                fd.append('body', raw);
            }
            return { contentType: false, processData: false, data: fd };

        } else if (ct === 'application/xml') {
            return { contentType: 'application/xml; charset=UTF-8', data: raw };

        } else {
            /* text/plain or anything else — send as raw text. */
            return { contentType: ct + '; charset=UTF-8', data: raw };
        }
    }

    $('#tryItBtn').on('click', function () {
        var $btn = $(this);
        if ($btn.prop('disabled')) return;

        $('#credError').removeClass('show');
        $('#authInput').removeClass('invalid');
        $('#bodyError').removeClass('show');
        $('#bodyInput').removeClass('invalid');
        $('.param-error').removeClass('show');
        $('.param-input').removeClass('invalid valid');

        var errors = validateForm();
        if (errors.length) {
            toast('Fix ' + errors.length + ' validation error' + (errors.length > 1 ? 's' : ''), 'error');
            var $first = $('.param-input.invalid, #authInput.invalid, #bodyInput.invalid').first();
            if ($first.length) { $first[0].scrollIntoView({ behavior: 'smooth', block: 'center' }); $first.focus(); }
            return;
        }

        var method = methodUpper(currentMethod);
        var url = buildUrl();
        var headers = {};

        var authHeader = getAuthorizationHeader();
        if (authHeader) {
            headers['Authorization'] = authHeader;
            debug('Try It! — Authorization header added:', authHeader.replace(/\s.+/, ' ••••'));
        }

        var ajaxOpts = {
            url: url,
            type: method,
            headers: headers,
            dataType: 'text',
            cache: false,
            crossDomain: true,
            complete: function () {
                $btn.prop('disabled', false).html('<i class="fa-solid fa-play"></i>Try It!');
            }
        };

        /* ✅ Convert the textarea contents into the picked content type. */
        var payload = buildAjaxPayload();
        if (payload) {
            ajaxOpts.contentType = payload.contentType;
            ajaxOpts.data = payload.data;
            if (payload.processData === false) ajaxOpts.processData = false;
        }

        debug('Try It! — ajax', method, url, ajaxOpts);

        $btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i>Sending…');
        var t0 = performance.now();

        $.ajax(ajaxOpts)
            .done(function (data, textStatus, jqXHR) {
                var ms = Math.round(performance.now() - t0);
                var text = (typeof data === 'string') ? data : (data == null ? '' : String(data));
                var bytes = new Blob([text]).size;
                var respCt = jqXHR.getResponseHeader('content-type') || 'application/json';

                showResponse(jqXHR.status, jqXHR.statusText || textStatus, text, ms, bytes, respCt);
                addRecentRow(jqXHR.status, currentMethod, 'just now');
                updateRequestCount();
                toast('Request succeeded · ' + jqXHR.status, 'success');
            })
            .fail(function (jqXHR, textStatus, errorThrown) {
                var ms = Math.round(performance.now() - t0);
                addRecentRow(jqXHR.status || 0, currentMethod, 'just now');

                if (jqXHR && jqXHR.status && jqXHR.status > 0) {
                    var responseText = jqXHR.responseText || '';
                    var bytes = new Blob([responseText]).size;
                    var respCt = jqXHR.getResponseHeader('content-type') || 'application/json';
                    showResponse(jqXHR.status, jqXHR.statusText || 'Error', responseText, ms, bytes, respCt);
                    updateRequestCount();
                    toast('Server responded · ' + jqXHR.status, 'error');
                    return;
                }

                var hint = 'The browser blocked the request. This usually means:\n' +
                    (currentRequireToken ? '  • The Authorization token is invalid or missing\n' : '') +
                    '  • CORS is not enabled for browser-origin requests\n' +
                    '  • You are offline\n\n' +
                    'Underlying error: ' + (errorThrown || textStatus || 'unknown');

                showErrorResponse('Network error', hint);
                toast('Request failed — see Response panel', 'error');
            });
    });

    /* ========================================================================
       29. COPY PAGE + CODE
       ======================================================================== */
    $('#copyPageBtn').on('click', function () {
        var $b = $(this), orig = $b.html();
        if (navigator.clipboard) {
            navigator.clipboard.writeText(window.location.href).then(function () {
                $b.html('<i class="fa-solid fa-check"></i>Copied!');
                toast('Page URL copied', 'success');
                setTimeout(function () { $b.html(orig); }, 1500);
            });
        }
    });

    $(document).on('click', '.code-copy', function () {
        var $b = $(this);
        var text = $b.closest('.code-block').find('.code-body').text();
        if (navigator.clipboard) {
            navigator.clipboard.writeText(text).then(function () {
                $b.html('<i class="fa-solid fa-check" style="color:#8ce99a"></i>');
                toast('Copied to clipboard', 'success');
                setTimeout(function () { $b.html('<i class="fa-regular fa-copy"></i>'); }, 1200);
            });
        }
    });

    /* ========================================================================
       30. KEYBOARD SHORTCUTS
       ======================================================================== */
    $(document).on('keydown', function (e) {
        if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
            e.preventDefault(); $filterInput.focus();
        }
        if ((e.ctrlKey || e.metaKey) && e.key === '/') {
            e.preventDefault(); $filterInput.focus();
        }
        if (e.key === 'Escape' && document.activeElement === $filterInput[0]) {
            $filterInput.val(''); runFilter(); $filterInput.blur();
        }
    });

    /* ========================================================================
       31. HEADER SHADOW
       ======================================================================== */
    var $topHeader = $('#topHeader');
    $(window).on('scroll', function () {
        $topHeader.toggleClass('scrolled', $(window).scrollTop() > 8);
    });

    /* ========================================================================
       32. SCROLL REVEAL
       ======================================================================== */
    (function initScrollReveal() {
        var SELECTOR = '.param, .content-footer, .response-accordion';
        var $targets = $(SELECTOR);

        if (!('IntersectionObserver' in window) || !$targets.length) return;

        try {
            var io = new IntersectionObserver(function (entries) {
                entries.forEach(function (en) {
                    if (en.isIntersecting) {
                        en.target.style.opacity = '1';
                        en.target.style.transform = 'translateY(0)';
                        io.unobserve(en.target);
                    }
                });
            }, { threshold: .05, rootMargin: '0px 0px -30px 0px' });

            var vh = window.innerHeight || document.documentElement.clientHeight;

            $targets.each(function (i) {
                var el = this;
                var rect = el.getBoundingClientRect();
                var visible = rect.top < vh && rect.bottom > 0;
                if (!visible) {
                    el.style.opacity = '0';
                    el.style.transform = 'translateY(8px)';
                    el.style.transition = 'opacity .4s var(--ease-out) ' + (i * .015) + 's, transform .4s var(--ease-out) ' + (i * .015) + 's';
                }
                io.observe(el);
            });

            setTimeout(function () {
                $targets.each(function () {
                    if (getComputedStyle(this).opacity === '0') {
                        this.style.opacity = '1';
                        this.style.transform = 'translateY(0)';
                    }
                });
            }, 1200);
        } catch (err) {
            console.error('[scroll-reveal] disabled:', err);
            $targets.each(function () {
                this.style.opacity = '1';
                this.style.transform = 'none';
            });
        }
    })();

    /* ========================================================================
       33. INIT
       ======================================================================== */
    currentEndpoint = '';
    currentRequireToken = true;

    (function restoreToken() {
        var $i = $('#authInput');
        if (!$i.length) return;

        var bare = stripSchemePrefix(storedToken);
        if (bare) {
            $i.val(bare);
            setStoredToken(bare);
        } else {
            setStoredToken('');
        }
    })();

    applyContentTypeUI();
    applyAuthSchemeUI();

    setMethod('get');
    applyTokenRequirementUI();
    renderSnippet();

    (function initEndpointName() {
        var $active = $('.sidebar-link.active:not(.sidebar-link-parent)').first();
        if ($active.length) {
            var info = getLinkInfo($active);
            if (info.text || info.camel.endpoint) {
                updateEndpointName(info);
                updatePageTitle(info.text);
            }
        }
    })();

});