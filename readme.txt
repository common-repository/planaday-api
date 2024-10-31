=== Planaday API ===
Contributors: Planaday
Donate link: n/a
Tags: course, cursus, planning, api, planaday, training, boeking, reserveren, cursusadministratie, cursistenadministratie
Requires at least: 3.x
Tested up to: 6.7
Stable tag: 6.7
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Toon het cursusaanbod vanuit Planaday op jouw website met deze Wordpress-plugin
Dit kan middels een lijst, op cursussoort of alle cursussen en in verschillende formats.

== Description ==

Met deze plugin is het mogelijk alle 'open' cursussen vanuit Planaday applicatie plaatsen op je website.
Optioneel kunnen jouw bezoekers een cursus boeken.

Deze boekingen komen direct in jouw eigen planaday omgeving.
Deze boekingen zijn een optie en moeten in Planaday zelf nog geaccoordeerd worden bij cursussen -> open aanvragen.

In de plugin kun je diversen opties zelf aan/uitzetten.
En met CSS kun je je overzichten etc verder personaliseren

== Screenshots ==

1. Overzicht van cursussen
2. Detail van cursus met boek mogelijkheid
3. Instellingen in Wordpress Admin

== Installation ==

Deze Planaday Wordpress plugin werkt enkel in combinatie met de Planaday omgeving.
Hiervoor heb je eveneens een API-sleutel nodig, welke in Planaday opgehaald kan worden.

1. Maak een API-steutel in Planaday
2. Activeer de API.
3. Download deze wordpress-plugin
4. Activeer de wordpress-plugin
5. Vul de API-sleutel van Planaday in de Wordpress Plugin
6. Vul de instellingen aan
7. Maak de benodigde pagina's aan met shortcode(s)
8. Optioneel kies je voor 'ideal'
9. Volg hiervoor de stappen bij 'betalingen' en zorg dat alle checks groen zijn

Zie volledige installatie hier: https://planaday.freshdesk.com/support/solutions/articles/11000058859-wordpress-in-website-met-publieke-api


== Frequently Asked Questions ==

= In welke PHP versie werkt deze plugin? =

Wij hebben de plugin getest in de volgende PHP-versies:
- 7.4
- 8.0
- 8.2

= Krijg ik alle cursussen te zien? =

Ja, maar wel de cursussen vanaf vandaag. En enkel de cursussen die 'open' zijn in Planaday en website/api 'ja'
Je hebt wel een optie om volle cursussen te tonen (deze zijn niet meer boekbaar).
En er is een optie (instelling) om cursussen met tenminste één dagdeel in het verleden optioneel niet te tonen.

= Kan ik ook één specifieke cursus op een pagina laten zien? =

Ja, maar wel enkel de cursussen vanaf vandaag. En enkel de cursussen die 'open' zijn in Planaday en website/api op: 'ja'
Deze pagina maak je eerst aan in Wordpress en vervolgens plaats je daar een specifieke shortcode.
Deze zijn toepasbaar op iedere pagina. Nummer id=3 verwijst naar cursus 3 in jouw Planaday omgeving:

    ([pad-name id=3]) geeft naam van cursus
    ([pad-dates id=3]) geeft lijst met datums van dagdelen
    ([pad-dates-locations id=3]) geeft lijst met datums van dagdelen inclusief locaties
    ([pad-price id=3]) geeft prijs per persoon van cursus
    ([pad-price-remark id=3]) geeft opmerkingen bij prijs
    ([pad-button id=3]) geeft een button met link naar de juiste cursus

= Ik heb geen API-key =

Ga hiervoor naar jouw eigen Planaday omgeving.
Daar kun je zelf via 'beheer -> instellingen -> koppelingen -> api sleutels' toevoegen en maken.
Hiervoor moet wel de optie aanstaan dat je dit mag.
Zie ook: https://planaday.freshdesk.com/support/solutions/articles/11000045430

= De omschrijving wordt niet meer goed getoond =

Je kunt hiervoor een extra CSS regel toevoegen. Hierdoor wordt de omschrijving in een 'block' getoond.
Voorbeeld:

