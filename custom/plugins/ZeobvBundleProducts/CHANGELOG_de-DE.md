# 2.4.23

- Der Fix Menge Auswahl in Kategorie-Listen mit zenitPlatformGravity Thema.

# 2.4.22

-   Das Problem mit der Rabattberechnung bei Bundles wurde behoben, indem die Reihenfolge der Warenkorbverarbeiter in cart.xml auf 4950 statt 4850 gesetzt wurde.

# 2.4.21

-   Die Korrektur stellt sicher, dass der Listenpreis bei der Preisaggregation nun korrekt berechnet und zugewiesen wird.
-   Verhindert den Aufruf einer Mitgliedsfunktion getExtension() auf null, indem sichergestellt wird, dass $product gültig ist, bevor auf seine Methoden zugegriffen wird.

# 2.4.20

-   Es wurde eine Konfiguration hinzugefügt, um den Preis für untergeordnete Bundle-Artikel in DHL-Versandetiketten auf 0,01 zu setzen.

# 2.4.19

-  Verbessertes Bundle-Plugin konnte auf der Ebene des Vertriebskanals nicht deaktiviert werden.
-  Plugin-Anleitung“ und ‚FAQ‘-Schaltflächen auf der Plugin-Konfigurationsseite hinzugefügt

# 2.4.18

-   Ein Problem wurde behoben, bei dem das Bundle-Plugin und das Plugin für Abonnement-Artikel in Konflikt gerieten, da beide die CartItemAddRoute dekorierten.

# 2.4.17

-   Unterstützung für Api-Bestellungen durch Anreicherung der Bestellung mit den Bündel-Unterpositionen für Bündelprodukte über Kanäle wie Channable.

# 2.4.16

-   Es wurde ein Problem behoben, bei dem Bundle-Artikel fälschlicherweise Rabatte erhielten, wenn der Preismodus „Summe der Preise der Produkte im Bundle zur Berechnung des neuen Bundle-Preises“ verwendet wurde.

# 2.4.15

-   Es wurde ein Problem behoben, bei dem das Bundle im Frontend doppelt berechnet wurde, was bei Verwendung des Custom Product Plugins zu falschen Preisen führte.

# 2.4.14

-   Aktualisierte Kompatibilität mit LineItemFactory

# 2.4.13

-   Ein Problem in MailServiceDecorator wurde behoben, bei dem array_filter() einen TypeError verursachte, wenn lineItems null war. Es wurden Überprüfungen hinzugefügt, um sicherzustellen, dass lineItems standardmäßig ein leeres Array ist, wenn es nicht gesetzt oder null ist, und dass sein Typ vor der Verarbeitung überprüft wird.
-   Behobener TypeError in DeliveryInformation aufgrund von null für freeDelivery sorgt dafür, dass getShippingFree() einen Boolean zurückgibt.

# 2.4.12

-   Die Kompatibilität mit SwagCustomisedProducts wurde entfernt.

# 2.4.11

- Fehlende deliveryInformation für untergeordnete Produktbündel hinzugefügt

# 2.4.10

- Behebung kritischer Frontend-Fehler bei der Preisberechnung für Produktbündel.

# 2.4.9

-   Das Problem, dass benutzerdefinierte Felder für Bündelartikel im Einkaufswagen nicht korrekt angezeigt wurden, wurde behoben.

# 2.4.8

-   Das Feld "Closeout" wurde zum Abschnitt "Bundle-Verfügbarkeit" hinzugefügt, um einen Einblick in den Rückstandsstatus eines Bundle-Produkts zu erhalten. Beachten Sie, dass dieses Feld automatisch auf der Basis der Artikel im Bundle gesetzt wird und ein Bundle-Produkt nur dann auf "closeout" gesetzt wird, wenn es aufgrund der Verfügbarkeit und des Rückstandsstatus seiner Artikel nicht verfügbar ist.
-   Aktualisierung des is_closeout-Feldes in der Datenbank von Bundle-Produkten, wenn sich die Verfügbarkeit ändert, um den Rückstandsstatus eines Bundle-Produkts genau wiederzugeben und eine bessere Zusammenarbeit mit bestimmten Shopware-Funktionen zu ermöglichen, die direkt die Datenbank abfragen.

# 2.4.7

