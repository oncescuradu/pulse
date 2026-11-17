(function (root) {
 'use strict';
 function normalize(raw) {
  if (typeof raw !== 'string' || raw.length > 2048) return null;
  var u; try { u = new URL(raw.trim()); } catch (_) { return null; }
  if (!/^https?:$/.test(u.protocol) || u.username || u.password || u.port) return null;
  var h = u.hostname.toLowerCase(), p = u.pathname, m;
  if (root.ROPulseProviders) { var extra = root.ROPulseProviders.normalize(u); if (extra) return extra; }
  if (['x.com','www.x.com','twitter.com','www.twitter.com','mobile.twitter.com'].includes(h) && (m = p.match(/^\/(?:[A-Za-z0-9_]+\/status|i\/web\/status)\/([0-9]+)(?:\/.*)?$/))) return 'https://twitter.com/i/web/status/' + m[1];
  if (['reddit.com','www.reddit.com','old.reddit.com','new.reddit.com'].includes(h) && /^\/r\/[A-Za-z0-9_]+\/comments\/[a-z0-9]+(?:\/[^\s<>"']*)?\/?$/i.test(p)) return 'https://www.reddit.com' + p.replace(/\/$/, '') + '/';
  if (['threads.net','www.threads.net','threads.com','www.threads.com'].includes(h) && /^\/(?:@[A-Za-z0-9_.]+\/post|t)\/[A-Za-z0-9_-]+\/?$/.test(p)) return 'https://www.threads.com' + p.replace(/\/$/, '') + '/';
  if (['bsky.app','www.bsky.app'].includes(h) && /^\/profile\/[A-Za-z0-9.:%_-]+\/post\/[a-z0-9]+\/?$/i.test(p)) return 'https://bsky.app' + p.replace(/\/$/, '');
  if (['facebook.com','www.facebook.com','m.facebook.com','web.facebook.com'].includes(h)) {
   var base = 'https://www.facebook.com' + p;
   if (/^\/(?:[A-Za-z0-9._-]+\/(?:posts|videos)\/[A-Za-z0-9._-]+|(?:reel|share\/p|share\/v|share\/r)\/[A-Za-z0-9._-]+)\/?$/.test(p)) return base;
   if (p === '/permalink.php' && /^[A-Za-z0-9]+$/.test(u.searchParams.get('story_fbid') || '') && /^\d+$/.test(u.searchParams.get('id') || '')) return base + '?story_fbid=' + u.searchParams.get('story_fbid') + '&id=' + u.searchParams.get('id');
   var k = ['/photo.php','/photo'].includes(p) ? 'fbid' : ['/watch','/watch/'].includes(p) ? 'v' : null;
   if (k && /^\d+$/.test(u.searchParams.get(k) || '')) return base + '?' + k + '=' + u.searchParams.get(k);
  }
  return null;
 }
 function parse(text) {
  var lines = text.split(/\r?\n/).map(function (s) { return s.trim(); }).filter(Boolean);
  var urls = [], invalid = [], duplicates = 0;
  lines.forEach(function (line) { var u = normalize(line); if (!u) invalid.push(line); else if (urls.includes(u)) duplicates++; else urls.push(u); });
  return { urls: urls, invalid: invalid, duplicates: duplicates, over: urls.length > 20 };
 }
 root.ROSocialBedLinks = { normalize: normalize, parse: parse };
})(typeof window !== 'undefined' ? window : globalThis);
