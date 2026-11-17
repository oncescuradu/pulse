(function(){
'use strict';
var loading;
function library(){
 if(window.instgrm&&window.instgrm.Embeds&&typeof window.instgrm.Embeds.process==='function')return Promise.resolve(window.instgrm);
 if(loading)return loading;
 loading=new Promise(function(resolve,reject){
  var attempts=0,interval;
  function check(){
   if(window.instgrm&&window.instgrm.Embeds&&typeof window.instgrm.Embeds.process==='function'){clearInterval(interval);resolve(window.instgrm);return;}
   if(++attempts>=100){clearInterval(interval);reject(new Error('Instagram nu a răspuns.'));}
  }
  interval=setInterval(check,150);
  var script=document.querySelector('script[src*="instagram.com/embed.js"]');
  if(!script){script=document.createElement('script');script.src='https://www.instagram.com/embed.js';script.async=true;script.addEventListener('load',check);script.addEventListener('error',function(){clearInterval(interval);reject(new Error('Script Instagram blocat.'));});document.head.appendChild(script);}
  else script.addEventListener('load',check);
  check();
 });
 return loading;
}
function render(){
 document.querySelectorAll('[data-rop-instagram-url]:not([data-rop-started])').forEach(function(node){
  var url=node.getAttribute('data-rop-instagram-url');
  if(!/^https:\/\/www\.instagram\.com\/(?:p|reel|tv)\/[A-Za-z0-9_-]+\/$/.test(url))return;
  node.setAttribute('data-rop-started','true');
  var quote=document.createElement('blockquote');quote.className='instagram-media';quote.setAttribute('data-instgrm-permalink',url);quote.setAttribute('data-instgrm-version','14');
  var link=document.createElement('a');link.href=url;quote.appendChild(link);node.appendChild(quote);
  var timer,observer=new MutationObserver(function(){if(node.querySelector('iframe')){observer.disconnect();clearTimeout(timer);}});
  observer.observe(node,{childList:true,subtree:true});
  timer=setTimeout(function(){observer.disconnect();if(!node.querySelector('iframe')){quote.remove();node.hidden=true;var card=node.closest?node.closest('.ro-socialbed-card'):null;if(card)card.hidden=true;}},20000);
  library().then(function(api){api.Embeds.process();}).catch(function(){observer.disconnect();clearTimeout(timer);quote.remove();node.hidden=true;var card=node.closest?node.closest('.ro-socialbed-card'):null;if(card)card.hidden=true;});
 });
}
document.addEventListener('rosocialbed:refresh',render);
if(document.readyState==='loading')document.addEventListener('DOMContentLoaded',render);else render();
})();