-   Die Kompatibilität mit SwagCustomisedProducts wurde wiederhergestellt.
-   Es wurde ein Problem behoben, bei dem der verfügbare Bestand eines Artikels unmittelbar nach einer Bestellung um 2 statt um 1 reduziert wurde.

# 2.4.6

-   Es wurde ein neuer Befehl hinzugefügt, um den berechneten Bestand für Bundle-Produkte mit der Datenbank zu synchronisieren.
-   Der neue Befehl deaktiviert die Pickware-Bestandsverwaltung für alle Bundle-Produkte, um Versandprobleme zu vermeiden.
-   Listener für Product Safe Event in der Administration hinzugefügt, um die Pickware-Bestandsverwaltung für Bundle-Produkte zu deaktivieren.
-   Es wurden neue Felder zur Bundle-Konfiguration hinzugefügt, um einen Einblick in den tatsächlichen Bestand und den verfügbaren Bestand eines Bundle-Produkts zu geben, da Pickware die Bestandsfelder entfernt, wenn die Pickware-Bestandsverwaltung deaktiviert ist.

# 2.4.5

-   Das Flag "Verfügbar" wurde bei Bundle-Produkten nicht mehr aktualisiert.

# 2.4.4

-   Das Picklistendokument wurde so geändert, dass die Positionen nach einer Änderung durch Pickware korrekt angezeigt werden.

# 2.4.3

-   Die Warnung für einen undefinierten Array-Schlüssel 'salesChannelId' wurde behoben.

# 2.4.2

-   Konfigurationsoption zum Ausblenden von Artikeln innerhalb eines Pakets in der Bestellbestätigungsmail behoben.

# 2.4.1

-   Es wurde eine zusätzliche Option zur Konfiguration der Artikelauswahl hinzugefügt, um einige Produkte als nicht-optional zu kennzeichnen.

# 2.4.0

-   Option hinzugefügt, um die Mengenauswahl für bestimmte Bündelartikel im Schaufenster zu deaktivieren.

# 2.3.3

-   Kompatibilitätsproblem mit der Back in Stock-Erweiterung behoben.

# 2.3.2

-   Das Problem mit der Erstellung von Rechnungen oder Dokumenten wurde behoben

# 2.3.1

-   Das Problem des Ausblendens von untergeordneten Artikeln des Typs Produkt in der Rechnung wurde behoben.

# 2.3.0

-   Option hinzugefügt, um die Bundles, in denen ein Produkt enthalten ist, auf der Produktdetailseite als Cross-Selling-Karussell zu präsentieren. Tab-Label und Darstellung können in der Konfiguration der Erweiterung konfiguriert werden.

# 2.2.2

-   Es wurde ein Problem behoben, bei dem der Artikeltyp im Warenkorb nicht unterstützt wurde.

# 2.2.1

-   Es wurde ein Problem behoben, bei dem CbaxOrderConverterDecorator eine Abhängigkeit von einem nicht existierenden Dienst hatte.

# 2.2.0

-   Problem mit der Injektion von Abhängigkeiten behoben
-   Möglichkeit hinzugefügt, den Preisberechnungsmodus für jedes einzelne Bundle festzulegen
-   Fehler bei der Preisberechnung behoben, wenn bestimmte Bundle-Konfigurationen von der Auflistungsseite aus in den Warenkorb gelegt werden.

# 2.1.3

-   Kompatibilität mit 6.6.3 hinzugefügt

# 2.1.2

-   Bundle-Artikel werden jetzt im Warenkorb und an der Kasse ausgeblendet, wenn 'Produkte im Bundle im Schaufenster anzeigen' deaktiviert ist.

# 2.1.1

-   Fehler im Warenkorb bei Bundle-Produkten mit deaktivierter Option "Produkte im Bundle im Schaufenster anzeigen" behoben

# 2.1.0

