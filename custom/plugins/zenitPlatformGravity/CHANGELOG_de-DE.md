# 4.4.1
- Optimierung: Anordnung der Inhalte im Cookie-Widget verbessert.
- Optimierung: Bildplatzierung der Hero-Sektion.
- Optimierung: Behebt ein Kontrastproblem für das Video Loader-Backdrop.
- Bugfix: Fix Sticky-Aside im Checkout.
- Bugfix: Fix 'Kategorie-Navigation' "Nur aktiver Pfad" Option.
- Bugfix: Problem mit der Kontrastfarbe der Eigenschaften-Tabelle behoben.

# 4.4.0
- Feature: Funktion hinzugefügt, um Demo-Konfigurationsvoreinstellungen in das Theme, untergeordnete Themes und Duplikate zu importieren. | Pro Version
- Feature: Neues CMS-Element „Kategorie-Navigation“ hinzugefügt. | Pro-Version
- Feature: Neue Symbole zur Produktfunktionskonfiguration hinzugefügt.
- Feature: Unterstützung des Medientyps 'video' für die Kategorie „Hero Sektion“.
- Feature: Unterstützung des Medientyps 'video' für das CMS-Element „Text Banner“. | Pro-Version
- Feature: Unterstützung des Medientyps 'video' für das CMS-Element „Teaser“. | Pro-Version
- Feature: Unterstützung des Medientyps 'video' für das CMS-Element „Suche Banner“. | Pro-Version
- Feature: Unterstützung des Medientyps 'video' für das CMS-Element „Bild Text Slider“. | Pro-Version
- Feature: Neue Konfiguration zum CMS-Element „Trenner“ hinzugefügt, um H-Tag zum Text hinzuzufügen. | Pro-Version
- Funktion: Neue Konfiguration zum CMS-Element „Kategorie Listing“ hinzugefügt, um eine benutzerdefinierte Kategorie als Einstiegspunkt auszuwählen. | Pro-Version
- Funktion: Neue Konfigurationsoption hinzugefügt, um die Flyout-Navigationsanimation zu deaktivieren.
- Kompatibilität: Kompatibilität mit dem Suche Pro hergestellt.
- Kompatibilität: Shopware 6.6.5.0 Änderungen implementiert.
- Kompatibilität: Kompatibilität mit dem Netzperfekt Blog Premium-Plugin hergestellt.
- Optimierung: Verbesserung der Theme-Konfiguration.
- Optimierung: Twig kann jetzt für den Text des "Marketing Text Slider" verwendet werden, sofern Twig-Syntax aktiviert ist.
- Optimierung: Standardwert für `product.purchaseSteps` hinzugefügt, da Pickware die Erstellung von Produkten mit Nullwert ermöglicht.
- Optimierung: Verhindert unter bestimmten Umständen einen Textabschnitt auf Produktboxen.
- Optimierung: Bessere Darstellung der PayPal-Schaltflächen in der Buybox, damit sie mit der Schaltfläche „Zum Warenkorb hinzufügen“ übereinstimmen.
- Optimierung: Akkordeon-Tabs scrollen jetzt nach dem Reduzieren in die Ansicht, um eine falsche Scroll-Position bei langen Inhalten zu verhindern.
- Optimierung: Ein Z-Index-Problem bei der Mengenauswahl im Off-Canvas-Warenkorb wurde behoben.
- Optimierung: Verbessertes Scrollen zur Berechnung nach dem Seitenwechsel in Produktlisten.
- Optimierung: Buybox-Steuerlink optimiert.
- Optimierung: Parameter in einer logischeren Reihenfolge für die Mixins `layout-container-horizontal-padding`, `layout-container-negative-horizontal-margin`, `layout-container-horizontal-margin` geändert.
- Optimierung: Storefront-Optimierungen.
- Optimierung: Floating Labels überarbeitet.
- Bugfix: Behebt das Z-Index-Problem bei Abschnitts-Offsets.
- Bugfix: Der Offcanvas-Warenkorb wurde nicht korrekt aktualisiert, wenn die Offcanvas-Funktion zum Hinzufügen zum Warenkorb nach Produktnummer verwendet wurde.
- Bugfix: Behebt unter bestimmten Umständen nicht funktionierende Scroll-Animationen.
- Bugfix: Behebt doppelte Links im Slider.

# 4.3.5
- Optimierung: Verbessert die Einrückung mehrzeiliger Produkt-Features bei `list-item`.
- Bugfix: Fix body-color;

# 4.3.4
- Optimierung: Logik für den Produkt-Listing Aktionsbutton 'Optionen auswählen' bei erweiterten Varianten ohne Eigenschaftszuweisung hinzugefügt.
- Optimierung: Farbkontrast im Account-Dropdown-Menü verbessert.
- Optimierung: Footer Links Farbe der Unterstreichung.
- Bugfix: Behebt ein Problem mit der mobilen Höhe des Galerie-Sliders aufgrund von Shopware !important Styles.

# 4.3.3
- Optimierung: Textfarbe von Badges in Flyout-Navigation wird auf `$body-bg` zurückgesetzt, wenn `$zen-sidebar-navigation-bg` transparent ist.
- Optimierung: Verhindert das Ausbrechen von Sektionen in voller Breite in Boxed Theme-Layouts bei Modals.
- Optimierung: Verbessert den Code Smell vom Code Review.
- Optimierung: Verbesserung der Spalten für CMS-Layout 'Erweitertes Layout' und 'Erweitertes Layout umgekehrt'. | Pro-Version
- Optimierung: Harmonisierung der Button in der Top Bar.
- Bugfix: Seitliches Scrollen auf mobilen Geräten verhindert, wenn Gallery-Scroller oder Gallery-Grid verwendet werden.
- Bugfix: Objektposition für Bilder im Contain-Modus für CMS-Element 'Kategorie-Listing' korrigiert | Pro-Version

# 4.3.2
- Kompatibilität: Shopware 6.6.3.0 Änderungen implementieren.
- Optimierung: Verbesserung der Theme-Konfiguration.
- Optimierung: Fehlende Farbe für `.category-navigation-toggle .icon` festgelegt.
- Bugfix: Bild `min-height` für `.gallery-slider-single-image` korrigiert.
- Bugfix: Sidebar Kategorie Navigation Toggler für aktives Element gefixt.

# 4.3.1
- Optimierung: Verbessert die vertikale Ausrichtung der Social-Media Icons in der unteren Fußzeile.
- Optimierung: Verbessert Innenabstände in der Flyout Navigation.
- Bugfix: Korrigiert die Farbe der Footerlinks.

# 4.3.0
- Feature: CMS-Element "Kategorie Listing" hinzugefügt | Pro-Version
- Funktion: Neue Konfigurationen hinzugefügt, um den Button 'Optionen wählen' für die Produktaktionen im Listing hinzuzufügen. | Pro-Version
- Kompatibilität: Kompatibilität mit dem Plugin "Custom Products" verbessert.
- Kompatibilität: Kompatibilität mit neoonlines Produktkonfigurator verbessert.
- Kompatibilität: Kompatibilität mit dem Atloss-Produktkonfigurator verbessert.
- Optimierung: Die Produktaktionen im Produktlisting wurden überarbeitet.
- Optimierung: 'ZenBuyWidgetQuantityInputPlugin' entfernt.
- Optimierung: Javascript nach HTML-Inhalt im CMS-Element "Code" laden. | Pro-Version
- Optimierung: Sticky-Header überarbeitet und `js-sticky` in `js-header-sticky` für Sticky-Header Kontext umbenannt.
- Optimierung: Sticky-Elemente mit Sticky-Header-Höhe überarbeitet.
- Optimierung: Offcanvas-Header haben jetzt einheitliche Titel.
- Optimierung: `$zen-product-gallery-slider-min-height-desktop` entfernt, da dies ab Shopware 6.6 ausschließlich in `cms-element-image-gallery` eingestellt wird.
- Optimierung: Breakpoint für `zen-product-listing-card-actions-mobile` von `lg` auf `xl` geändert.
- Optimierung: Die Sprachflagge wurde von `div` in `span` geändert, da Kindelemente eines Buttons inline-Elemente sein sollten.
- Optimierung: Optimiert einige Kontrastprobleme bei dunklem Layout.
- Optimierung: Optimiert springende Fußzeilen-Akkordeons.
- Optimierung: Anzeige überlappender Kontrollkästchen in der Offcanvas-Cookie-Zustimmung für Safari Mobile.
- Optimierung: Harmonisiert die Fehlausrichtung des mobilen Bewertungs-Bagdes.
- Optimierung: Optimiert die transparente Mengeneingabe auf Produktkarten.
- Optimierung: Block Layout Abstände von `auto` zu `20px` geändert, um näher am Shopware Standard zu sein.
- Bugfix: Ein z-index Problem des "Suche Banner" Elements wurde behoben.
- Bugfix: Überlappende Sticky Buybox behoben.
- Bugfix: Produktbewertung Umbrüche bei mobile Safari behoben.
- Bugfix: Fix previewSource in CMS Element Medienauswahl.

# 4.2.0
- Feature: Neue Konfigurationen hinzugefügt, um zu verhindern, dass der Offcanvas-Warenkorb automatisch geöffnet wird, wenn ein Produkt zum Warenkorb hinzugefügt wird.
- Feature: CMS-Block "Galerie und Buybox mit Beschreibung" hinzugefügt | Pro- und Cloud-Version
- Feature: Neue Konfigurationen hinzugefügt, um die Checkout-Zusammenfassung/Seitenleiste sticky zu machen.
- Optimierung: Line-height für Überschriften vergrößert.
- Optimierung: Google Fonts Loader, doppelte Anführungszeichen im Konfigurationsfeld zulassen.
- Optimierung: Edge-Case der Sticky-Funktionalität in Kombination mit Buybox-Tabs und Tabs-Mode-Scroller.
- Optimierung: Checkout-Hintergrund überarbeitetet.
- Optimierung: ACL-Regeln zu Einstellungselementen und Dashboard-Routen hinzugefügt. | Pro-Version
- Optimierung: Footer-Newsletter Spalte überarbeitet.
- Optimierung: Verhindert das Weiterleiten zum Warenkorb bei Eingabe einer Produktnummer im Offcanvas-Warenkorb.
- Optimierung: Floating Labels und Autofill.
- Kompatibilität: Kompatibilität mit dem BuI Hinsche Product Configurator-Plugin hergestellt.
- Bugfix: Animierte Produktboxen auf der Wunschliste für Gäste wurden nicht angezeigt.
- Bugfix: CMS Suche Banner `width` CSS war invalide. | Pro-Version
- Bugfix: CMS Features Element fehlendes Icon hinzugefügt. | Pro-Version
- Bugfix: Fix undefined `$parent.block` in Erlebniswelten. | Pro-Version
- Bugfix: Fix für display Probleme wegen Shopware 'ADR to make more use of Bootstrap tooling'.
- Bugfix: Behebt eine Überlappung der Sticky Buybox.

# 4.1.0
- Feature: CMS-Block "Galerie mit Beschreibung und Buybox mit Produktname & Hersteller-Logo" hinzugefügt | Pro- und Cloud-Version
- Feature: Neue Konfigurationen hinzugefügt, um Rahmen zu Shop-Navigationen hinzuzufügen.
- Feature: Neue Konfiguration hinzugefügt, um Flyout-Navigationsspalten dynamisch zu füllen.
- Feature: Neue Konfiguration hinzugefügt, um die maximale Höhe der Flyout-Navigation festzulegen.
- Feature: Neue Konfiguration hinzugefügt, um die text-transform der Tab-Navigation festzulegen.
- Feature: Neue Konfiguration hinzugefügt, um Abstände Tab-Navigation festzulegen.
- Feature: Neue Konfiguration hinzugefügt, um die Größe des Offcanvas-Logos festzulegen.
- Feature: Neue Konfiguration hinzugefügt, um das Sidebar Navigation Toggler-Icon festzulegen.
- Feature: Neue Konfigurationsoption hinzugefügt, um das Filter Toggler-Icon auf +/- festzulegen.
- Feature: Neue Konfigurationsoption hinzugefügt, um Kopf- und Fußzeile in voller Breite für das Grundlayout festzulegen.
- Optimierung: Die Zeilenhöhe der Seitenleistenkategorien wurde geändert, um sie an die Filterzeilenhöhe anzupassen.
- Optimierung: Überarbeitete Container-Haupt- und Breadcrumb-Container-Abstände.
- Optimierung: Es wurde ein Anzeigefehler behoben, bei dem das mobile Zoom-Modal nicht in voller Höhe angezeigt wurde.
- Optimierung: Trusted Shops-Badges werden jetzt ausgeblendet, wenn ein Modal geöffnet ist.
- Optimierung: Ungenutzte Funktion `zen-sidebar-navigation-arrow` entfernt.
- Optimierung: Die vertikalen Abstände des CMS-Grids auf margin-bottom umgestellt.
- Optimierung: Header-Tooltips an Header-Container gebunden.
- Optimierung: Custom-Fields Helper überarbeitet.
- Bugfix: Lazy Loading funktioniert jetzt für Teaser-Elemente.
- Bugfix: Fehlende CMS-Standardmedien in Storefront behoben.

# 4.0.0
- Kompatibilität: Kompatibilität mit Shopware 6.6 hergestellt.
- Kompatibilität: VUE 3-Upgrade für Dashboard und CMS-Elemente.
- Kompatibilität: Asynchrones Storefront-JavaScript und all.js-Entfernung.
- Kompatibilität: Nicht verwendete Theme-Konfigurationen für Nicht-CMS-Produktdetailseiten entfernt.
- Kompatibilität: Veraltete 'page/product-detail/' Templates entfernt.
- Kompatibilität: Neue Konfiguration hinzugefügt, um den Produktnamen in Quickview zu positionieren.
- Kompatibilität: Moved `zen-custom-global-tab.html.twig`, `zen-custom-tab1.html.twig`, `zen-custom-tab2.html.twig`, `zen-datasheets.html.twig`, `zen-manufacturer.html.twig`, `zen-properties-tab.html.twig`, `zen-video-tab.html.twig` and `zen-video-embed.html.twig` in 'component/product/' Verzeichnis verschoben.
- Feature: Feature: CMS-Block „Galerie und Buybox mit Produktname & Hersteller-Logo“ hinzugefügt | Cloud-Version
- Feature: Feature: CMS-Block „Galerie und Buybox mit Produktname & Hersteller-Logo & Beschreibung“ hinzugefügt | Cloud-Version
- Feature: CMS-Block "Footer navigation" hinzugefügt
- Feature: Neue Konfiguration hinzugefügt um die Produktnummer-Eingabe immer anzeigen zu lassen
- Feature: Neue Konfiguration hinzugefügt um die Produktnummer-Eingabe im Offcanvas-Warenkorb anzeigen zu lassen
- Optimierung: Vorschau von CMS-Elementen angepasst.
- Optimierung: YouTube Social-Icon aktualisiert.

# 3.8.0
- Feature: Telegramm zu Social-Media Icons hinzugefügt.
- Feature: Neue Konfiguration hinzugefügt, um `text-transform` für Footer Überschriften festzulegen.
- Feature: Navigations-Labels zur Off-Canvas-Navigation hinzugefügt.
- Feature: Kompatibilität mit Spatial Commerce / AR- und 3D-Features hinzugefügt
- Feature: Neue Konfiguration hinzugefügt, um das Erscheinungsbild der Überschrift der Produktdetailbeschreibung zu ändern.
- Feature: Neue Konfiguration hinzugefügt, um Produktbeschreibungstabs als linearen Inhalt anzuzeigen.
- Feature: Neue Konfiguration im CMS-Codeelement hinzugefügt, um die Länge von abgeschnittenen Zeichen zu ändern.
- Feature: Icon Vorschau zu CMS-Element 'zenit-features' hinzugefügt.
- Feature: Neue Symbole zu CMS-Element 'zenit-features' hinzugefügt.
- Optimierung: Springenden Header verhindert, wenn js-sticky entfernt wird.
- Optimierung: Mengeneingabe `border-end-radius` bei Container-Query.
- Optimierung: Sticky-Header überlagert Sticky-Bewertungen.
- Optimierung: Logo der Fußzeile.
- Optimierung: Reduziert vertikale Leerabstände bei Produkt-Boxen.
- Optimierung: Dropdown-Breite in der Offcanvas-Tab-Leiste anpassen.
- Optimierung: Verbessert die Einrückung mehrzeiliger Produkt-Features.
- Optimierung: Hintergrund der Erweiterte-Preise Tabelle auf Detailseiten.
- Optimierung: Verbesserte `form-select-indicator` Pfeilfarbe.
- Bugfix: Horizontales Scrollen in der Off-Canvas Topbar auf Safari behoben.
- Bugfix: Fehlende Blöcke `thumbnail_utility` und `thumbnail_utility_img` hinzugefügt.
- Bugfix: Ein Problem für Produkt-CMS-Layouts wurde behoben, wenn eine Zuweisung für eine Produktbox entfernt wurde.

