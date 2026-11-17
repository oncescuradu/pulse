=== Pulse ===
Contributors: ro-socialbed
Tags: social, embed, facebook, bluesky, threads
Requires at least: 6.3
Requires PHP: 7.4
Stable tag: 1.6.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Inserează până la 20 de postări sociale centrate, din linkuri lipite în articol.

== Instalare ==
Actualizare 1.1.0: numele afișat este Pulse. Folderul tehnic ro-socialbed și blocurile existente sunt păstrate pentru compatibilitate. Încarcă ro-pulse-1.1.0.zip și înlocuiește versiunea existentă.
1. WordPress > Module/Plugins > Adaugă modul nou > Încarcă modul.
2. Selectează ro-socialbed.zip, instalează și activează.

== Gutenberg ==
1. În articol, apasă + și caută Pulse.
2. Lipește adresele complete ale postărilor, câte una pe rând (maximum 20 unice).
3. Apasă Aplică linkurile. Linkurile incorecte trebuie corectate înainte de aplicare.
4. Salvează/actualizează articolul și deschide Previzualizare pentru postările integrate.
5. Pentru mai mult de 20 de linkuri, adaugă alt bloc.
Editorul arată lista salvată; postările integrate se încarcă în previzualizare și pe site.
Modificările din casetă trebuie aplicate înainte de salvarea articolului.

== Editor clasic ==
Apasă Pulse lângă Adaugă fișier media, lipește linkurile și apasă Inserează postările.
Alternativ, folosește shortcode-ul:
[ro_socialbed urls="https://x.com/nume/status/123456789 https://www.threads.com/@nume/post/ABC123/"]
Shortcode-ul acceptă linkuri separate prin spațiu, virgulă sau rând nou; interfața cere unul pe rând.

== Platforme și limitări ==
* Facebook: linkuri posts, videos, reel, permalink.php, photo.php, watch și share/p, share/v, share/r. Linkurile share pot necesita înlocuirea cu permalink-ul original.
* X/Twitter: linkuri status; variantele x.com și twitter.com sunt deduplicate după ID.
* Reddit: adrese complete /r/comunitate/comments/id/... (inclusiv comentarii).
* Threads: threads.com și threads.net, /@nume/post/id/ și /t/id/.
* Bluesky: bsky.app/profile/nume/post/id.
* Linkurile scurte t.co, redd.it, fb.watch și linkurile către profiluri nu sunt expandate. Folosește adresa completă a postării.
* Postările private, șterse, restricționate sau blocate de browser/platformă pot să nu se afișeze. Linkul spre sursă rămâne vizibil.
* Facebook folosește un iframe de 680px înălțime; unele postări lungi trebuie deschise la sursă.
* Bluesky folosește oEmbed cu cache 24h; eșecurile sunt păstrate 5 minute. Prima încărcare a mai multor postări Bluesky poate dura mai mult (timeout 3s/postare).
* Sunt inserate postările originale în articolul existent. Pluginul nu generează text editorial, nu copiază comentariile și nu publică automat articole.
* Sunt necesare o temă cu wp_head/wp_footer și acces la serviciile externe. Politicile CSP, extensiile de blocare și modulele de consimțământ pot împiedica afișarea.

== Servicii externe și confidențialitate ==
Pluginul nu are telemetrie proprie. Integrarea încarcă servicii terțe pentru platformele folosite:
Facebook: www.facebook.com/plugins/post.php sau video.php.
X: platform.twitter.com/widgets.js.
Reddit: embed.reddit.com/widgets.js.
Threads: www.threads.com/embed.js.
Bluesky: serverul WordPress trimite URL-ul postării la https://embed.bsky.app/oembed; vizitatorul încarcă embed.bsky.app/static/embed.js.
Aceste servicii pot primi adresa IP, date de browser, referrer și cookie-uri ale vizitatorului, conform propriilor politici.
Pluginul nu include un sistem de consimțământ. Administratorul site-ului configurează separat integrarea cu modulul său de confidențialitate, dacă este necesar.
Politici:
https://www.facebook.com/privacy/policy/
https://x.com/en/privacy
https://www.reddit.com/policies/privacy-policy
https://www.threads.com/privacy
https://bsky.social/about/support/privacy-policy

== Date stocate ==
URL-urile sunt stocate în conținutul articolului (atribute de bloc sau shortcode).
Răspunsurile Bluesky sunt păstrate temporar în cache-ul WordPress.
Dezactivarea păstrează URL-urile. Reactivează pluginul pentru a reda blocurile; shortcode-urile pot rămâne vizibile ca text cât timp pluginul este inactiv.