-   **ATTENTION** - Großes Update
-   Es wurde eine neue Funktion hinzugefügt, mit der der Kunde im Schaufenster auswählen kann, welche Artikel er im Paket haben möchte. Nur verfügbar für den Berechnungsmodus "SUMME der Preise".
-   Es wurde eine neue Funktion hinzugefügt, mit der der Kunde die Anzahl der Artikel im Paket im Schaufenster ändern kann. Nur verfügbar für den Berechnungsmodus "SUMME der Preise".
-   Es wurde eine Funktion hinzugefügt, die es dem Kunden ermöglicht, zwischen Varianten eines Bündelartikels zu wechseln, indem er das übergeordnete Produkt der Varianten dem Bündel zuordnet. Nur für den Berechnungsmodus "SUMMEN-Preise" verfügbar.
-   Es wurde die Möglichkeit hinzugefügt, einen Rabatt auf Produktebene im Bundle festzulegen. Nur für den Berechnungsmodus "SUMMEN-Preise" verfügbar.
-   Verbesserte UX der Bundle-Konfiguration in der Administration.
-   Die Struktur der Bundle-Artikelübersicht wurde geändert, einige Klassen wurden refaktorisiert und die Übersicht wurde in die buy-widget-form.html.twig-Vorlagen verschoben, die nun sw_include verwenden, um unsere zeobv-bundle-products/components/buy-widget-form.html.twig einzubinden, um Code-Duplikationen zu vermeiden.
-   Einige Refactorings wurden durchgeführt, um die Komplexität und Lesbarkeit des Codes zu verbessern. Aufteilung des Bundle-Produkt-Sammlers und -Prozessors, um die Komplexität dieser Klassen zu reduzieren.
-   Verbesserter Stil der Bundle-Referenz auf der Bundle-Artikel-Produktseite
-   Verbessertes Snippet der Bundle-Referenz auf der Bundle-Artikel-Produktseite
-   Erweiterte Preise sind jetzt für Bundle-Produkte in Kombination mit dem SUMPreis-Modus deaktiviert, um unerwünschte Rabatte auf Rabatte zu verhindern.

# 1.7.8

-   Das Problem der Anzeige von Informationen zur Lieferung digitaler Produkte wurde behoben.

# 1.7.7

-   Das Problem des unbekannten Lagerortes in der Auswahlliste wurde behoben.

# 1.7.6

-   Es wurde eine Konfigurationsoption hinzugefügt, mit der die Menge von Bundle-Artikeln in der Tabellendarstellung der Produktdetailseite ausgeblendet werden kann.
-   Es wurde eine Konfigurationsoption hinzugefügt, um die Hintergrund- und Rahmenfarbe in der Hauptprodukt-Spezialkarte zu ändern.

# 1.7.5

-   Behoben: Das Bild der responsiven Vorlage fehlt, stattdessen wird ein leeres Feld angezeigt.

# 1.7.4

-   Der Fehler von TaxDetector wurde behoben.

# 1.7.3

-   seo url in Bundle-Produkt-Link auf Produkt-Detail-Seiten hinzugefügt.

# 1.7.2

-   Gelöst die Pickliste pdf nicht generiert.

# 1.7.1

-   Fehlendes Titel-Tag in Bundle-Info hinzugefügt.
-   Konfigurationsoption zum Ausblenden von Bundle-Unterpositionen in der Auftragsbestätigungsmail hinzugefügt.

# 1.7.0

-   Viele strukturelle Änderungen am Plugin zur Verbesserung der Leistung und Stabilität
-   Einige Snippets wurden verbessert, damit sie in der Konfiguration und im Schaufenster deutlicher werden
-   Optionale Bundle-Unterstützung für Shopping Experience-Vorlagen hinzugefügt
-   Verbessertes Design der optionalen Bundle-Checkbox
-   Einige Konfigurationsoptionen wurden verschoben, um mehr Sinn zu machen

# 1.6.6

-   Fehler bei der optionalen Bundle-Konfiguration behoben, der dazu führte, dass das Bundle in manchen Fällen nicht zum Warenkorb hinzugefügt wurde
-   Verbesserte Leistung für umfangreiche Google-Feeds und -Listings durch Reduzierung der Anzahl der Abfragen, die zum Abrufen der Bundle-Produktinformationen erforderlich sind

# 1.6.5

-   Fehler behoben, bei dem die Bündelartikel einen Vorlagenfehler verursachten, wenn kein Titelbild für das Produkt festgelegt wurde

# 1.6.4

-   Option hinzufügen, um Bündel optional anzubieten

# 1.6.3

-   Problem mit der Mehrwertsteuerberechnung bei Verwendung von Nettopreisen in Kombination mit erweiterter Preisgestaltung und höheren Mengen behoben

# 1.6.2

-   Problem mit der Mehrwertsteuerberechnung bei der Verwendung von Nettopreisen in Kombination mit der erweiterten Preisgestaltung behoben

