# hddlinks_android
<h2>NOU: Pentru versiunea PC instalati ca player mpv</h2>
<H3>Instalare mpv. <a href="https://mpv.io/">Pagina oficiala</a></H3>
Pentru ca nu toate link-urile pot fi vizionate in "clasicul" flash player, solutia e folosirea unui player extern.<BR>
Solutia e o adaptare dupa ideea de <a href="https://github.com/b01o/mpv-url-proto">aici.</a><BR>
1. Descarcati ultima versiune disponibila de <a href="https://sourceforge.net/projects/mpv-player-windows/files/">aici.</a> Selectati 64(v)/32 bit si apoi alegeti cea mai noua versiune (ex. mpv-x86_64-v3-20230820-git-19384e0.7z)<BR>
2. Dezarhivati fisierul. NU puneti arhiva pe vreun folder "special" gen "Desktop", alegeti un folder "normal", de exemplu "C:/mpv".<BR>
3. Copiati "calea" fisierului mpv.exe. (De exemplu "C:/mpv/mpv.exe").<BR>
4. Mergeti in "Setari", completati ("paste") campul pentru "cale mpv.exe", si apasati "memoreaza".<BR>
5. Acum apasati pe "Genereaza reg file".<BR>
6. In folderul de instalare (ex. C:/mpv) o sa gasiti acum fisierul "add_url_protocol_mpv.reg".Click dreapta --> selectati "merge".<BR>
7. In Setari selectati "mod player" --> mpv. Apasati "memoreaza".
<h2>NOU: Server_for_PHP_7.3.3_HD4ALL.apk</h2>
Descarcati <a href="https://www.mediafire.com/file/bcw65ycm3wwp6ad/Server_for_PHP_7.3.3_HD4ALL.apk/file">Server_for_PHP_7.3.3_HD4ALL.apk</a>.

<h2>NOU: INSTALARE ANDROID</h2>
Descarcati <a href="https://www.mediafire.com/file/6mnyumjp0e9xhqj/Server_for_PHP_HD4ALL_nou.apk/file">Server_for_PHP_HD4ALL_nou.apk</a>
Deoarece aplicatia originala nu mai ofera suport, nu mai puteti descarca versiunea 5.6.35 pentru serverul php.
In versiunea facuta de mine aveti pus php 5.6.35 si toate fisierele pentru script.

<h2>Despre...</h2>
Versiunea HDD4ALL (hddlinks) pentru PC/android, optimizata in special pentru media playere cu android.
Aplicatia este de tip web browser, scrisa in PHP.
Prin intermediul aplicatiei puteti viziona filme si seriale online, posturi TV sau emisiuni inregistrate, clipuri video sau continut pentru adult.

<h2>Instalare</h2>
Pentru rularea aplicatiei aveti nevoie de un browser (evident), un server php si un player video (optional daca rulati pe PC).
<h3>Instalare pe PC (windows)</h3>
Pentru server PHP va recomand <b>XAMPP</b>, dar alegeti o versiune cu PHP 5.6.x.
Dupa instalare descarcati <a href="https://www.mediafire.com/file/eqx6vwyq6d2y5ya/hd4all_install.zip/file">hd4all_install.zip</a> in directorul <b>htdocs</b>.
Dezarhivati arhiva. Porniti browserul (serverul PHP trebuie sa fie pornit) si accesati http://localhost/scripts/index.php
In partea de sus a paginii o sa apara ("o noua versiune este disponibila..."). Accesati link-ul iar in cateva secunde se va face actualizarea.
Accesati acum "Setari", si alegeti setarile dorite (mod player = flash sau MPC/VLC, cale Media Player Clasic si VLC daca doriti sa le folositi ca player, tastatura = NU).

<h2>Instalare pe Android</h2>
Descarcati <a href="https://www.mediafire.com/file/hmdf5browqibf2t/Server_for_PHP_HD4ALL.apk/file">Server_for_PHP_HD4ALL.apk</a>
Este aplicatia "Server for PHP" din google play modificata de mine (contine si pachetul de instalare pentru HD4ALL).
Pentru browser, mai ales daca folositi un media player cu android (cu tastatura) va recomand sa instalati <a href="https://www.mediafire.com/file/tyt33k5vqisacil/org.lineageos.jelly.apk/file">Jelly Browser</a>.
(ATENTIE: Instalati aceasta versiune, nu cea din Google Play). Este un browser foarte bun si cel mai important e ca puteti naviga in pagina cu tastele sageti. Atentie, trubuie sa fie instalata o versiune de Android >= 7.1.2
Pentru video player folositi MX Player (pro sau ad).

<li>Pasul 1. Instalare Server for PHP</li>
Instalati Server_for_PHP_HD4ALL. Deschideti aplicatia. Din aplicatie descarcati si instalati PHP 5.6.35 (NU instalati PHP 7.x).
Dupa instalare PHP, setati sa porneasca la bootare si apasati "START SERVER".
<li>Pasul 2. Instalare browser (optional)</li>
Instalati Jelly Browser. Daca folositi un media player adaugati pe ecranul principal browser-ul (apasati pe + si adaugati browserul, e un cerc cu verde).
Deschideti browser-ul si setati ca pagina de start <b>http://127.0.0.1:8080/scripts/index.php</b>
Acum accesati in browser adresa <b>http://127.0.0.1:8080/scripts/index.php</b>
Daca totul e "OK", o sa apara pagina principala a aplicatiei, iar in partea de sus o sa apara "O noua versiune este disponibila".
Accesati acest link iar in cateva secunde se va face actualizarea.
Accesati "Setari" si alegeti: mod player = mediaplayer, tastatura = da (daca aveti mediaplayer), MX Player = pro sau ad.
Daca folositi o tableta mai antica puteti seta la mod player pe "direct".

<li>Pasul 3. Instalare si setare MX Player</li>
Dupa instalare, deschideti MX Player si setati:
La "decodor" bifati modul HW+
La "subtitrare":
La dosarul cu fonturi selectati "/scripts". Daca nu vreti sa folositi fontul implicit va recomand fontul "arialrb.ttf". 
Daca folositi acest font va recomand setarile: 
Marime 43, ingrosat=nu (debifat), contur=da (bifat) marime 100-120%, aliniere verticala pusa pe 0.
Alegeti dosarul cu subtitrari "/scripts/subs" (IMPORTANT!)
Acestea sunt setarile principale, va recomand sa pargurgeti si celelalte setari.

<h1>La final...</h1>
<p>Porniti aplicatia intotdeauna din pagina pricipala.</p>
<p>Puteti forta o actualizare daca accesati in pagina principala <b>HD4ALL</b>.</p>
<p>Cititi cu atentie instructiunile care apar in paginile aplicatiei.</p>

<p>Daca ceva nu merge sau vreti sa adaug si alte site-uri, scrieti un "Issues".</p>
<p>O mica prezentare (cei drept cam veche) puteti vedea aici <a href="https://www.youtube.com/watch?v=n-63D3K00oY">DEMO</a></p>