== Platforme noi în 1.2.0 ==
Substack: articole și Notes (substack.com, open.substack.com, publicații .substack.com și domenii proprii /p/slug).
YouTube: watch, youtu.be, Shorts și live cu ID video; TikTok: /@user/video/id și /photo/id.
Instagram: /p/, /reel/, /reels/, /tv/; Pinterest: /pin/id/.
LinkedIn: /posts/...activity-ID... și /feed/update/urn:li:activity|share|ugcPost:ID.
Mastodon: /@user/ID pe domenii publice; Telegram: t.me/canal/ID (mesaje publice).
Nu sunt necesare liste de domenii sau configurări per cont. Pentru domeniile proprii, tipul este dedus din formatul URL-ului. Folosește linkuri complete către postări, nu homepage-uri sau profiluri.
Linkurile scurte pin.it, vm.tiktok.com și vt.tiktok.com trebuie deschise înainte de a copia adresa finală. Story-urile Instagram, canalele Telegram private și conținutul care cere autentificare nu sunt integrate.
Linkurile substack.com/home/post/p-ID și /@autor/p-ID sunt acceptate; afișarea depinde de rezolvarea acestora de către Substack. Dacă un asemenea link nu se afișează, folosește URL-ul final al articolului.
Aspectul exterior este discret și centrat; designul și funcțiile din interiorul iframe-urilor sunt controlate de platformă. Nu se poate reproduce exact cardul X pentru toate platformele.
Mesajele de eroare din iframe-urile externe nu pot fi detectate universal. Pentru un embed blocat se poate activa linkul sursă din Setări. În editor rămân disponibile toate URL-urile salvate.
Bluesky are acum o casetă de rezervă când serviciul oEmbed nu răspunde.

== Servicii externe suplimentare ==
YouTube: www.youtube-nocookie.com; TikTok: www.tiktok.com/player/v1/; Instagram: www.instagram.com/embed.js; Pinterest: assets.pinterest.com/js/pinit.js; LinkedIn: www.linkedin.com/embed/feed/update/; Substack: substack.com/embedjs/embed.js; Telegram: telegram.org/js/telegram-widget.js; Mastodon: domeniul postării /embed.
Browserul vizitatorului contactează aceste servicii atunci când se încarcă integrările respective. Politicile de confidențialitate ale fiecărei platforme și ale instanței Mastodon se aplică. Nu sunt trimise cereri HTTP de pe serverul WordPress către domeniile proprii pentru detectare sau scraping.
Documentație:
https://support.substack.com/hc/en-us/articles/25665267911572-How-do-I-embed-Substack-posts-or-notes-on-a-website
https://developers.tiktok.com/docs/en/embed-player
https://core.telegram.org/widgets/post
https://www.linkedin.com/help/linkedin/answer/a529065/embed-content-from-the-linkedin-feed
https://dev.pinterest.com/docs/web-features/widgets/
https://developers.google.com/youtube/player_parameters

== Corecții 1.2.1 ==
Instagram: acceptă și /numecont/p/cod/ și /numecont/reel/cod/, normalizează linkurile și pornește explicit SDK-ul după încărcare. Preview și în Gutenberg după Aplică linkurile. Postările private, restricționate sau cu integrarea dezactivată rămân dependente de Instagram.
LinkedIn: recunoaște titluri cu diacritice sau caractere codificate și identificatorii activity, share și ugcPost. Păstrează tipul identificatorului, fără a converti share în activity.
YouTube Posts: /post/Ug... și linkuri /@canal/community?lb=Ug... sau /channel/ID/posts?lb=Ug.... Afișare ca o casetă cu link la original; nu reprezintă preview nativ și nu extrage textul, imaginile sau sondajele postării.


== Fără butoane de rezervă (1.3.1) ==
Pluginul nu mai generează butoane Deschide originalul, linkuri suplimentare Sursă sau casete de rezervă, pe nicio platformă. Eliminarea se aplică în articole, indiferent de vechea opțiune de sursă. Linkurile rămân salvate și accesibile în editor pentru administrare.
YouTube Posts nu mai au afișare pe site deoarece singura implementare disponibilă era caseta cu link. Videoclipurile YouTube continuă să funcționeze. Eșecurile X/Instagram nu mai generează linkuri; răspunsurile oEmbed Bluesky eșuate nu mai generează casete.
Legăturile și comenzile din interiorul integrărilor native sunt gestionate de platforme; acest update nu modifică interiorul iframe-urilor externe. Istoricul versiunilor de mai jos descrie comportamentul vechi, înlocuit de 1.3.1.

== Pulse 1.4.0 ==
Nume: Pulse.
Descriere: De pe internet culese.
Descriere tehnică: Built by Radu Oncescu with ChatGPT Codex
Noul logo Pulse este inclus în assets/pulse-logo.png și folosit implicit. Logo-urile personalizate sunt păstrate.
Folderul tehnic ro-socialbed, blocurile, shortcode-urile și opțiunile existente sunt păstrate pentru actualizare directă.

== Noul aspect (1.4.1) ==
Pictograma furnizată Pulse-256.png este inclusă nemodificată în assets/pulse-icon-256.png. Paletă: bleumarin #062542 și lila #c6aeff. Aspectul editorului și dialogului folosește noua paletă.
La prima vizită a administratorului după actualizare, pictograma nouă devine logo implicit; selecția anterioară este păstrată în opțiunea ro_pulse_logo_before_141. Ulterior poți alege din nou un logo personalizat.