# 3.7.2
- Optimierung: Verbesserte Darstellung und Benutzerfreundlichkeit der Theme-Konfiguration. | Pro-Version
- Optimierung: Mengeneingabe `border-end-radius` bei Container-Query.
- Optimierung: Changelog des Admin-Dashboards entfernt.
- Optimierung: positionIdentifier hinzufügen.
- Bugfix: `input&` verursacht einen Fehler im Watcher-Modus.

# 3.7.1
- Kompatibilität: Kompatibilität mit Shopware 6.5.8.2 hinzugefügt, da Shopware aufgrund von Kompatibilitätsproblemen mit „NEXT-31820 – Revert Update NPM packages“ NPM Pakete zurücksetzt.
- Feature: Neue Konfiguration hinzugefügt, um die Scroll-up-Button Farbe anzupassen.
- Optimierung: Kontrastfarbe von list-price-price.

# 3.7.0
- Kompatibilität: Kompatibilität mit Shopware 6.5.8.0 hinzugefügt.
- Optimierung: Stellt sicher, dass der Preis immer am unteren Rand der Box liegt, auch wenn andere Boxen höher sind.
- Bugfix: Mobile Cover-switch touchevent verhindert Click-Event.

# 3.6.6
- Bugfix: Gallery-Slider Thumbnails wrap

# 3.6.5
- Feature: Neue Konfiguration hinzugefügt, um die Versanddetails im anfänglich geöffneten Zustand anzuzeigen.
- Feature: Neue Konfigurationen für die Positionierung des Scroll-Up Buttons hinzugefügt.
- Optimierung: Warenkorb Bestellnummerneingabe und ausklappbare Versanddetails angepasst.
- Optimierung: Header border-bottom in Kombination mit border-radius.
- Optimierung: Offcanvas Außenabstand.
- Optimierung: Header-Randradius bei zweizeiligem Header.
- Optimierung: Header-Box-Schatten bei transparentem Header.
- Optimierung: Breadcrumb Abstand unten.
- Optimierung: Verhindert das Umbrechen der Thumbnails-Navigation des Galerie-Sliders.
- Optimierung: Checkout Versand-/Zahlungsmethode-Bild SVG-Korrektur für Safari.
- Bugfix: Benutzerdefinierte Felder Produkte pro Zeile.
- Bugfix: Zentrierung des einzeiligen Header-Logos für Mobilgeräte.
- Bugfix: Zentrierte Positionierung der Hauptnavigation.
- Bugfix: Theme-Dashboard Reihenfolge des Changelogs.
- Bugfix: Division durch Null korrigiert, wenn Flyout-Spalten auf 1 gesetzt wurden.

# 3.6.4
- Optimierung: Dropdown-Menü Flackern in der Hauptnavigation verhindern.
- Optimierung: Fehlende Container-Abstände im Boxed-Layout hinzugefügt.
- Optimierung: Zentrierte Ausrichtung der Hauptnavigation für Nav-Scroller Funktion überarbeitet.
- Optimierung: Offcanvas-Navigation paddings.
- Optimierung: Offcanvas-Headlines.
- Optimierung: Klassen für Mengeneingaben für bessere Kompatibilität geändert.
- Bugfix: Korrigiert Galerie-Slider contain Anzeigemodus.
- Bugfix: Einfaches Layout 4/8.
- Bugfix: Fehlendes Custom-Field für den Bildmodus hinzugefügt. | Cloud-Version

# 3.6.3
- Bugfix: missing app rewrites

# 3.6.2
- Optimierung: Unterstrich in der Account Sidebar.
- Optimierung: Breakpoint für dreispaltige Grids von `lg` auf `md` gesetzt.
- Funktion: Neue Konfiguration zum Festlegen der Badge-Farbe in der Shop-Navigation hinzugefügt.
- Bugfix: Nav Scroller bei zentrierter Navigation.
- Bugfix: Floating Labels Schriftfarbe bei Selects.

# 3.6.1
- Bugfix: App script empty media entity check.

# 3.6.0
- Feature: Angemeldet Indikator hinzugefügt;
- Feature: Neue Konfigurationsoption zur Anzeige der Sprach-ID im Sprachumschalter hinzugefügt.
- Feature: Neue Konfiguration hinzugefügt, um Badges der Shop-Navigation zu minimieren.
- Feature: Neue Konfigurationsoptionen für den Border-Radius hinzugefügt.
- Feature: Neue Konfiguration hinzugefügt, um `text-transform` für Breadcrumbs festzulegen.
- Feature: Neues benutzerdefiniertes Feld hinzugefügt, um den Anzeigemodus des Kategoriebildes festzulegen.
- Feature: Neue Konfiguration hinzugefügt, um Schriftgröße und Schriftstärke für die Top-Bar und die Shop-Navigation festzulegen.
- Feature: Neue Konfiguration hinzugefügt, um die Offcanvas-Breite festzulegen.
- Feature: Neue Konfiguration hinzugefügt, um den Text-Editor zum Code-Editor in den CMS-Elementen Suche-Banner und Text-Banner umschalten zu können und so eine Bereinigung zu verhindern. | Pro-Version
- Feature: Cover-Switch Bildwechsel zur Cloud hinzufügen. | Cloud-Version
- Optimierung: Verhalten von `margin-bottom` der Breadcrumbs geändert, um den Container-Layout-Abstand widerzuspiegeln.
- Optimierung: Unnötige Woff-Dateien entfernt, da die IE 11-Unterstützung mit Shopware 6.5 weggefallen ist.
- Optimierung: Verbessert die Kompatibilität zu verschiedenen Erweiterungen.
- Optimierung: Header Buttons vergrößern sich bei Mouseover.
- Optimierung: Verbesserte Sticky Buybox und Sticky Gallery auf der Produktdetailseite.
- Optimierung: `navigationTree`-Abfrage hinzufügen, um leere Kategorienavigations-Elemente zu verhindern.
- Optimierung: Verbesserung der "Floating Labels".
- Optimierung: Thumbnail-Navigation Pfeile des Galerie-Sliders optimiert.
- Optimierung: Lighthouse-Audit-Verbesserungen.
- Optimierung: Unload-Listener entfernt.
- Optimierung: Sidebar-Navigation aktiv / hover-Status.
- Optimierung: Refactoring der Footer Newslettereingabe.
- Optimierung: Verhindert, dass der `clip-path` der Hero-Sektion Überläufe abschneidet.
- Optimierung: Reduziert die Lücke zwischen Seitenleiste und Hauptinhalt bei automatischen Layoutabständen.
- Optimierung: Verbesserung der Scroll-Animation im Listing.
- Optimierung: Gestylte Filter-Scrollbars.
- Optimierung: Hintergrundfarbe und Hintergrundüberlagerungsfarbe für "line-item" Bilder hinzugefügt.
- Optimierung: Checkout-Header.
- Optimierung: Btns auf Bootstrap-Standard zurückgesetzt.
- Optimierung: Verbessert das Styling der Bewertungsmaske.
- Optimierung: Namensgebung Kategoriebild-Funktion in Hero-Sektion geändert.
- Optimierung: Twig-Codestil.
- Optimierung: Höhe der CMS-Elemente in höheren Spalten verbessert.
- Optimierung: Touch-Events zu Hover-Animationen hinzugefügt.
- Optimierung: Variablen-Mapping in CMS-Elementen werden nun korrekt aufgelöst.
- Optimierung: `sizes` in `breakpoints` umbenannt, da das sizes-Attribut der Thumbnails keine Werte enthielt.
- Bugfix: `webkit-prefix` zu `backdrop-filters` hinzugefügt.
- Bugfix: Z-Index der Vorschlagssuche des Suche-Banners;
- Bugfix: Verhindert die Berechnung falscher Thumbnails in Listings, wenn der Abstand im Listing auf `0` gesetzt wurde.
- Bugfix: Zoom-modales Schließen-Icon auf dunklen Hintergründen behoben.
- Bugfix: Vimeo-Video-Tab wird nicht angezeigt.

# 3.5.5
- Feature: Neue Konfiguration zu CMS-Elementen hinzugefügt, um eine Hintergrundunschärfe festzulegen.
- Funktion: Dem CMS-Element Bild-Text-Slider wurde eine neue Konfiguration hinzugefügt, um die Anzahl der Slides für verschiedene Viewports festzulegen.
- Optimierung: CMS-Element Overflow.
- Optimierung: Verhindert, dass Produkt-Slider Pfeile abgeschnitten werden.
– Optimierung: Konfiguration zum Deaktivieren der Service-Navigation im zweizeiligen Header und im Top Bar Off-Canvas.
- Optimierung: Remote-Click auf den Link .product-detail-reviews.
- Bugfix: Die Scroll-Animation der Produktliste wurde auf bestimmten Seiten nicht initialisiert.
- Bugfix: Die Schriftstärke des CMS-Element Divider wurde korrigiert.

# 3.5.4
- Bugfix: Fix icons packs.

# 3.5.3
– Breaking Change: Standardwert von `$zen-product-gallery-img-bg` wurde geändert. Die Verwendung von Variablen als Standardwerte ist problematisch, da die Ladereihenfolge der Variablen zufällig sein kann. Dies führte in seltenen Fällen zu undefinierten Variablen.
- Breaking Change: Neue Konfiguration `$zen-product-gallery-img-color-overlay` hinzugefügt, um die Hintergrundfarbüberlagerung auf Galeriebildern zu ermöglichen.
– Breaking Change: Konfiguration `$zen-product-gallery-img-display-bg` entfernt.
- Funktion: Neue Konfiguration hinzugefügt, um die Anzahl der Spalten im Navigations-Flyout festzulegen.
- Funktion: Neue Konfiguration hinzugefügt, um einen Off-Canvas Außenabstand festzulegen.
- Feature: Neue Konfigurationen zum Festlegen der Backdrop Hintergrundfarben hinzugefügt.
- Funktion: Neue Konfigurationen hinzugefügt, um die Hintergrundunschärfe bei Backdrops festzulegen.
- Optimierung: Verbessert die Initialisierung des Galerierasters.
- Optimierung: Javascript Event-Listener werden nun beim Initialisieren von Akkordeons bei Produktbeschreibungen beibehalten.
- Optimierung: Hover des aktiven „Breadcrumbs“ Element angepasst.
- Optimierung: Breite der Eigenschaften im Akkordeon bei CMS-Elementen.
- Optimierung: Stack-Icon der „Heroicons“ geändert.
- Optimierung: Verbesserung der Scroll-Animation des Produkt-Listings.
- Optimierung: Flackerndes Dropdown-Menü in der Hauptnavigation behoben.
- Optimierung: Akkordeon-Elemente bleiben auf dem Mobilgerät geöffnet, wenn ein anderes Element geöffnet wird.
- Bugfix: CMS-Element Divider wird nicht angezeigt.
- Bugfix: Galerie-Initialisierung mit Elementausrichtung.

# 3.5.2
- Optimierung: Verbesserung der Theme-Konfiguration.
- Optimierung: Pointer-Events auf CMS-Elementen mit Links verbessern.
- Optimierung: Abfrage zum Hersteller-Link hinzugefügt, um URL nur in einem neuen Tab zu öffnen, wenn die URL „://“ enthält.
- Optimierung: Neue Option zum CMS-Element „Features“ hinzugefügt, um kein Icon zu verwenden.
- Optimierung: Ausrichtung CMS-Element „Features“.
- Bugfix: Falsches for-Attribut im Postleitzahlenfeld der Gastauthentifizierung behoben, damit Floating-Labels funktionieren.
- Bugfix: `overflow:hidden` aus CMS-Element „Suchbanner“ entfernt.
- Bugfix: Suchfeld-Styling im CMS-Element „Suche Banner“.
- Bugfix: Horizontale Bildausrichtung in CMS-Elementen.

# 3.5.1
- Bugfix: Rahmen unten bei Sticky-Header.
- 
# 3.5.0
- Feature: CMS-Element „Suche Banner“ hinzugefügt. | Pro Version
- Feature: CMS-Element „Text Banner“ hinzugefügt. | Pro Version
- Feature: CMS-Block „Galerie und Buybox mit Produktname & Hersteller-Logo & Beschreibung“ hinzugefügt | Pro Version
- Feature: CMS-Grid hinzugefügt. Zweireihiges Layout – 5/5/5/5/5, zweireihiges Layout – 2/2/2/2/2. | Pro Version
- Feature: Neue Konfigurationen hinzugefügt, um die horizontale Ausrichtung für Teaser- und Bildtext-Slider-CMS-Elemente festzulegen. | Pro Version
- Feature: Neue Konfiguration hinzugefügt, um die Produktbeschreibung in der Buybox anzuzeigen.
- Feature: Neue Konfiguration hinzugefügt, um die Tabs der Produktbeschreibung als Akkordeon darzustellen.
- Feature: Neue Konfigurationen hinzugefügt, um Header-Schatten beim Mouseover zu aktivieren/deaktivieren.
- Feature: Neue Konfigurationen hinzugefügt, um Header-Schatten bei Sticky zu aktivieren/deaktivieren.
- Feature: Neue Konfiguration hinzugefügt, um die Hintergrundfarbe für Galeriebilder auf der Produktdetailseite festzulegen.
- Feature: Neue Konfiguration hinzugefügt, um die Anzahl der Zeilen für den Produktnamen im Listing anzugeben.
- Feature: Neue Konfiguration hinzugefügt, um die Position des Produktnamens auf Produktdetailseiten festzulegen.
- Feature: Neue Konfiguration hinzugefügt, um eine abweichende Header-Höhe für Sticky-Header anzugeben.
- Feature: Neue Konfiguration hinzugefügt, um die Galerie-Sticky-Funktionalität zu aktivieren/deaktivieren.
- Optimierung: Refactored CMS-Element „Teaser“ und „Text-Image-Slider“.
- Optimierung: Responsive Schriftgröße zum Teaser-Element hinzugefügt.
- Optimierung: Wert für den Elementrandradius zu den Galeriebildern auf der Produktdetailseite hinzugefügt.
- Optimierung: Verbessertes Storefront-Design.
- Optimierung: Vermeidet die Verwendung transparenter Hintergrundfarbe des Bodys zur Berechnung von Farben mit Kontrastfunktionen.
- Optimierung: Twitter-Symbol in X geändert, lol.
- Optimierung: Breadcrumbs im Header-Modus mit voller Breite auf volle Breite gesetzt.
- Optimierung: Scrollbare Off-Canvas-Warenkorbartikel nur ohne Amazon-Checkout und Paypal-Express Buttons.
- Optimierung: Benutzerdefinierte Produktfelder sind verfügbar, wenn die CMS-Buybox auf Landing-, Listing- oder Shop-Seitenlayouts platziert wird.
- Optimierung: „Plugin“ zu Javascript-Plugin-Klassennamen hinzugefügt, um der Shopware-Namenskonvention zu entsprechen.
- Bugfix: Fehler bei der Division durch Null aufgrund fehlender „purchaseSteps“ verhindert, wenn das Produkt von der API erstellt wird.
- Bugfix: Mouseover in der Seitenleiste.
- Bugfix: Suchstandard für Kontrast-Header.
- Bugfix: Ausrichtung der Hauptnavigation bei mehrzeiligen Kopfzeilen.
- Bugfix: Rand des Produktbilds stört das Seitenverhältnis des Produktbilds.
- Bugfix: Modal Schaltfläche zum Schließen bei dunklen Theme-Farben.
- Bugfix: Icon-Ausrichtung des CMS-Elements „Features“.

# 3.4.0
- Funktion: Neue Erlebniswelten-Konfiguration hinzugefügt, um Abstände in unseren Spalten-Blöcken festzulegen. | Pro Version
- Funktion: Neue Konfiguration hinzugefügt, um das Titelbild in der Galerie auf der Produkt-Detailseite im Rastermodus auf 100% zu setzen.
- Funktion: Neue Konfiguration hinzugefügt, um die Rasterspalten in der Galerie auf der Produkt-Detailseite im Rastermodus dynamisch festzulegen.
- Funktion: Neue Konfiguration hinzugefügt, um Filter-Optionen auszublenden, die keine Ergebnisse liefern.
- Funktion: Neue Konfiguration hinzugefügt, um das Limit der direkt sichtbaren Zahlungs-/Versandmethoden im Checkout zu ändern.
- Funktion: Neue Konfiguration hinzugefügt, um die Länge der Beschreibung der Zahlungs-/Versandmethoden im Checkout zu ändern.
- Funktion: Neue Konfiguration hinzugefügt, um Vimeo-Videos zum Tab „Video“ auf Produkt-Detailseite hinzuzufügen.
- Funktion: Neue Konfiguration hinzugefügt, um die Service-Navigation in der Top Bar zu aktivieren/deaktivieren.
- Funktion: Neue Konfiguration zum Aktivieren/Deaktivieren des Produktbeschreibungstitels hinzugefügt.
- Funktion: Neue Konfiguration hinzugefügt, um die Buybox-Sticky-Funktionalität zu aktivieren/deaktivieren.
- Optimierung: Behebt Fehldarstellungen im Zusammenhang mit „Sticky“ und „Overflow Hidden“.
- Optimierung: Verbesserung der Theme-Konfiguration.
- Optimierung: A-Tag vom Hersteller entfernt, wenn die URL leer ist.
- Optimierung: Beibehalten der Anführungszeichen für Schriftfamilien bei Interpolation.
- Optimierung: Erweiterter Produkt-Slider.
- Optimierung: Inhalte-Scroller Überschriften für mobile Geräte.
- Optimierung: Entfernt die Abhängigkeit von der Option „Volle Breite“ von Breadcrumbs für den vertikalen Abstand.
- Optimierung: Überarbeitete Fußzeile des Newsletters.
- Optimierung: Die Sticky-Sidebar-Funktionalität wurde überarbeitet.
- Kompatibilität: Kompatibilität mit NenoProductDetailConfigurator hinzugefügt.
- Bugfix: Konfiguration der Position von Kategoriebildern und Kategorie-Fallback-Bildern.
- Bugfix: Initialisierung des Bild-Text Slider auf der vertikalen Achse in Verbindung mit Lazyloading.
- Bugfix: `display-x`-Klassen in Slidern.
- Bugfix: Border-Radius Hauptnavigation.
- Bugfix: Entfernen des `only` Attribut bei sw_include für twigStringLoaderExtension.

