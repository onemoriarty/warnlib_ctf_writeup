# warnlib_ctf_writeup
sa beyler https://discord.gg/QRppCpjvZc sunucu içi duzenlediğimiz warnlib adlı ctf'in çözümünü anlattım, iyi okumalar.


**open_basedir Nedir?**
open_basedir, PHP'de bir güvenlik önlemidir. Web uygulamasının erişebileceği dosya ve dizinleri sınırlar. Bu sayede zararlı kodların sistemin kritik bölümlerine erişimi engellenir.
**file_exists()**
PHP'nin dahili (built-in) fonksiyonudur.
Parametre olarak aldığı dosya yolunun fiziksel olarak sunucuda var olup olmadığını kontrol eder.
Boolean (true/false) döner.
true dönerse dosya mevcut, false ise dosya yok demektir.
**include**
PHP'de başka bir dosyanın içeriğini çalışmakta olan PHP dosyasına dahil etmek için kullanılır.
Dahil edilen dosyadaki kodlar sanki mevcut dosyanın içinde yazılmış gibi çalışır.
Eğer dosya bulunamazsa, include uyarı (warning) verir, ama script çalışmaya devam eder.
**$file_to_include**
Değişkendir. Dahil edilmek istenen dosyanın yolunu tutar (string).
Mutlaka geçerli bir dosya yolunu içeriyor olmalı.

**allow_url_include Nedir?**
allow_url_include, PHP'nin yapılandırma (php.ini) dosyasında bulunan bir direktiftir ve PHP betiklerinin uzak URL’lerden dosya dahil etmesine (include/require) izin verip vermeyeceğini kontrol eder.

**Session File Inclusion (SFI) nedir**
Local File Inclusion (LFI) zafiyeti üzerinden sunucudaki PHP session dosyasının include edilmesiyle gerçekleşir. Oturum dosyaları PHP kodu içerebilir ve include edildiğinde bu kod çalıştırılır. Saldırgan, oturum dosyasına zararlı kod yerleştirip, LFI sayesinde bu dosyayı çalıştırarak sistemde komut çalıştırabilir. Böylece uzaktan kod yürütme (RCE) ve sistem ele geçirme riski ortaya çıkar.


**Wrapper nedir?**
PHP wrapper, farklı veri kaynaklarına (dosya sistemi, URL, bellek vb.) erişimi soyutlayan ve bunları dosya gibi kullanmayı sağlayan bir protokoldür. Böylece PHP fonksiyonlarıyla standart dosya okuma/yazma işlemleri dışında da veri akışları yönetilebilir.

- **Örnek 10 yaygın PHP wrapper:**

1. `file://` — Yerel dosya sistemi işlemleri için.
2. `http://` / `https://` — Uzak sunuculardan veri çekmek için.
3. `php://input` — Ham HTTP POST verisini okumak için.
4. `php://output` — PHP çıktısını yazmak için.
5. `php://filter` — Dosya içeriğine filtre uygulamak için (örneğin base64 encode).
6. `data://` — Inline veriyi base64 veya plain text olarak tutmak için.
7. `zip://` — ZIP arşivleri içindeki dosyalara erişim için.
8. `phar://` — Phar paketlerindeki dosyalara erişim için.
9. `expect://` — Komut satırı komutları çalıştırmak için (güvenlik riski yüksek).
10. `glob://` — Dosya sisteminde pattern ile dosya aramak için.


/index.php girdiğimizde bizleri güzel bir anasayfa tasarımı bekliyor
<img width="1920" height="886" alt="image" src="https://github.com/user-attachments/assets/1fbde13a-81fd-4af3-b270-31ccb625cb5a" />

fakaat sadece /index.php içerisinde temel client side işlemleri dışında başka işlemlerde var gibi?

<img width="577" height="90" alt="image" src="https://github.com/user-attachments/assets/686d75d3-ced7-4040-980d-2888110db854" />
<img width="321" height="156" alt="image" src="https://github.com/user-attachments/assets/fb94f9eb-8be0-475c-a778-d09a4043928c" />

insan bunları görünce "acaba giriş yapıncamı çıkıcak açık?", "sekmeler arasında dolaşırken kullanıcıdan veri alınıyormu acaba?", "login veya register üzerinde acaba zaafiyet olabilirmi?" tarzında birsürü soru oluşuyor, hepsine bakıcaz

