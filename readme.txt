================================================================================
    ovmlog - logtool voor operationeel verkeersmanagement
                                      README
================================================================================
ovmlog is een webgebaseerde logtool voor operationeel verkeersmanagement.
Het biedt voorzieningen voor registratie van acutele incidenten door
wegverkeersleiders en geplande wegwerkzaamheden/evenementen.

Er is een module aanwezig om een afdrukweergave te maken voor gebruik bij
periodieke evaluaties.

ovmlog is ontwikkeld door Gemeente Den Haag, afdeling 
Bereikbaarheid en Verkeersmanagement en aldaar geprogrammeerd door Jasper Vries.
De broncode is als open source software beschikbaar gesteld, om het voor alle 
wegbeheerders mogelijk te maken om gebruik te maken van deze ontwikkeling. Het 
formele auteursrecht berust bij de Gemeente Den Haag.

Vanwege de leeftijd van de software is herimplementatie in een nieuwe codebasis
aan te bevelen wanneer deze software elders wordt ingezet.


================================================================================
0. Inhoudsopgave
================================================================================

1. Systeemvereisten en benodigdheden
2. Installatie
3. Licentie
4. Verkrijgen van de broncode


================================================================================
1. Systeemvereisten en benodigdheden
================================================================================

De backend is geschreven in PHP (5.3+) en gebruikt een MySQL (5+) of 
MariaDB (5+) DBMS.


================================================================================
2. Installatie
================================================================================

Pas db.inc.php aan.

Voer install.php uit. Deze kan zowel vanuit een webbrowser worden aangeroepen
als via de commandline worden uitgevoerd.


================================================================================
3. Licentie
================================================================================

De broncode van assetwebsite is vrijgegeven onder de voorwaarde van de 
GNU General Public License versie 3 of hoger. Voor gebundelde libraries kunnen 
andere licentievoorwaarden van toepassing zijn. Zie hiervoor de documentatie in 
de betreffende submappen.

Met uitzondering van gebundelde libraries is voor assetwebsite het volgende van 
toepassing:

    assetwebsite - viewer en aanvraagformulier voor verkeersmanagementassets
    Copyright (C) 2013, 2020 Gemeente Den Haag, Netherlands
    Developed by Jasper Vries
 
    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.
 
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
 
    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <https://www.gnu.org/licenses/>.


================================================================================
4. Verkrijgen van de broncode
================================================================================

De broncode van ovmlog is gepubliceerd op GitHub:
https://github.com/VCDH/ovmlog/
