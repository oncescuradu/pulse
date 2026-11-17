(function () {
 'use strict';
 var choose = document.getElementById('rosb-logo-choose');
 var reset = document.getElementById('rosb-logo-reset');
 var input = document.getElementById('rosb-logo-id');
 var preview = document.getElementById('rosb-logo-preview');
 var status = document.getElementById('rosb-logo-status');
 if (!choose || !reset || !input || !preview) return;
 var frame;
 choose.addEventListener('click', function () {
  if (!window.wp || !window.wp.media) { status.textContent = 'Biblioteca Media nu s-a încărcat. Reîncarcă pagina.'; return; }
  if (!frame) {
   frame = window.wp.media({ title: 'Alege logo-ul Pulse', button: { text: 'Folosește acest logo' }, library: { type: 'image' }, multiple: false });
   frame.on('select', function () {
    var selected = frame.state().get('selection').first();
    if (!selected) return;
    var image = selected.toJSON();
    if (!image.id || !image.url) return;
    input.value = image.id; preview.src = image.url; input.dispatchEvent(new Event('change', { bubbles: true }));
    status.textContent = 'Logo selectat. Apasă „Salvează setările” pentru aplicare.';
   });
  }
  frame.open();
 });
 reset.addEventListener('click', function () {
  input.value = '0'; preview.src = window.roSocialBedSettings.defaultLogo; input.dispatchEvent(new Event('change', { bubbles: true }));
  status.textContent = 'Logo implicit selectat. Apasă „Salvează setările” pentru aplicare.';
 });
})();
