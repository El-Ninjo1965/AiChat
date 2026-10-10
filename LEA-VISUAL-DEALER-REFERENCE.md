# Lea Visual Dealer Reference

Stand: 10.10.2026
Status: Zielbild / Übergabehinweis. Kein Development-Auftrag aus Lea-Privat.

## Zweck

Die Blackjack-Dealerin ist ein praktischer Prototyp für eine spätere konsistente visuelle Lea-Darstellung. Im Blackjack selbst heißt die Figur ausschließlich „Dealer“.

## Verbindliches visuelles Zielbild

- Fotorealistisch, erwachsen, natürlich und professionell.
- Dunkelbraunes hochgestecktes Haar; lose natürliche Strähnen sind zulässig.
- Dezente schwarze Casino-Kleidung, elegant und funktional.
- Gesicht, Körperproportionen, Frisur und Grunderscheinung müssen über sämtliche Ansichten und Animationen konsistent bleiben.
- Anatomisch korrekte Hände und Finger haben hohe Priorität.
- Keine Comic-Anmutung, keine übertriebene Sexualisierung und keine privaten Lea-Schriftzüge im Spiel.
- Die erzeugte Charakter-Collage dient zusammen mit den vorhandenen Lea-Referenzbildern als visuelle Referenz. Bei Abweichungen soll die Identitätskonsistenz der vorhandenen Lea-Referenzen Vorrang vor zufälligen Details einzelner generierter Frames haben.

## Bewegungsqualität

Die Dealerin soll nicht wie eine Abfolge einzelner Bilder wirken. Bewegungen müssen ruhig, flüssig und körperlich plausibel ineinandergreifen.

Besonders wichtig:

- natürliche Beschleunigung und Abbremsung statt abruptem Start/Stopp,
- Blickbewegung beginnt sinnvoll vor bzw. zusammen mit der Körperbewegung,
- Augen, Kopf, Schultern, Oberkörper und Armbewegung müssen zusammenpassen,
- Spielerpositionen werden gezielt angesehen,
- Kartenbewegung endet exakt am vorgesehenen Ziel,
- Rückkehr in die Grundhaltung ohne sichtbaren Sprung,
- wiederholte Abläufe dürfen keine sichtbaren Reset-Zuckungen erzeugen,
- Animationstempo soll dem Eindruck einer professionellen Casino-Dealerin entsprechen, nicht einem schnellen UI-Effekt.

## Empfohlene technische Richtung

Für den interaktiven Blackjack-Einsatz ist ein riggbares 3D-Modell mit getrennt steuerbaren Animationsclips einer reinen Videodatei vorzuziehen. Zielrichtung: GLB/glTF, PBR-Materialien und browserseitige Steuerung, beispielsweise mit Three.js/WebGL.

LookAt sollte nicht als sechs vollständig getrennte starre Videos gedacht werden, sondern möglichst über steuerbare Blick-/Kopfziele oder sauber blendbare Clips erfolgen.

Deal/Hit/Split/Reveal sollten wiederverwendbare Bewegungsbausteine sein. Die Spiellogik bestimmt Spieler, Karten und Reihenfolge; die Figur visualisiert ausschließlich den vorgegebenen Zustand.

Für schwächere Geräte kann später eine 2D-/2.5D-Fallback-Darstellung vorgesehen werden.

## Qualitätspriorität

Qualität hat Vorrang vor schneller Fertigstellung. Falls ein verwendeter Agent oder ein Tool kein belastbares riggbares 3D-Ergebnis erzeugen kann, darf es kein Video oder Sprite-Sheet als echtes 3D-Modell ausgeben. In diesem Fall soll es hochwertige Referenzen, Keyframes oder Assets für eine geeignete 3D-Pipeline liefern.

## Zukunftsnutzen für Lea

Der Dealer-Prototyp soll so gedacht werden, dass erfolgreiche Grundlagen später für Leas eigene visuelle Darstellung wiederverwendbar sein können: natürliche Mimik, Blicksteuerung, Gestik, Lip-Sync, Sprachchat-Darstellung und kurze szenische Animationen.

Die technische Umsetzung dieser Lea-Zukunftsfunktionen gehört in Lea-Update. Dieses Dokument beschreibt nur das von Lea gewünschte Zielbild und die dafür wichtigen Qualitätsmerkmale.
