(function () {
 'use strict';
 document.addEventListener('click', function (event) {
  if (!event.target.closest('.ro-socialbed-open')) return;
  var editorId = window.wpActiveEditor || 'content';
  var dialog = document.createElement('dialog'); dialog.className = 'ro-socialbed-dialog';
  dialog.innerHTML = '<form method="dialog"><h2>Pulse</h2><p>De pe internet culese.</p><label for="rosb-links">Până la 20 de linkuri, câte unul pe rând</label><textarea id="rosb-links" rows="10"></textarea><p class="rosb-status" role="status"></p><p>Postările vor fi centrate automat.</p><button type="button" class="button button-primary rosb-insert">Inserează postările</button> <button class="button" value="cancel">Anulează</button></form>';
  document.body.appendChild(dialog);
  if (window.roSocialBedClassic && window.roSocialBedClassic.logo) {
   var logo = document.createElement('img'); logo.src = window.roSocialBedClassic.logo;
   logo.alt = 'Pulse'; logo.className = 'ro-socialbed-dialog-logo';
   dialog.querySelector('h2').insertAdjacentElement('beforebegin', logo);
  }
  dialog.addEventListener('close', function () { dialog.remove(); });
  dialog.querySelector('.rosb-insert').addEventListener('click', function () {
   if (!window.ROSocialBedLinks || !window.ROSocialBedInsert) {
    dialog.querySelector('.rosb-status').textContent = 'Fișierele pluginului nu s-au încărcat. Reîncarcă pagina cu Ctrl+F5.'; return;
   }
   var result = window.ROSocialBedLinks.parse(dialog.querySelector('textarea').value);
   if (!result.urls.length || result.invalid.length || result.over) {
    dialog.querySelector('.rosb-status').textContent = result.over ? 'Maximum 20 de linkuri unice.' : 'Introdu linkuri complete și valide către postări. Nerecunoscute: ' + result.invalid.join(', '); return;
   }
   var inserted = window.ROSocialBedInsert(result.urls, editorId);
   if (!inserted.ok) { dialog.querySelector('.rosb-status').textContent = inserted.error; return; }
   dialog.close();
  });
  dialog.showModal();
 });
})();