# 3.3.0
- Feature: Neue Konfigurationen hinzugefügt, um den Produkt Layout-Typ für Suchergebnisseite, Wunschliste und Cross-Selling festzulegen.
- Feature: Neue Konfigurationen hinzugefügt, um Produktboxen im Kategorie-Listing zu animieren.
- Feature: Speichern der Einwilligung für Datenblätter im Tab „Produktdetails – Datenblätter“ der Detailseite, wenn Google Docs Viewer verwendet wird.
- Feature: Neue Konfigurationen „Loop“ und „Rewind“ zum Bild-Text-Slider Element hinzugefügt. | Pro Version
- Optimierung: Styling zu CMS-Grid Zwei-Zeilen Layouts hinzugefügt.
- Optimierung: Den Menü-Scroller-Pfeilen wurden maximale Größen hinzugefügt.
- Optimierung: Initialisierung des `js-nav-scrollers`.
- Optimierung: Die Klassennamen von `js-nav-scroller` wurden geändert, um ihren zugewiesenen Elementen zu entsprechen.
- Optimierung: Die Klasse `.main-navigation-item` wurde in `main-navigation-dropdown-item` umbenannt, um ihrer Funktionalität besser zu entsprechen.
- Optimierung: Verbessert die Funktion erweiterter Produkt-Slider.
- Optimierung: CMS-Element `zenit-breadcrumb` in `zen-breadcrumb` umbenannt, um den Namenskonventionen zu entsprechen.
- Optimierung: CMS-Element `zenit-features` in `zen-features` umbenannt, um den Namenskonventionen zu entsprechen.
- Bugfix: Ionicons stack icon.
- Bugfix: Behebt die Anzeige von Checkboxen in der Theme-Konfiguration.
- Bugfix: Verhindert horizontales Scrollen beim Cross-Selling mit extra großen Produktbox-Abständen.
- Bugfix: `js-nav-scroller` hat die Ausrichtung der Hauptnavigation gestört.
- Bugfix: Fehlende Kontrastfarben im Header bei Verwendung des zwei-zeiligen Headers.
- Bugfix: Behebt NEXT-29203 | CMS-Seite: CSS-Klassen werden nicht mehr angezeigt.

# 3.2.0
- Feature: CMS-Element "Code" zum Schreiben von HTML/Twig, CSS und JS hinzugefügt. | Pro Version
- Feature: Bild-Text-Slider Bildersortierung per Drag & Drop hinzugefügt. | Pro Version
- Feature: CMS-Element „Trenner“ hinzugefügt. | Pro Version
- Feature: Javascript Navigation-Scroller für die Hauptnavigation hinzugefügt.
- Feature: Neue Konfiguration hinzugefügt, mit welcher der überlagernde Header mit verschiedenen Kontrast-Farben angezeigt werden kann.
- Feature: Neue Konfiguration hinzugefügt, um den Anzeigemodus der Tabs auf Produktdetailseite als Inhalts-Scroller auf mobilen Geräten anzuzeigen.
- Feature: Einwilligung-Abfrage für Datenblätter im Tab Produktdetails "Datenblätter" hinzugefügt, wenn Google Docs Viewer verwendet wird.
- Feature: CMS-Layouts hinzugefügt: Einfaches Layout – 4/8, Einfaches Layout – 8/4, Einfaches Layout umgekehrt – 4/8 und Einfaches Layout umgekehrt – 8/4. | Pro Version
- Feature: CMS-Layouts hinzugefügt: Erweitertes Layout und Erweitertes Layout umgekehrt. | Pro Version
- Feature: CMS-Grid hinzugefügt: Zwei Spalten / zwei Elemente – 6/6, Drei Spalten / zwei Elemente – 4/4/4, Vier Spalten / zwei Elemente – 3/3/3/3. | Pro Version
- Feature: CMS-Element Platzhalter im Elemente-Fenster verfügbar gemacht. | Pro Version
- Kompatibilität: Kompatibilität mit Shopware 6.5.1.0 hinzugefügt.
- Optimierung: Verbesserungen des Bild-Text-Sliders. | Pro Version
- Optimierung: Neue Konfiguration hinzugefügt, um im CMS-Element Bild-Text-Slider vom Text-Editor zum Code-Editor zu wechseln, um eine Code-Bereinigung zu verhindern. | Pro Version
- Optimierung: Neue Eigenschaft „enableTransparentBackground“ zum Texteditor hinzugefügt.
- Optimierung: Verbessert die deaktivierte Mengenauswahl für digitale Produkte.
- Optimierung: Typ der Variable `zen-layout-container-width` in int geändert.
- Optimierung: Breadcrumbs Text Sie sind hier Schema-Markup.
- Optimierung: Verbesserung der Theme-Konfiguration.
- Bugfix: Bild-Text-Slider Border-Radius und Zoom bei Hover Overflow.
- Bugfix: Preloader ausblenden, wenn Back-Forward-Cache verwendet wird.
- Bugfix: Sektion Sichtbarkeit auf Produktdetailseiten korrigiert.
- Bugfix: Die Anzeige der Miniaturansichten von Kategoriebildern bei Verwendung des Sektion-Hintergrundbild Fallbacks wurde korrigiert.

# 3.1.0
- Feature: Neue Konfigurationen hinzugefügt, um Spalten für Zahlungs- und Versandlogos hinzuzufügen.
- Feature: Neue Konfigurationen hinzugefügt, um benutzerdefinierte Spalte 2 und benutzerdefinierte Spalte 3 hinzuzufügen.
- Feature: Neue Konfiguration hinzugefügt, um einen unteren Abstand des Headers festzulegen.
- Feature: Neue Konfigurationen hinzugefügt, um die Sichtbarkeit der Slider Steuerung bei Mouseover einzustellen. 
- Feature: Fixierte Checkout Buttons für den Offcanvas Warenkorb hinzugefügt. 
- Feature: Neue Konfiguration hinzugefügt, um die Produktfeatures Überschrift zu entfernen.
- Feature: Neue Konfiguration hinzugefügt, um die Position der Produktfeatures festzulegen. 
- Feature: Dynamische URL-Felder zum Erlebniswelten Bild-Slider-Element und Teaser-Element hinzugefügt. 
- Optimierung: Thumbnails zur Darstellung von Kategoriebildern verwenden. 
- Optimierung: Veraltetes Konfigurationsfeld „zen-contrast-threshold“ entfernt. 
- Optimierung: Verbesserung der Theme-Konfiguration. 
- Optimierung: Allgemeine Storefront-Optimierungen. 
- Optimierung: Fixierte Position aus der Flyout-Navigation entfernt. 
- Optimierung: Verbesserung der Transition der Parallax-Mausbewegungsanimation. 
- Optimierung: Unnötige Titel-Tags aus der Navigation entfernt. 
- Optimierung: Marketing-Slider in der Top Bar zentriert, wenn angemeldet. 
- Optimierung: Marketing-Slider in der Top Bar nun auch auf Mobilgeräten. 
- Bugfix: Horizontales Scrollen wegen Konto-Dropdown. 
- Bugfix: Größe der Videoeinbettung in Video-Tabs. 
- Bugfix: Elementsymbol im Filterfeld. 
- Bugfix: Thumbnail-Slider im Qickview korrigiert. 
- Bugfix: XXL-Viewport zum Karussellmodus des Bild-Slider-Elements hinzugefügt.

# 3.0.0
- Feature: Neue Konfiguration hinzugefügt, um Checkout-Layouts zu ändern.
- Feature: Neue Konfiguration hinzugefügt, um Checkout-Schritte zu aktivieren/deaktivieren.
- Feature: Neue Konfiguration hinzugefügt, um die Position der Nutzungsbedingungen für die Kasse zu ändern.
- Feature: Neue Konfiguration hinzugefügt, um die Farben des Konfigurators anzupassen.
- Feature: Neue Konfigurationen hinzugefügt, um nicht-kombinierbaren Eigenschaften des Konfigurators zu kennzeichnen.
- Feature: Neue Konfigurationen hinzugefügt, um Sroll-Animation Verzögerung and Scroll-Animation Dauer einzustellen.
- Feature: Sticky-Sidebar-Funktionalität hinzugefügt.
- Feature: Neue Konfiguration hinzugefügt, um die globale Linkfarbe zu ändern.
- Feature: Neue Konfiguration hinzugefügt, um das Aussehen der Lieferstatusanzeige zu ändern.
- Kompatibilität: Kompatibilität mit Shopware 6.5.0.0 hergestellt. 
- Kompatibilität: SW6.5 - Entfernen alter Icons in der Verwaltung.
- Kompatibilität: Neuer Selektor zum Öffnen von Ajax-Modal.
- Kompatibilität: XXL-Viewport als Vererbung des XL-Viewports auf CMS fünf Spalten Raster hinzugefügt.
- Kompatibilität: Deprecations hinzufügen. 
- Kompatibilität: Erhalten der Mengenauswahl.
- Kompatibilität: Erhalten des Servicemenüs in der oberen Leiste.
- Kompatibilität: Dispatch event in ZenBuyWidgetQuantityInput behoben.
- Kompatibilität: Text-Slider im neuen Offcanvas korrigiert.
- Kompatibilität: Klasse `.has-product-actions` von product-box zu body verschoben, um ein Überschreiben zu verhindern.
- Optimierung: Namespaced-Datenattribute, um die eigene Funktionalität vom Shopware-Core und Drittanbietern zu unterscheiden. Z.B. `data-sticky` umbenannt zu `data-zen-sticky`.
- Optimierung: Überarbeitung von Ladeanimationen.
- Optimierung: Flackern von CMS-Animationen verhindert.
- Optimierung: Vereinheitlicht Produktbild-Darstellung bei Layout-Typ 'Großes Bild'.
- Optimierung: Vereinheitlicht Produktbildanzeige für Anzeigemodus 'Beinhalten'.
- Optimierung: Reduziert cumulative layout shift (CLS) auf Produkt Detailseite.
- Optimierung: Reduziert Höhe der Galerie mit Platzhalterbild.
- Optimierung: Verbessert die Positionierung des Dropdowns bei Verwendung des Menüscrollers.
- Optimierung: Die Konfiguration zum Festlegen der Anzahl von Produkten pro Zeile ist jetzt auf den Such- und Wunschlistenseiten verfügbar.
- Optimierung: Verbessert die Farbe des Formulartexts in der Fußzeile des Newsletters.
- Optimierung: Fehlende ID für aria-labelledby im Dropdown-Menü hinzugefügt.
- Optimierung: Stellt sicher, dass der Inhalt beim Checkout die verbleibende Höhe der Seite ausfüllt.
- Optimierung: Verbessert die Auswahl des passenden Eingabefeldes, auch wenn Label und Eingabefeld nicht benachbart sind.
- Optimierung: Floating Labels bei Textareas verfügbar.
- Optimierung: Verbesserung der Theme-Konfiguration.
- Optimierung: Elemente oberhalb des Headers werden nun beim Sticky Header berücksichtigt.
- Optimierung: Tooltip-Titel der oberen Leiste zum Einklappen verbessert.
- Optimierung: Lighthouse-Verbesserungen.
- Optimierung: a-Element zu Button-Element bei Basic-Captcha-Content-Refresh geändert.
- Bugfix: Fehlender Hintergrund bei Buttons zur Mengeneingabe im Listing bei Bild-Überlagerung.
- Bugfix: Verhindert die Division durch null Twig-Warnung bei der srcset-Berechnung der Produktbox.
- Bugfix: Benutzerdefinierte Tabs Titel.
- Bugfix: Sticky-Header-Viewport-Auswahl korrigiert.
- Bugfix: Checkout zusätzlicher Kommentar unterbricht Layout der Zielseite.

# 2.13.2
- Feature: Neue Konfiguration hinzugefügt, um die Tooltips des Produktkonfigurators zu aktivieren/deaktivieren.
- Optimierung: Flackern von CMS-Animationen verhindert.
- Optimierung: Anzeige des Seitenverhältnisses der Produktbilder überarbeitet. 
- Optimierung: `navigationMaxDepth` für Flyout-Navigation und Dropdown-Navigation überarbeitet. 
- Optimierung: Grenzfälle für Produkt-Overlay-Aktionen behoben. 
- Optimierung: Verbesserung der Anzeige von Produktaktionen. 
- Optimierung: Verbesserung der Theme-Konfiguration. 
- Optimierung: Kaufen-Button für Artikel mit erweiterten Preisen im Quickview. 
- Optimierung: CSS Selektion der CMS-Elemente anhand ihrer Klasse anstatt der ID, damit sie einfacher überschrieben werden können. 
- Optimierung: Optimierte CMS-Animation Verzögerung und Dauer. 
- Optimierung: Klasse `.small-gutters` entfernt, da sie nirgendwo verwendet wird. 
- Optimierung: Galerie-Slider-Anzeigemodus Contain verhält sich jetzt wie Object-Fit Contain. 
- Kompatibilität: Kompatibilität mit BstCatalogMode6 im Quickview hinzugefügt. 
- Bugfix: Paginierung Rahmenradius behoben, wenn die Option zum Ausblenden von Disabled-Buttons aktiv ist. 
- Bugfix: openThemeConfig-Funktion im Dashboard. 
- Bugfix: Ausrichtung der Hauptnavigation bei konfigurierter Dropdown-Navigation korrigiert.
- Bugfix: Verhindert die Division durch null Twig-Warnung bei der srcset-Berechnung der Produktbox.
- Bugfix: Benutzerdefinierte Tabs Titel.

# 2.13.1
- Feature: Label vor der Produktnummer im Listing hinzugefügt.
- Optimierung: Checkout-Aside Sticky Funktionalität hinzugefügt.
- Optimierung: Button-Abstand für gestapelte Buttons in Bild-Slidern hinzugefügt.
- Optimierung: Schlagschatten und Rahmenfarbe unten bei absolut positioniertem Header entfernt.
- Optimierung: `page.product.stock` in `page.product.stock` geändert.
- Optimierung: Wartungsmodus Seite optimiert.
- Kompatibilität: Größe der Konfigurator-Optionen auf Produktdetailseite eingegrenzt.
- Bugfix: Farbe des Breadcrumb-Vortextes in CMS Hero-Sektionen angepasst.
- Bugfix: Checkout-Zusatztext bricht Checkout Layout.
- Bugfix: Fix responsive Schriftgrößen im Bild-Slider.
- Bugfix: Breite der Mengeneingabe in Mozilla Firefox.

