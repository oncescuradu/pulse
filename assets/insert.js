(function (root) {
 'use strict';
 root.ROSocialBedInsert = function (urls, editorId) {
  try {
   var wp = root.wp, id = editorId || 'content';
   var shortcode = '[ro_pulse urls="' + urls.join(' ') + '"]';
   var tiny = root.tinymce && root.tinymce.get(id);
   if (tiny && !tiny.isHidden()) {
    tiny.focus();
    tiny.insertContent('<p>' + shortcode.replace(/&/g, '&amp;').replace(/</g, '&lt;') + '</p>');
    if (tiny.save) tiny.save();
    if (tiny.fire) tiny.fire('change');
    return { ok: true };
   }
   // A data registry can exist in Classic Editor without the Gutenberg store.
   // Only query that store when an actual block editor is mounted.
   if (wp && wp.data && wp.blocks && root.document.querySelector('.block-editor-writing-flow, .edit-post-visual-editor')) {
    var store = wp.data.select('core/block-editor');
    var dispatch = wp.data.dispatch('core/block-editor');
    if (store && typeof store.getBlocks === 'function' && dispatch && typeof dispatch.insertBlocks === 'function' && wp.blocks.getBlockType('ro-socialbed/posts')) {
     dispatch.insertBlocks(wp.blocks.createBlock('ro-socialbed/posts', { urls: urls }));
     return { ok: true };
    }
   }
   var area = root.document.getElementById(id);
   if (area && typeof area.setRangeText === 'function') {
    area.setRangeText('\n' + shortcode + '\n', area.selectionStart, area.selectionEnd, 'end');
    area.dispatchEvent(new root.Event('input', { bubbles: true }));
    area.dispatchEvent(new root.Event('change', { bubbles: true }));
    return { ok: true };
   }
   return { ok: false, error: 'Editorul nu este disponibil. Revino la editorul articolului și încearcă din nou.' };
  } catch (error) {
   return { ok: false, error: 'Inserarea a eșuat: ' + (error.message || String(error)) };
  }
 };
})(window);
