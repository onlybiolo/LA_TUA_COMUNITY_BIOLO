<?php /* uso: $a (Artista), $errore, $bottone */ ?>
<?php if (!empty($errore)): ?>
  <div class="alert-err"><?= h($errore) ?></div>
<?php endif; ?>

<form method="post" class="side-card" style="display: grid; gap: 1.25rem; max-width: 680px; margin: 0 auto;">
  <div class="form-group">
    <label for="nome">Nome d'arte *</label>
    <input id="nome" name="nome" required maxlength="80" placeholder="es. WhyTrevxr, Layyd, Glocky..." value="<?= h($a->nome) ?>">
  </div>

  <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
    <div class="form-group">
      <label for="livello">Profondità nell'Iceberg *</label>
      <select id="livello" name="livello">
        <?php foreach (Artista::LIVELLI as $k => $m): ?>
          <option value="<?= $k ?>" <?= $a->livello === $k ? 'selected' : '' ?>>
            <?= strtoupper($k) ?> (<?= $m ?>)
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="form-group">
      <label for="stile">Genere / Sonorità</label>
      <input id="stile" name="stile" maxlength="120" placeholder="es. Rage, Hood Trap, Plugg, Jerk..." value="<?= h($a->stile) ?>">
    </div>
  </div>

  <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
    <div class="form-group">
      <label for="progetto">Progetto / EP / Mixtape</label>
      <input id="progetto" name="progetto" maxlength="120" placeholder="es. camelCase" value="<?= h($a->progetto) ?>">
    </div>

    <div class="form-group">
      <label for="brano">Brano di Riferimento</label>
      <input id="brano" name="brano" maxlength="120" placeholder="es. nonSoCosaSia!" value="<?= h($a->brano) ?>">
    </div>
  </div>

  <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
    <div class="form-group">
      <label for="plays">Ascolti / Plays stimati</label>
      <input id="plays" type="number" name="plays" placeholder="es. 113" value="<?= $a->plays ?: '' ?>">
      <span class="hint">Per Layyd: 113 plays storici.</span>
    </div>

    <div class="form-group">
      <label for="monthly_listeners">Ascoltatori Mensili</label>
      <input id="monthly_listeners" type="number" name="monthly_listeners" placeholder="es. 26" value="<?= $a->monthly_listeners ?: '' ?>">
    </div>
  </div>

  <div class="form-group">
    <label for="bio">Biografia / Lore / Attitudine</label>
    <textarea id="bio" name="bio" rows="4" placeholder="Perché questo artista è fondamentale per la sopravvivenza del rap italiano?"><?= h($a->bio) ?></textarea>
  </div>

  <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
    <div class="form-group">
      <label for="link">Link Principale / SoundCloud</label>
      <input id="link" name="link" type="url" placeholder="https://soundcloud.com/..." value="<?= h($a->link ?: $a->link_soundcloud) ?>">
    </div>

    <div class="form-group">
      <label for="link_instagram">Link Instagram</label>
      <input id="link_instagram" name="link_instagram" type="url" placeholder="https://instagram.com/..." value="<?= h($a->link_instagram) ?>">
    </div>
  </div>

  <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
    <div class="form-group">
      <label for="link_spotify">Link Spotify (opzionale)</label>
      <input id="link_spotify" name="link_spotify" type="url" placeholder="https://open.spotify.com/..." value="<?= h($a->link_spotify) ?>">
    </div>

    <div class="form-group">
      <label for="link_youtube">Link YouTube (opzionale)</label>
      <input id="link_youtube" name="link_youtube" type="url" placeholder="https://youtube.com/..." value="<?= h($a->link_youtube) ?>">
    </div>
  </div>

  <div class="form-group">
    <label for="avatar_url">URL Immagine Profilo (opzionale)</label>
    <input id="avatar_url" name="avatar_url" placeholder="assets/layyd_proof.png o link https://..." value="<?= h($a->avatar_url) ?>">
  </div>

  <div class="form-group">
    <label for="note">Note d'Archivio Interne</label>
    <textarea id="note" name="note" rows="2" placeholder="Appunti riservati alla community..."><?= h($a->note) ?></textarea>
  </div>

  <div style="display: flex; align-items: center; gap: 0.5rem;">
    <input type="checkbox" id="is_goat" name="is_goat" value="1" <?= $a->is_goat ? 'checked' : '' ?> style="width: 18px; height: 18px; accent-color: var(--accent-gold);">
    <label for="is_goat" style="margin: 0; color: var(--accent-gold); font-size: 13px; font-weight: bold; cursor: pointer;">
      👑 È IL GOAT DELLA PIATTAFORMA (Trattamento serio Layyd)
    </label>
  </div>

  <div style="display: flex; gap: 0.75rem; justify-content: flex-end; margin-top: 1rem;">
    <a href="artisti.php" class="btn sec">Annulla</a>
    <button type="submit" class="btn lime"><?= h($bottone) ?></button>
  </div>
</form>