# 2.13.0
- Feature: Überarbeitung des Erlebniswelten-Elements Features mit neuen Einstellungen. | Pro Version
- Feature: Warenkorb und checkout überarbeitet.
- Feature: Checkout-Steps hinzugefügt.
- Feature: Ausklappbare Sidebar Navigation hinzugefügt.
- Feature: CSS Root-Variablen in der Storefront hinzugefügt.
- Feature: Neue Konfiguration hinzugefügt, um Bewertungsplatzhalter in Listings und auf der Detailseite hinzuzufügen.
- Feature: Neue Konfiguration hinzugefügt, um die Schriftstärke und Schriftgröße der Dropdown-Navigation festzulegen.
- Feature: Neue Konfiguration hinzugefügt, um Filterschriftgröße, Schriftstärke und Schriftstärke bei aktiv zu ändern.
- Feature: Neue Icon-Sets `fluent-outline`, `fluent-solid`, `heroicons-outline`, `heroicons-solid`, `iconic`, `ionicons-outline`, `ionicons-solid` hinzugefügt.
- Feature: Neue Konfiguration hinzugefügt, um den Aktiv-Status des Home-Eintrags darzustellen.
- Feature: Neue Konfiguration hinzugefügt, um die Anzeige von Produktaktionen auf mobilen Geräten zu steuern.
- Feature: Neue Konfiguration hinzugefügt, um die Farben heller Buttons zu ändern.
- Feature: Neue Konfiguration hinzugefügt, um die Farben dunkler Buttons zu ändern.
- Feature: Verpackungseinheit zu Mengeneingabefeldern hinzugefügt.
- Feature: Neue Konfiguration hinzugefügt, um Breadcrumbs Vortext hinzuzufügen.
- Kompatibilität: SW6.5 - backward-compatibility code - Added variable `dataBsToggleAttr` to replace `data-toggle` with `data-bs-toggle`.
- Kompatibilität: SW6.5 - backward-compatibility code - Added variable `dataBsTargetAttr` to replace `data-target` with `data-bs-target`.
- Kompatibilität: SW6.5 - backward-compatibility code - Added variable `formSelectClass` to replace `custom-select` with `form-select`.
- Kompatibilität: SW6.5 - backward-compatibility code - Added variable `gridNoGuttersClass` to replace `no-gutters` with `g-0`.
- Kompatibilität: SW6.5 - backward-compatibility code - Added variable `formRowClass` to replace `form-row` with `row g-2`.
- Kompatibilität: SW6.5 - backward-compatibility code - Added variable `marginStartClass` to replace `.ml-*` to `.ms-*`.
- Kompatibilität: SW6.5 - backward-compatibility code - Added variable `marginEndClass` to replace `.mr-*` to `.me-*.`.
- Kompatibilität: SW6.5 - backward-compatibility code - Replaced `data-placement` with `data-bs-placement`.
- Kompatibilität: SW6.5 - backward-compatibility code - Replaced `header-gutters` with `gx-1`.
- Kompatibilität: SW6.5 - backward-compatibility code - Replaced `btn-block` class with `d-grid` wrapper instead.
- Kompatibilität: SW6.5 - backward-compatibility code - Removed `input-group-append` wrapper and uses elements as direct children of `input-group`.
- Kompatibilität: SW6.5 - backward-compatibility code - Used the breakpoint itself instead of the next breakpoint in `media-breakpoint-down()`.
- Kompatibilität: SW6.5 - backward-compatibility code - Used the breakpoint itself instead of the next breakpoint in `media-breakpoint-between()` for second parameter.
- Kompatibilität: SW6.5 - backward-compatibility code - Renamed `color-yiq()` function and related variables to `color-contrast()` as it’s no longer related to YIQ colorspace.
- Kompatibilität: SW6.5 - backward-compatibility code - Renamed `$custom-select-background` into `$form-select-bg`.
- Kompatibilität: SW6.5 - backward-compatibility code - Renamed `$custom-select-light-background` into `$form-select-light-bg`.
- Kompatibilität: SW6.5 - backward-compatibility code - Removed `$form-select-light-bg`.
- Kompatibilität: SW6.5 - backward-compatibility code - Replaced `$custom-select-*` variable with `$form-select-*`.
- Kompatibilität: SW6.5 - backward-compatibility code - Replaced `border-right-radius` mixin with `border-end-radius`.
- Kompatibilität: SW6.5 - backward-compatibility code - Replaced `border-left-radius` mixin with `border-start-radius`.
- Kompatibilität: SW6.5 - backward-compatibility code - Replaced `sr-only` with `visually-hidden` in SCSS.
- Kompatibilität: SW6.5 - backward-compatibility code - Added additional `order-`classes in bootstraps utility API.
- Kompatibilität: SW6.5 - backward-compatibility code - Added new configuration `zen-contrast-threshold`.
- Kompatibilität: SW6.5 - backward-compatibility code - Deprecated configuration `zen-min-contrast-ratio`.
- Kompatibilität: SW6.5 - backward-compatibility code - Migrated all jQuery implementations to Vanilla JavaScript in all JavaScript plugins.
- Kompatibilität: SW6.5 - backward-compatibility code - Added `xxl` viewport to viewport detection in all JavaScript plugins.
- Kompatibilität: SW6.5 - backward-compatibility code - Renamed embed helpers to ratio helpers.
- Kompatibilität: SW6.5 - change tooltip instances to prevent multiple instances per element.
- Kompatibilität: SW6.4.19.0 - Mengenauswahl für digitale Produkte entfernt.
- Kompatibilität: Remove override of cookiePermission plugin to increase compatibility.
- Optimierung: Verbesserte Button-Innenabstände.
- Optimierung: Merkliste-Buttons immer sichtbar bei bereits hinzugefügten Produkten.
- Optimierung: Inhalt aus Erlebniswelten-Rasterplatzhalterelementen entfernt, sodass diese standardmäßig als Spaltenabstandshalter verwendet werden können.
- Optimierung: Namespaced-Datenattribute, um die eigene Funktionalität vom Shopware-Core und Drittanbietern zu unterscheiden. Z.B. `data-sticky` umbenannt zu `data-zen-sticky`.
- Optimierung: Footer-Link hover unterstrichen.
- Optimierung: Footer-Farbe zu h-Tags hinzufügen.
- Optimierung: Verringert den Versatz der CMS-Inhaltsanimationen, um die Verzögerung zu verringern.
- Optimierung: Verbesserung der Theme-Konfiguration.
- Optimierung: Verringert die Standardschriftgröße und -stärke der unteren Fußzeile.
- Optimierung: Verbessert das initiale Laden des Text-Sliders mit `document.fonts.ready`.
- Optimierung: Abfrage der Versand- und Zahlungslogos in der Fußzeile hinzugefügt.
- Optimierung: Newsletter-Eingabe in der Fußzeile verbessert.
- Optimierung: Kompatibilität zwischen Menü-Scroller und Dropdown-Navigation hinzugefügt.
- Optimierung: Bilder im Hochformat in Thumbnails src-sets werden nun besser berücksichtigt, da die Breite ihrer Thumbnails geringer ist als die berechnete Thumbnail-Spezifikation.
- Optimierung: Erzwingt das Ausblenden des Preloaders, wenn ein JavaScript-Fehler auftritt.
- Optimierung: Optimierte CMS-Grid-Breakpoint-Klassen.
- Optimierung: Legt einen vertikalen Abstand für CMS-Grids fest, da dies der häufigste Anwendungsfall ist. Abstände können mit den Abstandklassen aus der Doku überschrieben werden.
- Optimierung: Sticky-Header-Animation von css-transform zu margins geändert, um Probleme mit dem Stacking-Kontext zu beheben.
- Optimierung: Platzhaltertext für CMS-Elemente in Auswahlen entfernt, um leere Werte zu verhindern.
- Optimierung: Viele viele Styling-Verbesserungen.
- Optimierung: Veraltete Design-Konfigurationsfelder entfernt `zen-layout-container-main-padding-top`, `zen-layout-container-main-padding-bottom`, `zen-category-filter-seperator`, `zen-container-color`, `zen-product-listing-img-width`, `zen-product-listing-img-height`, `zen-sidebar-navigation-max-depth`.
- Optimierung: Benutzerdefinierte SCSS-Variablen haben nun ein Präfix.
- Optimierung: Gallery-Grid und Gallery-Scroller komplett überarbeitet.
- Bugfix: Gallery-Slider Platzhalter.
- Bugfix: Ausrichtung der Bewertungen der Produktboxen auf die Standardposition.
- Bugfix: Verhindert, dass sich nachfolgende Slides während der initialen Animation auf dem Text-Slider überlagern.
- Bugfix: Sticky Header bei überlagernden Headern.
- Bugfix: Hersteller auf der CMS-Produktdetailseite ausblenden wieder möglich.
- Bugfix: Fix NEXT-24201 für Slider Elemente. | Pro Version
- Bugfix: Footer Konfigurationen korrigiert.

# 2.12.2
- Bugfix: Verwendung von iconSets-key in theme.json wieder entfernt, da es in der Shopware-Cloud nicht richtig funktioniert.
-
# 2.12.1
- Bugfix: Korrigiert iconSets Pfade in der theme.json.

# 2.12.0
- Feature: Neue Konfiguration hinzugefügt, um die Schriftfamilie für Footer Inhalte festzulegen.
- Feature: Neue Konfiguration hinzugefügt, um die Schriftgröße und Schriftstärke des unteren Footers festzulegen.
- Feature: Neue Konfiguration hinzugefügt, um die Schriftfamilie für den Namen der Produktdetails festzulegen.
- Feature: Neue Konfiguration hinzugefügt, um die Schriftfamilie für den Produktnamen auf Produktseiten festzulegen.
- Feature: Neue Konfiguration hinzugefügt, um den oberen Rahmen der Hauptnavigation auf volle Breite einzustellen.
- Feature: Neue Konfiguration hinzugefügt, um Galerie-Thumbnail-Navigation im eingestellten Produkt-Seitenverhältnis anzuzeigen.
- Feature: Neue Konfiguration hinzugefügt, um zwischen verschiedenen Galerie-Thumbnail-Navigationsstilen zu wählen.
- Feature: Neue Konfiguration hinzugefügt, um Breadcrumbs einen Home-Link hinzuzufügen.
- Feature: Neue Konfiguration zum Einstellen des Galerie-Zoomfaktors hinzugefügt.
- Feature: Neue Konfiguration hinzugefügt, um die Herstellerposition für Produktdetails und Schnellansicht festzulegen.
- Feature: Neue Icon-Sets `Iconly Bulk`, `Iconly Broken`, `Iconly Light-Outline` hinzugefügt
- Feature: Neue Konfiguration hinzugefügt, um den Rand der oberen Leiste unten festzulegen.
- Feature: Bootstrap Responsive Font Size Mixin wird nun in SCSS verwendet.
- Optimierung: Icons für den Icon-Cache vorbereitet.
- Optimierung: Entfernt den aktiven Zustand des Breadcrumb-Elements.
- Optimierung: Layout-Containerbreite für Thumbnail-Utility hinzugefügt.
- Optimierung: Allgemeine Verbesserungen der Theme-Konfiguration.
- Optimierung: Verbessert die zentrierte Ausrichtung der Kategorie-Navigation, wenn sie in eine neue Zeile umbricht.
- Optimierung: Spalten zu Blöcken für Thumbnail-Größen hinzugefügt.
- Optimierung: Option zum automatischen Ausblenden von Sticky-Header bei mehrzeilige Headern auf Mobilgeräten hinzugefügt.
- Optimierung: Hover-Status der Suchschaltfläche entfernt.
- Optimierung: Kompatibilität der floating Labels Funktion erhöht.
- Bugfix: Doppelte Itemprops entfernt.
- Bugfix: Behebt die doppelte Anzeige von Themes im Dashboard.
- Bugfix: Tooltips verzögern sich bei Kartenaktionen.
- Bugfix: Umbenennung des Icons "eye" in "eye-open", um ein Problem mit dem Quickview Icon zu beheben.

# 2.11.0
- Feature: Neue Themen-Schriften hinzugefügt: „Comfortaa“, „Frank Ruhl Libre“, „Montserrat“, „Open Sans“, „Playfair Display“, „Raleway“, „Signika“, „Vidaloka“.
- Feature: Standardwert für 'Lade Schriften von Google' auf `false` gesetzt und Schriften als Theme-Schriften implementiert.
- Feature: Neue Konfiguration hinzugefügt, um Artikelnummer `Farbe`, `Schriftgröße`, `Gewicht` und `Textausrichtung` in Produkt-Boxen festzulegen.
- Feature: Neue Konfiguration hinzugefügt, um Variantenmerkmale `Farbe`, `Schriftgröße`, `Gewicht` und `Textausrichtung` in Produkt-Boxen festzulegen.
- Feature: Neue Konfiguration hinzugefügt, um Varianten günstigster Preis `Farbe`, `Schriftgröße`, `Gewicht` und `Textausrichtung` in Produkt-Boxen festzulegen.
- Feature: Neue Konfiguration hinzugefügt, um Grundpreis `Farbe`, `Schriftgröße`, `Gewicht` und `Textausrichtung` in Produkt-Boxen festzulegen.
- Feature: Ablaufdatum zum optionalen Google Fonts-Cookie hinzugefügt.
- Feature: Zusätzliche Möglichkeit zum Schließen der Overlay-Suche und ausklappbare Suche mit Escape-Taste.
- Feature: Die Sichtbarkeit der Recaptcha-Felder in der Fußzeile ändert sich je nach Eingabewert des Newsletters.
- Optimierung: Das Bildseitenverhältnis wird in die Berechnung der besten srcset-Größen für Hochformat-Produktkartenbilder basierend auf verschiedenen Themeneinstellungen berechnet.
- Optimierung: Verbesserung der Theme-Konfiguration.
- Optimierung: Zusätzliche Abfrage zur Anzeige des Hersteller-Tabs auf Produktdetailseiten hinzugefügt.
- Optimierung: Legt horizontale Abstände für die gesperrten Erlebniswelt Produktseiten Standardseite fest.
- Optimierung: Legt horizontale Abstände für gesperrte Erlebniswelt Kategorieseiten Standardseiten fest.
- Optimierung: Verhindert das Problem NEXT-9276 | Erlebniswelten Slider ungenau.
- Optimierung: Laden von Symbolen für Produktdetailfunktionen.
- Optimierung: Benutzerdefinierte Komponente hinzugefügt, um Konfigurationswarnungen anzuzeigen.
- Optimierung: Neuinitialisierung von Tooltip-Utilities bei der Initialisierung des Produkt-Sliders.
- Bugfix: Verbessert Floating-Labels, um zu verhindern, dass diese Zahlungsartenfelder überlagern.
- Bugfix: Verhindert, dass der Erlebniswelten Crossselling-Slider die Seite horizontal erweitert, wenn kein horizontaler Blockabstand angegeben ist.
- Bugfix: Fehlendes End-Div bei vertikaler Ausrichtung des Slider hinzugefügt.
- Bugfix: Footer Captcha Randradius rechts optimiert.

# 2.10.3
- Kompatibilität: Shopware 6.4.17

# 2.10.2
- Optimierung: `ProductCrossSellingStreamCriteriaEvent` hinzugefügt, um Cover Switch im Cross-Selling-Stream zu ermöglichen. | Pro Version
- Bugfix: Verhindert die Neuerstellung des Bootstrap-Grids für ein Fünfer-Grid.

# 2.10.1
- Optimierung: Top-Bar Dropdown Aktiv-Status Farbe bei Buttons.
- Optimierung: Darstellung der Theme-Konfiguration Switch-Fields. | Pro Version
- Bugfix: CMS Teaser-Element konnte nicht positioniert werden. | Pro Version

# 2.10.0
- Feature: Neue Konfiguration-Option hinzugefügt, um Produkt-Listings in einem Fünferraster darzustellen.
- Feature: Neue Konfiguration hinzugefügt, um den Border-Radius für jedes Element des Bild-Text-Sliders festzulegen. | Pro Version
- Feature: Neue Konfiguration hinzugefügt, um den Border-Radius für den Bild-Text-Slider festzulegen. | Pro Version
- Feature: Neue Konfiguration hinzugefügt, um den vertikalen Abstand der Breadcrumbs festzulegen.
- Kompatibilität: Shopware 6.4.15 - Fix twig source path for icons.
- Kompatibilität: Schaltfläche zum Schließen der Overlay-Suche von `layout_header_search_input_group` nach `layout_header_search` verschoben, um Kompatibilität mit SasBlogModule herzustellen.
- Kompatibilität: Kompatibilität mit NetiNextAccessManager im Quickview hinzugefügt.
- Optimierung: Galerie-Raster Abstände.
- Optimierung: Verbesserung der Theme-Konfiguration.
- Optimierung: Übersetzung der CMS Teaser-Konfiguration.
- Optimierung: Placeholder-Farbe für Eingabefelder im Fokus.
- Optimierung: Zusätzliche Prüfung für die Service-Navigation im Footer Bottom.
- Optimierung: Galerie-Scroller und Galerie-Raster an das Galerie-Slider-Element anstatt an den CMS-Block gebunden.
- Optimierung: Sticky-Header Ausblenden Animation.
- Optimierung: Zusätzliche Abfrage zu Social-Media Icons in der Fußzeile.
- Optimierung: Refactoring von sw_includes und sw_extends `@Storefront` neuer Template-Dateien.
- Optimierung: Checkout minimal Header Layout Logo Position.
- Optimierung: Übergabe des aktuellen Slides an das Galerie-Scroller Zoom-Modal.
- Optimierung: Optimierte vertikale Position des Logos in der einzeiligen mobilen Kopfzeile und Standardsuche.
- Optimierung: Fallback für `zen-layout-container-width` in `box.html.twig` hinzugefügt.
- Optimierung: Verbessert die Cover Switch Bild ermittlung.
- Bugfix: Galerie-Scroller-Thumbnail-Navigation.
- Bugfix: Schriftarten werden jetzt auch geladen, wenn sie nicht in Anführungszeichen angegeben sind.
- Bugfix: Text-Transform des Produktnamens im Listing.
- Bugfix: Crossselling auf CMS-Seiten führt unter Umständen zu horizontalem Scrollen.

# 2.9.2
- Kompatibilität: Shopware 6.4.15 - CMS element data resolving mismatch | Pro version
- Kompatibilität: Shopware 6.4.15 - Add twig blocks for image to the product box card template
- Kompatibilität: Shopware 6.4.15 - Main variant visualisation NEXT-17544
- Bugfix: Galerie-Scroller und Galerie-Grid korrigiert.

# 2.9.1
- Feature: Ermöglicht Twig-Syntax für Variablen und Snippets im globalen Custom Tab | Pro version
- Bugfix: Behebt den Fehler `Unknown "template_from_string" function` error. | Cloud version