ha bide unutmadan aşağıdaki görseldeki kitap detaylarınıda inceleyebiliriz, bence oralardada bişeyler olabilir :>
<img width="1851" height="313" alt="image" src="https://github.com/user-attachments/assets/00932a34-c979-4250-99b3-b9bf4161a6c1" />


o zaman ilk kayıt olup neler yapabiliyoruz, nasıl bir ortam var, backend nasıl işliyor incelemeye baslıyalım

POST /index.php?belge=register HTTP/1.1
Host: localhost
Cookie: user_id=20; PHPSESSID=1b7a9de3a8afbc448e0b7cf92e912018; __test=b32de2271a94977fda75b7d76067e791
Content-Length: 66
Cache-Control: max-age=0
Sec-Ch-Ua: "Chromium";v="133", "Not(A:Brand";v="99"
Sec-Ch-Ua-Mobile: ?0
Sec-Ch-Ua-Platform: "Windows"
Accept-Language: tr
Origin: http://localhost
Content-Type: application/x-www-form-urlencoded
Upgrade-Insecure-Requests: 1
User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36
Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7
Sec-Fetch-Site: same-origin
Sec-Fetch-Mode: navigate
Sec-Fetch-User: ?1
Sec-Fetch-Dest: document
Referer: https://localhost/index.php?belge=register
Accept-Encoding: gzip, deflate, br
Priority: u=0, i
Connection: keep-alive

username=%3Cimg+src%3Dx+onerror%3Dalert%2831%29%3E&password=kekeme

username input'una html injection payloadı yazıp kayıt oldum, belki bir yerde kullanıcı adını sanitize etmeden ekrana basıyordur ? her olasılıgı düşünmek gerekir değilmi. bu varsayımımız eğerki dogruysa bunun xss seviyesine yukselmeye deniyebiliriz ve bu sayede stored xss elde etmiş olabilme ihtimalimiz var, neyse dahada incelere inmiyim konuda sapmayalım.

giriş yaptıgımızda profil ekranında bizi boyle biyer karsılıyor 
<img width="1909" height="842" alt="image" src="https://github.com/user-attachments/assets/53d56fe0-931e-4bc3-94bc-d3819a0cf47b" />
burda username basılırken sanitize ediliyor demekki pekala

anasayfada sekmeler arasında dolaşalım birazcık belki bişeyler dönüyordur orda

<img width="1894" height="358" alt="image" src="https://github.com/user-attachments/assets/e2f895a5-6759-423f-9316-54b92216641e" />

burayı görünce insanın aklına su geliyor "demeqqi developer buralarda, her sekme arasında dolaşırken include veyahut require_once tarzı bir fonksiyon kullanmış" güzel bilgi!

include veya require_once veya o tarzda bir "başka bir belgeyi içe katma" fonksiyonu oldugunu ise <img width="278" height="62" alt="image" src="https://github.com/user-attachments/assets/ec14192f-a7a3-4564-a443-253dfbaf7505" />
resimdeki index.php belge adından cıkardım, yani sistem php yazılım dilini kullanıyor yani backend php ile yazılmış diyerek buralarda include kullanıcagını varsaydım. 

yani <img width="324" height="71" alt="image" src="https://github.com/user-attachments/assets/4146e852-50a1-4a24-97ec-a9fab557ec8c" />
burda resimde ?belge= GET metodu ile alınan veriyi (belge adını) arkada include eden bir yapı var.
örnek:

```
<?php
$belge = $_GET['belge'];
$file_to_include = $belge . '.php';

if (file_exists($file_to_include)) {
    include $file_to_include;
} else {
    echo '<div class="bg-red-500 p-4 rounded-lg text-white font-bold">Aradığınız sayfa bulunamadı.</div>';
}
?>
```


otomatik sonuna .php ekliyor yani ?belge=index yazılırsa arkada index.php olcak ve index.php include edicek ama zaten index.php oldugun için sonsuz döngü olusucaktır burda.


pekii bir hacker kafa yapısıyla düşünerek burda neler yapabiliriz diye bi bakalımmı?