# 1.6.1

-   Varianten-Details auf der Produkt-Detailseite anzeigen, wenn der untergeordnete Bundle-Artikel ein Variantenprodukt ist.

# 1.6.0

-   Responsives Design-Update für die Bündel-Unterprodukt-Tabelle auf der Produkt-Detailseite.

# 1.5.13

-   Konfigurationsoption für die Anzeige von Details zu Bündelprodukten im Popup-Fenster hinzugefügt.
-   Konfigurationsoption hinzugefügt, um Bundle-Produkte als nicht verfügbar zu markieren, wenn eines ihrer Kinder nicht verfügbar ist.
-   Konfigurationsoption hinzugefügt, um die Preise für Bündelpositionen auf der Rechnung auszublenden, wenn der Preis 0,0 ist.
-   Konfigurationsoption zum Ausblenden von Bündelunterpositionen auf Rechnungsdokumenten hinzugefügt.

# 1.5.12

-   Gelöst Vorhandene Bundle-Verbindungen löschen Zeit löscht alle Einträge aus dem Produkt.
-   Benutzerdefinierte Felddaten im Warenkorb hinzugefügt.

# 1.5.11

-   Kompatibilitäts-Patch für das Plugin zum Importieren von eBay-Bestellungen auf dem Marktplatz hinzugefügt.

# 1.5.10

-   Überarbeitete die Berechnung des Bündelprodukt-Fit-Preises, um genauer zu sein
-   Option hinzugefügt, um die Referenz auf das Bündelprodukt von der Produktdetailseite der Produkte innerhalb eines Bündels zu deaktivieren

# 1.5.9

-   Entfernt . in Vorlage

# 1.5.8

-   Patch für Leistungseinbußen bei Shops mit einer großen Anzahl von Produkten

# 1.5.7

-   Hinzugefügte neue Konfigurationsoption, die es Ihnen ermöglicht, die Anzahl der Verweise auf die Bündel, zu denen ein Produkt gehört, zu begrenzen, um die Leistung der Produktdetailseite zu schützen. Standardmäßig ist sie auf 10 eingestellt.

# 1.5.6

-   Die Leistung der Bündelsammlung wurde erheblich verbessert.
-   Die synchronisierten Bestandsaktualisierungen während der Auftragsabwicklung werden asynchron über die Nachrichtenwarteschlange durchgeführt.

# 1.5.5

-   Ein untergeordneter Artikel eines Bundles wurde aus der Bestellung entfernt, wenn die Produktkonfigurator-Erweiterung mit dem Bundle verwendet wurde und das Produkt mit einer anderen Konfiguration in den Warenkorb gelegt wurde.

# 1.5.4

-   Gelöst Unbekannter "pickware_erp_product_name" Filter auf Pickliste mit CogiPickLists Erweiterungen.

# 1.5.3

-   Konfigurationsoption zur Deaktivierung der Kaufpreisüberschreibung hinzugefügt.

# 1.5.2

-   Link zu Bundle-Produkten auf Produktdetailseiten von Produkten, die sich in einem Bundle befinden, hinzugefügt

# 1.5.1

-   Gelöst Produktlink bündeln, wenn Sichtbarkeit ausgewählt ist Ausblenden in Listings und Suche.

# 1.5.0

-   Einführung der Kompatibilität für Shopware 6.5.0

# 1.4.13

-   Umwandlung von maxPurchase in int, um Typfehler zu vermeiden

# 1.4.12

-   Fehler in der Anzeige der Produktdetail-Bündeltabelle im Vorauspreis behoben.
-   Konfigurationsoption hinzugefügt, um die Überschreibung der Maximalabnahme zu deaktivieren.
-   Problem bei der Gewichtsberechnung für Bündelartikel mit einer Menge von mehr als 1 behoben

# 1.4.11

-   Legen Sie fest, dass das Bundle-Produkt den niedrigsten Wert für die "Maximale Bestellmenge" von den Produkten im Bundle erbt.
-   Fehler bei der Preisberechnung in der Produktliste der Verwaltung behoben

# 1.4.10

-   Der Stil der Produktnummer in der Tabelle der Bundle-Positionen wurde geändert.
-   Die Beschriftung der Konfigurationsoptionen wurde überarbeitet, um mehr Klarheit zu schaffen.
-   Fehler behoben, bei dem der Hersteller nicht mehr angezeigt wurde