# 2.9.0
- Feature: Variantenwechsel im Quickview hinzugefügt. | Pro Version
- Feature: Ermöglicht Twig-Syntax für Variablen und Snippets in benutzerdefinierten Feldern des Themes und eigenen Erlebniswelten Elementen. | Pro Version
- Feature: Neue Konfiguration hinzugefügt, um YouTube-Video-Platzhalter-Thumbnails ein- / auszublenden.
- Optimierung: Erlaubt HTML-Syntax im Erlebniswelt Element Teaser Text.
- Optimierung: Quickview überarbeitet.
- Optimierung: Event zum Schließen der Quickview-Ansicht, beim Öffnen des Offcanvas-Warenkorbs.
- Optimierung: Verbessert die Animation beim Öffnen des Quickview-Modals.
- Optimierung: Floating Labels werden bereits bei Fokus hochgesetzt für bessere Benutzerfreundlichkeit.
- Optimierung: Optimierte Paginierungs-Pills bei größerer Basisschriftgröße.
- Optimierung: Schriftgröße der Preiseinheit der Produktkarte auf die Schriftgröße der Beschreibung gesetzt.
- Optimierung: Header-Padding zum minimalen Header im Checkout hinzugefügt.
- Optimierung: Cover-Switch Hover verbessert.
- Optimierung: Neuformatierung des SCSS-Codes.
- Optimierung: Verbesserungen beim Button Hover.
- Optimierung: Produkt-Aktionen im Listing.
- Optimierung: Refactoring von sw_includes neuer Templatedateien.
- Bugfix: Schriftgröße von Produktnummern in Listings korrigiert.
- Bugfix: Überprüfung auf Filterpanel-Elemente im Offene-Filter Plugin.

# 2.8.0
- Feature: Neue Konfiguration hinzugefügt, um den mobilen Header auf eine einzelne Zeile einzustellen.
- Feature: Neue Konfiguration hinzugefügt, um benutzerdefiniertes Javascript im Head hinzuzufügen.
- Feature: Neue Konfiguration hinzugefügt, um den Anzeigemodus der Registerkarten auf der Produktdetailseite auf einen Inhalts-Scroller einzustellen.
- Feature: Neue Konfiguration hinzugefügt, um benutzerdefinierte srcset-Größen für Produkt-Cards Produktbilder festzulegen.
- Feature: Berechnung der besten srcset-Größen für Produkt-Cards Produktbilder basierend auf verschiedenen Themeneinstellungen.
- Feature: Überarbeitete Darstellung von Produktaktionen.
- Feature: Neue Konfiguration hinzugefügt, um die Position der Wunschliste-Schaltfläche auf der Galerie festzulegen.
- Feature: Neue Konfiguration hinzugefügt, um die Zustimmung für Youtube-Videos zu erfragen im Video-Tab der Produktdetails.
- Feature: Neue Konfiguration hinzugefügt, um den Fallback der alt- und title-Tags der Image Gallery zu ändern.
- Optimierung: Sticky-Header-Offset verbessert, um Sticky-Header ohne Offset zu verwenden.
- Optimierung: Die Ausgabe von benutzerdefiniertem CSS- und JS-Code wurde verbessert.
- Optimierung: Überarbeitete Buttons.
- Optimierung: Lazy Loading für Slider nur im Cover-Modus verfügbar gemacht, da es sonst zu Problemen bei der Höhenberechnung kommen kann.
- Optimierung: Styling von Thumbnail-Steuerelementen der Image Gallery.
- Optimierung: Verbessert das Styling des Newsletter-Logos in der Fußzeile.
- Optimierung: Verbessert das Styling der Sidebar-Navigation.
- Optimierung: Verbessert die Subpixel der Galerie-Slider.
- Optimierung: Erweitert nun `base_body_script` anstatt `base_script_hmr_mode`
- Optimierung: Drop-down-Menü des Kontomenüs überarbeitet.
- Optimierung: Textdekoration Unterlinie zu hellem und dunklem Text in der CMS Hero-Section hinzugefügt.
- Optimierung: Hover-Hintergrundfarbe für Hauptnavigationselemente in transparenter Kopfzeile anzeigen.
- Optimierung: Block `zen_base_webfont` nach meta.html.twig verschoben, um Webfont Loader früher zu laden und FLOUT zu vermeiden.
- Optimierung: Verbesserte Abstände auf der Produktdetailseite.
- Optimierung: Verbessertes Styling von Offcanvas-Menüaktionen.
- Optimierung: Erlebniswelten Sektion Sidebar row auf `no-gutters` gesetzt.
- Optimierung: Verbesserte Produkt-Slider-Animation auf Mobilgeräten.
- Optimierung: Verbesserung verschiedener Darstellungen in der Storefront.
- Bugfix: Korrigiert abgerundete Ecken im Dropdown-Menü.
- Bugfix: Korrigiert die Breadcrumbs-Position einer Hero-Section.
- Bugfix: Korrigiert das Styling des Breadcrumb-Elements der Strukturkategorie.
- Bugfix: Behebt einen Javascript-Fehler bei der erweiterten Anzeige von Filtern.
- Bugfix: Dynamische Zeilenhöhe für Überschriften.
- Bugfix: Anzeigen von Galerie- und Buybox-Elementen außerhalb einer Produktseite.

# 2.7.2
- Feature: Neue Konfiguration hinzugefügt, um maximale Elemente so einzustellen, dass die Punkte-Navigation angezeigt werden.
- Optimierung: Horizontaler Abstand im Footer angepasst, um die Inhalte besser auszurichten.
- Optimierung: Footer Grid basiert nun auf Layout-Container-Abständen.
- Optimierung: Account Navigation angepasst.
- Optimierung: Anpassung der Farben im Account für dunkle Layouts optimiert.
- Optimierung: Verhindert, dass der Scroll-Up-Button die untere Fußzeilennavigation überlagert.
- Kompatibilität: `margin` zu `padding` in `cms-section-block-container.html.twig` geändert.

# 2.7.1
- Kompatibilität: Kompatibilität mit NetiNextEasyCoupon hergestellt.
- Optimierung: Veraltetes `@elseif` in `@else if` in SCSS geändert.
- Bugfix: Fallback-Wert für SCSS-Variable `zen-product-listing-card-big-img-height` geändert.

# 2.7.0
- Kompatibilität: Shopware 6.4.13 - Variable `maxPurchaseThreshold` in `selectQuantityThreshold` geändert.
- Kompatibilität: Shopware 6.4.13 - Wert der Variable `selectQuantityThreshold` auf 100 gesetzt.
- Kompatibilität: Shopware 6.4.13 - Merge Issue NEXT-9423 "Eingabetyp bei hoher Menge umschalten" mit eigener Eingabetyp-Funktionalität.
- Feature: Neue Konfiguration hinzugefügt, um die Abstände der Produktdetails festzulegen.
- Feature: "Sen" als neue Theme-Schriftart hinzugefügt.
- Feature: Neue Konfiguration hinzugefügt, um die Hintergrundfarbe des Produktbildes auf den Listing-Seiten zu aktivieren/deaktivieren.
- Feature: Neue Konfiguration hinzugefügt, um die Hintergrundfarbe des Produktbildes auf der Produktdetail-Seiten zu aktivieren/deaktivieren.
- Optimierung: Verschiedene CSS-Styling-Verbesserungen.
- Optimierung: Unterlängen im Produktnamen wurden teilweise abgeschnitten.
- Optimierung: Verbesserung des Aussehens des Dropdown-Menüs.
- Optimierung: Verbesserung der Bezeichnungen in der theme.json.
- Optimierung: Gesamtbewertungslink zu Bewertungen im Überschriftenbereich hinzugefügt.
- Optimierung: Optimiert den Wortumbruch der Produktdetail-Seiten Eigenschaftstabelle.
- Optimierung: Optimierte Anzeige des unteren Footers.
- Bugfix: Behebt ein Anzeigeproblem der CMS-Produktdetailseite im Zusammenhang mit der Sticky-Buybox.
- Bugfix: Behebt das Problem beim Öffnen der Cookie-Einstellungen Offcanvas von einem anderen Offcanvas heraus.

# 2.6.2
- Feature: Neue Konfiguration zur Einstellung der Dropdown Navigation Navigations-Ebenen.
- Optimierung: Positionskonstanten für CMS-Slots hinzugefügt. | Pro Version
- Optimierung: Zusätzliche Node Prüfung in Overlay Search Javascript-Plugin hinzugefügt.
- Bugfix: Route der Update-Links im Dashboard aktualisiert. | Pro Version

# 2.6.1
- Bugfix: Werte der Zusatzfelder für Produkte pro Zeile beim Listing Render Event hinzugefügt.

# 2.6.0
- Feature: Galerie Raster als neuen Anzeigemodus für die Produktdetailseite hinzugefügt.
- Feature: Neue Konfiguration hinzugefügt, um die Subnavigation als Dropdown-Menü anzuzeigen.
- Feature: Neue Konfiguration zum Stylen des Dropdown-Menüs hinzugefügt.
- Feature: Neue benutzerdefinierte Felder hinzugefügt, um benutzerdefinierte CSS-Klassen pro Kategorie hinzuzufügen.
- Feature: Neue benutzerdefinierte Felder hinzugefügt, um Labels in der Navigation hinzuzufügen.
- Feature: Neue benutzerdefinierte Felder hinzugefügt, um Produkte pro Zeile in der Kategorie zu ändern.
- Feature: Neue Konfiguration hinzugefügt, um die Mengenauswahl anzuzeigen oder auszublenden.
- Feature: Neue Konfiguration hinzugefügt, um die Service-Navigation unten in der Fußzeile anzuzeigen oder auszublenden.
- Feature: Neue Konfiguration hinzugefügt, um das Symbol zum Einklappen der Kopfzeile der Fußzeile zu ändern.
- Feature: Neue Konfiguration hinzugefügt, um die Schriftstärken von Basistext und Überschriften zu ändern.
- Feature: Neue Konfiguration hinzugefügt, um die maximale Bildhöhe des Produktbox-Layouts "Großes Bild" zu ändern.
- Feature: Verbessert die Adressanzeige im Checkout.
- Optimierung: Google Fonts Cookie Consent Manager-Integration. | Pro Version
- Optimierung: Verbesserung der Hersteller-Tab-Abfrage.
- Optimierung: Verbesserung von Bezeichnungen in der theme.json.
- Optimierung: Block `base_noscript` als direktes Kind von body verschoben.
- Optimierung: Platz zwischen Breadcrumbs und Buybox hinzugefügt.
- Optimierung: Breadcrumb-Trenner hinzugefügt.
- Optimierung: Basis-Slider-Punktnavigation auf Mobilgeräten ausgeblendet.
- Optimierung: Unbenutzte Klassen `img-darken` und `img-lighten` entfernt.
- Optimierung: Cross-Selling `displayMode` von `minimal` auf `standard` geändert.
- Optimierung: Cross-Selling `boxLayout` von `standard` auf `minimal` geändert.
- Optimierung: Zusätzlicher Abstand zwischen Auflistung und Paginierung hinzugefügt, wenn der Abstand zwischen den Produktboxen gering ist.
- Optimierung: Verbesserte Platzierung von Labels und Bewertungen in der Produktbox.
- Optimierung: Disabled-Stil für deaktivierte Filteroptionen hinzugefügt.
- Optimierung: Der Menüscroller ist jetzt anklickbar, auch wenn das Flyout-Menü geöffnet ist.
- Optimierung: Verbessert die Darstellung der Mengeneingabe im Listing.
- Bugfix: Neuregistrierung von Floating-Labels Javascript im Checkout beim Ändern der Rechnungs-/Versandadresse in Pseudomodals.
- Bugfix: Galerie-Scroller auf CMS-Block "Bildergalerie und Kaufbox mit Produktnamen & Herstellerlogo" behoben | Pro Version
- Bugfix: Set Footer Newsletter Basic Captcha max-width.
- Bugfix: Erweitert die Header-Opacity Abfrage, um den Vererbungswert korrekt anzuzeigen.
- Bugfix: Erweitert die Abfrage der Navigation-Opacity, um den Vererbungswert korrekt anzuzeigen.

# 2.5.4
- Kompatibilität: Kompatibilität mit Shopware 6.4.11
- Feature: Bereitstellung von Standardmedien für neue CMS-Blöcke | Pro Version
- Optimierung: Entfernt die neue Konfiguration `zen-sidebar-navigation-max-depth` zur Einstellung der Sidebar Navigation Navigations-Ebenen. Stattdessen wird die Konfiguration aus dem Verkaufskanal genutzt.
- Bugfix: Korrigiert `is--sidebar` Klasse, wenn Filter geöffnet dargestellt werden.

# 2.5.3
- Optimierung: Verbesserung der Preloader Javascript Initialisierung
- Optimierung: Device detection für das `load`-Event hinzugefügt, um das von iOS unterstützte `pageshow`-Event zu verwenden
- Optimierung: Device detection für das `unload`-Event hinzugefügt, um das von iOS unterstützte `pagehide`-Event zu verwenden
- Bugfix: Backward compatibility for strict comparison in twig

# 2.5.2
- Info: Child-Theme Update verfügbar (optional) - Besuche https://childtheme-update.zenit.design um mehr über Child-Theme Updates zu erfahren.
- Feature: Erlebniswelten Block Abstands-Klassen hinzugefügt.
- Feature: Neue Konfiguration hinzugefügt, um die Grundpreisanzeige auf Produktboxen zu aktivieren oder zu deaktivieren.
- Feature: Neue Konfiguration hinzugefügt, um den günstigsten Produktpreis auf Produktboxen zu aktivieren oder zu deaktivieren.
- Feature: Neue Konfiguration hinzugefügt, um Produktpreis-Ersparnis in % auf Produktboxen an verschiedenen Positionen anzuzeigen.
- Optimierung: Überarbeitete Preisanzeige in den Produktboxen.
- Optimierung: Blöcke zu `meta.html` hinzugefügt.
- Optimierung: Überarbeitung der `meta.html`.
- Optimierung: Der Standardwert von `textMarginRight` und `textMarginLeft` wurde auf 10 % geändert.
- Optimierung: Das Option-Label des Schriftgewichts "Schwarz – 900" in der Konfiguration des Erlebniswelten-Teaser-Elements wurde korrigiert.
- Optimierung: Verbesserung der Overflow-Darstellung des Erlebniswelten Teaser Elements in der Administration.
- Optimierung: Verbesserung der Dot-Navigation Außen im Erlebniswelten Bild Text Slider Element in der Administration.
- Optimierung: HTML-Ausgabe im Features-Element der Erlebniswelten zulassen.
- Optimierung: Überarbeitung der Überlagerung bestimmter vordefinierter Blöcke bei Header-Überlagerung.
- Optimierung: Überarbeitung der Styles für Produktdetailseite und CMS-Produktdetailseite.
- Optimierung: Erhöhung der Responsive-Font-Skalierung für Anzeigeklassen von Bild Text Slider Elementen.
- Optimierung: Überarbeitung der Zoom Modal-Hintergrundfarbe.
- Optimierung: Entfernt den unteren Außenabstand des Breadcrumb-Containers.
- Optimierung: Verbesserung der Styles für die Produktbewertung in Listings.
- Optimierung: Verbesserungen in der Theme-Konfiguration.
- Optimierung: Verbesserung des Flyout-Menüs.
- Bugfix: Anzeige des Produktbild-Platzhalters in Firefox korrigiert.
- Bugfix: Behebt das Anzeigeproblem im Zoom Gallery Modal.
- Bugfix: Korrigiert eine Abfrage, um Paddings im Card-Body der Produktboxen anzuzeigen.

# 2.5.1
- Optimierung: Zusätzliche Überprüfung für die Darstellung Header Kategoriebild Hero-Sektion hinzugefügt.
- Optimierung: Verbesserung des Main-Navigation-Scroller Fadeout-Bereichs.
- Optimierung: Verbesserte Darstellung der Spalte des Bild Text Sliders, wenn mehrere Elemente angezeigt werden. | Pro Version
- Optimierung: Verbesserung der Darstellung der Teaser-Elemente. | Pro Version
- Optimierung: Verbesserung der Darstellung von Hover-Animationen beim Teaser-Element, wenn keine URL gesetzt ist. | Pro Version
- Optimierung: Verbesserung der `has-bg-opcaity`-Klasse Bedingung.
- Bugfix: Setzen der Standardwerte beim Hochladen von Bildern im Bild Text Slider korrigiert. | Pro Version

