# David Wiki Transfer

## Umwandeln der HTML Artikel ins JSON Format

```
php -f david-2-json.php
```

## Erstellen/Aktualisieren der Artikel im Forum

- Als erstes im Forum einloggen und die entsprechen Werte über die Browser Entwicklertools auslesen.
- Als zweites, die Werte im Skript anpassen
- Als drittes, das Skript ausführen. Am besten in "kleinen Schritten" um auf Fehler reagieren zu können.

```
php -f json-2-woltlab.php
```