.pad-detail-description {
  display: block;
  float: left;
}

= Detailpagina is aangemaakt met [pad-course], maar werkt niet =

Het kan zijn dat jouw Wordpress omgeving de 'slug' nog niet snapt.
Ga hiervoor naar 'Instellingen -> Permalinks' en sla je wijzigingen nog een keer op.
Hierna zal de 'slug' worden herkent en zal de pagina wel werken.
In enkele uitzonderlijke gevallen moet je 'cache' (kan een plugin zijn) worden geleegd.

= Ik ontvang geen mail bij een aanmelding =

Het mailen van een aanvraag gaat via E-mail (of eventueel SMTP) van/via Wordpress.
Dit kan worden aangemerkt als Spam en kunnen wij al developer van de plugin niet voorkomen.
Kijk daarom altijd in de spamfolder en voeg de afzender/mail toe aan je vertrouwde personen/mappen.
Of maak gebruik van een SMTP plugin die het mailen wat veilgiger en stabieler maakt.

= Ik kan niet meer vertalen =

Vanaf pluginversie 8.2 zijn de vertalingen verplaatst naar /translation van Wordpress zelf.
Kijk na of hier planaday*.mo bestanden staan. Als die er niet staan moeten deze opnieuw gemaakt worden

= Als een betaling niet is gelukt of geannuleerd, staat deze nog steeds in Planaday =

Je wil als opleider geen boekingen missen. Daarom wordt een boeking gedaan en hierna doorverwezen om te betalen
(als dit actief is).
Tip:
    Je kunt optioneel via 'betalingen' de boeking ook 'annuleren' als betaling om welke reden dan ook is mislukt

== Changelog ==

= 11.1 =
* Test tot WP 6.7

= 11.0 =
* Bugfix voor niet altijd aanmaken van initiele database
* Code verbeteringen
* Bugfix voor het draaien van oude migraties + default settings
* Toon in/ex btw achter de prijs afhankelijk van instellingen
* Bugfix voor het tonen van op lengte gelimiteerde cursus omgeschrijvingen
* Bugfix voor het niet altijd correct tonen van de begintijden van een cursu in de agenda
* Nieuw! Geef aan de shortcodes: courselistblock, courselistblock2, courselistlist, courselisttable en coursesearch extra (optionele) attributen mee:  showprice=1/0 withvat=1/0 mee om de globle instellingen te overschrijven

= 10.6 =
* Puntjes verwijderd indien er geen omschrijving is bij een cursus
* Deelnemer die was toegevoegd in boekingformulier kan ook weer worden verwijderd
* Link toevoegen deelnemer is button geworden ipv een link

= 10.5 =
* Bugfix voor het niet correct tonen prijzen btw bij materialen

= 10.4 =
* Bugfix voor het niet correct tonen prijzen en aantal dagdelen in detailscherm

= 10.3 =
* Bugfix voor het niet correct tonen prijzen in de widget

= 10.2 =
* Bugfix voor het niet correct tonen van sommige prijzen bij cursusdetail pagina's

= 10.1 =
* Afvangen van diverse foutmeldingen

= 9.11 =
* Meld pluginversie aan planaday api, zodat we makkelijk naar fouten kunnen zoeken

= 9.10 =
* Bugfix voor het niet correct tonen van sommige prijzen

= 9.9 =
* Afvangen van diverse foutmeldingen

= 9.8 =
* Afvangen van diverse foutmeldingen


= 9.7 =
* Kunnen tonen van cursus in of ex btw

= 9.6 =
* Bugfix bij bijwerken tabel structuur

= 9.5 =
* Bugfix omschrijving beter tonen en beter afbreken

= 9.4 =
* Bugfix extra velden en verplicht beter evalueren
* Bugfix tonen van cursussen met elearning icm cursussen met datum in verleden in het zelfde overzicht

= 9.3 =
* Bugfix2: Cursus met Elearning wordt getoond indien:
  - startdatum van Elearning valt in de periode: vandaag - 1 jaar
    en de optionele einddatum valt in de periode: vandaag of later of niet is gezet
     (in Planaday)