ha birde unutmadan ctf yöneticisi şunu atmıştı duyuruya <img width="570" height="289" alt="image" src="https://github.com/user-attachments/assets/21a38610-9f54-4ac3-8075-8838100f65bf" />
yani buraya bakarak derizki herşey htdocs altında yani ~/home/log.php var ~/home/keklik.php elimizde var dümdüz upload edilen dizinler arasında saklanmış. heralde burasıda ctf ownerının şerefsizliği gibi düşünerebilirsiniz ama mori öyle şeyler yapmaz, infinityfree izin vermediği için yapmamıştır:) evet yani bu yapıda gidersek çoğu yapıyı yavaştan çözebiliriz!

<img width="1907" height="838" alt="image" src="https://github.com/user-attachments/assets/df0cc564-5e0c-4e06-acc4-1e3ad41454ab" />
dizinler arası dolaşım yaparken dediğim gibi ?belge parametresiyle include aracılıgı ile yapılıyor işlemler! buna dayanarak baska üst dizinlere bakalım?

<img width="1919" height="655" alt="image" src="https://github.com/user-attachments/assets/f9b5314c-9d95-44e4-b6b0-e5b9ad1a90ff" />
bada bim bada buum al sana lfi :D

bu arada "Warning: file_exists(): open_basedir restriction in effect. File(../.././etc/passwd.php) is not within the allowed path(s): (/php_sessions:/tmp:..:/var/www/errors:/home/vol19_2/infinityfree.com/if0_39297276/htdocs) in /home/vol19_2/infinityfree.com/if0_39297276/htdocs/index.php on line 55" hatasıda bir açıktır 1-information disclosure yani bilgi sızdırılması 2-path disclosure yani yol sızdırılması.
ve bu hatada şunu diyor:index.php'nin 55.satırında "  if (file_exists($file_to_include)) {
            include $file_to_include;
        } else {
            echo '<div class="bg-red-500 p-4 rounded-lg text-white font-bold">Aradığınız sayfa bulunamadı.</div>';
        }" Burada file_exists() fonksiyonu izin verilmeyen bir dizine erişmeye çalışıyor ve PHP open_basedir kısıtlaması nedeniyle hata fırlatıyor.

 yemişim hatasını ben ilk flagı cebe attım hacı :D aslında 2.flag neyse bu konuya girmicem..
 **"FLAG{LFI_Basarili_Giris}"** devamkee
 
o zaman sırayla başlayalım ve home/keklik.php check edelim bakalım neler varmış? 

https://yzzz.rf.gd/index.php?belge=home/keklik yazıp girdim ben ama dizin sistemde gömülü olmadıgı için sonradan upload edildiği yani sandbox ortam oldugu için sizler dümdüz
https://yzzz.rf.gd/home/keklik.php üzerindende girebilirsiniz


ve karşımıza <img width="476" height="213" alt="image" src="https://github.com/user-attachments/assets/091abd27-4754-4ded-a002-346123375d69" /> böyl bi yazı çıkıyor

burdaki "ZXRjL3Bhc3N3ZApldGMvc2hhZG93" random atılmış gibi gözüken string verisi aslında base64 ile encode edilmiş bir veri.
***
Base64 kodlaması sadece şu karakterleri kullanır:
Büyük harfler: A-Z
Küçük harfler: a-z
Rakamlar: 0-9
+ ve / işaretleri
Sonunda bazen = karakteri (padding için)

Uzunluk ve Yapı:
Base64 kodlamaları genellikle 4'ün katları şeklinde uzunlukta olur. Burada uzunluk 24 karakter (tam 4'ün katı).
Bu da Base64 standardına uyuyor.
***

sistemimde bulunan default base64 aracı ile -d parametresi ile decode işlemi yaptım
"echo "ZXRjL3Bhc3N3ZApldGMvc2hhZG93" | base64 -d"
ve cıktı:
etc/passwd
etc/shadow      

şeklindeydi. 
bunu görünce "Vay BEE!!" dediğimi düşünüyorsanız düşünmeyin olm yapan benim zaten niye sasırayım:D

herneyse bu yeni aldıgımız bilgilerden yola cıkıp etc/passwd ve etc/shadow inceleyek bakalım ne varmıss

vee <img width="1916" height="374" alt="image" src="https://github.com/user-attachments/assets/0f86e635-4e31-41c9-bc8f-e39a05a1a2f8" />