== Interfață WordPress (1.5.0) ==
Intrare proprie Pulse în meniul WordPress, cu pictograma furnizată. Antet cu descriere și atribuire, taburi Prezentare/Afișare/Identitate, carduri cu datele salvate și formular unic de salvare. Paletă bleumarin/lila inspirată de interfața Email.
Taburile se pot schimba din tastatură cu săgețile, Home și End. Valorile nu sunt șterse la schimbarea tabului. Mesajul Modificări nesalvate apare când editezi câmpuri sau alegi un logo. Fără JavaScript, toate secțiunile rămân accesibile în aceeași pagină.
Pagina veche options-general.php?page=ro-socialbed rămâne accesibilă pentru compatibilitate. Pagina principală nouă este admin.php?page=pulse. Salvarea folosește aceleași opțiuni, verificări și nonce WordPress. Stilurile noi sunt limitate la paginile de administrare Pulse.

== Curățare linkuri RO-Health (1.5.1) ==
Linkurile suplimentare Deschide pe X și echivalentele celorlalte platforme sunt generate de RO-Health în elemente health-social-source. Pulse le ascunde imediat prin CSS și le elimină din DOM după încărcare. Curățarea vizează numai elementele suplimentare identificate ale temei și vechile legende de sursă Pulse, nu ancorele structurale ale embed-urilor sau linkurile din iframe-uri.

== Changelog ==
= 1.6.0 =
Elimină secțiunea Postări alese, inclusiv interfața, codul și vechile setări ale pluginului. Integrările din articole sunt păstrate.
= 1.5.2 =
Pictograma Pulse din meniul WordPress are 20x20px pe toate ecranele de administrare.

= 1.5.1 =
Elimină și linkurile Deschide pe... generate de secțiunea socială a temei RO-Health. Păstrează UI-ul Pulse 1.5.0 și integrările native.

= 1.5.0 =
Interfață nouă de administrare cu meniu, taburi, carduri, antet și formular responsive, păstrând toate funcțiile, setările și integrările existente.

= 1.4.1 =
Pictograma nouă și paleta bleumarin/lila în interfața Pulse. 

= 1.4.0 =
Branding Pulse, descrierea și atribuirea cerute, logo inclus cu inscripție Pulse. Păstrează eliminarea butoanelor de sursă și datele existente.

= 1.3.1 =
Elimină toate butoanele proprii de sursă și casetele/linkurile de rezervă. Ascunde integrările fără conținut în locul generării unui link. Elimină setarea de afișare a sursei.

= 1.2.1 =
Corecții Instagram și LinkedIn pentru linkurile furnizate de utilizator. SDK Instagram inițializat explicit, inclusiv în Gutenberg. Suport pentru linkuri YouTube Community Posts sub formă de casete către original.

= 1.2.0 =
Adaugă Substack, YouTube, TikTok, Instagram, Pinterest, LinkedIn, Mastodon și Telegram. Păstrează Bluesky, X, Facebook, Reddit, Threads, logo-ul și setările existente. Maximum 20 de linkuri. Fără liste de domenii. Interfață exterioară discretă, fără linkuri suplimentare sub postări implicit.

= 1.1.2 =
Aplică logo-ul Pulse inclus: fișier cu nume nou și URL versionat pentru cache. Revine o singură dată la noul logo implicit; selecția anterioară este păstrată într-o opțiune de backup, iar fișierul din Biblioteca Media nu este șters. Personalizările ulterioare rămân disponibile.
= 1.1.1 =
Logo în butonul Pulse din post composer și în pictograma blocului Gutenberg (inserter și toolbar). Respectă logo-ul selectat în setări.
= 1.1.0 =
Branding Pulse cu noul logo furnizat. Setări pentru lățime (320–800px), aliniere, spațiere (0–100px), înălțime Facebook (300–1600px), tema X și link sursă opțional (ascuns implicit). Elimină textele Vezi postarea pe. Acceptă shortcode-ul ro_pulse și păstrează ro_socialbed.
= 1.0.3 =
Setări > Pulse: logo din Biblioteca Media, previzualizare, salvare și revenire la logo-ul implicit. Logo-ul se aplică în Gutenberg și în dialogul editorului clasic.
= 1.0.2 =
X: randare explicită după ID prin createTweet, încărcare sincronizată a scriptului și preview în Gutenberg după aplicarea linkurilor. Elimină linkul suplimentar de sub postările Threads.
= 1.0.1 =
Corectează alegerea editorului pentru butonul Inserează postările; sincronizează TinyMCE și afișează erorile de inserare.
= 1.0.0 =
Prima versiune: Gutenberg, editor clasic, maximum 20 de linkuri, deduplicare și aliniere centrală.