# 2.5.0
- Info: Child-Theme Update verfügbar (optional) - Besuche https://childtheme-update.zenit.design um mehr über Child-Theme Updates zu erfahren.
- Feature: CMS-Element "Bild Text Slider" hinzugefügt | Pro Version
- Feature: CMS-Element "Galerie und Buybox mit Produktname & Hersteller-Logo" hinzugefügt | Pro Version
- Feature: CMS-Element "Teaser" hinzugefügt | Pro Version
- Feature: CMS-Block "Teaser - Eine Spalte" hinzugefügt | Pro Version
- Feature: CMS-Block "Teaser - Zwei Spalten" hinzugefügt | Pro Version
- Feature: CMS-Block "Teaser - Drei Spalten" hinzugefügt | Pro Version
- Feature: CMS-Block "Teaser - Vier Spalten" hinzugefügt | Pro Version
- Feature: CMS-Block Grid "Eine Spalte" hinzugefügt
- Feature: CMS-Block Grid "Zwei Spalten" hinzugefügt
- Feature: CMS-Block Grid "Drei Spalten" hinzugefügt
- Feature: CMS-Block Grid "Vier Spalten" hinzugefügt
- Feature: CMS-Block Grid "Fünf Spalten" hinzugefügt
- Feature: CMS-Block Grid "Sechs Spalten" hinzugefügt
- Feature: CMS-Block Grid "Zwei Spalten - 9/3" hinzugefügt
- Feature: CMS-Block Grid "Zwei Spalten - 3/9" hinzugefügt
- Feature: CMS-Block Grid "Zwei Spalten - 8/4" hinzugefügt
- Feature: CMS-Block Grid "Zwei Spalten - 4/8" hinzugefügt
- Feature: CMS-Block Grid "Zwei Spalten - 7/5" hinzugefügt
- Feature: CMS-Block Grid "Zwei Spalten - 5/7" hinzugefügt
- Feature: CMS-Block Grid "Drei Spalten - 6/3/3" hinzugefügt
- Feature: CMS-Block Grid "Drei Spalten - 3/3/6" hinzugefügt
- Feature: CMS-Block Grid "Drei Spalten - 3/6/3" hinzugefügt
- Feature: CMS-Block Grid "Drei Spalten - 2/8/2" hinzugefügt
- Feature: CMS-Block Layout "Einfaches Layout" hinzugefügt | Pro Version
- Feature: CMS-Block Layout "Einfaches Layout umgekehrt" hinzugefügt | Pro Version
- Feature: CMS-Block Layout "Einfaches Teaser Layout" hinzugefügt | Pro Version
- Feature: CMS-Block Layout "Einfaches Teaser Layout umgekehrt" hinzugefügt | Pro Version
- Feature: Benachrichtigung für Child-Theme Update hinzugefügt | Pro Version
- Feature: Zusatzfelder für Landingpages hinzugefügt | Pro Version.
- Feature: Neue Konfiguration zur Darstellung eines TikTok Icons als Social Media Icon hinzugefügt.
- Feature: Neue Konfiguration zur Einstellung der Sidebar Navigation Navigations-Ebenen.
- Feature: Neue Konfiguration zur Einstellung der Flyout Navigation Navigations-Ebenen.
- Feature: Darstellung von Varianten Merkmale im Quickview hinzugefügt.
- Feature: Main Navigation Menu-Scroller hinzugefügt.
- Optimierung: Theme Dashboard zum Erweiterungen Menu hinzugefügt.
- Optimierung: Theme Einstellungen zum Erweiterungen Menu hinzugefügt.
- Optimierung: Konfiguration der Produktbild Überlagerung zum Tab 'Allgemein' in der Theme-Konfiguration verschoben.
- Optimierung: Produktbild Farbüberlagerung optimiert, um Subpixel-Darstellungen zu vermeiden.
- Optimierung: Hintergrundfarbe zum Image Zoom hinzugefügt, um transparente PNG besser zu unterstützen.
- Optimierung: Download-Links von der Preloader-Animation ausgeschlossen.
- Optimierung: Newsletter Privacy `_key` Variablen durch Übergabe im Include-Befehl hinzugefügt.
- Optimierung: Layout-Modus Standard für den Galerie-Slider optimiert.
- Optimierung: Verhindert, dass Produkt-Slider Pfeile abgeschnitten werden.
- Optimierung: Verhindert, dass Produkt-Slider-Pfeile in mobilen Browsern blau umrandet werden.
- Kompatibilität: Laut Telemediendatenschutzgesetz dürfen die Buttons eines Cookie-Banners nur in derselben Farbe und Größe dargestellt werden.
- Kompatibilität: Behebt die Kompatibilität des Features-Elements in Verbindung mit dem Moorl Foundation Plugin.
- Bugfix: Behebt CMS-Element Breadcrumbs horizontale Ausrichtung.
- Bugfix: Blendet die Preloader-Animation aus, wenn wir im Beforeunload-Ereignis stecken bleiben.

# 2.4.7
- Optimierung: Verhindert, dass Textfarbe der Filter durch Text Kontrast Einstellungen einer Hero-Section beeinflusst werden.
- Optimierung: Fügt eine neue Abfrage hinzu, welche die Darstellung von Mengenauswahl im Listing zu Mengeneingabe wechselt, sollte die maximale Auswahlmenge 1000 überschreiten.
- Optimierung: Erzeugt Copyright Jahr mit twig date Function.
- Bugfix: Korrigiert eine Abfrage zum Überschreiben der "Kategoriebild anzeigen" Einstellung aus der Theme-Konfiguration.
- Bugfix: Behebt die Fehlermeldung incompatible units px and rem bei einer bestimmten Konfiguration.
- Bugfix: Revision of CMS Element Resolver decorator | Pro Version.

# 2.4.6
- Feature: Neue Konfiguration hinzugefügt, um Produktfeatures auf der Produktdetailseite anzuzeigen.
- Optimierung: Input readonly und Input disabled Farben festgelegt.
- Optimierung: Klasse `mr-1` bei der Suche entfernt
- Optimierung: Tooltip-Initialisierung der Shop-Navigation.
- Optimierung: Shop-Navigation und Top-Bar Übergangsanimationen.
- Optimierung: Farbkontrast Wunschliste für die Überschrift.
- Optimierung: Verbesserte Positionierung von Produkt-Slider Controls.
- Bugfix: Position der Shopnavigation im Header.

# 2.4.5
- Feature: Cover-Switch-Funktionalität für CMS-Elemente hinzugefügt.
- Feature: Möglichkeit hinzugefügt, Texte aus der Konfiguration zu übersetzen.
- Feature: Neues Konfigurationsfeld für zusätzliche CSS-Dateien zur Theme-Plugin-Konfiguration hinzugefügt.
- Feature: Neues Konfigurationsfeld für die aktive Farbe des Filterrahmens hinzugefügt.
- Optimierung: Fügt `$css-relative-bundles-path` zu Bundles Assets-Paths in SCSS hinzu, da `$css-relative-bundles-path` die Domain enthält, die von der Administration aufgerufen wird, die wiederum eine andere Domain als die des Verkaufskanals sein kann.
- Optimierung: Verbesserung in der Administration des Themes | Pro Version.
- Optimierung: Verbessert Fehldarstellungen der Theme-Konfiguration, die nach Update 6.4.8.0 auftreten, bis Shopware das Problem selbst löst | Pro Version.
- Optimierung: Fügt die Theme-Konfiguration Block-Titel hinzu, die nach Update 6.4.8.0 fehlen, bis Shopware das Problem selbst löst - NEXT-20077
- Optimierung: Reduziert die minimalen Höhen des Galerie-Sliders auf den Standardwert von 430 Pixel.
- Optimierung: Verbesserung der Bezeichnungen in der theme.json.
- Optimierung: Verhindert, dass der Rand eines Produkts vom linken inaktiven Element in den sichtbaren Teil des Produkt-Sliders scheint.
- Optimierung: Verbessert den Textkontrast der Zahlungsmethodenbeschreibung im Checkout.
- Optimierung: Kleine Abstände zu Header Grid-Spalten hinzugefügt.
- Optimierung: Button border-radius zur Main-Navigation hover-0 hinzugefügt.
- Optimierung: Ausblenden der Standard-Breadcrumbs auf CMS-Produktseiten, wenn benutzerdefinierte Zenit-Breadcrumbs verwendet werden.
- Optimierung: Optimiert die Breite der Header Icon-only Buttons.
- Bugfix: Außenabstände in layout-container-negative-vertical-margin für mobile Geräte hinzugefügt.
- Bugfix: Behebt die Darstellung der Farben der Paginierung.
- Bugfix: Platzhalter im Galerie-Slider werden nun korrekt anzeigen.
- Bugfix: Behebt ein Anzeigeproblem der Floating-Labels mit dem Custom Products-Plugin.
- Kompatibilität: Verhindert, dass Produkt-Slider-Controls und Artikel aufgrund von Änderungen im Shopware-Update 6.4.8.0 abgeschnitten werden

# 2.4.4
- Child-Theme Update verfügbar (erforderlich): Bei Nutzung eines unserer Child-Themes Vorlagen Sets bieten wir auch ein Update des Child-Themes auf Version 2.4.4. Dieses Child-Theme Update-Paket ist kumulativ und beinhaltet ein Kompatibilitäts-Update für Shopware 6.4.8.0. Unsere Child-Themes kannst Du hier kostenlos herunterladen: https://themes.zenit.design Bitte beachte den Update-Guide, sollten individuelle Programmierungen im Child-Theme vorgenommen worden sein: https://themedocs-sw6.zenit.design/vorlagen#updates
- Kompatibilität: Kompatibilität mit Shopware 6.4.8.0
- 
# 2.4.3
- Feature: Neue Konfiguration zum Anzeigen eines Externer-Link Icons zur Main Navigation und Flyout-Navigation hinzugefügt.
- Feature: Neue Konfiguration zur Anzeige der Meta-Beschreibung anstelle der Produktbeschreibung im Produkt-Listing Layout Typ Standard hinzugefügt.
- Feature: Schließen-Icon bei der ausklappbaren Suche hinzugefügt.
- Feature: Die ausklappbare Suche schließt nun zusätzlich beim Klick außerhalb der Suche.
- Optimierung: Setzt den Cursor auf den Standardwert bei Struktur-Elementen der Hauptnavigation.
- Optimierung: Die Darstellung von CMS-Elementen auf Kategoriebildern im Header wurde verbessert.
- Optimierung: Die Darstellung von Floating Labels bei Auswahlfeldern bei Kategoriebildern im Header wurde verbessert.
- Optimierung: Die Darstellung von Floating Labels bei Auswahlfeldern wurde verbessert.
- Optimierung: Verbessert die hover-1 Klammer Farbe.
- Optimierung: Anpassungen für Bootstrap-Responsive-Tabellen.
- Optimierung: Die Darstellung der Produktaktionen im Listing wurde verbessert.
- Optimierung: Verbesserte Main-Navigation Hover Effekte.
- Optimierung: Anzeige des Herstellerlogos verbessert.
- Optimierung: Die Anzeige von horizontalen Innenabständen von abgerundeten Schaltflächen und abgerundeten Eingabefeldern wurde verbessert.
- Optimierung: Verbesserung der Option für die Deckkraft des Header-Hintergrunds bei Theme-Farben, die bereits eine Transparenz beinhalten.
- Optimierung: Überarbeitung des Subscribers für Bildwechselfunktion im Listing. | Pro Version
- Optimierung: Eine Funktion zum Laden des Plugin-Namens in sw-theme-manager-detail wurde entfernt.
- Optimierung: Verhindert ein Problem beim Einpassen des Produktbildes der Produktbox, wenn ein Produktbildrand definiert wurde.
- Bugfix: Die Anzeige abgerundeter Ecken in Textareas wurde verbessert.
- Bugfix: Verbesserung der Preloader-Darstellung beim Zurücknavigieren in der Browser-History.

# 2.4.2
- Feature: Neue Konfiguration für Seitenverhältnis beim Zoomen beibehalten hinzugefügt, die in SW 6.7 eingeführt wurde.
- Feature: Neue Konfiguration zur Anzeige eines Detail-Buttons für Variantenartikel im Listing aufgrund des SW-Tickets NEXT-19146 anzuzeigen.
- Feature: Neue Konfiguration hinzugefügt, um den Preloader Timeout zu ändern.
- Feature: Neue Konfiguration hinzugefügt, um den Kontrastschwellenwert zu ändern.
- Optimierung: Umbenennung von `preloader.html.twig` in `zen-preloader.html.twig`
- Optimierung: Unnötige ID bei der Mengeneingabe entfernt.
- Optimierung: Verbesserung der Code-Qualität anhand der Code quality analysis.
- Optimierung: Setze den Standardwert für die Mengeneingabe -/+ auf die Mindestbestellmenge.
- Optimierung: Btn-Icon Transitions hinzugefügt.
- Optimierung: Hinzufügen von Layout-Innenabständen zum Header-Container bei Full-Width-Header Layouts.
- Optimierung: Entfernen von Layout-Innenabständen im Footer.
- Optimierung: Verbesserung von Bezeichnungen in der theme.json
- Optimierung: Verbessert die Darstellung des Marketing-Sliders, wenn Webfonts geladen wurden.
- Optimierung: Verbessert den Stacking-Kontext der Buybox bei Sticky-Option.
- Optimierung: Verhindert, dass die Produkte untereinander angezeigt werden, bis das Produkt Slider Javascript geladen und der Slider initialisiert ist.
- Optimierung: Überarbeitung der CustomFields-Definitionen.
- Optimierung: Verbessert die Darstellung der Mengeneingabe im Listing bei Überlagerung des Produktbildes.
- Optimierung: Angleichung der Innen- und Außenabstände der CMS Layouts zwischen CMS Editor und CMS Storefront.
- Optimierung: Optimiert die Darstellung des Datenschutz-Hinweises im Footer Newsletter - Hinweis erscheint erst bei Eingabe und verhindert Hüpfen des Footers

# 2.4.1
- Feature: Neue Konfigurationsoption für Preloader hinzugefügt.
- Optimierung: Verbessert den Preloader beim Seitenwechsel.
- Optimierung: Verbesserung verschiedener Kontrastfunktionen im Inhalt der Produktdetails-Tabs.
- Optimierung: Kontrastfarbfunktion für Gutscheineingabe.
- Optimierung: Ausrichtung der Top Bar Icons.
- Optimierung: Gestaltung von account-menu-offcanvas auf dunklen Offcanvas-Hintergründen.
- Optimierung: Hinzufügen von `#{$sw-asset-theme-url}` zu Bundles Assets-Paths in SCSS.
- Optimierung: Verwendung der Twig-Block-Funktion, um unveränderte Eltern-Blöcke anzuzeigen.
- Optimierung: Unnötige defaultValue-Elemente in config.xml entfernt.
- Bugfix: Verhindert horizontales Scrollen auf Mobilgeräten aufgrund der minimalen Breite des Grid gutters.
- Bugfix: Behebt ein Problem bei den anpassbare Links in Kategorien.

# 2.4.0
- Feature: Neue Konfigurationen für die Farben der Paginierung hinzugefügt.
- Feature: Neue Konfigurationen für Produkt-Slider-Steuerelemente hinzugefügt.
- Feature: Neue Konfiguration für Breadcrumbs-Schriftgröße hinzugefügt.
- Feature: Neue Konfiguration für Schriftgewicht der aktiven Hauptnavigation hinzugefügt.
- Feature: Neue Konfigurationen für Sprachwechsler hinzugefügt.
- Feature: Position sticky zur CMS-Buybox hinzugefügt.
- Feature: Galerie-Scroller als neuer Anzeigemodus für die Galerie hinzugefügt.
- Feature: Neue Konfiguration für Meta theme-color hinzugefügt.
- Optimierung: Optimierung des Sticky-Header-Offsets für das Unstick-Ereignis beim Hochscrollen.
- Optimierung: Verbessert die Positionierung von Eingabe-Labels im Floating-Labels-Modus.
- Optimierung: Auslassungspunkte für die floating Labels hinzugefügt, um mehrzeilige Labels zu verhindern.
- Optimierung: Fußzeilen-Spaltenabstände verbessert.
- Optimierung: Neue Update Methode zum Aktualisieren der base_config von Theme-Duplikaten aufgrund von Issue NEXT-10994
- Optimierung: Produkt-Slider Innenabstände verbessert.
- Optimierung: Cookies Ablehnen-Button für eine bessere Benutzerfreundlichkeit auf btn-link gesetzt.
- Optimierung: Verbesserung der Theme-Konfiguration Footer Spaltenbreiten-Auswahl Sortierung.
- Optimierung: Verbesserung der Theme-Vererbung in theme.json.
- Optimierung: Horizontalen Innenabstand zum Full-width Header hinzugefügt.
- Bugfix: Korrigiert die Anzeige der Mengeneingabe auf kleinen Produkt-Cards.
- Bugfix: Behebt die Anzeige der Mengeneingabe auf kleinen mobilen Geräten im Kassenbereich.