yine geldi base64 ile encode edilmiş string verimiz
echo "cm9vdDp4OjA6MDpyb290Oi9yb290Oi9iaW4vYmFzaApkYWVtb246eDoxOjE6ZGFlbW9uOi91c3Ivc2JpbjovdXNyL3NiaW4vbm9sb2dpbgpiaW46eDoyOjI6YmluOi9iaW46L3Vzci9zYmluL25vbG9naW4KbW9yaWFydHk6eDoxMDAwOjEwMDA6TW9yaWFydHkgVXNlcjovaG9tZS9tb3JpYXJ0eTovYmluL2Jhc2gKY3RmdXNlcjp4OjEwMDE6MTAwMTpDVEYgVGVzdCBVc2VyOi9ob21lL2N0ZnVzZXI6L2Jpbi9iYXNoCmJhY2t1cDp4OjEwMDI6MTAwMjpCYWNrdXAgU2VydmljZTovdmFyL2JhY2t1cHM6L3Vzci9zYmluL25vbG9naW4Kc2hhZG93IGJhYnk=" | base64 -d yazıp decode ettim ve


root:x:0:0:root:/root:/bin/bash
daemon:x:1:1:daemon:/usr/sbin:/usr/sbin/nologin
bin:x:2:2:bin:/bin:/usr/sbin/nologin
moriarty:x:1000:1000:Moriarty User:/home/moriarty:/bin/bash
ctfuser:x:1001:1001:CTF Test User:/home/ctfuser:/bin/bash
backup:x:1002:1002:Backup Service:/var/backups:/usr/sbin/nologin
shadow baby

çıktı, herşey harikaydı ama
<img width="216" height="53" alt="image" src="https://github.com/user-attachments/assets/9c996761-d116-47a4-93e3-24a51c55c45c" />
ne alakaa :D heralde etc/shadow bak diyor,peki bakalım

gene bir encode edilmiş  veri
cm9vdDokNiRDMG1QbDN4SDRzaCRoRmp6WlVNWlQzRzVLNGUwVC5uSXVCRlhPcHZLcmNtTTlMWndUWHgwRW9GMWtHNGszWnNYV2MuVktwek5RdlpmQnlVNEw0T2VKb0FxWnA3RlBUOEpwLjoyMDAwMDowOjk5OTk5Ojc6OjoKZGFlbW9uOio6MjAwMDA6MDo5OTk5OTo3Ojo6CmJpbjoqOjIwMDAwOjA6OTk5OTk6Nzo6Ogptb3JpYXJ0eTokNiRGYWtlSGFzaFZhbHVlSGVyZSRrOW1YbzJGOTNkOEc1VDZONFZZYjJmMktmVDZ4R1ExVXFmUjFjU2Qzb1F3MmNCdk9heGZRempackVzUDNrLjoyMDAwMTowOjkwOjc6OjoKY3RmdXNlcjokNiRBbm90aGVyRmFrZUhhc2gkelgxYk0zeFVqWTVFNkE0WHZKY1drTzNMcFZ6TW5CMXEyZ0ZwNHE2aFRkNnNMMGhQOWVVeVM0cFNuWHdNMnJWcVp4WXkuOjIwMDAxOjA6OTA6Nzo6OgpiYWNrdXA6IToyMDAwMjowOjk5OTk5Ojc6OjoKYnVudSBpbGVyZGUgc2lsaWNlbSwgc2FrxLFuIHVudXRtYSBtb3JpLiAKOiBsb2cucGhwP3VzZXI9PHVzZXJuYW1lPg==

decode edelim

root:$6$C0mPl3xH4sh$hFjzZUMZT3G5K4e0T.nIuBFXOpvKrcmM9LZwTXx0EoF1kG4k3ZsXWc.VKpzNQvZfByU4L4OeJoAqZp7FPT8Jp.:20000:0:99999:7:::
daemon:*:20000:0:99999:7:::
bin:*:20000:0:99999:7:::
moriarty:$6$FakeHashValueHere$k9mXo2F93d8G5T6N4VYb2f2KfT6xGQ1UqfR1cSd3oQw2cBvOaxfQzjZrEsP3k.:20001:0:90:7:::
ctfuser:$6$AnotherFakeHash$zX1bM3xUjY5E6A4XvJcWkO3LpVzMnB1q2gFp4q6hTd6sL0hP9eUyS4pSnXwM2rVqZxYy.:20001:0:90:7:::
backup:!:20002:0:99999:7:::
bunu ilerde silicem, sakın unutma mori.
: log.php?user=<username>    

