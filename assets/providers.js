(function(root){
'use strict';
function publicHost(h){return h.length<=253 && /^(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.)+[a-z]{2,63}$/.test(h) && !/\.(?:local|localhost|internal|test|invalid)$/.test(h);}
function parse(u){
 var h=u.hostname.toLowerCase(),p=u.pathname,m,id;
 if(['youtube.com','www.youtube.com','m.youtube.com','youtu.be','www.youtu.be','www.youtube-nocookie.com'].includes(h)){
  if(['youtube.com','www.youtube.com','m.youtube.com'].includes(h)){
   if((m=p.match(/^\/post\/(Ug[A-Za-z0-9_-]{8,198})\/?$/)))return'https://www.youtube.com/post/'+m[1];
   var post=u.searchParams.get('lb');
   if(/^Ug[A-Za-z0-9_-]{8,198}$/.test(post||'')&&/^\/(?:@[A-Za-z0-9_.-]+|channel\/[A-Za-z0-9_-]+)\/(?:community|posts)\/?$/.test(p))return'https://www.youtube.com/post/'+post;
  }
  if(h.endsWith('youtu.be'))id=p.slice(1);else if(p==='/watch')id=u.searchParams.get('v');else if((m=p.match(/^\/(?:shorts|embed|live)\/([A-Za-z0-9_-]{11})\/?$/)))id=m[1];
  if(/^[A-Za-z0-9_-]{11}$/.test(id||''))return'https://www.youtube.com/watch?v='+id;
 }
 if(['tiktok.com','www.tiktok.com','m.tiktok.com'].includes(h)&&/^\/@[A-Za-z0-9_.]+\/(?:video|photo)\/\d+\/?$/.test(p))return'https://www.tiktok.com'+p.replace(/\/$/,'');
 if(['instagram.com','www.instagram.com','m.instagram.com'].includes(h)&&(m=p.match(/^\/(?:(?!share\/)[A-Za-z0-9_.]+\/)?(p|reel|reels|tv)\/([A-Za-z0-9_-]+)\/?$/)))return'https://www.instagram.com/'+(m[1]==='reels'?'reel':m[1])+'/'+m[2]+'/';
 if(['pinterest.com','www.pinterest.com'].includes(h)&&(m=p.match(/^\/pin\/(\d+)\/?$/)))return'https://www.pinterest.com/pin/'+m[1]+'/';
 if(['linkedin.com','www.linkedin.com'].includes(h)){
  if((m=p.match(/^\/(?:embed\/)?feed\/update\/urn:li:(activity|share|ugcPost):(\d+)\/?$/)))return'https://www.linkedin.com/feed/update/urn:li:'+m[1]+':'+m[2];
  if((m=p.match(/^\/posts\/[^/]+-(activity|share|ugcPost)-(\d+)-[A-Za-z0-9_-]+\/?$/)))return'https://www.linkedin.com/feed/update/urn:li:'+m[1]+':'+m[2];
 }
 if(['t.me','telegram.me','www.t.me'].includes(h)&&(m=p.match(/^\/(?:s\/)?([A-Za-z][A-Za-z0-9_]{3,})\/(\d+)\/?$/)))return'https://t.me/'+m[1]+'/'+m[2];
 if(['substack.com','www.substack.com','open.substack.com'].includes(h)){
  if((m=p.match(/^\/(?:home\/post\/p-|@[A-Za-z0-9_.-]+\/p-|inbox\/post\/)(\d+)\/?$/)))return'https://substack.com/home/post/p-'+m[1];
  if((m=p.match(/^\/pub\/([A-Za-z0-9-]+)\/p\/([A-Za-z0-9_-]+)\/?$/)))return'https://'+m[1].toLowerCase()+'.substack.com/p/'+m[2];
  if(/^\/(?:(?:@[A-Za-z0-9_.-]+|profile\/[A-Za-z0-9_-]+)\/)?note\/c-\d+\/?$/.test(p))return'https://substack.com'+p.replace(/\/$/,'');
 }
 if(/^(?:(?:www|m|mobile|old|new)\.)?(?:x\.com|twitter\.com|facebook\.com|reddit\.com|threads\.(?:com|net)|instagram\.com|tiktok\.com|linkedin\.com|pinterest\.com|youtube\.com|bsky\.app)$/.test(h))return null;
 if(publicHost(h)&&/^\/p\/[A-Za-z0-9_-]+\/?$/.test(p))return'https://'+h+p.replace(/\/$/,'');
 if(publicHost(h)&&/^\/@[A-Za-z0-9_.-]+\/\d+\/?$/.test(p))return'https://'+h+p.replace(/\/$/,'');
 return null;
}
root.ROPulseProviders={normalize:parse};
})(typeof window!=='undefined'?window:globalThis);
