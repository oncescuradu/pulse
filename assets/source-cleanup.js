(function(){
'use strict';
function clean(){
 document.querySelectorAll('.health-tweets a.health-social-source,.health-social-item > a.health-social-source,.ro-socialbed-group .ro-socialbed-source').forEach(function(link){link.remove();});
}
if(document.readyState==='loading')document.addEventListener('DOMContentLoaded',clean);else clean();
document.addEventListener('rosocialbed:refresh',clean);
})();