# 2.3.3
- Feature: Neue Konfiguration hinzugefügt, um die Detail-Buttons in Listings anzuzeigen, auch wenn die Option Kaufenbutton in Produktlistings anzeigen in Einstellungen > Produkte deaktiviert ist.
- Optimierung: Hinzufügen einer service-menu-widget.html.twig Datei, um das Servicemenü in zukünftigen Versionen beizubehalten.
- Optimierung: Reduzieren der Größe des Hilfe-Icons in der Top Bar.
- Optimierung: Verhindert, dass der Quickview-Link in einem neuen Fenster geöffnet wird.
- Optimierung: Hinzufügen von light-text-muted Farbe in Offcanvas Warenkorb Elementen.
- Optimierung: Verbessern der Mengeneingabe Button Textfarben auf verschiedenen Elementen.
- Optimierung: Verbesserung der Theme-Konfigurationslabels.
- Optimierung: Optimierung des Sticky-Header-Offsets für das Unstick-Ereignis beim Hochscrollen.
- Optimierung: Optimiert eine Seitenleisten-Navigationsabfrage, wenn`zen-sidebar-navigation-active-bg` Variable transparent ist;
- Optimierung: `Normal - 400` in `Regular - 400` umbenannt, um die Benennung in theme.json zu standardisieren.
- Optimierung: Unnötige `footer-contact`-Klassen in Fußzeilenspalten entfernt.
- Optimierung: Setzen von Fallback-Farben für den modalen Zoom-Hintergrund, wenn `zen-product-listing-card-img-bg` transparent ist.
- Optimierung: Setzen des linken Abstandes der cms section sidebar main content auf 0, damit ein gleichmäßigerer Abstand zwischen der Seitenleiste und dem Hauptinhalt entsteht.
- Kompatibilität: Entfernen des Overrides der `$app-css-relative-asset-path` aufgrund von Shopware Issue NEXT-10560 von Version 2.1.0, da Shopware das Problem nun selbst gelöst hat.
- Kompatibilität: Behebt ein Problem aufgrund der neuen Funktion in Issue NEXT-16879, bei der Assets() die Symfony Flysystem-Versionsstrategie verwenden und der PDF-Viewer die URL nicht mehr korrekt zusammensetzen konnte.
- Bugfix: Behebt ein Problem mit der Fehlermeldung `Argument 1 passed to Shopware\\Core\\Framework\\Uuid\\Uuid::isValid() must be of the type string` bei verwaisten Konfigurationswerten.
- Bugfix: Korrigiert die Anzeige der Mengeneingabe auf kleinen mobilen Geräten.

# 2.3.2
- Feature: Hinzufügen einer neuen Konfiguration für Containerabstände.
- Optimierung: Überarbeitung der Containerabstände.
- Optimierung: Überarbeitung der Parameter Containerabstand Mixins um zwei separate Werte aufzunehmen.
- Optimierung: Verbessert die Logo-Platzierung, wenn die Höhe größer als 100% ist
- Optimierung: Verbessert die Logo-Platzierung mit `object-fit`, um eine korrekte Anzeige von SVG ohne Initialmaß zu gewährleisten.
- Optimierung: Klassenname in Breadcrumbs cms-Element in der Administration umbenannt.
- Optimierung: Abstand zwischen `.product-detail-tab-navigation` und `.product-detail-tabs-content` hinzugefügt, wenn Hintergrundfarbe und Tabs-Darstellung nicht auf Tabs gesetzt ist
- Optimierung: Verbesserung von Labels in der Theme-Konfiguration.
- Optimierung: Verbesserung der Hilfetexte in der Theme-Konfiguration.
- Optimierung: Fügt eine Abfrage hinzu, um den Tab Eigenschaften anzuzeigen.
- Optimierung: Buttons werden nun aus Footer-Link-Stylings nicht mehr überschrieben.
- Optimierung: Platz zwischen Header und Sticky Buybox hinzugefügt.
- Optimierung: Neue Tabellen-Akzentfarbe für dunkle Themes hinzugefügt.
- Optimierung: Verbesserung des Zoom-Modal-Layouts.
- Optimierung: Breadcrumbs, die ein Bild überlagern, erhalten keine Hintergrundfarbe mehr.
- Optimierung: Einige Font-Weight-Variablen hinzugefügt, die Bootstrap nicht liefert.
- Optimierung: Hauptnavigation aktive Schriftstärke auf `semi-bold` geändert.
- Optimierung: Aktive Breadcrumbs font-weight auf Konfigurationswert geändert.
- Optimierung: Einige IE11-Fixes für Sticky Elements und Floating Labels hinzugefügt.
- Optimierung: Top-Abstand zu sticky `.checkout-aside-container` hinzugefügt wegen Sticky Header.
- Optimierung: Verbesserte Auswahlfelder für Floating Labels, wenn keine Option ausgewählt ist.
- Optimierung: Standardhintergrundfarbe der Hero-Sektion auf `transparent` geändert.
- Optimierung: Klassen mit `--` in `-` umbenannt wegen des Codestils.
- Optimierung: Verbesserte Preloader Logo-Animation und Logo-Darstellung mit `object-fit`.
- Optimierung: Verbesserte Offcanvas Logo-Anzeige mit `object-fit` und Header-Logo-Höhenkonfiguration.
- Optimierung: Verbesserte Footer-Logo-Anzeige mit `object-fit` und Header-Logo-Höhenkonfiguration.
- Bugfix: Verwendung der Variable `$zen-footer-custom-column-color` hinzugefügt, da sie nie verwendet wurde.
- Bugfix: Überarbeitung einer Abfrage, um den Schließen-Button der Top Bar anzuzeigen.

# 2.3.1
- Bugfix: Korrigiert die Darstellung der Optionen von Auswahl-Elementen bei der Verwendung der Funktion Floating Labels.

# 2.3.0
- Feature: Floating Labels für Form-Elemente
- Feature: Hinzufügen einer Konfiguration für Zoom Lupe über Galerie
- Feature: Hinzufügen eines neuen Konfiguration-Wertes für Produkt-Karten Action-Positionen
- Feature: Hinzufügen einer Konfiguration für die Produkt-Karten Aufmerksamkeits-Animation bei Mouseover
- Feature: Hinzugefügt minified pdf-viewer/-renderer.
- Feature: Hinzufügen einer neuen Konfiguration für Produkte pro Zeile für kleine Smartphones
- Feature: Hinzufügen einer neuen Konfiguration für die vertikalen Innenabstände des Hauptinhalte-Containers
- Feature: Hinzufügen einer neuen Konfiguration um Breadcrumbs auf volle Breite zu setzen
- Feature: Hinzufügen eines Schließen-Icons zu ein- und ausklappbaren Top Bar
- Optimierung: Sticky Buybox prüft nun, ob die Fensterhöhe größer als die Buybox-Höhe ist
- Optimierung: Vermeidet das Durchblitzen des Hintergrunds bei Produktbildern mit Hintergrundüberlagerung aufgrund von Pixelungenauigkeiten bei Elementen mit Randradius.
- Optimierung: Verbesserung der Produktkarten Innenabstände bei unterschiedlichen background-color Kombinationen
- Optimierung: Verbesserung der Theme-Konfigurationstexte.
- Optimierung: Setzen des Galerie-Slider Rasterabstands auf 0, auf Grund von der Farbe in zen-product-listing-card-img-bg welche durch das Raster durchblitzte.
- Optimierung: Entfernen der zusätzlichen Abfrage auf `product.childCount` für den Kaufen-Button im Listing, sodass aufgefächerte Varianten den Kaufenbutton anzeigen können.
- Optimierung: Verbesserung des Hilfetextes für die Schrift-Einstellungen.
- Optimierung: Verhindert das Abschneiden von Box-Shadows in Produktkarten des Produktsliders.
- Optimierung: Entfernen überflüssiger Anführungszeichen bei @if-Statements in SCSS
- Optimierung: Stellt nun sicher, dass die Galeriebild-Slider-Konfiguration für Produktseiten nur für Bild-Slider der Produktseite greifen
- Optimierung: Einstellungen für das Seitenverhältnis von Produktbildern in den Allgemein-Tab verschoben
- Optimierung: Anpassung der Initialisierung des Javascript-Plugins, das zu ungültigen Formularelementen scrollt.
- Optimierung: Verbesserung der Hintergrundfarbe des Overlay-Suche Button Loaders
- Bugfix: Mengenänderung im Checkout Quickview Modal
- Bugfix: Darstellung der Vorschauansicht des Breadcrumb-CMS-Elements
- Bugfix: Behebt das Problem der nicht bearbeitbaren Rahmenfarbe für Produktboxen
- Bugfix: Korrektur des zen-product-gallery-slider-display-mode Standard-Werts

# 2.2.2
- Optimierung: Überarbeitung der Bildüberlagerung im Listing, sodass bei Mouseover das Titelbild hinter dem zweiten Bild verschwindet.
- Optimierung: Korrigiert die Anzeige des Breadcrumb-Hintergrundes, sodass sie sich nur über die Breite der Breadcrumbs erstrecken.
- Optimierung: Überarbeitung der Animation Hover 5 der Main Navigation (Kategorien).
- Bugfix: Korrektur der Text Ausrichtung für Produktpreise im Listing
- Bugfix: Korrektur der Quickview Input-Style Abfrage
- Bugfix: Korrektur des maxPurchase Wertes von maxQuantity zu calculatedMaxPurchase

# 2.2.1
- Optimierung: Darstellung der Mengenauswahl in Produkt-Listings
- Optimierung: Transparenter Header auf mobilen Geräten
- Optimierung: Korrektur eines Übersetzungsfehlers in der theme.json
- Optimierung: Verbesserung von Bezeichnungen in der theme.json
- Optimierung: Gallery Slider Weite mit Höhen-Konfiguration zusammengelegt
- Bugfix: Korrektur der Produkt-Slider Style Abfrage

# 2.2.0
- Feature: Hinzufügen einer Konfiguration zu Anzeige der Produktnummer im Listing
- Feature: Konfiguration hinzugefügt, um den Stil der Mengenauswahl auf -/+ Eingabe zu ändern
- Feature: Integriertes Lazy-Loading von Bildern auf Browserebene
- Feature: Hinzufügen einer Konfiguration für Produkt-Slider-Layout hinzugefügt, um zwischen dem Standard und erweitertem Modus zu wählen. Der erweiterte Modus zeigt die übergelaufenen Elemente an.
- Feature: Hinzufügen einer Konfiguration für die Breite der Produktdetails der Bildergalerie hinzugefügt
- Feature: Hinzufügen einer Konfiguration für den Anzeigemodus der Produktdetails-Bildergalerie hinzugefügt
- Feature: Hinzufügen einer Konfiguration für Produktdetails Bildergalerie Pfeilnavigationsposition
- Feature: Hinzufügen einer Konfiguration für Produktdetails Bildergalerie Punkte Navigationsposition
- Feature: Hinzufügen einer Konfiguration für Produktdetails Bildergalerie Vorschau-Navigation Position
- Feature: Hinzufügen einer Konfiguration für Produkt Detailseite Image Gallery Zoom
- Feature: Hinzufügen einer Konfiguration für Produkt Detailseite Image Gallery Fullscreen Gallery
- Feature: Hinzufügen einer Konfiguration für Produkt Detailseite Hintergrundfarbe
- Feature: Hinzufügen einer Konfiguration für Produkt Detailseite Tabs Hintergrundfarbe
- Feature: Hinzufügen einer Konfiguration für Produkt Detailseite Tabs Inhalte Hintergrundfarbe
- Feature: Hinzufügen einer Konfiguration für Produkt Detailseite Produktname Texttransform
- Feature: Hinzufügen einer Konfiguration für Produkt Listing Cards Produktname Texttransform
- Feature: Hinzufügen einer Konfiguration für Newsletter Position über dem Footer
- Feature: Hinzufügen einer Konfiguration für Footer Innenabstände
- Feature: Hinzufügen einer Konfiguration für Footer Rahmen
- Feature: Hinzufügen einer Konfiguration für Header Rahmen
- Feature: Hinzufügen einer Konfiguration für Listing Actions Rahmen
- Feature: Hinzufügen einer Konfiguration für Base Slider Navigation Farben im Modus Anzeige Außen, welcher mit SW 6.4 eingeführt wurde
- Feature: Position Sticky zur Buybox hinzugefügt
- Optimierung: Footer Konfiguration in theme.json überarbeitet
- Optimierung: Titel in SVG-Images entfernt
- Optimierung: Corrects the display of the slider arrows of product sliders if an alternative display of slider arrows was selected.
- Optimierung: Korrigiert die Anzeige der Slider Pfeile von Produkt-Slidern, wenn eine alternative Darstellung von Slider Pfeilen gewählt ist.
- Optimierung: Umbenennung `Font Align` in `Text align` in der theme.json.
- Optimierung: Umbenennung `Settings` in `Einstellungen` in der Administration.
- Optimierung: Anpassung der Schriftfarben von `payment-method-description` und `shipping-method-description` auf Grund von problematischen Farbgebungen im Shopware Default Theme
- Optimierung: Überarbeitung des Suchlayouts "expanded", um Overflow-x nicht mehr im page-wrapper zu verwenden, damit position sticky funktioniert.
- Optimierung: Neue Abfrage ob ein Produkt Cross-Selling Artikel besitzt
- Optimierung: Überarbeitung des Subscribers für Produkt-Listing Medien
- Optimierung: Überarbeitung einer if-Abfrage für die Kategorie-Navigation
- Optimierung: Der icon-style "square" erhält nun den border-radius aus der Button-Konfiguration
- Optimierung: Navigationselemente sind jetzt wieder aktiv, wenn sie sich im aktiven Pfad befinden
- Bugfix: Behebt den Wert Text-transform bei Filter-Panels
- Bugfix: Behebt eine falsche Variable, die verhindert, dass das Feature-Element angezeigt wird.

# 2.1.2
- Feature: Hinzufügen einer Konfiguration für wesentliche Merkmale zur Anzeige auf der Produktdetailseite.
- Optimierung: Änderung einiger Konfigurationen `"type": "switch"` in `"type": "bool"` in der theme.json.
- Optimierung: Änderung der Konfiguration `"type": "checkbox"` in `"type": "bool"` in der theme.json.
- Optimierung: Darstellung des Marketing-Text-Sliders auch bei einzeiligen Inhalten.
- Optimierung: Umbenennung `Mindesthöhe` zu `Minimale Höhe` in theme.json.
- Optimierung: Verbesserung der CMS Breadcrumbs border bottom.
- Optimierung: Behebt das Verschwinden der Pfeile in Select-Felder bei Mouseover.
- Optimierung: Verbesserung der Darstellung des Warenkorb-Buttons im Listing bei deaktivierter Mengenauswahl.
- Optimierung: Verbesserung der Innenabstände von Produktboxen bei aktiviertem Schatten.
- Optimierung: Verbesserung der Innenabstände von Produkt-Actions bei inaktiven Schatten oder Rahmungen.
- Optimierung: Verbesserung der Darstellung des Merken-Icons in Produktboxen bei inaktiven Schatten oder Rahmungen.
- Optimierung: Austauschen des Service/Help Icons für das Feather Old Icon Set.
- Optimierung: Verbesserung der Darstellung der border-radius in der Sidebar, wenn der aktive Pfad dieselbe Hintergrundfarbe besitzt wie Shop-Hintergrund oder Container Hintergrund.
- Optimierung: Verhindert, dass Schatten von Produktboxen abgeschnitten werden.  
- Optimierung: Input Placeholder auf 50% Sichtbarkeit reduziert.  
- Optimierung: Verhindern den Twig Error "Division by zero", wenn CMS Features-Element verwendet, aber nie konfiguriert wurde.
- Bugfix: Behebt eine Fehldarstellung des Galerie-Sliders bei fehlender Konfiguration bei veraltetem Child-Theme Set durch Setzen eines Fallback-Wertes.

# 2.1.1
- Optimierung: Entfernen unnötiger Referenz zu SnippetFiles, dank der Auto-Registration.
- Optimierung: Rechtschreibung und Überarbeitung der Themekonfiguration.
- Optimierung: Verbesserung der Berechnung der maximalen Breite des Marketing-Sliders.
- Optimierung: Umbenennung der eigenen CMS-Elemente Dateinamen von  `cms-xyz-zenit-xyz.html.twig` in `cms-xyz-zen-xyz.html.twig`.
- Optimierung: Umbenennung der eigenen CMS-Elemente `zenit` in `zen` zur Vereinheitlichung.
- Optimierung: Theme-Konfiguration 'Sidebar Navigation' vom Reiter 'Kategorie' zu 'Komponenten' verschoben aufgrund der Verwendungsmöglichkeiten auf verschiedenen Seitentypen.
- Optimierung: Hinzufügen der Galerie-Slider `minHeight` `sliderConfig` aus dem Konfigurationsfeld `zen-product-gallery-slider-min-height-desktop`.
- Optimierung: Verbesserung der Custom Select Hover Farben.
- Bugfix: Behebt einen Fehler, bei welchem die Textbausteine in den zusätzlichen CMS Elementen nicht geladen wurden.
- Bugfix: Behebt einen Fehler beim Absenden der Suche im Modus Overlay Suche.
- Bugfix: Aktualisierung des Mozilla PDF-Viewers auf Version v2.9.359 wegen eines Darstellungsfehlers in Mozilla Firefox.

