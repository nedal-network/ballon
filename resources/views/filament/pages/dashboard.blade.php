<x-filament-panels::page class="fi-dashboard-page">
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
    <div class="welcomeText">
        <p class="textLine" style="font-size:10pt;">Elsőként látogass el profil oldaladra és kérlek add meg telefonszámod, a könnyebb kapcsolatfelvétel érdekében. Ezt kétféle képen teheted meg. Az egyik módja, hogy a képernyő jobb felső sarkában rákattintasz a nevednek kezdőbetűit tartalmazó körre, majd a lenyiló menüben a "<b>Profil</b>" menüpontot választod...de van ennek egy egyszerűbb módja is: a bal oldali navigációs menüben a "<b>Saját profil</b>" menüpontra kattintasz.</p><br>
        <p class="textLine" style="font-size:10pt;">A profil beállítását követően először rögzíts egy már megvásárolt kupont a rendszerhez. Ezt a "<b>Kuponjaim</b>" menüpontra kattintva tudod elvégezni.</p><br>
        <p class="textLine" style="font-size:10pt;">Ha már rendelkezel kuponnal a rendszerben, már csak egyetlen dolgod van, kiválasztani a számodra legmegfelelőbb időpontott a felhőlátogatásra, amit a "<b>Repülési időpontok</b>" menüpont alatt meg is tehetsz.</p><br>
        <p class="textLine" style="font-size:10pt;">Amennyiben érdekelnek további repülési helyszínek, amik várhatóan elérhetőek lesznek, akkor látogatsd meg a "<b><badge>Repülési helyszínek</badge></b>" menüpontot.</p><br>
    </div>
</x-filament-panels::page>
