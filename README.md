RRZE-Publikationen
==================

WordPress-Plugin
----------------

Version 1.0

Erfassung und Darstellung von Publikationen, ergänzt um grundlegende Felder für den Verkauf (Preis + Vorrätig)

### Technische Details
* Custom Post Type "Publikation" mit folgenden Feldern:
  * Titel -> Titel der Publikation
  * Text -> Inhalt / Abstract
  * Meta-Box 'Daten zur Publikation':
    * Autor(en)
    * Ort
    * Verlag
    * Erscheinungsjahr
	* ISBN
    * Weitere Informationen
  * Meta-Box 'Verwaltung':
    * Preis
    * Vorrätig
* Shortcodes zur Einbindung in Seiten
* Hierarchische Schlagwörter
* Für Übersetzung vorbereitet (Standardsprache: Deutsch)

#### Shortcode-Definition:

##### Aufruf
`[publications]`

##### Parameter
allgemein:
- `show="list"`: Liste aller erfassten Publikationen (Standardeinstellung)
- `show="table"`: tabellarische Übersicht
- `show="single"`: einzelne Publikation (zusätzlich Parameter "id" erforderlich)
- `link="yes"` / `"no"`: Titel der Publikation wird mit der Einzelansicht verlinkt (Standardeinstellung: "yes")
- `show_sold_out="yes"` / `"no"`: nicht verfügbare Publikationen anzeigen (s. Meta-Box 'Verwaltung') (Standardeinstellung: "yes")
- `tags="schlagwort1|schlagwort2|schlagwort3"`: nur Publikationen mit diesen Schlagwörtern anzeigen (einzelne Schlagwörter durch Pipe ("|") getrennt)

bei Listen und Tabellen:
- `orderby="year"`: nach Jahren absteigend sortiert (Standardeinstellung)
- `orderby="author"`: alphabetisch nach erstem Autorennamen sortiert
- `orderby="title"`: alphabetisch nach Titel sortiert

bei Tabellen:
- `cols="title|publisher|price|availible|updated"`: Liste und Reihenfolge der Felder für die Tabelle (einzelne Schlagwörter durch Pipe ("|") getrennt)<br />
  verfügbare Felder: author, title, location, publisher, isbn, price, availible, updated<br />
  Standardeinstellung: "author|title|publisher|price|availible"

bei Einzelansicht (single):
- `id="5415"`: ID der Publikation für die Einzelansicht (steht beim Bearbeiten-Fenster in der Meta-Box 'Daten zur Publikation)

### Sonstige Infos
#### Hauptansprechpartner
RRZE-Webteam
http://blogs.fau.de/webworking/