= 9.2 =
* Bugfix: Cursus met Elearning wordt getoond indien:
  - startdatum van Elearning valt in de periode: vandaag - 1 jaar
    en de optionele einddatum valt in de periode: vandaag of later of niet is gezet
     (in Planaday)

= 9.1 =
* Bugfix bij eerste dagdeel indien Elearning geen datum gevuld

= 9.0 =
* Bugfix bij (Ideal) betalingen moet ook BTW worden meegerekend

= 8.9 =
* Bugfix op database functie

= 8.8 =
* Bugfix extra velden op de juiste manier evalueren

= 8.7 =
* Kleine fix voor spaties
* Upruimen scripts voor updates onder 8.x

= 8.6 =
* Nieuw: Extra (vrije) velden die beschikbaar zijn gesteld kunnen uitgevraagd worden bij de deelnemer in boekingformulier
  - Let op: je kunt maar 1 deelnemer per keer laten boeken (geen deelnemers toevoegen)
  - Zie ook: https://planaday.freshdesk.com/nl/support/solutions/articles/11000119045-meer-informatie-uitvragen-in-boekingformulier-in-wordpress-op-website
* Bugfix; {naam} geeft nu ook initialen en tussenvoegsels mee (terug) in pagina en mail
* Bugfix; adres uitvragen (optioneel) werd niet altijd getoond

= 8.5 =
* Kostenplaats kan nu optioneel verplicht worden uitgevraagd indien deze actief is bij cursist op boekingformulier
* Bugfix kostenplaats werdt niet altijd doorgegeven aan Planaday

= 8.4 =
* Prijs kan optioneel verborgen worden op cursusdetail-pagina

= 8.3 =
* Instelbaar dat enkel particuliere inschrijvingen mogelijk zijn
* De omschrijving van de cursus (pagina detail) indien 'tonen = ja' is verplaatst onder de iconen
* Indien een betaling is gelukt via Paytium/Mollie, de boeking in Planaday ook op betaald zetten
* Instelbaar dat als betaling niet is gelukt, deze ook geannuleerd (is verwijderd) wordt in Planaday
* Bugfix dat bij 'bedrijf' niet meer cursisten toegevoegd konden worden indien cursus geen attributen
  of geen stap bevatte
* Meer teksten zijn aanpasbaar via 'vertalingen'. In welke taal je ook wil
  Tip: wij raden 'Loco translate' aan als plugin

= 8.2 =
* Vertalingen verplaatst naar standaard folder van Wordpress

= 8.1 =
* Geoptimaliseerd tot php 8.1

= 8.0 =
* Bugfix kosten hoger dan 1000 werkte niet

= 7.9 =
* in 'Tekst geen cursussen' is nu HTML mogelijk zodat je kunt verwijzen naar een pagina oid
* Optioneel ook mogelijk om mail van aanvraag te mailen naar bedrijf (standaard staat dit uit)
  Deze mail kan zelf ook worden 'opgemaakt' met enkele velden
* Indien cursus 'stap' heeft, dan wordt vraag gesteld of zij hierin willen meedoen, deze komt dan mee met de boeking
  Hierin worden extra velden (verplicht) uitgevraagd, zoals adres en geboortedatum
  Andere limieten zijn dat enkel als particulier kan worden aangemeld en per stuk

= 7.8 =
* In pad-title margin: 10px verwijderd
* Toon komma in prijs ipv punt

= 7.7 =
* Meer divs om velden heen voor juiste opmaakmogelijkheden

= 7.6 =
* Meer optionele velden uitvragen bij boeken van cursus, standaard uit
  - roepnaam
  - meisjesnaam
  - interne referentie (wordt getoond bij aanvragen en bij cursist in de cursus)
* Bugfix opmaak

= 7.5 =
* Meer divs om velden heen voor juiste opmaakmogelijkheden

= 7.4 =
* Bugfix bij installatie

= 7.3 =
* Bugfix startgarantie tonen in overzichten op de juiste manier

