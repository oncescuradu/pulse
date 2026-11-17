(function(){
'use strict';
var root=document.querySelector('.pulse-admin-wrap');if(!root)return;
var tabs=Array.from(root.querySelectorAll('[data-pulse-tab]')),panels=Array.from(root.querySelectorAll('[data-pulse-panel]'));
if(!tabs.length)return;
var nav=root.querySelector('.pulse-tabs'),form=root.querySelector('form');nav.setAttribute('role','tablist');
tabs.forEach(function(tab){tab.setAttribute('role','tab');tab.setAttribute('aria-controls','pulse-'+tab.dataset.pulseTab);});
panels.forEach(function(panel){panel.setAttribute('role','tabpanel');panel.setAttribute('aria-labelledby','pulse-tab-'+panel.dataset.pulsePanel);});
function activate(key,focus){
 if(!tabs.some(function(t){return t.dataset.pulseTab===key;}))key='overview';
 tabs.forEach(function(t){var active=t.dataset.pulseTab===key;t.setAttribute('aria-selected',String(active));t.tabIndex=active?0:-1;if(active&&focus)t.focus();});
 panels.forEach(function(p){p.hidden=p.dataset.pulsePanel!==key;});
 var hash='#pulse-'+key;try{history.replaceState(null,'',hash);}catch(e){}
 var referer=form&&form.querySelector('input[name="_wp_http_referer"]');if(referer)referer.value=referer.value.split('#')[0]+hash;
}
tabs.forEach(function(tab,index){
 tab.addEventListener('click',function(e){e.preventDefault();activate(tab.dataset.pulseTab,false);});
 tab.addEventListener('keydown',function(e){var next;if(e.key==='ArrowRight')next=(index+1)%tabs.length;else if(e.key==='ArrowLeft')next=(index+tabs.length-1)%tabs.length;else if(e.key==='Home')next=0;else if(e.key==='End')next=tabs.length-1;else return;e.preventDefault();activate(tabs[next].dataset.pulseTab,true);});
});
activate(location.hash.replace('#pulse-',''),false);
if(form){
 var status=root.querySelector('.pulse-save-status');
 function dirty(){if(status){status.textContent='Modificări nesalvate';status.classList.add('is-dirty');}}
 form.addEventListener('input',dirty);form.addEventListener('change',dirty);
 form.addEventListener('invalid',function(e){var panel=e.target.closest('[data-pulse-panel]');if(panel)activate(panel.dataset.pulsePanel,false);},true);
}
})();