ve bada bum!

"bunu ilerde silicem, sakın unutma mori.
: log.php?user=<username>    
"

şizofren amk 😃  log.php?user= vay bee yanii burada user filtrelemeli bir işlem dönüyor artık log.php içinde ne varsa merak uyandırıcı
bi log.php inceleyelim o zaman
<img width="1061" height="427" alt="image" src="https://github.com/user-attachments/assets/6ec64397-11a2-43a8-bf59-7ca859faee89" />
<img width="691" height="232" alt="image" src="https://github.com/user-attachments/assets/8e332ffa-d903-4c89-a6d9-ac51fbe4b482" />


# HTTP ve HTTP Status Kodları - Kısa ve Öz

## HTTP Nedir?
HTTP (Hypertext Transfer Protocol), web tarayıcıları ile sunucular arasında veri alışverişini sağlayan protokoldür.  
İstemci (browser) istekte bulunur, sunucu yanıt verir.

## HTTP Status Code Nedir?
HTTP status kodları, sunucunun isteğe verdiği cevabın durumunu üç haneli sayılarla belirtir.  
Bu kodlar, işlemin başarılı mı, hata mı var mı, yetki sorunu mu yaşanıyor gibi bilgiyi taşır.

---

## Status Kodlarının Kategorileri
```
| Kategori | Kod Aralığı | Anlamı               |
|----------|-------------|----------------------|
| 1xx      | 100-199     | Bilgilendirme        |
| 2xx      | 200-299     | Başarı               |
| 3xx      | 300-399     | Yönlendirme          |
| 4xx      | 400-499     | İstemci Hatası       |
| 5xx      | 500-599     | Sunucu Hatası        |
```
---
```
## Önemli HTTP Status Kodları

| Kod  | Anlamı               | Kısa Açıklama                           |
|-------|----------------------|----------------------------------------|
| 200   | OK                   | İstek başarılı                         |
| 201   | Created              | Yeni kaynak oluşturuldu                |
| 301   | Moved Permanently    | Kalıcı olarak başka URL’ye yönlendir  |
| 302   | Found                | Geçici yönlendirme                     |
| 304   | Not Modified         | İçerik değişmedi, önbellekten kullan  |
| 400   | Bad Request          | Geçersiz istek                        |
| 401   | Unauthorized         | Giriş yapılmalı                       |
| 403   | Forbidden            | Yetkin yok, erişim engellendi         |
| 404   | Not Found            | Kaynak bulunamadı                     |
| 429   | Too Many Requests    | Çok fazla istek, istek sınırı aşıldı  |
| 500   | Internal Server Error| Sunucu hatası                         |
| 503   | Service Unavailable  | Sunucu hizmet veremiyor               |
-------------------------------------------------------------------------------
```

uh :d büyük hayal kırıklıgı, saka saka bu bize işlermi:D developer buradaki kontrolü/dogrulamayı nasıl yapmıs onu tespit edersek aşabiliriz mekanizmayı.

düşündüm ve acaba
```
if ($_SERVER['REQUEST_URI'] === 'home/log.php') {
    http_response_code(403);
    die('Bu sayfaya erişemezsiniz.');
}
```
not:$_SERVER['REQUEST_URI'], kullanıcının talep ettiği URL’in alan adından (domain) sonraki kısmını, yani yol ve varsa query string’i döner.
```
URL: https://example.com/home/log.php?user=admin
REQUEST_URI: /home/log.php?user=admin
```
kontrol yapısını dizin bazlımı yapmıştır diye düşündüm ve hemen test ettim
<img width="372" height="57" alt="image" src="https://github.com/user-attachments/assets/f62b17d0-3a83-4cb5-bb40-225b0281c751" />

ve yanıt olarak
<img width="765" height="507" alt="image" src="https://github.com/user-attachments/assets/47725e68-80f1-4525-bb76-17bcb54e2603" /> yanıtını aldım:/