= 7.2 =
* Bugfix overzicht cursisten die waren aangemeld stonden soms niet in de mail naar opleider
* Instelling toegevoegd waarin cursussen met enkel Elearning standaard worden genegeerd
* Dagdelen in verleden bij een cursus met Elearning wordt tekst niet getoond 'Dagdelen in verleden'
* Bugfix API-attributen indien verplicht ook verplicht maken

= 7.1 =
* Bugfix overzicht cursisten die waren aangemeld stonden soms niet in de mail naar opleider
* Ook emailadres van bedrijf / cursist worden in Email getoond

= 7.0 =
* Mail naar bedrijf toegevoegd bij cursus aanvraag
* Overzicht van cursisten toegevoegd aan mail naar opleider en bedrijf
* Betaal informatie kan optioneel worden opgenomen in mail en op scherm (resultaat) indien betalingen actief

= 6.9 =
* Indien betaald via iDeal (via Paytium), dan ook betaald zetten in Planaday zelf
* Resultaatpagina na betaling gelijk getrokken met 'gewone' paginatekst of redirect
* Meer velden toegevoegd voor E-mail, teksten op pagina indien dit gekozen is
* Persoonsgegevens cursist ook in mail naar opleider zetten
* Indien adresgegevens worden uitgevraagd van cursist, deze ook verplicht maken
* Als bedrijf met meerdere cursisten wordt aangemeld, dan wordt enkel de opleider gemaild (niet de cursisten)

= 6.8 =
* Huisnr extensie mag langer zijn dan 6 tekens bij 'locaties' in database
* CSS is totaal opgeruimd en gebruiker heeft meer mogelijkheden om zelf nog dingen aan te passen
  Zo zijn naam met initialen, adresgegevens gegroepeerd in boekingformulier

= 6.7 =
* Optioneel wordt E-learning wel meegeteld als 'dagdeel'

= 6.6 =
* Optioneel kun je ook financiele gegevens uitvragen bij bedrijf zoals adres en contactpersoon

= 6.5 =
* Bugfix tonen van financiele informatie in overzichten 'luisterde' niet naar instellingen

= 6.4 =
* Bugfix velden werden onterecht rood (formerror) als formulier nog niet gepost was

= 6.3 =
* Bugfix juist tonen api-attributen in boekingform indien actief
* Indeling van 'tabs' aan linkerzijde verbeterd
* Pagina's beter ingedeeld
* Alle pagina's en shortcodes verbeterd
* Dagdelen worden nu op volgorde van datum gesorteerd
* Indien cursus E-learning, dan wordt deze niet als 'eerste datum' getoond in overzichten en detailpagina
* Widget toont nu ook juiste datum van eerste dagdeel

= 6.2 =
* Zoeken verbetering formulier

= 6.1 =
* Bugfix datum goed tonen
* Bugfix defaults werden niet altijd goed uitgelezen

= 6.0 =
* Bugfix redirect naar 'bedankt' pagina gefixt
* Verbetering: Indexen op database gezet
* Verbetering: Ophalen cursussen (eerst updaten, dan oude verwijderen)
* Verbetering: Enkel 'open' cursussen ophalen via API
* Bugfix calender

= 5.5 + 5.6 + 5.7 =
* Support menu item toegevoegd & bestanden toegevoegd voor support

= 5.4 =
* Contactformulier aangepast volgorde velden

= 5.3 =
* Bugfix indien betalingen actief was kwam er een dubbele boeking als Paytium niet geinstalleerd was

= 5.2 =
* Remove planaday-bootstrap.css
* Bugfix db charset

= 5.1 =
* Bugfix css

= 5.0 =
* Tussenvoegsel toegevoegd aan boekingsformulier
* Bugfix bij geen API-attributen wel extra deelnemer kunnen toevoegen

= 4.9 =
* API-attributen geimplementeerd waarmee eigen velden gedefinieerd kunnen worden en 'meegeboekt' kunnen worden bij
  een boeking.

= 4.7 en 4.8 =
* Tijden op juiste manier tonen op cursus detailpagina & overzichten (uu:mm)

= 4.6 =
* Tijden op juiste manier tonen in schermen