# 2.1.0
- Feature: Hinzufügen einer Konfiguration für globale Schriftgrößen (base, h1-h6).
- Feature: Hinzufügen einer Konfiguration für die Höhe des Galerie-Sliders auf Produkt-Detailseiten.
- Feature: Hinzufügen einer Konfigurationsoption zum Ausblenden der Top-Bar.
- Feature: Hinzufügen einer Konfigurationsoption für das Erscheinungsbild der Dots-Navigation `Expanding`.
- Feature: Hinzufügen eines PDF-Viewers zum eingebetteten Anzeigen von Datenblättern und PDFs.
- Feature: Hinzufügen einer Konfiguration zur Auswahl von zwei verschiedenen PDF-Viewer/-Renderer.
- Feature: Hinzufügen eines neuen Icon-Sets `entypo`.
- Feature: Hinzufügen eines CMS-Elements `features` (nicht verfügbar in Shopware Cloud).
- Feature: Der Galerie-Slider auf Produkt-Detailseiten wird in seiner Höhe nun vom eingestellten Produktbildverhältnis beeinflusst, sofern sie nicht in der neuen Konfigurationsoption zur manuellen Einstellung überschrieben wird.
- Feature: Slider Controls Position `Alternative` nun auch für Galerie-Slider der Produkt-Detailseiten verfügbar.
- Optimierung: Listing Render Event abonniert, um Javascript beim Filtern neu zu initiieren.
- Optimierung: Behebt einen Shopware-Bug, bei dem der Kaufen-Button auch im Listing für Varianten angezeigt wird.
- Optimierung: Verbesserung der Darstellung des Marketing-Sliders, sodass Text und Button näher beieinander liegen.
- Optimierung: Vermeidet das Durchblitzen des Hintergrunds bei Produktbildern mit Hintergrundüberlagerung aufgrund von Pixelungenauigkeiten bei Elementen mit prozentualer Breite.
- Optimierung: Rechtschreibung und Überarbeitung der Themekonfiguration.
- Kompatibilität: Überschreiben der Variable `$app-css-relative-asset-path` aufgrund von Shopware Issue NEXT-10560 (Resolve fonts relative to the theme app.css) zum Auflösen von Schriftarten mithilfe von Bundle-/Storefront-Elementen.
- Bugfix: Umbenennung der Variable `zen-category-filter-seperator` zu `zen-category-filter-separator`.

# 2.0.1
- Optimierung: Hinzufügen der SCSS Funktion strip-units zur Verbesserung der Fehlertoleranz bestimmter Konfigurationsfelder.
- Optimierung: Wechsel des Feld-Typs der Produktbild-Weite und -Höhe Konfiguration zur Verbesserung der Fehlertoleranz. 
- Optimierung: Hinzufügen von min/max Werten in der Theme-Konfiguration zur Verbesserung der Fehlertoleranz.
- Optimierung: Verbesserung der Darstellung einzelner Elemente der Theme-Konfiguration.
- Optimierung: Verbesserung von Theme-Konfiguration Hilfe-Texte.
- Optimierung: Die optionale Hintergrundfarbe wurde aus Breadcrumbs entfernt, wenn sie im Kategoriebild im Kopfbereich angezeigt werden.
- Optimierung: Umbenennung der Klasse `ZenitOverrideCookiePermissionPlugin` zu `ZenOverrideCookiePermissionPlugin`
- Optimierung: Werte von url() in Anführungszeichen gesetzt, um Darstellungsprobleme bei URLs mit Sonderzeichen zu vermeiden.
- Optimierung: Anpassung der Farbe des Wishlist-Links auf der Detailseite für dunkle Hintergründe.
- Optimierung: Verbesserung der Darstellung der Abverkauf-Labels auf der Detailseite.
- Kompatibilität: Entfernen der SnippetFile PHP-Dateien, da diese aufgrund der auto-load Funktion nicht merh benötigt werden.
- Bugfix: Verhindert, dass Dropdown Overflow-boundary die Sidebar-Filter beeinflusst.

# 2.0.0
Kompatibilität mit Shopware 6.4.0.0
__Storefront__
- Kompatibilität: Überarbeitung Logo Größe und Positionierung
- Kompatibilität: Überarbeitung der Breadcrumbs
- Kompatibilität: Hinzufügen des Wunschlisten-Templates im Header
- Kompatibilität: Hinzufügen des Wunschlisten-Templates in Produktboxen
- Kompatibilität: Hinzufügen der Wunschlisten Icons
- Kompatibilität: Entfernen der `zentheme` Variable, verwende `theme_config('my_config_key')` stattdessen
- Kompatibilität: Entfernen der `zenconfig` Variable, verwende `config('my_config_key')` stattdessen
- Kompatibilität: Entfernen der `shopware.theme.breakpoint`, verwende spezifische Größen-Variable stattdessen `theme_config('breakpoint.sm')`
- Optimierung: Entfernen der `filter-rating.html.twig`, da sie nicht mehr verwendet wird
- Optimierung: Überarbeitung der Top-Bar Weite bei Sticky Header
- Optimierung: Überarbeitung der Template-Variablen in der base.html.twig
- Optimierung: Umbenennung `zenOpenFilterOptions` in `openFilterOptions`
- Optimierung: Hinzufügen des Blocks `zen_component_login_register`
- Optimierung: Hinzufügen des Blocks `zen_component_text_slider`
- Optimierung: Hinzufügen des Blocks `zen_component_social_icons`
- Optimierung: Überarbeitung der Twig-Templates zur verbesserten Nutzung von `{{ parent() }}`
- Optimierung: Hinzufügen von Präfixen bei über 190 Twig-Blöcken zur Verbesserung der Kompatibilität
- Optimierung: Überarbeitung `zen-footer-newsletter.html.twig` als Erweiterung der `newsletter-form.html.twig`
- Optimierung: Umbenennung von `action-buy-widget-container.html.twig` in `zen-action-buy-widget-container.html.twig`
- Optimierung: Umbenennung von `action-quickview-toggle.html.twig` in `zen-action-quickview-toggle.html.twig`
- Optimierung: Umbenennung von `cover-switch.html.twig` in `zen-cover-switch.html.twig`
- Optimierung: Umbenennung von `login-register.html.twig` in `zen-zen-login-register.html.twig`
- Optimierung: Umbenennung von `text-slider.html.twig` in `zen-text-slider.html.twig`
- Optimierung: Umbenennung von `top-bar-offcanvas.html.twig` in `zen-top-bar-offcanvas.html.twig`
- Optimierung: Umbenennung von `custom-global-tab.html.twig` in `zen-custom-global-tab.html.twig`
- Optimierung: Umbenennung von `custom-tab1.html.twig` in `zen-custom-tab1.html.twig`
- Optimierung: Umbenennung von `custom-tab2.html.twig` in `zen-custom-tab2.html.twig`
- Optimierung: Umbenennung von `datasheets.html.twig` in `zen-datasheets.html.twig`
- Optimierung: Umbenennung von `manufacturer.html.twig` in `zen-manufacturer.html.twig`
- Optimierung: Umbenennung von `properties-tab.html.twig` in `zen-properties-tab.html.twig`
- Optimierung: Umbenennung von `video.html.twig` in `zen-video.html.twig`
- Optimierung: Umbenennung von `zen-social.html.twig` in `zen-social-icons.html.twig`
- Bugfix: Sticky Header bei mehrzeiligen Headern führte beim Verschwinden der Hauptnavigation zu nicht klickbaren Flächen hinter der Hauptnavigation
- Bugfix: Verhindert das Abschneiden von Filter-Dropdowns, indem ein HTMLElement als Grenze gesetzt wird

__CMS__
- Kompatibilität: Hinzufügen der Produktseiten CMS Templates
- Kompatibilität: Hinzufügen der Breadcrumbs in Produktseiten CMS
- Kompatibilität: Hinzufügen der `src/Resources/views/storefront/element/cms-element-product-description-reviews.html.twig` zum Anzeigen des `product-description-reviews` CMS-Elements
- Kompatibilität: Hinzufügen der `src/Resources/views/storefront/component/product/descrption.html.twig` zur veränderten Darstellung der Eigenschaften als Tab
- Kompatibilität: Hinzufügen der `src/Resources/views/storefront/block/cms-block-product-heading.html.twig` zur Implementierung der Produkt-Kurzbeschreibung
- Kompatibilität: Hinzufügen der `src/Resources/views/storefront/element/cms-element-product-name.html.twig` zur Umpositionierung der Bewertungen
- Optimierung: Hinzufügen von Auto-Außenabständen als Fallback der Erlebniswelten Layout-Einstellungen

__SCSS__
- Optimierung: Verbesserung der Code-Qualität anhand der Codequality-Analysis

__Administration__
- Kompatibilität: Überarbeitung des Administrations-Moduls Einstellungs-Links

__CustomFields__
- Optimierung: Entfernen der `zenCustomFieldsCategory` Variable, verwende `zenCustomFields.cagtegory` stattdessen
- Optimierung: Hinzufügen der `zenCustomFields.product` Variable anstelle von `page.product.translated.customFields`
- Optimierung: Überarbeitung des Aufrufs von Custom Fields in Twig-Templates

__Subscriber__
- Optimierung: Entfernen der `generateConfigId` Methode
- Kompatibilität: Überarbeitung des `StorefrontRenderSubscriber` Zugriff auf die Theme-Konfigurations

# 1.2.4
- Optimierung: Verbesserungen für die Anzeige von Datenblättern auf Mobilgeräten
- Optimierung: Verbesserung der Anzeige des Herstellerlinks, wenn im Hersteller kein Link gesetzt ist
- Optimierung: Refaktorierung der Plugin-Bootstrap
- Optimierung: Refaktorierung der Plugin-Konfigurations-ID für die Kompatibilität mit der Shopware Cloud
- Optimierung: Refaktorierung des Breadcrumb- und Kategorie-Image-Managements für die Kompatibilität mit der Shopware Cloud

# 1.2.3
- Feature: Datenblätter im Produkt hinterlegen und als Tab anzeigen
- Optimierung: Verbessert die Darstellung der Scroll-Navigation bei dunklem Body-Hintergrund Verbindung mit dem Plugin CMS-Extension
- Optimierung: Kleinere Verbesserungen der Custom Fields
- Optimierung: Kleinere Ausbesserungen in der Theme-Konfiguration  
- Optimierung: Refaktorierung der Variable zenplugin zu zenconfig auf Grund ihrer semantischen Bedeutung
- Optimierung: Vorbereitung für Wishlist Implementierung 
- Bugfix: Verhindert, dass die Tab-Navigation durch das negative Margin des Produkt-Sliders überdeckt werden kann

# 1.2.2
- Bugfix: Anzahl an Produkten pro Reihe ging beim Paginieren verloren
- Optimierung: Korrigiert die Darstellung der vertikalen Ausrichtung von Cross-Selling Produktslidern

# 1.2.1
- Optimierung: Verbesserung der Darstellung der Suche durch Input-Groups
- Optimierung: Verbesserung des Produkt-Box Schattens bei Produkt-Slidern
- Optimierung: Korrigiert die Darstellungsoption zur vertikalen Ausrichtung von Erlebniswelten-Elementen
- Kompatibilität: Kompatibilität mit unserem Font Change Plugin
- Bugfix: Optimiert die Darstellung des Newsletter-Inputs bei größerer Schrift
- Bugfix: Konfigurator Option Label Textfarbe bei dunkler Shop-Hintergrundfarbe

# 1.2.0
- Feature: Implementierung einer Auswahl von Google-Fonts als lokale Theme Schriften zur einfachen datenschutzkonformen Nutzung ohne externe Einbettung.
- Feature: Neue Konfiguration zur Auswahl der Theme-Schriftarten.
- Feature: Neue Konfiguration zum Umschalten der ein- und ausklappbaren Footer Spalten, sodass diese auf Mobilgeräten standardmäßig geöffnet sind.
- Feature: Neue Konfiguration um die Anzahl der Produkte pro Zeile im Listing für jeden Viewport/Gerät festzulegen.
- Feature: Neue Konfiguration zum Aktivieren oder Deaktivieren von wesentlichen Merkmalen auf Produktboxen.
- Optimierung: Verbesserungen in der theme.json
- Optimierung: Zur besseren Anpassbarkeit wurden in base.html.twig einige Twig-Blöcke hinzugefügt.
- Optimierung: Verbesserung der line-height der Produktbeschreibungen in Produktboxen
- Optimierung: Überarbeitung der Hilfetexte für Custom Code

# 1.1.4
- Feature: Code Editor in der Plugin-Konfiguration für Custom Code
- Optimierung: Positionierung der zentrierten Breadcrumbs im Header-Modus absolute und volle Breite
- Optimierung: Disabled Buttons Kontrast optimiert
- Optimierung: Erscheinungsbild des Produktkonfigurators mit abgerundeten Rändern verbessert
- Optimierung: Überarbeitung der Produkt-Box Big Image Bilddarstellung
- Optimierung: Überarbeitung der Kategoriebild-Steuerung

# 1.1.3
- Bugfix: Darstellung der Breadcrumbs auf der Detailseite bei SW 6.3.4.0

# 1.1.2
- Optimierung: Positionierung der Suggest Suchergebnisse
- Optimierung: Verhindert das Überlagern des Produktbildes von Elementen
- Optimierung: Berechnung zusätzlichen Body Paddings für Cookie Banner nur bei Default-Anzeige
- Optimierung: Administration Modul injects repositoryFactory anstatt des theme Mixins
- Kompatibilität: Verbesserung der Kompatibilität der icon.html.twig
- Kompatibilität: Verstecke Thumbnails und Dot-Navigation bei zu vielen Produktbildern - SW 6.3.4.0

# 1.1.1
- Optimierung: Überarbeitung des Events zum Ausblenden des Preloaders
- Optimierung: Überarbeitung der Produkt-Kachel Bilder Anzeigemodi im Listing
- Optimierung: Verhindert das Springen der Produkt-Actions zwischen den Transition-states im Listing
- Optimierung: Verhindert das Durchblitzen der Hintergrundfarbe der Artikelbilder auf Detailseiten
- Optimierung: DOM .page-wrapper verschoben
- Optimierung: Breite des Flyout Menüs verbessert
- Bugfix: Quickview Cart Button - PageController can't be requested via XmlHttpRequest.

# 1.1.0
- Feature:  Neue Konfiguration zur Steuerung der Footer Spalten auf Tablet- und Desktop-Geräten
- Optimierung: Verbesserung der Darstellung in bestimmten Einstellungs-Kombinationen
- Optimierung: Breite des Erweiterten Menüs im Boxed Modus
- Optimierung: Product Slider Gutter über Konfiguration der Cards Abstände setzbar. Dadurch erhalten alle Grids dieselben Abstände.
- Optimierung: Überarbeitung der Header Kombinationen in Bezug auf die Darstellung des Offcanvas Navigation Buttons
- Optimierung: Vertikale Ausrichtung von Text und Icon im Header Warenkorb Button
- Optimierung: Überarbeitung der Sticky Header Funktion
- Optimierung: Suggest Suchergebnisse scrollbar
- Bugfix: Horizontales Scrollen durch Ausklappbare Suche in Verbindung mit Header Modus Full-width-boxed behoben
- Bugfix: Deavtivating toltips on touch-devices

# 1.0.5
- Optimierung: Verbesserung Lighthouse Report: font-display: swap zur Schrifteinbettung implementiert
- Optimierung: Weitere Verbesserungen der Accessibility des Lighthouse Report
- Optimierung: Verbesserte Darstellung der Bilder im Crossselling
- Bugfix: Schließen Button der Overlay-Suche hat nach Eingabe eines Suchstrings die Suche ausgeführt, wenn der Nutter sich dazu entschieden hat, doch keine Suche durchzuführen.
- Bugfix: header-main im sticky mode in Kombination mit der hide-Option überdeckte Elemente

# 1.0.4
- Optimierung: Überarbeitung der Header Darstellung für Single Header, Standard Suche, Logo mittig und Offcanvas Main Navigation XS-MD und XS-LG
- Optimierung: Positionierung des zentrierten Logos bei Darstellung von Elementen über dem Header, z.B. Conversion Banner
- Optimierung: Verbesserung des Sticky Headers bei absoluter Positionierung
- Optimierung: Verbesserung der Sticky Header Animation bei einzeiligem Header und hide-Option
- Optimierung: Verbesserung der Sticky Header Berechnung im Javascript in Verbindung mit der hide-Option
- Kompatibilität: Absolut positionierte Header überdecken nun keine Conversion-Leisten und andere Elemente über dem Header
- Bugfix: Verhindert das Escapen von HTML der Quickview Beschreibung und Hersteller Beschreibung im Hersteller Tab

# 1.0.3
- Optimierung: Überarbeitung der Footer Paddings im Boxed Layout
- Optimierung: Überarbeitung der ausklappbaren Suche in verschiedenen Header Styles
- Optimierung: Überarbeitung der Header Erlebniswelt Überlagerung
- Feature: Header Erlebniswelt Ünberlagerung auch auf Hintergrundfarbe unabhängig eines Kategorie-Bildes
- Bugfix: Abstände der Breadcrumbs im Boxed Layout
- Bugfix: Quickview Preisausrichtung
- Bugfix: Product-Actions nach Sortierung nicht sichtbar

# 1.0.2
- Optimierung: Überarbeitung der Main Navigation Hovers
- Optimierung: Überarbeitung des Quickview Icons
- Optimierung: Überarbeitung des Header mit mehrzeiligem Header, Logo links, Suche Standard
- Bugfix: Detailseite mobile Tab-Menü
- Bugfix: Einklappbare Top Bar initialer Zustand

# 1.0.1
- Feature: Content Animationen für Erlebniswelten hinzugefügt. (mehr dazu in der Doku)
- Kompatibilität: Verbesserung der Kompatibilität bei Platzierung des Flyout Menü im Fullwidth-Modus
- Optimierung: Kleinere Optimierungen von Bezeichnungen in der Konfiguration

# 1.0.0
- Initial plugin release