başka türlü şansımı deniyom bide
yapı acaba
```
$remote_addr       = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
$x_forwarded_for   = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $remote_addr;

// Sadece 127.0.0.1 erişimine izin ver
if ($x_forwarded_for !== '127.0.0.1') {
    http_response_code(403);
    die('<div class="bg-red-500 p-4 rounded-lg text-white font-bold">Bu alana erişiminiz yok.</div>');
}
```
not:X-Forwarded-For, proxy veya CDN arkasındaki istemcinin gerçek IP adresini iletmek için kullanılan HTTP header’ıdır.
İstemci tarafından kolayca sahte olarak gönderilebilir, bu yüzden güvenlik kontrollerinde tek başına kullanılmamalıdır.


(kodun türkçesi:sunucuya uzaktan atılan isteğin adresini (ip) yakala ve $remote_addr koy ama eğer yoksa uzaktaki gelen isteğin ipsi 0.0.0.0 değerini ata. sonra x forwarded for header baslıgı ile gelen değeri yakala tut ama eğer yoksa burda değer remote_addr ile yakalayıp koy sonrasındada kontrol yap ; eğer uzaktan gelen istek 127.0.0.1 ip adresi yani localhost ipsine eşit değilse 403 döndür kullanıcıda ve ekrana şunu döndür "Bu alana erişiminiz yok")

yani acaba developer log belgesine sadece localhosttan girilecek şekilde ayarlamak için boyle bir ayarmı yaptı diye dusundum ve bende bunu baz alarak şu isteği attım

<img width="1282" height="349" alt="image" src="https://github.com/user-attachments/assets/5c82af92-7398-42b3-a319-225f0499978e" />

veee bada bim bada bum

<img width="1349" height="395" alt="image" src="https://github.com/user-attachments/assets/656edd10-9208-46ec-9b9f-15e6108ac277" />

ve işte diğer flag'ıda bulduk huhu
**FLAG{Log_Belgesine_Erisim}**

ek olarak yeni bilgi öğrendik

```
HTTP/1.1 200 OK
Server: openresty
Date: Sat, 09 Aug 2025 15:13:33 GMT
Content-Type: text/html; charset=utf-8
Transfer-Encoding: chunked
Connection: keep-alive
Expires: Thu, 19 Nov 1981 08:52:00 GMT
Cache-Control: no-store, no-cache, must-revalidate
Pragma: no-cache

<div class="container mx-auto mt-8 px-4">
        <div class="bg-green-500 p-4 rounded-lg text-white font-bold text-center">
            Tebrikler! Erişimi sağladınız. İşte üçüncü bayrak: FLAG{Log_Belgesine_Erisim}
        </div>
      </div>
<h2 class="text-3xl font-bold text-yellow-400 mb-4">Giriş Logları</h2>
<p class="text-gray-400 mb-8">Bu sayfa adminler için kısıtlamasız, normal kullanıcılar için belirli koşullarda erişilebilir.</p>

<div class="bg-gray-800 p-4 rounded-lg mt-4" style="white-space: pre-wrap; word-wrap: break-word;">
    <h3 class='text-xl font-bold text-gray-300'>Kullanıcı '&lt;img src=x onerror=alert(31)&gt;' için Loglar</h3><p class='text-gray-400'>Hiç log bulunamadı.</p></div>
```
not 1: Sanitizasyon, zararlı veya istenmeyen verilerin temizlenip güvenli hale getirilmesi işlemidir.
not 2: htmlspecialchars, PHP'de kullanılan bir fonksiyondur ve HTML özel karakterlerini (örn. <, >, &, ", ') güvenli HTML entity'lerine çevirerek, XSS (Cross-Site Scripting) saldırılarını önlemeye yardımcı olmaktadır

burdaki h3 tagı içerisinde kullanıcı adı basılırken farketmişsinizdir:
<img width="921" height="50" alt="image" src="https://github.com/user-attachments/assets/10cc0e1a-b36d-4956-8c57-e9c7e2cb772d" />
kullanıcı adını database yani veritabanından çekiyor ve burada ekrana basıyor fakat sanitize yani gelen veriyi temizleyip güvenli hale getirip bastırıyor, backend php ile yazılmıştı yani muhtemelen htmlspecialchars kullanıyor olabilir arka planda developer arkadas
```
            echo "<h3 class='text-xl font-bold text-gray-300'>Kullanıcı '" . htmlspecialchars($filter_username) . "' için Loglar</h3>";
```

