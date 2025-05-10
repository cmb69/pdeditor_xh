# Pdeditor_XH

Pdeditor_XH ermöglicht das Betrachten und Bearbeiten der Pagedata im
Backend von CMSimple_XH. Es bietet darüber hinaus eine Diagnose
bezüglich potentieller Desynchronisierung von Content und Pagedata (was unter
CMSimple_XH ≥ 1.6 niemals vorkommen sollte). Es ist als eine Alternative zur
manuellen Bearbeitung der Pagedata-Abschnitte in content.htm gedacht,
so dass es nur *von erfahrenen Anwendern verwendet werden sollte*, die genau wissen, was sie tun.
Es ist *kein Werkzeug für unerfahrene Anwender*! Diese sollten die Pagedata
ausschließlich über die Schnittstellen, die von den entsprechenden Plugins
angeboten werden (meist in einem Reiter über dem Editor), bearbeiten.

- [Voraussetzungen](#voraussetzungen)
- [Download](#download)
- [Installation](#installation)
- [Einstellungen](#einstellungen)
- [Verwendung](#verwendung)
- [Problembehebung](#problembehebung)
- [Lizenz](#lizenz)
- [Danksagung](#danksagung)

## Voraussetzungen

Pdeditor_XH ist ein Plugin für [CMSimple_XH](https://cmsimple-xh.org/de/).
Es benötigt CMSimple_XH ≥ 1.7.0 und PHP ≥ 7.1.0.
Pdeditor_XH benötigt weiterhin [Plib_XH](https://github.com/cmb69/plib_xh) ≥ 1.8;
ist dieses noch nicht installiert (siehe `Einstellungen` → `Info`),
laden Sie das [aktuelle Release](https://github.com/cmb69/plib_xh/releases/latest)
herunter, und installieren Sie es.

## Download

Das [aktuelle Release](https://github.com/cmb69/pdeditor_xh/releases/latest)
kann von Github herunter geladen werden.

## Installation

Die Installation erfolgt wie bei vielen anderen CMSimple_XH-Plugins auch.

1. Sichern Sie die Daten auf Ihrem Server.
1. Entpacken Sie die ZIP-Datei auf Ihrem Computer.
1. Laden Sie den gesamten Ordern `pdeditor/` auf Ihren Server in den
   `plugins/` Ordner von CMSimple_XH hoch.
1. Vergeben Sie Schreibrechte für die Unterordner `css/` und
   `languages/`.
1. Navigieren Sie zu `Plugins`→ `Pdeditor` im Administrationsbereich,
   und prüfen Sie, ob alle Voraussetzungen für den Betrieb erfüllt sind.

## Einstellungen

Die Konfiguration des Plugins erfolgt wie bei vielen anderen
CMSimple_XH-Plugins auch im Administrationsbereich der Website
unter `Plugins` → `Pdeditor`.

<!-- Sie können die Original-Einstellungen von Pdeditor_XH unter `Konfiguration`
ändern. Beim Überfahren der Hilfe-Icons mit der Maus werden Hinweise zu den
Einstellungen angezeigt. -->

Die Lokalisierung wird unter `Sprache` vorgenommen. Sie können die
Zeichenketten in Ihre eigene Sprache übersetzen, falls keine entsprechende
Sprachdatei zur Verfügung steht, oder sie entsprechend Ihren Anforderungen
anpassen.

Das Aussehen von Pdeditor_XH kann unter `Stylesheet` angepasst werden.

## Verwendung

Pdeditor_XH wird ausschließlich im Administrationsbereich verwendet.
Gehen Sie zu `Plugins` → `Pdeditor` → `Pagedata`.

<img src="https://raw.githubusercontent.com/cmb69/pdeditor_xh/refs/heads/master/help/admin.png" alt="Screenshot des Pagedata Editors" style="width: 100%">

Direkt unter dem Pluginmenü befindet sich die Attribut-Auswahlliste und ein
Schalter zum Entfernen des gewählten Attributs und all seiner Werte aus den
Pagedata.

Das voreingestellte Attribut ist `url`, das eine Prüfung auslöst, ob die
URLs, die in den Pagedata gespeichert sind, mit denen der entsprechenden Seiten
im Content übereinstimmen. Wenn nicht, wird ein Warnhinweis angezeigt. Beachten
Sie, dass diese Warnung nicht unbedingt bedeutet, dass die Pagedata fehlerhaft
sind, sondern Sie zeigt lediglich *eine mögliche Korruption* der Pagedata
an, besonders, wenn *alle* Seiten ab einer bestimmten Seiten markiert
sind, wie in obigem Screenshot gezeigt. Dieser Screenshot wurde tatsächlich mit
einer absichtlich korrumpierten Pagedata-Datei aufgenommen. Wenn Sie die
Seitenüberschriften mit den URLs vergleichen, dann sehen Sie, dass die Pagedata
um eine Seite nach unten verschoben wurden. In diesem Fall müssen Sie die
Pagedata manuell prüfen und reparieren, oder die letzte korrekte Sicherungskopie
wieder herstellen.

Nachdem Sie ein Attribut ausgewählt haben, können Sie die Werte dieses
Attributs aller Seiten ansehen und bearbeiten.

## Problembehebung

Melden Sie Programmfehler und stellen Sie Supportanfragen entweder auf
[Github](https://github.com/cmb69/pdeditor_xh/issues)
oder im [CMSimple\_XH Forum](https://cmsimpleforum.com/).

## Lizenz

Pdeditor_XH ist freie Software. Sie können es unter den Bedingungen
der GNU General Public License, wie von der Free Software Foundation
veröffentlicht, weitergeben und/oder modifizieren, entweder gemäß
Version 3 der Lizenz oder (nach Ihrer Option) jeder späteren Version.

Die Veröffentlichung von Pdeditor_XH erfolgt in der Hoffnung, daß es
Ihnen von Nutzen sein wird, aber *ohne irgendeine Garantie*, sogar ohne
die implizite Garantie der *Marktreife* oder der *Verwendbarkeit für einen
bestimmten Zweck*. Details finden Sie in der GNU General Public License.

Sie sollten ein Exemplar der GNU General Public License zusammen mit
Pdeditor_XH erhalten haben. Falls nicht, siehe <https://www.gnu.org/licenses/>.

Copyright © Christoph M. Becker

## Danksagung

Das Plugin-Icon wurde von [schollidesign](https://schollidesign.deviantart.com/) entworfen.
Vielen Dank für die Veröffentlichung unter GPL.

Vielen Dank an die Community im
[CMSimple_XH-Forum](https://www.cmsimpleforum.com/)
für Tipps, Vorschläge und das Testen.

Und zu guter Letzt vielen Dank an
[Peter Harteg](https://www.harteg.dk/), den „Vater“ von CMSimple,
und alle Entwickler von [CMSimple_XH](https://www.cmsimple-xh.org/de/),
ohne die dieses phantastische CMS nicht existieren würde.
