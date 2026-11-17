(function (wp) {
 'use strict';
 var el = wp.element.createElement, useState = wp.element.useState, C = wp.components;
 var brandIcon = window.roSocialBed && window.roSocialBed.logo ? el('img', { src: window.roSocialBed.logo, alt: '', 'aria-hidden': true, style: { width: 24, height: 24, objectFit: 'contain', borderRadius: 4 } }) : 'share';
 wp.blocks.registerBlockType('ro-socialbed/posts', {
  apiVersion: 2, title: 'Pulse', description: 'De pe internet culese.',
  icon: { src: brandIcon }, category: 'embed', keywords: ['facebook', 'social', 'embed'],
  attributes: { urls: { type: 'array', default: [] } }, supports: { html: false },
  edit: function (props) {
   wp.element.useEffect(function () { document.dispatchEvent(new Event('rosocialbed:refresh')); }, [JSON.stringify(props.attributes.urls)]);
   var state = useState((props.attributes.urls || []).join('\n')), text = state[0], setText = state[1];
   var messageState = useState(''), message = messageState[0], setMessage = messageState[1];
   var parsed = window.ROSocialBedLinks.parse(text);
   var disabled = !parsed.urls.length || parsed.invalid.length > 0 || parsed.over;
   return el('div', wp.blockEditor.useBlockProps({ className: 'ro-socialbed-editor' }),
    el('img', { src: window.roSocialBed.logo, alt: 'Pulse', className: 'ro-socialbed-logo' }),
    el('h3', null, 'De pe internet culese.'),
    el('p', null, 'Facebook · X · Bluesky · Substack · YouTube · TikTok · Instagram · Pinterest · LinkedIn · Mastodon · Telegram · Reddit · Threads'),
    el(C.TextareaControl, { label: 'Lipește până la 20 de linkuri, câte unul pe rând', value: text, rows: 8, onChange: function (value) { setText(value); setMessage('Modificări neaplicate — apasă „Aplică linkurile”.'); } }),
    el('p', { 'aria-live': 'polite' }, parsed.urls.length + '/20 linkuri unice' + (parsed.duplicates ? ' · ' + parsed.duplicates + ' duplicate eliminate' : '')),
    parsed.over ? el(C.Notice, { status: 'error', isDismissible: false }, 'Limita este de 20 de linkuri unice. Împarte lista în două blocuri.') : null,
    parsed.invalid.length ? el(C.Notice, { status: 'error', isDismissible: false }, 'Corectează linkurile nerecunoscute. Folosește adresa completă a postării:', el('ul', null, parsed.invalid.map(function (s, i) { return el('li', { key: i }, s); }))) : null,
    el(C.Button, { variant: 'primary', disabled: disabled, onClick: function () { props.setAttributes({ urls: parsed.urls }); setText(parsed.urls.join('\n')); setMessage('Linkuri aplicate. Salvează sau actualizează articolul, apoi deschide previzualizarea.'); } }, 'Aplică linkurile'),
    el('p', { role: 'status' }, message),
    el('div', { className: 'ro-socialbed-group' }, (props.attributes.urls || []).filter(function (url) { return /^https:\/\/twitter\.com\//.test(url); }).map(function (url) {
     var match = url.match(/\/status\/(\d+)/);
     return match ? el('div', { key: url, className: 'ro-socialbed-x-preview', 'data-rosb-theme': window.roSocialBed.theme || 'light', 'data-rosb-tweet-id': match[1], contentEditable: false }) : null;
    })),
    el('div', { className: 'ro-socialbed-group' }, (props.attributes.urls || []).filter(function (url) { return /^https:\/\/www\.instagram\.com\//.test(url); }).map(function (url) {
     return el('div', { key: url, className: 'rop-instagram-host', 'data-rop-instagram-url': url, contentEditable: false });
    })),
    (props.attributes.urls || []).length ? el('details', null, el('summary', null, 'Postările salvate în bloc (' + props.attributes.urls.length + ')'), el('ol', null, props.attributes.urls.map(function (url) { return el('li', { key: url }, el('a', { href: url, target: '_blank', rel: 'noopener noreferrer' }, url)); }))) : null,
    el('small', null, 'X și Instagram au preview în editor. Verifică celelalte platforme în previzualizarea articolului. Postările indisponibile nu primesc butoane sau casete de rezervă.'));
  },
  save: function () { return null; }
 });
})(window.wp);