# 1.4.9

-   Produktnummer zur Bündelprodukt-Tabelle hinzugefügt, wenn in der Plugin-Konfiguration aktiviert.
-   Produktmenge im Warenkorb und an der Kasse bündeln, wenn in der Plugin-Konfiguration aktiviert.
-   Bundle-Informationen im Warenkorb und Checkout ausblenden, wenn in der Plugin-Konfiguration aktiviert.
-   Die Anzeige des Preises in Warenkorb, Bestellungsabschluss und Benutzerkonto-Bestellung wurde korrigiert.

# 1.4.8

-   Das Designproblem im Schaufenster wurde behoben.

# 1.4.7

-   Feinabstimmung bei der Sortierung von Artikeln in der Pickware-Vorlage Picklist Override
-   Ein Problem wurde behoben, bei dem das Bundle-Produkt während der Nachbestellung über die Storefront auseinanderfiel
-   Es wurde ein Fehler in Kombination mit erweiterten Preisen bei der Bundle-Erstellung behoben

# 1.4.6

-   Fehlendes Cover-bild in der Liste der Bundle-Artikel behoben
-   Fehler im Warenkorb-Sammler bei Verwendung des Shopware Plugins für benutzerdefinierte Produkte behoben

# 1.4.5

-   Fehler behoben, der auftrat, wenn ein Kunde von der Steuer befreit wurde

# 1.4.4

-   Kleiner Fehler in der erweiterten Preisauswahl in der Bundle-Produktübersicht auf der Produkt-Detailseite behoben

# 1.4.3

-   Die Neuberechnung von Unterprodukten beim Hinzufügen eines Gutschriftartikels in der Bestellung wurde behoben.
-   Problem behoben, bei dem Bundle-Produkte nicht immer gespeichert wurden
-   Doppelte Nummern in Bundle-Artikelpositionen wurden korrigiert
-   Die Sortierung von Bundle-Positionen im Schaufenster wurde korrigiert.
-   Bundle-Produkte berücksichtigen jetzt die Vererbung von Varianten innerhalb eines Bundles, wodurch der Preis eines Bundle-Produkts, das Varianten enthält, in der Verwaltung korrigiert wurde

# 1.4.2

-   Reihenfolge in der Auswahlliste korrigiert

# 1.4.1

-   Problem mit Division durch Null behoben

# 1.4.0

-   Unterstützung für erweiterte Preise hinzugefügt
-   Optimierungen der Preisberechnung hinzugefügt
-   Option zum Ausblenden des Gesamtpreises in der Bundle-Preisliste hinzugefügt

# 1.3.3

-   Aktualisierte Gewichtsüberschreibung, um besser konfigurierbar zu sein

# 1.3.2

-   Gewicht überschreiben hinzugefügt

# 1.3.1

-   Verbesserte Darstellung von Bundle-Produkten im Picklisten-Dokument
-   Bei Produktvarianten wird die Verfügbarkeit eines Bündelprodukts jetzt korrekt angezeigt.

# 1.3.0

-   Teilweise Unterstützung mit Pickware ERP Starter hinzugefügt. Achtung, dieses Update ist experimentell und kann die Funktion des Plugins auf bestimmten Installationen verändern. Deaktiviere die neu hinzugefügte Konfigurationsoption für die Abwärtskompatibilität, damit diese neue Kompatibilität wirksam wird.
-   Neue Konfigurationsoption hinzugefügt, um Rückwärtskompatibilität mit älteren Installationen herzustellen. Um die Kompatibilität mit dem Pickware ERP-Starter zu verbessern, deaktiviere die neue Option "Einen benutzerdefinierten Positionstyp anstelle des Standardpositionstyps 'Produkt' verwenden".
-   Die Konfigurationseinstellung für die Lagerverwaltung ist veraltet. Wenn Positionen vom Typ Produkt verwendet werden, verwaltet Shopware den Bestand jetzt entsprechend.
-   Es wurden asynchrone harte Aktualisierungen des "Bestands" und des "verfügbaren Bestands" über die Nachrichtenwarteschlange hinzugefügt.
-   Fehler bei der Rundungsgenauigkeit behoben, die vom Cart Collector stammen

# 1.2.6

