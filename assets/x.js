(function () {
 'use strict';
 var loading;
 function library() {
  if (window.twttr && window.twttr.widgets && window.twttr.widgets.createTweet) return Promise.resolve(window.twttr);
  if (loading) return loading;
  loading = new Promise(function (resolve, reject) {
   var script = document.querySelector('script[src*="platform.twitter.com/widgets.js"],script[src*="platform.x.com/widgets.js"]');
   var timer = setTimeout(function () { reject(new Error('X nu a răspuns.')); }, 15000);
   function ready() {
    if (window.twttr && window.twttr.widgets && window.twttr.widgets.createTweet) { clearTimeout(timer); resolve(window.twttr); }
    else if (window.twttr && window.twttr.ready) window.twttr.ready(function (api) { clearTimeout(timer); resolve(api); });
   }
   if (!script) {
    script = document.createElement('script'); script.src = 'https://platform.twitter.com/widgets.js'; script.async = true;
    script.addEventListener('load', ready);
    script.addEventListener('error', function () { clearTimeout(timer); reject(new Error('Scriptul X este blocat.')); });
    document.head.appendChild(script);
   } else { script.addEventListener('load', ready); ready(); }
  });
  return loading;
 }
 function render() {
  var targets = document.querySelectorAll('[data-rosb-tweet-id]:not([data-rosb-started])');
  if (!targets.length) return;
  targets.forEach(function (node) {
   node.setAttribute('data-rosb-started', 'true');
   var id = node.getAttribute('data-rosb-tweet-id');
   if (!/^\d+$/.test(id)) return;
   node.textContent = 'Se încarcă postarea X…';
   library().then(function (api) {
    node.textContent = '';
    return api.widgets.createTweet(id, node, { align: 'center', theme: node.getAttribute('data-rosb-theme') === 'dark' ? 'dark' : 'light', dnt: true, conversation: 'none' });
   }).then(function (frame) {
    if (!frame) throw new Error('Postarea nu este disponibilă.');
   }).catch(function () {
    node.textContent = '';
    node.hidden = true;
    var card = node.closest ? node.closest('.ro-socialbed-card') : null;
    if (card) card.hidden = true;
   });
  });
 }
 document.addEventListener('rosocialbed:refresh', render);
 if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', render); else render();
})();