= 4.5 =
* Functies opruimen en betere opbouw voor tabellen-reload

= 4.4 =
* Toon ook opties bij database (overzicht)
* Alter table werkt nu ook voor Mysql 8.0+

= 4.3 =
* Optioneel wordt rekening gehouden met opties voor beschikbare plaatsen

= 4.0.2 =
* Betere database update/upgrade functie

= 4.0.1 =
* Afbeelding optioneel kunnen tonen in bepaalde overzichten en op detailpagina (standaard = nee)
  - Voor overzicht is css: .pad-imagecover-overzicht
  - Voor detail is css: .pad-imagecover-detail
  Afbeelding in Planaday moet de naam 'wordpress' zijn en enkel de eerste wordt getoond
* In Planaday standaard stylesheet voor Wordpress is overflow: auto; toegevoegd voor pad-course,
  zodat dit ook goed werkt met afbeeldingen
* CSS heeft meer uitleg en voorbeelden
* Widget cursusdetails verbeterd, wel alle thema's en plugins updaten!
* Nieuw overzicht toegevoegd (nieuwe layout) (courselistblock2)
* Database views om direct in te zien wat er werkelijk in de database staat
* Database queries geoptimaliseerd voor Mysql 8.0

= 4.0.0 =
* Optimalisatie middels opslaan in database voor snelheidsverbetering op website
* Instellingen nu per tab instelbaar, schermen zijn nu een stuk overzichtelijker
* Voorkeur bedrijf/particulier verbeterd via Jquery (javascript)
* Optie voor code95 indien cursus code95 bevat meegeven in booking
* Optie voor soob indien cursus soob bevat meegeven in booking
* Geboorteplaats deelnemer kan worden uitgevraagd
* Initialen van deelnemer worden nu ook uitgevraagd
* Mail naar cursist is meer uitgebreid met eigen velden
* Tekst voor bevestiging mail naar cursist/deelnemer kan zelf worden ingesteld
* Nettere CSS voor invulvelden in bookingform
* Alle teksten zijn vertaalbaar (loco translate)
* Optioneel eigen pagina maken met bedankttekst en eigen opmaak
* Bugfix bij posten indien particulier bedrijf verplicht werd gesteld
* Velden taal en niveau toegevoegd in API en Wordpress. Optioneel te tonen in details cursus
* Bugfix: Tekst van omschrijving op juiste plaats gezet (tab: Algemeen)
* Bugfix: Limiet van omschrijving in overzichten kan ingesteld worden
* Verbetering: Alle instellingen worden beheerd via een eigen 'tabblad'
* Omschrijving wordt nu altijd getoond zoals deze is opgemaakt in Planaday bij 'omschrijving'
* Optioneel tonen van omschrijving van dagdeel op detailpagina
* Tijden in overzichten en op detailpagina worden nu netjes getoond zoals tijden getoond moeten worden
* Iedere label heeft nu eigen div eromheen, zodat je deze zelf kunt 'opmaken in css'
* Optioneel labels tonen in Widget welke je zelf kunt plaatsen op een detailpagina van cursus
* Bugfix voor betalingen met Mollie/Paytium gefixt (testmodus instelling)
* Zoeken naar cursus uitgebreid en verbeterd met meerdere opties en zoekinstellingen
* Bugfix tonen geen volle cursussen indien opgegeven bij instellingen bij coursecalender
* Geboortedatum op Nederlandse manier opgeven bij boeken

= 3.2.2 =
* Fix 'deelnemer toevoegen' bij voorkeur bedrijf tonen in bookingform

= 3.2.1 =
* Prijs met 2 cijfers achter de komma tonen

= 3.2.0 =
* Plugin update geschikt naar Wordpress 5.6
* Zoeken naar cursus met [coursesearch] gefixt
  Details van zoekopdracht idem als courselistblock
  Hierdoor ook probleem met 'Json request' opgelost