harika güzel gidiyoruz, developer dostumuz kullanıcı adını temizleyerek basıyor fakatt belki logları basmıyordur??
- log poisoning nedir:Log poisoning, saldırganın kötü amaçlı kod veya veri enjekte ederek sunucu log dosyalarını zehirlemesidir; bu kötü içerik daha sonra uygulama tarafından çalıştırılırsa sistemde uzaktan kod çalıştırma (RCE) açığı oluşabilir. Örneğin, HTTP isteğinde <?php system($_GET['cmd']); ?> kodu gönderilip log dosyasına kaydedilirse ve bu dosya include edilirse, saldırgan cmd parametresiyle sistem komutları çalıştırabilir.
- log poisoning üzerinden ilerleyerek türlüce geniş alt baslıklarıyla işlemler yapılabilinir burada. eğerki herangi bir istemciden alınan ve sanitize edilmeden basılan veri varsa. rce bile :>>>>
-------------------------------------------------------------------
| Amaç                       | Payload Örneği                        | Sonuç                                                       |
| -------------------------- | ------------------------------------- | ----------------------------------------------------------- |
| Stored XSS                 | `<script>alert('XSS')</script>`       | Admin panelinde JS çalışır, oturum çalınabilir              |
| HTML Attribute Injection   | `" onmouseover="alert('XSS')`         | Element attribute içine kod enjekte edilir                  |
| JavaScript URI Injection   | `javascript:alert('XSS')`             | URL veya event attribute içinde kod çalıştırılabilir        |
| Log Poisoning              | `\n[CRITICAL] Malicious log injected` | Log dosyasında kayıt formatı bozulur                        |
| Command Injection (teorik) | `; rm -rf / #`                        | Sistem komutu çalıştırılırsa tehlike oluşabilir (çok nadir) |
---------------------------------------------------------------------

# denemeden bilemeyiz
burası aklımızın bir köşesinde kalsın, loglara basarak tetikliceğimiz olasılıkları aklımızın bi yanına kazıyalım (log poisoning)

- pekala o zaman birazda kitap detaylarını inceleyek:d

normal bir yorumun isteği şu şekilde gönderiliyor:
```
POST /index.php?belge=kitap_detay&id=3 HTTP/1.1
Host: yzzz.rf.gd
Cookie: user_id=20; __test=b32de2271a94977fda75b7d76067e791; PHPSESSID=a98b5f07128c159aec81313afa26ea0d
Content-Length: 20
Cache-Control: max-age=0
Sec-Ch-Ua: "Chromium";v="133", "Not(A:Brand";v="99"
Sec-Ch-Ua-Mobile: ?0
Sec-Ch-Ua-Platform: "Windows"
Accept-Language: tr
Origin: https://yzzz.rf.gd
Content-Type: application/x-www-form-urlencoded
Upgrade-Insecure-Requests: 1
User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36
Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7
Sec-Fetch-Site: same-origin
Sec-Fetch-Mode: navigate
Sec-Fetch-User: ?1
Sec-Fetch-Dest: document
Referer: https://yzzz.rf.gd/index.php?belge=kitap_detay&id=3
Accept-Encoding: gzip, deflate, br
Priority: u=0, i
Connection: keep-alive

comment=mori+babapro
```

buraya bakarak olası saldırı metodolojisi cıkarsaydım sunları derdim
1-post metoduyla index.php dizinine atılan istekteki parametreler leziz duruyor 
1.1 belge=kitap_detay üzerinde zaten lfi bulmuştuk ek olarak burada deneyebilceğimiz olasılık php wrapperlar var onlar üzerinde lfi korunaklı olsaydı eğer aşmayı falan deneyebilirdik.
1.2 gene belge parametresinden rce almayı deneyebiliriz "allow_url_include"  direktifi eğerki acıksa bunun üzerinde remote file inclusion sayesinde uzaktaki bir webshell dahil ederek sistemin amına goyardık :d 
1.3 tekrar belge param'ı üstünden lfi yapılarak log poisoning sayesinde (log.php belgesinde) rce, xss, sfi tarzı yöntemler ile baya iyi yerlere adım atabilirik.
1.4 id param'ı üzerinden ise olası yapılabilincek şeyler: buranın db ile etkileşimi var yani eğerki db ile etkileşim hatalı yapılandırılmıssa SQL enjeksiyonu cıkar, veya type juggling oluşadabilir veriyi alma tarzına baglı kalarak, veya  idden alınan anahatar degeri ekrana basıyorsa sanitize etmeden aynı sekilde reflected xss oluşur ve diğer temel html injection vs. (yazmaktan yoruldum kısa kesiom)