-   Teilweise Kompatibilität mit dem Plugin Customized Products von Shopware hinzugefügt. Einige Anomalien treten noch auf

# 1.2.5

-   Fehler im Adminbereich beim Hinzufügen eines neuen Produkts zum Bundle behoben

# 1.2.4

-   Es wurde eine Option hinzugefügt, um den Bestand durch die Menge des Produkts im Bündel zu teilen

# 1.2.3

-   Fehler beim Hinzufügen zum Warenkorb in einem Tax Free-Kontext behoben

# 1.2.2

-   Positionsunterstützung für Bundle-Produkte vor Version 1.2.0 hinzugefügt.

# 1.2.1

-   Block zum Buy-Widget Override namens buy_widget_buy_container_bundle_list hinzugefügt, um die Erweiterbarkeit zu verbessern

# 1.2.0

-   Manuelle Sortierfunktion für Produkte innerhalb eines Pakets hinzugefügt.

# 1.1.27

-   Patch für ein in 1.1.26 eingeführtes Problem, das einen Fehler bei Produkten ohne Hersteller verursachte.

# 1.1.26

-   Der Hersteller wurde zur Verwendung in Dokumenten und E-Mail-Vorlagen in die Nutzlast von Bündelartikeln aufgenommen
-   Der Name des Herstellers wurde zu den Artikeln im Warenkorb und an der Kasse hinzugefügt, wenn dies in der Plugin-Konfiguration aktiviert wurde.

# 1.1.25

-   Fehler in der deutschen Übersetzung behoben

-   # 1.1.24
-   Ein Problem bei der Steuerberechnung wurde behoben, wenn die Option "Den Preis von Produkten innerhalb eines Pakets auf 0,00 setzen" verwendet wurde.

# 1.1.23

-   Die Berechnung "Anpassung der Produktpreise an den Bündelpreis" wurde geändert, um repräsentativere Preise zu erhalten.

# 1.1.22

-   Die Berechnung der Mehrwertsteuer ist in Verbindung mit der Einstellung "Keine Berechnungen durchführen" in den Plugin-Einstellungen deaktiviert.

# 1.1.21

-   Unterstützung für mehrere Mehrwertsteuersätze hinzugefügt

# 1.1.20

-   Link zur Produkt-Detailseite von Produkten innerhalb eines Bundles auf der Storefront hinzugefügt
-   Option hinzugefügt, um das Titelbild von Produkten innerhalb des Bundles als Miniaturansicht im Schaufenster anzuzeigen
-   Option hinzugefügt, um die Verfügbarkeit von Produkten innerhalb des Bundles im Schaufenster anzuzeigen
-   Import/Export-Unterstützung für Bundle-Produktverbindungen hinzugefügt

# 1.1.19

-   Fehler behoben, bei dem Bundle-Verbindungen nicht aktualisiert wurden, wenn man mit der Funktion "Produkt anzeigen" zu einem anderen Produkt wechselte.

# 1.1.18

-   Bündelverbindungslimit von 25 auf 100 erhöht
-   Neukompilierung der Verwaltungs-JS-Dateien unter v6.4.5.0 durchgeführt, um das Problem https://github.com/shopware/platform/issues/2420 zu beheben

# 1.1.17

-   Ein in seltenen Fällen auftretendes Problem beim Speichern von Bündelverbindungen wurde behoben.

# 1.1.16

-   Es wurde ein Problem behoben, bei dem Produktlayouts mit der Berechnung des Preises für neue Produktpakete in Konflikt gerieten.

# 1.1.15

-   Option hinzugefügt, um das Feld "Bestand" des Produkts mit dem Bestand des Produkts im Bundle mit dem niedrigsten verfügbaren Bestand zu überschreiben.

# 1.1.14

-   Berechnung des Einkaufspreises für Pakete auf der Grundlage der Einkaufspreise der Produkte im Paket hinzufügen

# 1.1.13

-   Jetzt wird das Gewicht des Bündelprodukts anhand des Gewichts der Produkte im Bündel berechnet.
-   Der Preis, der auf der Produktdetailseite der Produkte im Bundle angezeigt wird, basiert jetzt auf dem berechneten Preis. Leider ist es noch nicht möglich, erweiterte Preise in diese Preisberechnung einzubeziehen.

# 1.1.12

-   Abwärtskompatibilitäts-Patch mit v.6.4.1.0

# 1.1.11

