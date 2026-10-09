# contao-member-jumpto-bundle

**Deutsch** | [English below](#english)

## Deutsch

Mitglieder mit Login wählen im Frontend (z. B. auf der Seite „Mein Konto“) selbst, auf welcher Seite sie nach dem Login
landen.

### Installation

```bash
composer require e-spin/contao-member-jumpto-bundle
```

Danach im Contao-Manager oder per `vendor/bin/contao-console contao:migrate` die Datenbank aktualisieren (neue Felder
`tl_member.memberJumpToPage` und `tl_module.memberJumpToPages`).

### Einrichtung

1. Im Backend ein Frontend-Modul vom Typ **Zielseite nach dem Login** anlegen.
2. In der Modul-Konfiguration die möglichen Zielseiten eintragen. Jede Zeile hat eine Seite, eine optionale
   Bezeichnung (sonst der Seitentitel) und die Markierung **Standard**. Es gibt genau einen Standard, ohne Markierung
   gilt die erste Zeile. Dieselbe Seite darf mehrfach vorkommen, z. B. mit anderer Bezeichnung.
3. Das Modul auf einer Seite einbinden, auf die nur angemeldete Mitglieder kommen.

Das Modul zeigt nur Seiten, die veröffentlicht sind und auf die das Mitglied nach seinen Gruppen zugreifen darf. Ist
keine Seite verfügbar oder niemand angemeldet, erscheint es nicht.

### Mehrere Listen und Sprachen

Pro Liste ein eigenes Modul, z. B. eins pro Sprache oder pro Mitgliedergruppe mit eigenen Seiten und Bezeichnungen. Das
Mitglied hat nur eine Auswahl (Feld `memberJumpToPage` in `tl_member`), die zuletzt gespeicherte gilt.

### Verhalten beim Login

Hat das Mitglied eine Seite gewählt und ist sie erreichbar, geht der Login dorthin. Sie hat Vorrang vor der Weiterleitung
der Mitgliedergruppe und vor `_target_path` des Login-Formulars. Sonst bleibt die Weiterleitung von Contao unverändert,
auch bei Zwei-Faktor-Login wird erst nach dessen Abschluss umgeleitet. Der Standard der Liste ist nur die Vorauswahl im
Formular, er gilt für Mitglieder ohne eigene Wahl nicht beim Login.

Im Backend steht die Wahl in den Mitgliedsdaten im Bereich „Login“ und lässt sich dort ändern oder leeren.

### Voraussetzungen

Contao ^5.3 oder ^6.0, PHP ^8.3/^8.4, `menatwork/contao-multicolumnwizard-bundle`.

## English

Members with a login choose themselves, in the frontend (e.g. on a "My account" page), which page they land on after the
login.

### Installation

```bash
composer require e-spin/contao-member-jumpto-bundle
```

Then update the database in the Contao Manager or with `vendor/bin/contao-console contao:migrate` (new fields
`tl_member.memberJumpToPage` and `tl_module.memberJumpToPages`).

### Setup

1. Create a frontend module of the type **Page after the login** in the backend.
2. List the possible target pages in the module settings. Each row has a page, an optional label (the page title
   otherwise) and the **Default** mark. There is exactly one default, without a mark the first row is used. The same page may be listed several times, e.g. with another label.
3. Put the module on a page which only logged in members can reach.

The module only shows pages which are published and which the member may access according to the member groups. It stays
empty if no page is available or nobody is logged in.

### Several lists and languages

Use one module per list, e.g. one per language or per member group with its own pages and labels. A member has a single
choice (field `memberJumpToPage` in `tl_member`), the last saved one counts.

### Behavior at login

If the member has chosen a page and it is accessible, the login goes there. It wins over the redirect of the member
group and over `_target_path` of the login form. Otherwise the Contao redirect stays untouched, with two-factor
authentication the redirect waits until it is complete. The default of the list is only the preselection in the form,
it does not apply at login to members without their own choice.

In the backend the choice is part of the member data in the "Login" section, where it can be changed or cleared.

### Requirements

Contao ^5.3 or ^6.0, PHP ^8.3/^8.4, `menatwork/contao-multicolumnwizard-bundle`.