2 HTTP Header’ların Loglanması ve Buradan Türeyen Saldırılar
2.1-user agent, host falan fistan loglanıyorsa rceye ve xss'e yol alınabilinir veya yapıya göre değişir işte 
ve bunun türevleri, araştırın olm yoruldum.

3.Cookie Flags ve Güvenlik Politikaları
3.1 HttpOnly, Secure, SameSite gibi cookie flag’ler yoksa, XSS veya CSRF saldırılarında cookie çalınabilir veya çalınan cookie ile oturum ele geçirilebilir.
PHPSESSID cookie’sinin HttpOnly ve Secure flag’lerine sahip olup olmadığı kontrol edilmeli.
3.2 oturum ele geçirme :Oturum ele geçirme, saldırganın hedef kullanıcının oturum kimliğini (PHPSESSID gibi) ele geçirerek, kimlik doğrulaması yapılmış kullanıcı haklarına sahip olmasıdır. Bu, oturum sabitleme (session fixation), oturum kimliği tahmini veya açıkta kalan oturum cookie’lerinin ağ üzerinden ele geçirilmesi (ör. HTTP trafiğinin şifrelenmemesi durumunda) gibi tekniklerle yapılabilir. Ayrıca, eksik HttpOnly ve Secure cookie flag’leri saldırganın tarayıcı tabanlı XSS ile oturum çalmasını kolaylaştırır.

4.Kullanıcı Girdilerinin İşlenmesi ve Güvenlik Açıkları
4.1 comment içinden alınan veri al,ısır,ye,emcükle,böl,parçala,yakala,mıncıkla,kaydet,bastır mekanizmasıyla calısıyorsa eğerki ayvasyı yedik:D demek istediğim veriyi alıp işleyip bir filtrelemeden geçirip sonrasında database'e kaydetme işlemiydi. filtreleme yoksa çok harika olurdu ve xss yedirebilirdik mesela stored gibisiiinden, sqli olabilir bi db kullanılıyor çünkü veya dos-ddos salıdırıs yapabilinir milyonlarca yorum atan kod yazarak sunucu diskini doldururuz, işlem hacmini ham yabarık. Cross-Site Request Forgery (CSRF) sayesinde milledi düdükleyerek kandırarak uzaktan fake formlar ile işlemler yaptırabiliriz sahte yorumlar attırabiliriz (piçlik:<)

ve nicesi işte aq yoruldum.


biranda aklıma melekler şunu getirdi : yorum atarken user agent üzerinde xss payloadı enjekte et ve sonuca bak ?? 
deneyelim bakalım:d  


<img width="383" height="600" alt="image" src="https://github.com/user-attachments/assets/abc82f3b-a9be-437b-a0d2-5816900ceb6f" />
user agenta önceden hazırladıgım exploit server linkimi koydum ve adam loglara baktıgı an tetiklencek bicimde suan


admin arkadası "abi koş loglarda benim username çıkmıyor" diyip kandırdık ve loglara bakmaya gitti
<img width="815" height="571" alt="image" src="https://github.com/user-attachments/assets/579447b7-110b-4dd7-b634-15328c56fa84" />

exploit sunucumuza bahalım gelmişmi cookiemiz
<img width="435" height="101" alt="image" src="https://github.com/user-attachments/assets/3592a6e1-3b46-4fa1-a64a-a0a602e040c3" />


en son olarakta admine elde ettiğimiz session ile girelim bakalım

<img width="426" height="32" alt="image" src="https://github.com/user-attachments/assets/4e212520-b877-4184-8f6a-08904211e81e" />


veee 
<img width="1187" height="943" alt="image" src="https://github.com/user-attachments/assets/675717f8-1e5e-4953-8a60-ed7944990ea4" />

la olm şaka maka ben bile cozunce mutlu oldum bu neymiş arkadas :D en zor kısmı moriyi kandırmaktı.



iyi günler dilerim, kodları incelemek isteyenler /ctf altına baksın.
