=== Invelity SPS connect ===
Author: Invelity s.r.o.
Author URI: https://www.invelity.com
Tags: GLS, shipping, WooCommerce
Donate link: https://www.paypal.com/cgi-bin/webscrčcmd=_s-xclick&hosted_button_id=38W6PN4WHLK32
Requires at least: 4.6.1
Tested up to: 5.4.2
Stable tag: 5.4.2
Requires PHP: 5.6
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Plugin Invelity SPS (Slovak parcel service) connect je vytvorený pre obchodníkov na platforme Woocommerce ktorý potrebuju automaticky exportovat údaje o objednávkach do systému SPS za účelom vytlačenia doručovacích lístkov.

== Description ==
Plugin Vám umožnuje jednoduchý prenos údajov o objednávkach z Wordpress adminu priamo do systému SPS bez exportovania/importovania akýchkolvek súborov pomocou API volaní.
Po označení objednávok pre export máte hned dostupný odkaz na stiahnutie PDF súboru s doručovacími lístkami

== Installation ==

Táto sekcia popisuje inštaláciu pluginu.

1. Stiahnite plugin a nahrajte ho priamo cez FTP (`/wp-content/plugins/invelity-sps-connect) alebo plugin stiahnite priamo z Wordpress repozitára.
2. Aktivujte plugin cez 'Plugins' obrazovku vo WordPress.
3. V hlavnom menu (ľavý sidebar) uvidíte položku "Invelity plugins" a jej pod-položku "Invelity SPS connect".
4. Vpíšte všetky potrebné údaje vrátane údajov ktoré ste dostali priamo od služby GLS.
5. Po správnom nastavení pluginu môžete pristúpiť k exportovaniu údajov o objednávkach do SPS.
6. Vo výpise objednávok zaškrtnite objednávky ktoré chcete exportovať do SPS. Z drop-down zvoľte možnosť "Stiahnuť SPS lístky"
7. Po obnovení stránky, pokiaľ všetko prebehlo ako má, vo vrchnej časti webu nájdete odkaz na PDF s dodacími lístkami

== Frequently Asked Questions ==

= Potrebujem ešte niečo pre správnu funkcionalitu tohto pluginu? =

Áno, potrebujete mať dohodnutú spoluprácu s SPS a mať pristupové údaje ktoré zadáte do nastavení pluginu

= Je tento plugin zdarmo? =

Áno. Plugin ponúkame úplne zdarma v plnej verzii bez akýchkoľvek obmedzení, avšak bez akejkoľvek garancie podpory alebo funkcionality.
Podporu nad rámec hlavnej funkcionality pluginu ako jeho úpravy, nastavenia alebo inštálácie poskytujeme za poplatok po dohode.
V prípade záujmu nás kontaktujte na https://www.invelity.com/ alebo priamo na mike@invelity.com

== Screenshots ==

1. Konfigurácia pluginu
/assets/screenshot-1.png
2. Používanie pluginu
/assets/screenshot-2.png
3. Používanie GLS online

== Change log ==

= 1.0.0 =
* Plugin Release


= 1.0.1 =
* Bugfixing

= 1.0.2 =
* Added preberaci protokol (printEndOfDay)

= 1.0.3 =
* Fixed company name. Added support for zasilkovna cod "dobirka"

= 1.0.4 =
* Fixed server error when generating local pickup Orders by mistake"

= 1.0.5 =
*Fixed remote data call on servers that block it

= 1.0.6 =
*Soap client fix

= 1.0.7 =
*Getting order number instead of order id

= 1.0.8 =
*Fixed notices hook

