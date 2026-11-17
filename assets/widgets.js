(function(){
'use strict';
function start(){
 document.querySelectorAll('[data-rop-telegram]:not([data-rop-started])').forEach(function(node){
  var post=node.getAttribute('data-rop-telegram');
  if(!/^[A-Za-z][A-Za-z0-9_]{3,}\/\d+$/.test(post))return;
  node.setAttribute('data-rop-started','true');
  var script=document.createElement('script');script.async=true;script.src='https://telegram.org/js/telegram-widget.js?22';
  script.setAttribute('data-telegram-post',post);script.setAttribute('data-width','100%');script.setAttribute('data-userpic','true');
  var observer=new MutationObserver(function(){var frame=node.querySelector('iframe');if(frame){var link=node.querySelector('.rop-link-card');if(link)link.remove();observer.disconnect();}});
  observer.observe(node,{childList:true,subtree:true});
  script.addEventListener('error',function(){observer.disconnect();});node.appendChild(script);
 });
 document.querySelectorAll('iframe.rop-mastodon:not([data-rop-started])').forEach(function(frame,index){
  frame.setAttribute('data-rop-started','true');
  var origin=new URL(frame.src).origin,id=index+1;
  function resize(event){
   var data=event.data;
   if(event.source!==frame.contentWindow||event.origin!==origin||!data||data.type!=='setHeight'||data.id!==id)return;
   var height=Number(data.height);if(Number.isFinite(height)&&height>=100&&height<=4000)frame.style.height=height+'px';
  }
  window.addEventListener('message',resize);
  function ask(){if(frame.contentWindow)frame.contentWindow.postMessage({type:'setHeight',id:id},origin);}
  frame.addEventListener('load',ask);ask();
 });
}
if(document.readyState==='loading')document.addEventListener('DOMContentLoaded',start);else start();
document.addEventListener('rosocialbed:refresh',start);
})();
