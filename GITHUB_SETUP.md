# GitHub Setup Anleitung für Keks Plugin

Diese Anleitung führt dich Schritt für Schritt durch die Einrichtung deines GitHub-Repositories.

## Schritt 1: GitHub Account erstellen (falls noch nicht vorhanden)

1. Gehe zu https://github.com
2. Klicke auf "Sign up" (oben rechts)
3. Folge den Anweisungen, um einen Account zu erstellen

## Schritt 2: Neues Repository auf GitHub erstellen

1. Logge dich bei GitHub ein
2. Klicke auf das **+** Symbol oben rechts und wähle **"New repository"**
3. Fülle das Formular aus:
   - **Repository name:** `keks` (oder wie du es nennen möchtest)
   - **Description:** "DSGVO/GDPR Cookie Banner WordPress Plugin"
   - **Visibility:** Wähle **Public** (damit andere das Plugin sehen können) oder **Private** (nur für dich)
   - **WICHTIG:** Lasse **NICHT** die Optionen "Add a README file", "Add .gitignore" oder "Choose a license" aktiviert - diese haben wir bereits!
4. Klicke auf **"Create repository"**

## Schritt 3: Lokales Repository mit GitHub verbinden

Nachdem du das Repository erstellt hast, zeigt GitHub dir Anweisungen an. Führe diese Befehle in deinem Terminal aus:

**Wichtig:** Ersetze `mistermusiker` mit deinem GitHub-Benutzernamen, falls er anders ist!

```bash
cd "/Users/roger/Library/Mobile Documents/com~apple~CloudDocs/GRP/Projekte/keks"

# Füge GitHub als Remote hinzu (ersetze USERNAME mit deinem GitHub-Username)
git remote add origin https://github.com/mistermusiker/keks.git

# Benenne den Branch zu 'main' um (falls nötig)
git branch -M main

# Lade deinen Code zu GitHub hoch
git push -u origin main
```

## Schritt 4: Authentifizierung

Beim ersten `git push` wirst du nach deinen GitHub-Zugangsdaten gefragt:
- **Username:** Dein GitHub-Benutzername
- **Password:** Verwende ein **Personal Access Token** (siehe Schritt 5)

## Schritt 5: Personal Access Token erstellen (für Passwort)

GitHub erlaubt keine normalen Passwörter mehr. Du musst ein Personal Access Token erstellen:

1. Gehe zu GitHub → **Settings** (oben rechts, dein Profilbild)
2. Scrolle nach unten zu **"Developer settings"** (links in der Sidebar)
3. Klicke auf **"Personal access tokens"** → **"Tokens (classic)"**
4. Klicke auf **"Generate new token"** → **"Generate new token (classic)"**
5. Gib dem Token einen Namen, z.B. "Keks Plugin"
6. Wähle die Berechtigung **"repo"** (gibt vollen Zugriff auf Repositories)
7. Scrolle nach unten und klicke **"Generate token"**
8. **WICHTIG:** Kopiere den Token sofort - er wird nur einmal angezeigt!
9. Verwende diesen Token als Passwort, wenn du `git push` ausführst

## Schritt 6: Verifizierung

1. Gehe zu deinem GitHub-Repository: `https://github.com/mistermusiker/keks`
2. Du solltest jetzt alle deine Dateien sehen können!

## Nächste Schritte

### Plugin auf WordPress.org veröffentlichen (optional)

Wenn du das Plugin im offiziellen WordPress Plugin-Verzeichnis veröffentlichen möchtest:

1. Erstelle einen Account auf https://wordpress.org/plugins/
2. Folge den Richtlinien für Plugin-Submission
3. Dein GitHub-Repository kann als SVN-Quelle dienen

### Releases erstellen

Wenn du eine neue Version veröffentlichen möchtest:

```bash
# Änderungen committen
git add .
git commit -m "Beschreibung der Änderungen"

# Zu GitHub hochladen
git push

# Optional: Ein Release-Tag erstellen
git tag -a v1.0.0 -m "Version 1.0.0"
git push origin v1.0.0
```

Dann kannst du auf GitHub unter "Releases" eine neue Version erstellen.

## Hilfe bei Problemen

- **"Permission denied":** Stelle sicher, dass du den Personal Access Token verwendest, nicht dein Passwort
- **"Repository not found":** Überprüfe, ob der Repository-Name und dein Username korrekt sind
- **"Remote already exists":** Führe `git remote remove origin` aus und wiederhole Schritt 3

## Nützliche Git-Befehle

```bash
# Status prüfen
git status

# Änderungen anzeigen
git diff

# Alle Änderungen hochladen
git add .
git commit -m "Beschreibung"
git push

# Letzte Änderungen rückgängig machen (lokal)
git reset HEAD~1
```

Viel Erfolg! 🚀