-   Anpassung des Systems der Bestandsvererbung, um den Bestand und die Verfügbarkeit besser zu bestimmen.

# 1.1.10

-   Hinzugefügte Lieferzeitvererbung aus dem Produkt mit der längsten Lieferzeit

# 1.1.9

-   Zusätzliche Konfigurationsoption hinzugefügt, um einen Preisberechnungsmodus sowohl für Bündelprodukte als auch für Artikel innerhalb eines Bündelprodukts auszuwählen
-   Zusätzliche Konfigurationsoption hinzugefügt, um den Preis aller Positionen innerhalb eines Bündelprodukts auf 0,00 zu setzen.

# 1.1.8

-   Option zur Aktivierung der Lagerverwaltung für Produkte innerhalb eines Bündels hinzugefügt.
-   Die Produktnummer wurde zu den Ergebnissen der Bündelproduktauswahl hinzugefügt, um die Identifizierung von Varianten zu erleichtern.

# 1.1.7

-   Reihenfolge der Bundle-Positionen in Dokumenten korrigiert

# 1.1.6

-   Die Zuweisung von Bündelbeziehungen wurde überarbeitet, was zu einer erheblichen Leistungssteigerung führte.
-   Verbesserte UX der Bündelproduktverwaltung in der Administration
-   Konfigurationsoption hinzugefügt, um die automatische Neuberechnung der Preise von Produkten in einem Bundle zu deaktivieren.

# 1.1.5

-   Zusätzliche Optionen für die Präsentation von Bundle-Artikeln im Schaufenster hinzugefügt
-   Einige kleine Visualisierungsprobleme behoben

# 1.1.4

-   Fehler bei der Berechnung des Preises für Bündelpositionen behoben

# 1.1.3

-   Fehler bei der Integritätsbeschränkung von Positionen behoben, wenn ein Auftrag mit einer Bündelposition über die Verwaltung hinzugefügt/bearbeitet wird
-   Fehler bei der Mengenvalidierung beim Hinzufügen/Bearbeiten eines Auftrags mit einer Bündelposition, die einen Bestand > 1 enthält, behoben

# 1.1.2

-   Kompatibilitätspatch für Cart Recovery Plugin hinzugefügt
-   Problem mit der Dezimalpräzision behoben, das auftrat, wenn der Warenkorb leer war.

# 1.1.1

-   Fehler bei der Deinstallation behoben
-   Referenzversionsfelder hinzugefügt, um Fehler beim Befehl dal:schema:create zu vermeiden
-   Die Ermittlung des verfügbaren Bestands wurde verbessert, indem die Bündelartikelmenge in die Gleichung aufgenommen wurde

# 1.1.0

-   Kompatibilität für v6.4 hinzugefügt

# 1.0.5

-   Hotfix zur Fehlerbehebung beim Checkout

# 1.0.4

-   Reihenfolge der Bundle-Positionen in Dokumenten korrigiert

# 1.0.3

-   Verbesserte Leistung.
-   Bestell-Lieferpositionen für Warenkorb-Kinderartikel erstellt
-   Die Bundle-Informationen wurden aus der Nutzlast-Info in die untergeordneten Artikel des Warenkorbs verschoben.

# 1.0.2

-   getPayloadValue wurde durch hasPayloadValue ersetzt, um zu verhindern, dass in älteren Shopware-Versionen eine Ausnahme ausgelöst wird

# 1.0.1

-   Preisberechnung von Bundle-Produkt-Unterartikeln korrigiert
-   Bessere Einblicke in den Preis von Bundle-Artikeln in den Bestelldetails hinzugefügt
-   Verbessertes Styling des Bündelprodukt-Gitters auf der Produkt-Detailseite
-   Fehler behoben, bei dem der Schalter "Produkte im Bundle im Schaufenster anzeigen" versteckt war
-   Fehler behoben, bei dem Bündelartikel auf dem Bestellabschlussbildschirm immer noch sichtbar waren
-   Fehler behoben, bei dem Bundle-Artikel immer noch in den Bestelldetails des Kontos sichtbar waren
-   Fehler behoben, bei dem der Stückpreis von Bundle-Artikeln in den Bestelldetails des Kontos als "FREE:" angezeigt wurde

# 1.0.0

-   Erste Version des Artikel-Sets, Stücklisten & Bundles für Shopware 6
