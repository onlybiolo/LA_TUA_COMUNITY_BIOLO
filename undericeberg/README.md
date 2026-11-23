# UNDERGROUND / UNDERICEBERG — THE INTERNET'S WORST KEPT SECRET

> Archivio PHP + MySQL & Community Board della scena trap sperimentale, rage, pluggnb e SoundCloud italiana.

Think **Reddit meets SoundCloud meets Italian underground TikTok**.

Non è Spotify. Non è Instagram. Non è LinkedIn per rapper.

---

## 👑 THE MAIN CHARACTER: LAYYD

C'è un GOAT indiscusso su questa piattaforma: **LAYYD** (`@itslayydbaby`).
- **Progetto:** `camelCase`
- **Traccia:** `nonSoCosaSia!`
- **Ascolti:** **113 PLAYS** (Storici, zero piante industriali)

Mentre l'industria fabbrica superstar con budget milionari e bot, Layyd sta silenziosamente costruendo un impero. Il counter dei 113 ascolti è trattato con solennità titanica, con counter live interattivo e discussioni dedicate.

---

## 🧊 L'ICEBERG UNDERGROUND (PROFONDITÀ DA 0M A -100M)

1. **🏔️ Punta dell'Iceberg (0 m):** Glocky, 18K, Aira, Waze RRX, Drefgold... (riconosciuti, pogo continuo).
2. **🌊 Superficie (-10 m):** WhyTrevxr, Rooc Lee, Fashion Forty, Trenchmafia, Yungenob... (SoundCloud core, sparizioni misteriose e ritorni con capolavori).
3. **⚓ Profondo (-40 m):** Layyd (The GOAT), 19cola, Loui, BBY2K... (il santuario per adepti veri, 808 scivolati).
4. **🌌 Abisso (-100 m):** KiloSb, Bruce Laren, Ermy... (26 ascoltatori mensili, mistero totale, tracce introvabili).

---

## 🚀 FUNZIONALITÀ PRINCIPALI

- **Account Utente & Profili:**
  - Registrazione e Login con password hashing `password_hash()`.
  - Badge identitari: *113 Believer*, *OG Listener*, *FL Studio Warrior*, *Gatekeeper Emeritus*, *Unemployed Critic*, *Rage Scientist*.
  - Accesso rapido demo (1 click) per testare immediatamente ruoli diversi.
- **Profili Artisti Completi:**
  - Nome d'arte, bio/lore, foto, genere/stile, profondità iceberg in metri.
  - Tracce pubblicate con counter plays, anno e crediti produttore ("prod by...").
  - Link esterni a SoundCloud, Instagram, YouTube, Spotify.
  - Pagina speciale GOAT dedicata a Layyd con milestone "Road to 114 plays".
- **Esploratore Iceberg & Ricerca:**
  - Mappa visiva a livelli di profondità con le infografiche originali della scena.
  - Ricerca istantanea per nome e genere (rage, plugg, jerk, hood trap...).
  - Ordinamento per Iceberg, Plays e Nome.
- **Community Board (Reddit / Forum Style):**
  - Discussioni con titoli, flair colorati (`[LAYYD EMPIRE]`, `[SNIPPET]`, `[HOT TAKE]`, `[DISCUSSIONE]`, `[GATEKEEPING]`, `[PRODUCER LORE]`, `[RANKING]`).
  - Sistema di upvote interattivo (AJAX con fallback noscript).
  - Commenti con badge utente e upvoting delle risposte.
  - Ordinamento Hot, Recenti e Top.
- **CRUD Gestione Archivio:**
  - Aggiunta, modifica ed eliminazione artisti con tutti i metadati.

---

## 🛠️ AVVIO CON DOCKER

```bash
docker compose up -d --build
```

- **Sito Web:** [http://localhost:8080](http://localhost:8080)
- **phpMyAdmin:** [http://localhost:8081](http://localhost:8081) (`ice` / `ice`)
- **Reset Database:** `docker compose down -v`

### Profili Demo preimpostati (password per tutti: `underground123` o 1-click):
- `layyd_believer` (Badge: *113 Believer*, Admin)
- `preset_merchant` (Badge: *FL Studio Warrior*)
- `underground_curator` (Badge: *Gatekeeper Emeritus*)
- `whytrevxr_stan` (Badge: *OG Listener*)
