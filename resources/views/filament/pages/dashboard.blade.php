<x-filament-panels::page class="fi-dashboard-page">
    @php
        $couponResource = 'App\Filament\Resources\CouponResource';
        $listCoupons = 'App\Filament\Resources\CouponResource\Pages\ListCoupons';

        $checkinPage = 'App\Filament\Pages\Checkin';

        $flightlocationResource = 'App\Filament\Resources\FlightlocationResource';
    @endphp
    <style>
        .textLine {line-height: 20px;}
        /* Extra small devices (phones, 600px and down) */
        @media only screen and (max-width: 600px) {
            .welcomeText {width:100%;}
        }

        /* Small devices (portrait tablets and large phones, 600px and up) */
        @media only screen and (min-width: 600px) {
            .welcomeText {width:100%;}
        }

        /* Medium devices (landscape tablets, 768px and up) */
        @media only screen and (min-width: 768px) {
            .welcomeText {width:80%;}
        } 

        /* Large devices (laptops/desktops, 992px and up) */
        @media only screen and (min-width: 992px) {
            .welcomeText {width:80%;}
        } 

        /* Extra large devices (large laptops and desktops, 1200px and up) */
        @media only screen and (min-width: 1200px) {
            .welcomeText {width:50%;}
        }
        
    </style>
    <p>
        <b>
            Ezen a fülön található információk igen fontosak és mindenképpen olvasd el, ugyanis sokat segít a jövőbeli kellemetlen meglepetések elkerülésében, ha tudod, pontosan mit várhatsz az utas foglaló rendszertől, illetve mire kell figyelned a jelentkezéskor.
        </b>
    </p>
    <p>
        <i>
            Az utasfoglaló rendszer arra való, hogy gördülékennyé tegye a repüléskre való jelentkezés folyamatát számodra, számunkra pedig a jelentkezők hatékony beosztását segítse elő.
        </i>
    </p>
    <p>
        A működése röviden abból áll, hogy a kiírt és általad látható repülések közül be tudod jelölni, ami nektek megfelelő lehet. A jelölések alapján a repülések előtti néhány hétben véglegesítjük a repüléseket és az ezekre beosztottakat erről értesíti a rendszer. Beosztás hiánya esetén a fentebbi folyamat ismétlődik, amég aktív beosztást nem tudunk neked adni. Ezután pedig várjuk közösen a jó időt a repüléshez.
    </p>
    <br>
    <h2 class="fi-header-heading text-xl font-bold tracking-tight text-gray-950 dark:text-white sm:text-2xl">A repülésre jelentkezés folyamata a következő az oldalon:</h2>
    <ul class="flex flex-col gap-4 list-disc">
        <li>Ha esetleg nem rendelkezel még jeggyel, vagy kuponnal, akkor mindenképpen szükséged lesz egyre amit a ballonozz.hu oldalon is be tudsz szerezni. (Ez valószínúleg már megvan, ha itt vagy). Ha szeretnéd más konstrukcióra váltani a jegyedet, vagy hozzáadni utasokat, erre is van lehetőséged. Ez esetben keress minket.</li>
        <li>Regisztrálj az utasfoglaló oldalra. Ez a lépés is megvan, mivel itt vagy és az ismertetőnket olvasod éppen. 🙂 <b>Fontos:</b> Ha egy mód van rá @gmail.com címet használj és semmiképpen sem freemail-t, hotmail-t például, mert visszadobálják a leveleket és nem fogod megkapni az értesítéseket, amit küldünk.</li>
        <li>A jegyedet/kuponodat a <a class="text-primary-600 dark:text-primary-400 font-semibold" href="{{ route($couponResource::getRouteBaseName().'.index') }}">@svg($couponResource::getNavigationIcon(), ['class' => 'w-6 inline-block align-bottom']) {{ $couponResource::getNavigationLabel() }}</a> menüpontban tudod felvinni az <i>„{{ $listCoupons::getCreateActionLabel() }}”</i> gombbal a jobb felső sarokban.</li>
        <li>
            <span>A kupon azonosító 1-2, illetve kibocsátó esetében a következők szerint kell felvinned a jegyedet / kuponodat:</span>
            <br>
            <table class="bg-zinc-100 dark:bg-white/5">
                <thead>
                    <tr class="font-semibold">
                        <td class="border-2 p-1">Jegyed „forrása”, kiállítója</td>
                        <td class="border-2 p-1">Azonosító 1 -></td>
                        <td class="border-2 p-1">Azonosító 2</td>
                        <td class="border-2 p-1">Kibocsátó</td>
                    </tr>
                </thead>
                <tbody>
                    <tr style="color: rgb(4, 164, 60)">
                        <td class="border-2 p-1">Ballonozz.hu jegy #0123 formátumú rendelési azonosítóval</td>
                        <td class="border-2 p-1">„0123” azonosító # szimbólum nélkül</td>
                        <td class="border-2 p-1"><üres></td>
                        <td class="border-2 p-1">ballonozz.hu</td>
                    </tr>
                    <tr style="color: rgb(126, 177, 255)">
                        <td class="border-2 p-1">Ballonozz.hu kupon #kupon_00 formátumú rendelési azonosítóval</td>
                        <td class="border-2 p-1">„#kupon_00” azonosító</td>
                        <td class="border-2 p-1"><üres></td>
                        <td class="border-2 p-1">Egyéb</td>
                    </tr>
                    <tr style="color: rgb(4, 164, 60)">
                        <td class="border-2 p-1">Meglepkék kupon</td>
                        <td class="border-2 p-1">„AB01234” 7 jegyű azonosító beírása</td>
                        <td class="border-2 p-1"><üres></td>
                        <td class="border-2 p-1">Meglepkék</td>
                    </tr>
                    <tr style="color: rgb(126, 177, 255)">
                        <td class="border-2 p-1">Élménypláza voucher</td>
                        <td class="border-2 p-1">„012345” 6 jegyű voucher kód</td>
                        <td class="border-2 p-1">„987654” 6 jegyű voucher biztonsági kód</td>
                        <td class="border-2 p-1">Élménypláza</td>
                    </tr>
                    <tr style="color: rgb(126, 177, 255)">
                        <td class="border-2 p-1">Él A Mának voucher</td>
                        <td class="border-2 p-1">„01234-01234-01234” voucher kód</td>
                        <td class="border-2 p-1"><üres></td>
                        <td class="border-2 p-1">ÉljAMának</td>
                    </tr>
                    <tr style="color: rgb(126, 177, 255)">
                        <td class="border-2 p-1">Feldobox élménykártya</td>
                        <td class="border-2 p-1">„0123456789” azonosító</td>
                        <td class="border-2 p-1"><üres></td>
                        <td class="border-2 p-1">Aji kártya</td>
                    </tr>
                    <tr style="color: rgb(126, 177, 255)">
                        <td class="border-2 p-1">Aji VIP Kártya </td>
                        <td class="border-2 p-1">„0123456789” azonosító</td>
                        <td class="border-2 p-1">„0123” pinkód</td>
                        <td class="border-2 p-1">Feldobox</td>
                    </tr>
                </tbody>
            </table>
        </li>
        <li>Ha felvitted az adatokat akkor kattints az <i>Ellenőrzés</i> gombra és eljutsz a „Kupon adatok megadása” részhez. Amennyiben jobb felső sarokban hibaüzenetet kapsz, akkor lehet, hogy éppen a weboldal, ahonnét az adatokat lekéri a rendszer nem válaszol, de a sokkal valószínűbb eset, hogy az azonosítód hibás 🙂 Ezt mindenképpen ellenőrizd le. Tipikus hiba a „#„ kettős kereszt feltüntetése a 4 jegyű Ballonozz.hu rendelési azonosítónál.</li>
        <li>
            <span style="color: rgb(4, 164, 60)">A táblázatban zölddel jelölt kibocsátók sorainak esetében a jegyed ellenőrzése automatikusan történik</span><span>, azaz ha helyesen csináltad az azonosítód bevitelét, akkor a rendszer a kupon adatokat beemeli és felhasználható státuszba kerül. Ez után azonnal tudod folytatni a kitöltést. </span>
            <br>
            <span style="color: rgb(126, 177, 255)">A táblázatban halvány kékkel jelölt soroknál ezen a ponton a jegyed még feldolgozás alatt lesz</span><span>, mi fogjuk ellenőrizni, hogy helyes-e kupon, illetve felvinni a szükséges alapadatokat. Ezt tipikusan néhány nap alatt (repülési szezonon kívül 1 hét) megtesszük és átállítjuk a jegyedet felhasználható státuszra. Erről fogsz kapni e-mailt. Ekkor tudod folytatni kupon adatok megadását.</span>
        </li>
        <li>
            <span class="flex-wrap inline-flex gap-1">A kuponod státuszát, a <a class="text-primary-600 dark:text-primary-400 font-semibold" href="{{ route($couponResource::getRouteBaseName().'.index') }}">@svg($couponResource::getNavigationIcon(), ['class' => 'w-6 inline-block align-bottom']) {{ $couponResource::getNavigationLabel() }}</a> menüpontban a kuponok utolsó oszlopában <i>Státusz</i> elnevezéssel látod. Ez lehet
            @foreach (App\Enums\CouponStatus::cases() as $status)
                @if ($loop->last)
                    <span>és</span>
                @endif
                <x-filament::badge
                    :color="$status->getColor()"
                    :icon="$status->getIcon()"
                    class="w-max inline-block"
                >
                    {{ $status->getLabel() }}
                </x-filament::badge>
            @endforeach
            <span>
        </li>
        <li>A kuponod jegytípusa is a fentebbi sorban látszik és lehet példul normál utasjegy, romantikus ajándék, exkluzív páros, sztratoszféra, csoportos is, melyek különböző méretű ballonokra és konstrukciókra szólnak.</li>
        <li>ENNÉL A PONTNÁL ÁLJ MEG EGY PILLANATRA ÉS GONDOLD ÁT, HOGY AMENNYIBEN NEM BALLONOZZ.HU A FORRÁSA A KUPONODNAK BIZTOSAN EZEN A PROGRAMON FOGOD ÉS AKAROD-E FELHASZNÁLNI. EZ AZÉRT FONTOS, MERT A KUPONODAT ESETLEGESEN MÁS SZOLGÁLTATÓKTÓL ELTÉRŐEN NEM A SZOLGÁLTATÁS IGÉNYBEVÉTELE UTÁN VEZETJÜK KI / VÁLTJUK BE, HANEM AKKOR, AMIKOR AZ ELLENŐRÉS SORÁN A KUPONODAT FELHASZNÁLHATÓ STÁTUSZBA TESSZÜK. AZAZ ETTŐL A PONTTÓL KEZDŐDŐEN MINDENT A KUPONODDAL KAPCSOLATBAN VELÜNK TUDSZ CSAK INTÉZNI AZ ITTENI FELTÉTELEK SZERINT, NEM A KUPONOD KIBOCSÁTÓJÁNAK A FELTÉTELEIVEL (PÉLDÁUL A HOSSZABBÍTÁS LEJÁRAT UTÁN). EZZEL A MÓDSZERREL TUDJUK GARANTÁLNI, HOGY IDŐKÖZBEN NEM SZŰNIK MEG A FEDEZET A KUPON MÖGÖTT, AZAZ NEM KERÜL VISSZAVÁLTÁSRA VAGY ÁTVÁLTÁSRA, MIKÖZBEN ITT IS JELENTKEZÉS TÖRTÉNIK. A KUPON ADATOK MEGADÁSA A FENTIEK ELFOGADÁSÁT JELENTI. HA EZ SZÁMODRA NEM ELFOGADHATÓ OPCIÓ, AKKOR MINÉL HAMARABB ÍRJÁL NEKÜNK EMAILT, HOGY A MEGADOTT KUPONODAT TÖRÖLJÜK A RENDSZERBŐL ÉS NE VEZESSÜK KI.</li>
        <li><b>FONTOS</b> A kibocsátóknál csak a <span style="color: rgb(4, 164, 60)">zöld sorok (Ballonozz.hu és Meglepkék)</span> esetében létezik olyan lehetőség, ahol 1 kupon több személyre is szól egyben. MINDEN egyéb esetben személyenként 1-1 kupont kell felvinned, majd ezeket a kuponokat <i>Kuponok összevonása / szétválasztása</i> jobb oldali opcióval kell közösíteni, hogy „egy csapatot” alkossatok a repülésen. Ha ezt nem teszed meg, akkor külön látjuk a kuponokat és ezért valószínűleg külön repülésre is kaptok beosztást.</li>
        <li><i>Kupon adatok megadása</i> esetében ellenőrizd le az adatokat, hogy helyesek-e. Jelöld be a számodra érdekes régiókat. Ez csak nekünk jelzi, mennyi jelentkező van egy térségben, neked meg fog jelenni minden régiós repülésünk. Ha nem jelölöd meg az adott régiót, ahol szeretnél repülni, akkor nem fogjuk látni, hogy szeretnél ott repülni és lehet hogy a szezonban nem is írunk ki oda repülést.</li>
        <li>
            Utasok esetében <i>Új utas felvitele</i> gombbal tudsz új utast felvinni és annak adatait megadni. A rendszer annyi utast enged hozzáadni, amennyi a kupon, vagy összevont esetben kuponok összlétszáma. A kötelezően megadatanó adatok pirossal látszódnak. Az opcionális utasadatokat javasolt megadni. Arra semmilyen értesítést nem küldünk a repülés előtt.
            <b>Ameddig minden utas kötelező adata nem kerül felvitelre, addig a repülési időpontokat nem látod és jelentkezni sem tudsz rájuk.</b>
            A jobb felső sarokban egy figyelmeztetés jelzi ezt számodra <i>Hiányzó utasadatok</i> jelzéssel és a <a class="text-primary-600 dark:text-primary-400 font-semibold" href="{{ route($couponResource::getRouteBaseName().'.index') }}">@svg($couponResource::getNavigationIcon(), ['class' => 'w-6 inline-block align-bottom']) {{ $couponResource::getNavigationLabel() }}</a> menüpontban is egy @svg('tabler-alert-triangle', ['class' => 'w-6 inline-block align-bottom text-red-500']) piros háromszöget fogsz látni. Amikor egy repülésre beszálltál, akkor az utasadatoakt már nem tudod módosítani, csak akkor ha minden repülésből „kiszállsz”, majd módosítá után visszalépsz.
        </li>
        <li>
            A <a class="text-primary-600 dark:text-primary-400 font-semibold" href="{{ route($checkinPage::getRouteName()) }}">@svg($checkinPage::getNavigationIcon(), ['class' => 'w-6 inline-block align-bottom']) {{ $checkinPage::getNavigationLabel() }}</a> menüpontban az utasadatok helyes megadása esetén látni fogod az aktív repülési időpontjainkat. Akkor is van néhány, ha nincs éppen repülési szezonunk. Ekkor az adott év 12.31. dátummal fogsz látni minimum 1 Demó Repülés elnevezésű repülést, ami azért van ott, hogy le tudd ellenőrizni, hogy minden működik a kuponodnál. Jejárt és nem hiányos utasadatokkal rendelkező kuponnal nem fogsz látni egy repülési időpontot sem.
            Ha több kuponod is van, akkor vagy nem vontad össze őket, vagy tényleg több csapat szeretne nálad repülni, esetleg különböző típusú jeggyel. Ezen kuponok között a kuponra kattintva tudsz váltani és típustól függően különböző repülési időpontok fognak ilyenkor megjelenni.
        </li>
        <li>
            Repülésekre jelentkezni az alábbi módon lehet: A repülési időpontoknál rákattintasz a <i class="font-semibold" style="color: rgb(126, 177, 255)">Jelölöm</i> lehetőségre, mellyel jelzed nekünk, hogy ez az időpont neked megfelelő lenne. Amennyiben mégsem jó, úgy a <i class="text-red-500 font-semibold">Törlés</i> gombbal tudod törölni ezt. Amennyiben a repülés még csak kiírt állapotában van, háttér nélküli időpontot fogsz látni: 
            <img src="{{ asset('images/jelentkezes.png') }}" alt="">
            Amennyiben a repülés véglegesített és arra beosztást kaptál, akkor megjelenik egy Résztveszek kiírás az adott repülésen, illetve a zöld háttérsznít kap: 
            <img src="{{ asset('images/resztveszek.png') }}" alt="">
            Ha a repülés véglegesített és nem szerepelsz annak utaslistáján, akkor szürkével jelenik meg az időpont: 
            <img src="{{ asset('images/lezarva.png') }}" alt="">
            Javasoljuk, hogy MINDEN lehetséges időpontot jelölj be, ami jó lehet számodra, mert ha 1 alkalmat jelölsz meg és ezen a repülésen van 20 jelentkező, a másikra senki, akkor a jelentkezők felének nem tudunk beosztást adni. A rendszer 5-10 aktív időpontra történő jelentkezést enged egy időben. Érdemes kihasználni.
        </li>
        <li>
            <b>FONTOS: A repüléskre történő jelölésed és annak törlése szabadon megtehető a repülés előtt 7 napig.</b> Így amég rendszer ezt engedi, ez semmilyen következménnyel nem jár számodra. Egy már véglegesített / lezárt repülést is bejelölhetsz, ezzel nincs semmi gond, mivel ha felszabadul hely, akkor az aktuális jelentkezők kapnak beosztást erre a helyre. <b>Ellenben a véglegesített repülés esetében 7 nappal a repülés időpontja előtt a beosztásod véglegesé válik.</b> Ez után a repülésen részt kell venned. Lemondani igen indokolt esetben teheted meg anélkül, hogy a kuponod ne veszne el. Ezt a változtatást már csak mi tudjuk már elvégezni, a megkeresésed után. Azaz arra figyelj, hogy ha egy időpont nem jó, akkor arról az időpontot megelőző 7 napig jelentkezz le, hogy ne kapj végleges beosztást. Egyetlen kivétel a repülésen való részvételi közelezettség alól ebben az időszakban, ha a beosztásodat a hátralevő 7 napban kapod, mert valaki helyett osztottunk be, vagy akkor véglegesítettük az időpontot. Ekkor a tájékoztató e-mail beérkezte után 24 óráig van lehetőséged jelezni, hogy a beosztás már nem alkalmas, utána a repülésen részt kell venned. Ilyenkor általában az idő rövidsége miat telefonon is egyeztetünk veled beosztás előtt.
            <br>
            <b>Minden jelentkezett repülésedben bekövetkező változásról fogsz kapni e-mailt, hogy ne kelljen naponta nézned az oldalt, hogy történt-e változás.</b> Arról nem kapsz e-mailt, ha egy repülést véglegesítettük és nem osztottunk be rá, csak akkor, ha bekerültél abba a csapatba.
        </li>
        <li>
            <b>Ha időpont feláras opciójú jeggyel rendelkezel</b>, akkor a beosztásod máshogy kapod, melyről adtunk részletes tájékoztatást a jegyvásárláskor. Amennyiben szeretnéd ezt az opciót a normál kontsrukciós jeggyel, úgy ennek a plusz költsége a jegyár +30%-a illetve 4 felnőtt személytől, vagy privát jellegű repülésektől kérhető (például romantikus ajándék), viszont ezt akkor tartjuk megfelelő választásnak, ha tényleg nehéz megszervezni a repülést például külföldi hazalátogatás miatt. Erről szívesen adunk további információt.
        </li>
        <li>
            <b>Egy repülésnél, ahol beosztottunk, minden információt meg fogsz kapni emailben</b>, amiben benne lesz például a kontakt személy elérhetősége, aki tudsz keresni akkor ha nem találod meg a találkozási pontot, vagy egyéb nem várt helyzet esetén. (Az aktuális időjárással és repülései esélyekkel ne keresd, mert nem fog róla innormációt adni 🙂 Ilyenkor az email, vagy telefonos közös sms-t fogja használni, amikor a repülés vezetője erről dönt és ez az információ kiküldésre kerül). Meg fogjuk adni továbbá véglegesítéskor a tipikus találkozási pontját a régiónak, amin a repülés előtti napokban változtatunk, ha indokoltnak tartjuk.
        </li>
        <li>
            A <a class="text-primary-600 dark:text-primary-400 font-semibold" href="{{ route($flightlocationResource::getRouteBaseName().'.index') }}">@svg($flightlocationResource::getNavigationIcon(), ['class' => 'w-6 inline-block align-bottom']) {{ $flightlocationResource::getNavigationLabel() }}</a> menüpont tartalmazza a lehetséges találkozási pontok listáját MINDEN régióra. Azaz fogsz látni olyanokat is, amikre a jegyed nem felhasználható. Ezeket hagyd figyelmen kívül. A listát azért szedtük össze neked, hogy meg tudd nézni a repülés előtt a lehetséges pontokat az adott régióban, mivel a találkozási időpontot tipikusan a repülés előtti 24 órában változtatjuk. Egy változtatás esetében hasznos lesz számodra az itt levő információ, ha már előre szemügyre vetted és megnézted a lehetséges helyszíneket, így nem lepődsz meg, amikor 10km-el alrébb kell majd gyülekezni.
        </li>
        <li>
            <b>Ha a repülés meghiúsul</b>, azaz végleges volt és be is osztottunk, de a körülmények nem tették lehetővé a repülést, akkor a jegyed felhasználható marad természetesen és a további megjelölt időpontjaid esetében igyekszünk új beosztást keresni neked, vagy a következő kiírások esetében új időpontokat tudsz megjelölni.
        </li>
        <li>
            <b>Ha a repülés sikeres</b>, akkor a jegyed felhasználásra kerül, így a jövőben már nem fogod látni a repüléseket (mivel nincs érvényes jegyed). Újabb repülés esetében örülünk, ha visszatérőként fogadhatunk ezen a remek programon. Másik, új kupont bármikor fel tudsz vinni a fiókodba a jövőben ennek a leírásnak megfelelően.
        </li>
        <p>
            Amennyiben kérdésed van, vagy ebben a leírásban nem találod a megoldást számodra, akkor keress minket az elérhetőségeinken. Nem sürgős esetben kérjük, írj e-mailt a hívás helyett. Telefonon kereszül általában a konkrét technikai jellegű kérdésben nem tudunk segíteni, mert nem vagyunk számítógép előtt és az lesz a kérésünk, hogy írd le üzenetben, amint megnézünk, amint tudunk: <a href="mailto:info@ballonozz.hu">info@ballonozz.hu</a>, +36207779081 (Balázs - kapcsolattartó)
        </p>


        <br>
        <h2 class="fi-header-heading text-xl font-bold tracking-tight text-gray-950 dark:text-white sm:text-2xl">Gyakori kérdésekre válaszok röviden:</h2>
        <li>
            <b>Hány fővel repülünk:</b> A nagy utasrepültető ballonunk 16 fős. Ezen túl van még egy kisebb ballon ami néhány fő kapaciású privát jegyekre és egy verseny ballon, amivel csak 1 fő tud utazni. A kisrepülő esetében 1 személy tud eljönni egy időben repülni a pilóta mellett.
        </li>
        <li>
            <b>Repülés személyi feltételi:</b> A ballon esetében felelősség vállalási nyilatkozat és hozzá tartozó részben részletesen szerepelnek ezek a feltételek, amit érdemes átolvasni. Ami fontos:
            <ul class="flex flex-col gap-4 list-disc" style="margin-inline-start: 25px;">
                <li>Egyedüli utas esetében betöltött 18 életév.</li>
                <li>Egészségesnek érzed magad.</li>
                <li>Komolyabb műtéted az elmúlt fél évben nem volt.</li>
                <li>Mozgásszervi betegséggel nem rendelkezel, különösen sérvvel, vagy csontritkulással.</li>
                <li>Nem állsz tudatmódosító szer hatása és pszichiátriai kezelés alatt.</li>
                <li>Repülésre alkalmas állapotban jelentél meg.</li>
                <li>Repülésre alkalmas ruházatban jelentél meg.</li>
            </ul>
            <br>
            Részletes személyi feltételeinket az ÁSZF végén a „17.1 számú melléklet: Biztonsági tájékoztató” részben éred el.
            Kisrepülőgép esetében kiegészítés a maximális utastömeg, ami 80-100kg. Ez repülőgéptől és időszaktól függően változik. 100kg felett kényelmetlenül fogod magad érezni az ülésben, mivel a 2 személyes gépek nem a helyről híresek. Ahhoz nagyobb, akár 4 üléses típus kell. Maximális magasság 180-185 cm, illetve minimum életkor 14 év.
        </li>
        <li>
            <b>Mikor van szezonja a repüléseknek:</b> A ballonnak tipikusan május – október között, amiből általában a június- szeptember szokott teljesülni, amikor szép repülő idők vannak. Privát jellegő kisebb ballonos repülések kicsit korábban kezdődnek, de a szezon igen hasonló. Kisrepülős esetben inkáb a tavasz és őszi időszak esetében van jó repülő idő. Nyáron az idővel nincs gond, csak megsülünk a pilótafülkében a nyári nap alatt.
        </li>
        <li>
            <b>Milyen napokon lesznek a repülések:</b> Ballon esetében nagyon ritka kivételtől eltekintve péntek este, szombat reggel-este és vasárnap reggel-este, ami kiírásra kerül. Kis repülőgép esetében hétköznapok is műkődőképesek, de itt is főleg hétvégi időpontok lesznek.
        </li>
        <li>
            <b>Milyen napszakban történnek a repülések:</b> Ballon esetében kizárólag napfelkelte és naplemente időszakában lehet repülni. Ez reggel a napfelkeltét jelenti (május-június ~5 óra, szeptember ~7 óra), illetőleg délután a naplemente előtti 2-3 órát (május-június ~17-18 óra, szeptember ~16 óra) találkozási időpontnak. Kis repülőgép esetében elméletben napfelkeltétől naplementéig van lehetőség repülni, de utasélmény miatt itt is inkább a délelőtt és késő délutáni órák a megfelelő, mivel napközben a termikek össze-vissza fogják dobálni a gépet. Ez a pilótát és a gépet sem zavarja, de téged valószínűleg fog.
        </li>
        <li>
            <b>Mennyi ideig tartanak a repülések:</b> Ballon esetében a csoportosan szervezett nagy ballonos repülések teljes programának időtartama 4 óra, amiből ~50-60 perc a repülési idő. Kisebb ballonos esetben ~3.5 órás a program. Kisrepülőnél a tényleges repülési idő mellé + 30 perces előkészületi időtartammal érdemes számolni. Azaz 60 perc repülésnél másfél óra.
        </li>
        <li>
            <b>Milyen gyakoriak a régiókban a repülések:</b> A repülések gyakoriságát a régiók megjelölése állítja be részedről, melyből látjuk, mire számíthatunk és mennyi repülést írjunk ki. A fő régiónk a ballon esetében Siófoki, ahol tipikusan minden második hétvégén repülünk. A többi régióban a repülések ritkábbak.
            <table>
                <tbody>
                    <tr>
                        <td>Eger-Miskolc és Velencei-tó</td>
                        <td>-></td>
                        <td>havonta néhány.</td>
                    </tr>
                    <tr>
                        <td>Győr</td>
                        <td>-></td>
                        <td>évente néhány hétvége.</td>
                    </tr>
                    <tr>
                        <td>Szekszárd és Pécs</td>
                        <td>-></td>
                        <td>évente 2-3 repülés</td>
                    </tr>
                    <tr>
                        <td>Szeged</td>
                        <td>-></td>
                        <td>évente 1 hétvége a maximum szeptember közepén.</td>
                    </tr>
                </tbody>
            </table>
            Ballonos privát jellegű repüléseknél havi szinten írunk ki időpontokat, viszont szinte csak Siófok, Velencei-tó és Eger-Mickolc térségében.
            Kisrepülőgép esetében a létszám csak 1 fő, így itt nagyobb a rugalmasság, viszont egymás után több személlyel tervezzük egymás után a repülést.
        </li>
        <li>
            <b>Ruházat, egyéb kellékek repüléshez:</b> Ballonozáshoz fontos, hogy legyen kinyomtatott, kitöltött felelősség vállalási nyilatkozat, amit itt is leírunk újra. Érdemes nálad lennie némi apró nasinak, illetve folyadéknak, mert 35-40 fok is tud lenni a felszálló területen, ahol várnod kell. Ez főleg a nyári délutáni repülések esetében fontos, ahol minimum 1liter/fővel érdemes készülni. A repülés előtt ne koplalj egész nap, mert ha a program végére elég éhes leszel, vagy elszédülsz az alacsony cukor szint miatt, akkor kevésbé lesz élvezhető a program. Fényképezőt, telefont és hasonlókat tudsz magaddal hozni, illetve maximum egy kisebb táskát is, ha szükséges.
            <br>
            Ballonos program esetében a ruházat a szezonnak megfelelő sportosabb ruházat a jó, ami esetében nem jelent gondot, ha esetleg poros lesz. Érdemes rétegesen öltözködni, mivel napfelkeltekor van a leghidegebb, ami augusztusban is 15 fok tud lenni, de leszálláskor már 30 fok lesz. A nadrágnak érdemes hosszú szárúnak lennie. Cipő esetében mindenképpen zárt legyen. A szandállal nekünk nincs bajuk, viszont neked lesz, ha a leszálló területen a térdig érő gazból rövidgatyában és szandálban kell kisétálnod. 🙂
            Kisrepülőgép esetében érdemes rétegesen öltözködni és a helyszínen eldöntjük, mit hagyunk meg, mivel gépe válogatja, melyik milyen fűtéssel, szellőztetéssel és napellenzővel rendelkezik.
        </li>
        <li>
            <b>Lejárt jegy és megújíjtási folyamata:</b> A jegyek általában 1 évig érvényesek. Fontos, hogy a vásárlástól számít a felhasználásunk, ami a gyakorlatban 1 szezont jelent. Ezután le fog járni és a rendszer még néhány hét türelmi idővel lehetővé teszi hogy jelentkezz repülésre, majd a repülési időpontokat már nem fogod látni. A hosszabbítás a jegy értékének 25%-a és 1 évet ad hozzá az érvényességhez. Ezt tőlünk kell kérned.
        </li>
    </ul>
</x-filament-panels::page>