= 3.1.1 =
* Huisnummer toevoeging uitvragen is optioneel
* Voorkeur particulier of bedrijf kunnen opgeven
* Optioneel ook labels tonen bij dagdelen
* Optioneel icoon tonen indien cursus SOOB bevat, standaard nee in overzicht en detailpagina
* Optioneel icoon tonen indien cursus Code95 bevat, standaard nee in overzicht en detailpagina
* Toon labels bij dagdeel indien dagdeellijst bij overzicht ook zichtbaar (standaard uit)
* Optioneel cursist ook mailen (als Emailadres is opgegeven en actief)
* Indien dagdeel E-learning, dan geen tijden laten zien

= 3.0.0 =
* Plugin getest tot wordpress versie 5.5
* Toon optioneel een icon indien cursus elearning bevat (in overzichten en detail)
* Toon optioneel een icon indien dagdeel van cursus elearning (in cursus detail) bevat
* Indien dagdeel elearning bevat is tekst anders (van tot tijd is verwijderd)
* Meerdere velden zijn optioneel te tonen en uit te vragen (standaard uit)
* Meerdere velden zijn optioneel verplicht te stellen (standaard uit)
* Optioneel 'algemene voorwaarden' verplicht stellen
* Meerdere cursisten kunnen uitvragen en kunnen boeken bij keuze van bedrijf
* Optioneel labels kunnen tonen bij een cursus indien aanwezig (standaard uit)
* Alle overzichten zijn uit te breiden met zoekactie enkel op één label (gebruik wel andere zoekterm bij shortcode!
* Alle 'losse' shortcodes welke beschikbaar zijn ook vermeld bij 'welkom' in plugin
* Meer voorbeelden voor CSS toegevoegd
* Meer elementen toegankelijk voor CSS en input velden generieker gemaakt
* Optioneel adres voor particulier (cursist) kunnen uitvragen, (standaard uit)
* Optioneel dagdelen lijst kunnen tonen in blocklist (overzicht) met optioneel de locatie (standaard uit)
* Optioneel toelichting bij prijs geven indien ook aanwezig in Planaday bij financieel (standaard uit)

= 2.5.1 =
* Emailadres voor facturatie kunnen uitvragen optioneel (kan enkel als men als bedrijf inschrijft)
* Opmerkingen veld optioneel kunnen uitvragen
* Bij debug informatie meer informatie over data en responses

= 2.5.0 =
* Nieuwe shortcodes toegevoegd
  Deze shortcodes kunnen overal in de website aangeroepen worden met de unieke cursusid
  [pad-name id=x] geeft naam van cursus
  [pad-dates id=x] geeft lijst met datums van dagdelen
  [pad-dates-locations id=x] geeft lijst met datums van dagdelen inclusief locaties
  [pad-price id=x] geeft prijs per persoon van cursus
  [pad-price-remark id=x] geeft opmerkingen bij prijs
  [pad-button id=x] geeft een button met link naar de juiste cursus

= 2.4.0 =
* Teksten kunnen wijzigen van inschrijfbutton
* Teksten kunnen wijzigen van titel
* Teksten kunnen wijzigen van kostenplaats (in boekingformulier)
* Optioneel tonen van toelichting bij prijzen (bij financieel in Planaday)
* Optioneel uitvragen van functie bij boekingformulier
* Optioneel bezoeker akkoord laten gaan met algemene voorwaarden in boekingformulier

= 2.3.1 =
* Verwijder uitput api-calls

= 2.3.0 =
* Bugfix coursekalander (jquery) gefixt. Kalender wordt nu weer goed getoond
* Mogelijkheid om materialen mee te bestellen bij booking
* Geslacht kan worden opgegeven in bookingformulier
* Personeelsnummer kan optioneel worden uitgevraagd
* Kostenplaats kan optioneel worden uitgevraagd
* Bij courslisttable en courslistlist wordt nu ook tekst getoond als er geen cursussen zijn voor de gekozen datum selectie
* Enkele instellingen en uitleg eenvoudiger gemaakt

= 2.1.2 =
* Instellen of 'dagdelen' of 'dagen' wordt getoond als tekst

= 2.1.1 =
* FAQ aangepast
* Tested (Wordpress versie) tot versie bijgewerkt tot 5.4
* Velden zoals telefoonnummer en geboortedatum optioneel verplicht kunnen maken bij boeken

= 2.1.0 =
* Extra optie om cursus niet te tonen als er dagdelen van die cursus in het verleden liggen

= 2.0.3 =
* Remove a lot of html css styles in Planaday.css

= 2.0.2 =
* Remove a lot of html css styles in Planaday.css
* Remove 'laffe' jquery descript

= 2.0.1 =
* Bugfixes non existing class

= 2.0 =
* Refactoring files, functions etc
* Bugfix tonen cursusdetail
* Enkel open cursussen tonen, ook in calendar
* iDeal implementatie middels Paytium en Mollie (optioneel)
* Diversen verbeteringen bij boeken cursus
* Bedanktekst niet meer via pagina, maar eigen veld in plugin
* Aparte bedanktekst na online betaling
* Javascript beter en efficienter laden
* Meerdere checks op intropagina om te controleren of alles compleet is
* Intropagina verbeterd en meerdere tabs in je plugin voor overzicht
* Check op email is verbeterd

= 1.9.3 =
* Bugfix bij maken van booking, was de url niet altijd juist
* Bugfix de widget bij cursusdetails wordt op juiste manier getoond indien actief
* CSSeditor toont ook fouten in CSS

= 1.9.2 =
* Radio selectie wordt weer getoond
* Slug gewijzigd voor tonen van cursus ivm problemen met andere plugins met dezelfde slug (course)
  BELANGRIJK: wijzig [course] op jouw cursus/detailpagina in [pad-course]!
* CSS editor toegevoegd voor het kunnen stylen en kleuren van diversen onderdelen

= 1.9.1 =
* Debug uitzetten

= 1.9.0 =
* Startgarantie van cursus optioneel tonen in lijst en/of details
* Niet goed geld terug garanatie van cursus optioneel tonen in lijst en/of details

= 1.8.3 =
* Toon cursus met boekingsformulier op juiste manier en stabiel met versie 5.2.2

= 1.8.2 =
* Toon zoekformulier op juiste manier en stabiel met versie 5.2.1

= 1.8.1 =
* Plugin stabiel voor wp-versie tot 5.2.1

= 1.8.0 =
* Bugfix tonen juiste content na juiste boeking van cursus (de content van bedanktpagina wordt getoond ipv
boekingsformulier
* Standaard keuze bij particulier en/of bedrijf bij geen post
* Toevoeging van titel 'dagdelen' bij tonen van dagdelen indien actief (style: 'pad-detail-dayparts-title')
* Indien locatie tonen 'aan' staat bij dagdelen voor details, dan wordt nu de stad getoond
* Plugin geschikt en getest voor wp-versie tot 5.2.1

= 1.7.0 =
* Toon cursus en bookingformulier ook bij een foutieve post
* Toon locaties bij zoeken indien zo ingesteld!

= 1.6.2 =
* Debug message niet tonen

= 1.6.1 =
* Bugfix, tonen van button. Sla voor de zekerheid alle instellingen nog een keer op!
* Juist mailen van open aanvraag

= 1.6.0 =
* Plaats wordt getoond indien gewenst in cursusoverzicht
* Tekst kunnen opgeven indien er geen ingeplande en beschikbare cursussen zijn
* Er wordt mail gestuurd bij iedere aanvraag indien Emailadres ingevuld is.

= 1.5.0 =
* Verplicht veld wordt met enkel een * aangegeven in boekingsformulier
* In dienst van bedrijf vraag voorzien van id (company_choice).
* Opmaak voor omschrijving van cursus wordt niet meer gewijzigd
* Bij foutieve ingave van boekingformulier, werd formulier niet meer getoond.
* Handleiding voor installatie uitgebreid in Wordpress plugin directory

= 1.4.0 =
* Afbeeldingen voor Plugin juiste formaat toegevoegd

= 1.3.0 =
* Bedanktpagina wordt goed getoond na een juiste boeking
* Afbeeldingen voor Plugin toegevoegd

= 1.2.0 =
* Fix boooking with API

= 1.1.0 =
* Show the coursedetai
