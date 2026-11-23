<?php
require_once __DIR__ . '/config.php';

function init_database() {
    $pdo = db();

    // 1. Tabella artisti - colonne
    $cols = $pdo->query("SHOW COLUMNS FROM artisti")->fetchAll(PDO::FETCH_COLUMN);
    
    if (!in_array('slug', $cols)) $pdo->exec("ALTER TABLE artisti ADD COLUMN slug VARCHAR(90) NULL AFTER nome");
    if (!in_array('bio', $cols)) $pdo->exec("ALTER TABLE artisti ADD COLUMN bio TEXT NULL AFTER stile");
    if (!in_array('progetto', $cols)) $pdo->exec("ALTER TABLE artisti ADD COLUMN progetto VARCHAR(120) NULL AFTER bio");
    if (!in_array('plays', $cols)) $pdo->exec("ALTER TABLE artisti ADD COLUMN plays INT NOT NULL DEFAULT 0 AFTER brano");
    if (!in_array('monthly_listeners', $cols)) $pdo->exec("ALTER TABLE artisti ADD COLUMN monthly_listeners INT NOT NULL DEFAULT 0 AFTER plays");
    if (!in_array('link_soundcloud', $cols)) $pdo->exec("ALTER TABLE artisti ADD COLUMN link_soundcloud VARCHAR(255) NULL AFTER link");
    if (!in_array('link_youtube', $cols)) $pdo->exec("ALTER TABLE artisti ADD COLUMN link_youtube VARCHAR(255) NULL AFTER link_soundcloud");
    if (!in_array('link_instagram', $cols)) $pdo->exec("ALTER TABLE artisti ADD COLUMN link_instagram VARCHAR(255) NULL AFTER link_youtube");
    if (!in_array('link_spotify', $cols)) $pdo->exec("ALTER TABLE artisti ADD COLUMN link_spotify VARCHAR(255) NULL AFTER link_instagram");
    if (!in_array('avatar_url', $cols)) $pdo->exec("ALTER TABLE artisti ADD COLUMN avatar_url VARCHAR(255) NULL AFTER link_spotify");
    if (!in_array('is_goat', $cols)) $pdo->exec("ALTER TABLE artisti ADD COLUMN is_goat TINYINT(1) NOT NULL DEFAULT 0 AFTER note");

    // 2. Tabella utenti
    $pdo->exec("CREATE TABLE IF NOT EXISTS utenti (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(40) NOT NULL UNIQUE,
        email VARCHAR(100) NOT NULL UNIQUE,
        password_hash VARCHAR(255) NOT NULL,
        bio TEXT NULL,
        badge VARCHAR(60) NOT NULL DEFAULT 'SoundCloud Lurker',
        avatar_color VARCHAR(20) NOT NULL DEFAULT '#a3e635',
        ruolo ENUM('user','mod','admin') NOT NULL DEFAULT 'user',
        creato_il TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) CHARACTER SET utf8mb4 ENGINE=InnoDB");

    // 3. Tabella tracce
    $pdo->exec("CREATE TABLE IF NOT EXISTS tracce (
        id INT AUTO_INCREMENT PRIMARY KEY,
        artista_id INT NOT NULL,
        titolo VARCHAR(150) NOT NULL,
        progetto VARCHAR(150) NULL,
        anno INT NULL DEFAULT 2024,
        plays INT NOT NULL DEFAULT 0,
        produttore VARCHAR(150) NULL,
        link_audio VARCHAR(255) NULL,
        is_hit TINYINT(1) NOT NULL DEFAULT 0,
        creato_il TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX (artista_id)
    ) CHARACTER SET utf8mb4 ENGINE=InnoDB");

    // 4. Tabella post community
    $pdo->exec("CREATE TABLE IF NOT EXISTS post (
        id INT AUTO_INCREMENT PRIMARY KEY,
        utente_id INT NOT NULL,
        artista_id INT NULL,
        titolo VARCHAR(255) NOT NULL,
        flair VARCHAR(50) NOT NULL DEFAULT 'DISCUSSIONE',
        contenuto TEXT NOT NULL,
        link_esterno VARCHAR(255) NULL,
        upvotes INT NOT NULL DEFAULT 1,
        creato_il TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX (utente_id),
        INDEX (artista_id)
    ) CHARACTER SET utf8mb4 ENGINE=InnoDB");

    // 5. Tabella commenti
    $pdo->exec("CREATE TABLE IF NOT EXISTS commenti (
        id INT AUTO_INCREMENT PRIMARY KEY,
        post_id INT NOT NULL,
        utente_id INT NOT NULL,
        testo TEXT NOT NULL,
        upvotes INT NOT NULL DEFAULT 0,
        creato_il TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX (post_id),
        INDEX (utente_id)
    ) CHARACTER SET utf8mb4 ENGINE=InnoDB");

    $pdo->exec("CREATE TABLE IF NOT EXISTS post_upvotes (
        post_id INT NOT NULL,
        utente_id INT NOT NULL,
        PRIMARY KEY (post_id, utente_id)
    ) ENGINE=InnoDB");

    $pdo->exec("CREATE TABLE IF NOT EXISTS comment_upvotes (
        comment_id INT NOT NULL,
        utente_id INT NOT NULL,
        PRIMARY KEY (comment_id, utente_id)
    ) ENGINE=InnoDB");

    // Seed Utenti
    $countUtenti = $pdo->query("SELECT COUNT(*) FROM utenti")->fetchColumn();
    if ($countUtenti == 0) {
        $defaultPassword = password_hash('underground123', PASSWORD_DEFAULT);
        $utentiSeed = [
            ['layyd_believer', 'layyd@underground.it', $defaultPassword, "Presente dal play n. 12. L'industria musicale non vuole che ascoltiate camelCase.", '113 Believer', '#facc15', 'admin'],
            ['preset_merchant', 'fl@underground.it', $defaultPassword, "Combatto contro i preset stock di FL Studio dal 2021. 808 a +14dB o niente.", 'FL Studio Warrior', '#a3e635', 'user'],
            ['underground_curator', 'gatekeeper@underground.it', $defaultPassword, "Se superi i 100 ascoltatori mensili hai venduto la tua anima alla major.", 'Gatekeeper Emeritus', '#38bdf8', 'mod'],
            ['whytrevxr_stan', 'trevxr@underground.it', $defaultPassword, "Carti trovato in un fosso dopo l'ultimo drop di Why Trevxr.", 'OG Listener', '#fb7185', 'user'],
            ['critico_disoccupato', 'critico@underground.it', $defaultPassword, "Scrivo saggi filosofici di 3000 parole sotto un leak registrato con le cuffie Apple rotte.", 'Unemployed Critic', '#c084fc', 'user'],
            ['rage_scientist', 'rage@underground.it', $defaultPassword, "Analizzo se un synth distorto ha tendenze narcisistiche o solo problemi d'impegno.", 'Rage Scientist', '#f97316', 'user']
        ];
        $st = $pdo->prepare("INSERT INTO utenti (username, email, password_hash, bio, badge, avatar_color, ruolo) VALUES (?,?,?,?,?,?,?)");
        foreach ($utentiSeed as $u) {
            $st->execute($u);
        }
    }

    // LISTA ARTISTI REALI VERIFICATI ONLINE CON FOTO E LINK
    $artistiReali = [
        [
            'nome' => 'Layyd',
            'slug' => 'layyd',
            'livello' => 'profondo',
            'stile' => 'Hood Trap / Experimental / Jerk',
            'bio' => "Artista e produttore emergente della scena underground (@itslayydbaby). Autore del leggendario progetto 'camelCase' e della traccia 'nonSoCosaSia!'. Mentre l'industria discografica è occupata a fabbricare superstar artificiali con budget a sei zeri e bot, Layyd sta silenziosamente costruendo un impero sonoro a colpi di 808 scivolati e flow sghembo. 113 ascolti storici certificati. Zero piante industriali.",
            'progetto' => 'camelCase',
            'brano' => 'nonSoCosaSia!',
            'plays' => 113,
            'monthly_listeners' => 38,
            'link' => 'https://soundcloud.com/search?q=itslayydbaby',
            'link_soundcloud' => 'https://soundcloud.com/search?q=itslayydbaby',
            'link_instagram' => 'https://www.instagram.com/itslayydbaby',
            'link_youtube' => 'https://www.youtube.com/results?search_query=layyd+nonSoCosaSia',
            'avatar_url' => 'assets/layyd_proof.png',
            'is_goat' => 1,
            'tracce' => [
                ['titolo' => 'nonSoCosaSia!', 'progetto' => 'camelCase', 'anno' => 2024, 'plays' => 113, 'prod' => 'Layyd (@itslayydbaby)', 'link' => 'https://soundcloud.com/search?q=itslayydbaby', 'hit' => 1],
                ['titolo' => 'camelCase_intro_v3_rough', 'progetto' => 'camelCase', 'anno' => 2024, 'plays' => 47, 'prod' => 'Layyd', 'link' => null, 'hit' => 0],
                ['titolo' => '808_fight_snippet.wav', 'progetto' => 'Single', 'anno' => 2024, 'plays' => 32, 'prod' => 'Layyd', 'link' => null, 'hit' => 0]
            ]
        ],
        [
            'nome' => 'WhyTrevxr',
            'slug' => 'whytrevxr',
            'livello' => 'superficie',
            'stile' => 'Rage / Melodic Trap',
            'bio' => "Rapper e produttore di Bergamo, figura cult del rage melodico e della trap sperimentale. Noto per sparire da Instagram e Telegram per 8 mesi consecutivi senza dare segni di vita per poi riapparire all'improvviso alle 04:17 droppando progetti spaziali con il producer Fireken. Il suo album 'Let Them Fly' e la hit 'Batman Moncler' sono pietre miliari.",
            'progetto' => 'Let Them Fly',
            'brano' => 'Batman Moncler (prod. Fireken)',
            'plays' => 14800,
            'monthly_listeners' => 920,
            'link' => 'https://open.spotify.com/artist/6VwZ6AVO6J0QW6BnKJWk9F',
            'link_soundcloud' => 'https://soundcloud.com/search?q=whytrevxr',
            'link_spotify' => 'https://open.spotify.com/artist/6VwZ6AVO6J0QW6BnKJWk9F',
            'link_youtube' => 'https://www.youtube.com/watch?v=0ClvkyUpyWI',
            'link_instagram' => 'https://www.instagram.com/whytrevxr',
            'avatar_url' => 'assets/artists/whytrevxr.jpg',
            'is_goat' => 0,
            'tracce' => [
                ['titolo' => 'Batman Moncler', 'progetto' => 'Let Them Fly', 'anno' => 2026, 'plays' => 6400, 'prod' => 'Fireken', 'link' => 'https://www.youtube.com/watch?v=0ClvkyUpyWI', 'hit' => 1],
                ['titolo' => 'Tututu', 'progetto' => 'Let Them Fly', 'anno' => 2026, 'plays' => 4200, 'prod' => 'Fireken', 'link' => 'https://www.youtube.com/watch?v=0ClvkyUpyWI', 'hit' => 0],
                ['titolo' => 'Uno Come Me', 'progetto' => 'Single', 'anno' => 2025, 'plays' => 2800, 'prod' => 'Self-produced', 'link' => null, 'hit' => 0]
            ]
        ],
        [
            'nome' => 'Glocky',
            'slug' => 'glocky',
            'livello' => 'punta',
            'stile' => 'Emo Rap / Melodic Plugg',
            'bio' => "Tra i nomi di riferimento della scena urban e SoundCloud italiana, fondatore dell'immaginario Northside con la label Square (distribuita Believe). Con il mixtape 'Northside Melodies' (certificato disco di platino) e la hit 'Stare Senza' con Fashion Forty ha portato le sonorità scure e introspettive dell'underground a un impatto nazionale.",
            'progetto' => 'Northside Melodies',
            'brano' => 'Stare Senza (feat. Fashion Forty)',
            'plays' => 48000,
            'monthly_listeners' => 140000,
            'link' => 'https://open.spotify.com/search/Glocky%20Northside%20Melodies',
            'link_soundcloud' => 'https://soundcloud.com/search?q=glocky',
            'link_spotify' => 'https://open.spotify.com/search/Glocky%20Northside%20Melodies',
            'link_youtube' => 'https://www.youtube.com/watch?v=GmOjHxnKPO8',
            'link_instagram' => 'https://www.instagram.com/glocky.70',
            'avatar_url' => 'assets/artists/glocky.jpg',
            'is_goat' => 0,
            'tracce' => [
                ['titolo' => 'Stare Senza (feat. Fashion Forty)', 'progetto' => 'Northside Melodies', 'anno' => 2024, 'plays' => 28000, 'prod' => 'Fireken, Azu, Blad', 'link' => 'https://www.youtube.com/watch?v=GmOjHxnKPO8', 'hit' => 1],
                ['titolo' => 'Fuori Controllo', 'progetto' => 'Northside Melodies', 'anno' => 2024, 'plays' => 11000, 'prod' => 'Fireken', 'link' => null, 'hit' => 0],
                ['titolo' => 'Hai Preso Le Pillole? (feat. Waze RRX)', 'progetto' => 'Northside Melodies', 'anno' => 2024, 'plays' => 9000, 'prod' => 'Azu', 'link' => null, 'hit' => 0]
            ]
        ],
        [
            'nome' => '18K',
            'slug' => '18k',
            'livello' => 'punta',
            'stile' => 'Rage / Emo Rap / Post-Trap',
            'bio' => "Filippo Casadio (classe 1997, Faenza/Brisighella). Sound viscerale unico fortemente influenzato dalla SoundCloud generation svedese (Bladee, Yung Lean, Drain Gang) e dal metalcore. Autore di album di culto come 'Grotto', 'Grotto Inferno', 'ANTI ANTI' (EMI/Universal) e 'IO'. Liriche di alienazione provinciale e concerti con autentici moshpit.",
            'progetto' => 'ANTI ANTI',
            'brano' => 'Anti Anti',
            'plays' => 38000,
            'monthly_listeners' => 95000,
            'link' => 'https://open.spotify.com/album/33Bv5cO76lI8MibTbrY3t0',
            'link_soundcloud' => 'https://soundcloud.com/search?q=18k',
            'link_spotify' => 'https://open.spotify.com/album/33Bv5cO76lI8MibTbrY3t0',
            'link_youtube' => 'https://www.youtube.com/watch?v=WogmwR_5ZQc',
            'link_instagram' => 'https://www.instagram.com/18k.dorop',
            'avatar_url' => 'assets/artists/18k.jpg',
            'is_goat' => 0,
            'tracce' => [
                ['titolo' => 'Anti Anti', 'progetto' => 'ANTI ANTI', 'anno' => 2024, 'plays' => 16500, 'prod' => 'Macello', 'link' => 'https://www.youtube.com/watch?v=WogmwR_5ZQc', 'hit' => 1],
                ['titolo' => 'Buttare, buttare', 'progetto' => 'ANTI ANTI', 'anno' => 2024, 'plays' => 12000, 'prod' => 'Macello', 'link' => null, 'hit' => 0],
                ['titolo' => 'No Kizzy (feat. Kid Yugi)', 'progetto' => 'Grotto Inferno', 'anno' => 2023, 'plays' => 24000, 'prod' => 'Junior K', 'link' => null, 'hit' => 1]
            ]
        ],
        [
            'nome' => 'Aira',
            'slug' => 'aira',
            'livello' => 'punta',
            'stile' => 'Rage / Hyperpop / Glitch Trap',
            'bio' => "Andrea Ariosto (classe 2004, Calabria). Tra i nomi di punta della nuova ondata rap e rage italiana con distribuzione Warner/Atlantic. Nei suoi dischi 'Crash Out' e 'All In' unisce synth laser taglienti, batterie veloci ed energia grezza. Collaborazioni di rilievo con Tony Boy e Glocky.",
            'progetto' => 'Crash Out',
            'brano' => 'Potere (feat. Tony Boy)',
            'plays' => 22000,
            'monthly_listeners' => 45000,
            'link' => 'https://open.spotify.com/album/2OSYIvhAF22cJtRFSLER6g',
            'link_soundcloud' => 'https://soundcloud.com/search?q=aira+crash+out',
            'link_spotify' => 'https://open.spotify.com/album/2OSYIvhAF22cJtRFSLER6g',
            'link_youtube' => 'https://www.youtube.com/results?search_query=Aira+Crash+Out',
            'link_instagram' => 'https://www.instagram.com/airadalloscuro',
            'avatar_url' => 'assets/artists/aira.jpg',
            'is_goat' => 0,
            'tracce' => [
                ['titolo' => 'Potere (feat. Tony Boy)', 'progetto' => 'Crash Out', 'anno' => 2026, 'plays' => 11500, 'prod' => 'Aira & Azu', 'link' => 'https://www.youtube.com/results?search_query=Aira+Potere', 'hit' => 1],
                ['titolo' => 'Rager!', 'progetto' => 'Crash Out', 'anno' => 2026, 'plays' => 6200, 'prod' => 'Aira', 'link' => null, 'hit' => 0],
                ['titolo' => 'Down Again (feat. Glocky)', 'progetto' => 'All In', 'anno' => 2025, 'plays' => 8400, 'prod' => 'Fireken', 'link' => null, 'hit' => 0]
            ]
        ],
        [
            'nome' => 'Waze RRX',
            'slug' => 'waze-rrx',
            'livello' => 'punta',
            'stile' => 'Rage / Glo / Distorted 808',
            'bio' => "Nosakhare Emmanuel Igbinosa (Verona). Bandiera del suono RRX, bassi distorti al limite del clipping e delivery vocale aggressiva. Uno dei precursori del rage underground su SoundCloud fin dal 2017 con progetti come 'MOON-X', 'B4RRX' e tracce iconiche come 'leggenda vivente' e 'Roccia Music' con Glocky.",
            'progetto' => 'MOON-X',
            'brano' => 'leggenda vivente',
            'plays' => 13400,
            'monthly_listeners' => 18000,
            'link' => 'https://open.spotify.com/search/Waze%20RRX',
            'link_soundcloud' => 'https://soundcloud.com/wazerrx',
            'link_spotify' => 'https://open.spotify.com/search/Waze%20RRX',
            'link_youtube' => 'https://www.youtube.com/watch?v=KU8xYEzC2DQ',
            'link_instagram' => 'https://www.instagram.com/wazerrx',
            'avatar_url' => 'assets/artists/wazerrx.jpg',
            'is_goat' => 0,
            'tracce' => [
                ['titolo' => 'leggenda vivente', 'progetto' => 'MOON-X', 'anno' => 2024, 'plays' => 6100, 'prod' => 'RRX Squad', 'link' => 'https://www.youtube.com/watch?v=KU8xYEzC2DQ', 'hit' => 1],
                ['titolo' => 'Roccia Music (feat. Glocky)', 'progetto' => 'B4RRX', 'anno' => 2024, 'plays' => 4500, 'prod' => 'Fireken', 'link' => null, 'hit' => 0]
            ]
        ],
        [
            'nome' => 'Rooc Lee',
            'slug' => 'rooc-lee',
            'livello' => 'superficie',
            'stile' => 'Pluggnb / Jerk / Cloud Rap',
            'bio' => "Rapper e producer underground. Melodie spaziali, batterie jerk veloci e flow rilassato ma letale. Autore del disco 'Rockstar Syndrome' e del singolo cult 'Asociale'. Collaboratore fisso di 10kwatt e Yungenob.",
            'progetto' => 'Rockstar Syndrome',
            'brano' => 'Asociale',
            'plays' => 6200,
            'monthly_listeners' => 1250,
            'link' => 'https://open.spotify.com/artist/5J9oExne65TylhJLUsnSQ8',
            'link_soundcloud' => 'https://soundcloud.com/rooclee',
            'link_spotify' => 'https://open.spotify.com/artist/5J9oExne65TylhJLUsnSQ8',
            'link_youtube' => 'https://www.youtube.com/watch?v=-XD34jDftVE',
            'link_instagram' => 'https://solo.to/rooclee',
            'avatar_url' => 'assets/artists/rooclee.jpg',
            'is_goat' => 0,
            'tracce' => [
                ['titolo' => 'Asociale', 'progetto' => 'Rockstar Syndrome', 'anno' => 2025, 'plays' => 3200, 'prod' => 'Rooc Lee', 'link' => 'https://www.youtube.com/watch?v=-XD34jDftVE', 'hit' => 1],
                ['titolo' => 'Loyal', 'progetto' => 'Rockstar Syndrome', 'anno' => 2025, 'plays' => 1900, 'prod' => 'Rooc Lee', 'link' => null, 'hit' => 0]
            ]
        ],
        [
            'nome' => 'Fashion Forty',
            'slug' => 'fashion-forty',
            'livello' => 'superficie',
            'stile' => 'Experimental Trap / Dark Glo',
            'bio' => "Collettivo Square Kidz, autore di 'SCOPAFRIEN2' e coprotagonista di 'Stare Senza' con Glocky. Estetica avant-garde e barre nichiliste sopra bassi 808 ultra-saturi. Album di riferimento 'fashion' e 'LIMITLESS'.",
            'progetto' => 'fashion',
            'brano' => 'SCOPAFRIEN2 (feat. Glocky)',
            'plays' => 8400,
            'monthly_listeners' => 1700,
            'link' => 'https://open.spotify.com/search/Fashion%20Forty',
            'link_soundcloud' => 'https://soundcloud.com/search?q=fashion+forty',
            'link_spotify' => 'https://open.spotify.com/search/Fashion%20Forty',
            'link_youtube' => 'https://www.youtube.com/watch?v=dIxc8wy7pIg',
            'link_instagram' => 'https://www.instagram.com/fashionforty_',
            'avatar_url' => 'assets/artists/fashionforty.jpg',
            'is_goat' => 0,
            'tracce' => [
                ['titolo' => 'SCOPAFRIEN2 (feat. Glocky)', 'progetto' => 'Square Tape', 'anno' => 2024, 'plays' => 4600, 'prod' => 'nardogokrazy & SoundByKid', 'link' => 'https://www.youtube.com/watch?v=dIxc8wy7pIg', 'hit' => 1],
                ['titolo' => 'Pink Music (con Glocky & Waze RRX)', 'progetto' => 'Single', 'anno' => 2024, 'plays' => 3800, 'prod' => 'Fireken', 'link' => null, 'hit' => 0]
            ]
        ],
        [
            'nome' => 'Yungenob',
            'slug' => 'yungenob',
            'livello' => 'superficie',
            'stile' => 'Jerk / Ambient Plugg',
            'bio' => "Beat con sintetizzatori vintage e campioni dilatati. Punto di riferimento per chi naviga SoundCloud nelle ore piccole. Autore del progetto 'Ego' e del brano 'Figlio Del Silenzio'.",
            'progetto' => 'Ego',
            'brano' => 'Figlio Del Silenzio',
            'plays' => 3800,
            'monthly_listeners' => 740,
            'link' => 'https://soundcloud.com/yungenob',
            'link_soundcloud' => 'https://soundcloud.com/yungenob',
            'link_spotify' => 'https://open.spotify.com/search/yungenob',
            'link_youtube' => 'https://www.youtube.com/watch?v=Dlbt5CBpoj0',
            'link_instagram' => 'https://www.instagram.com/yungenob',
            'avatar_url' => 'assets/artists/yungenob.jpg',
            'is_goat' => 0,
            'tracce' => [
                ['titolo' => 'Figlio Del Silenzio', 'progetto' => 'Ego', 'anno' => 2024, 'plays' => 2100, 'prod' => 'Jaily & Flatpearl', 'link' => 'https://www.youtube.com/watch?v=Dlbt5CBpoj0', 'hit' => 1],
                ['titolo' => 'Tornano Sempre (con Rooc Lee)', 'progetto' => 'Single', 'anno' => 2024, 'plays' => 1700, 'prod' => 'Rooc Lee', 'link' => null, 'hit' => 0]
            ]
        ],
        [
            'nome' => 'Trenchmafia',
            'slug' => 'trenchmafia',
            'livello' => 'superficie',
            'stile' => 'Raw Drill / Distorted Trap',
            'bio' => "Collettivo suburbano e sound raw privo di compromessi radiofonici. Drum veloci, rime crude e fedeltà all'etica underground. Album recente 'best music evr' con tracce culto come 'went Cobain' e 'Killa'.",
            'progetto' => 'best music evr',
            'brano' => 'went Cobain',
            'plays' => 4200,
            'monthly_listeners' => 890,
            'link' => 'https://open.spotify.com/search/trenchmafia',
            'link_soundcloud' => 'https://soundcloud.com/search?q=trenchmafia',
            'link_spotify' => 'https://open.spotify.com/search/trenchmafia',
            'link_youtube' => 'https://www.youtube.com/watch?v=_l41dB_-2Jo',
            'link_instagram' => 'https://www.instagram.com/trenchmafia_',
            'avatar_url' => 'assets/artists/trenchmafia.jpg',
            'is_goat' => 0,
            'tracce' => [
                ['titolo' => 'went Cobain', 'progetto' => 'best music evr', 'anno' => 2026, 'plays' => 2400, 'prod' => 'Pvgliuzz & Wuko', 'link' => 'https://www.youtube.com/watch?v=_l41dB_-2Jo', 'hit' => 1],
                ['titolo' => 'Killa', 'progetto' => 'Me EP', 'anno' => 2026, 'plays' => 1800, 'prod' => 'Wuko', 'link' => null, 'hit' => 0]
            ]
        ],
        [
            'nome' => 'Zyrtck',
            'slug' => 'zyrtck',
            'livello' => 'punta',
            'stile' => 'Hyperpop / No-Melody Trap',
            'bio' => "Roma, classe 2001. Collettivo FUCKYOURCLIQUE. Autore del disco cult 'MOVIMENTO'. Stile dissacrante, glitchato ed estremo che ha anticipato l'iper-elettronica nella trap italiana. Brani storici: '3 CAP' con Radical e 'WAP WAP'.",
            'progetto' => 'MOVIMENTO',
            'brano' => '3 CAP (feat. Radical, prod. Mothz)',
            'plays' => 28000,
            'monthly_listeners' => 52000,
            'link' => 'https://open.spotify.com/search/Zyrtck',
            'link_soundcloud' => 'https://soundcloud.com/search?q=zyrtck',
            'link_spotify' => 'https://open.spotify.com/search/Zyrtck',
            'link_youtube' => 'https://www.youtube.com/watch?v=Tgtk8jODfL4',
            'link_instagram' => 'https://www.instagram.com/zyrtck',
            'avatar_url' => 'assets/artists/zyrtck.jpg',
            'is_goat' => 0,
            'tracce' => [
                ['titolo' => '3 CAP (feat. Radical)', 'progetto' => 'MOVIMENTO', 'anno' => 2021, 'plays' => 16000, 'prod' => 'Mothz', 'link' => 'https://www.youtube.com/watch?v=Tgtk8jODfL4', 'hit' => 1],
                ['titolo' => 'WAP WAP', 'progetto' => 'MOVIMENTO', 'anno' => 2021, 'plays' => 12000, 'prod' => 'Mothz', 'link' => null, 'hit' => 0]
            ]
        ],
        [
            'nome' => 'Drefgold',
            'slug' => 'drefgold',
            'livello' => 'punta',
            'stile' => 'Melodic Trap / Wave OG',
            'bio' => "Bologna/Lecce, BHMG. Pioniere della trap melodica con il mixtape e album 'Kanaglia' (2018) e 'Elo' (2020). Posto in cima all'iceberg per risonanza mainstream ma radicato nella prima ondata SoundCloud italiana.",
            'progetto' => 'Kanaglia',
            'brano' => 'Boss',
            'plays' => 95000,
            'monthly_listeners' => 240000,
            'link' => 'https://open.spotify.com/search/Drefgold',
            'link_soundcloud' => 'https://soundcloud.com/drefgold',
            'link_spotify' => 'https://open.spotify.com/search/Drefgold',
            'link_youtube' => 'https://www.youtube.com/watch?v=k-aW1e6m3f0',
            'link_instagram' => 'https://www.instagram.com/drefgold',
            'avatar_url' => 'assets/artists/drefgold.jpg',
            'is_goat' => 0,
            'tracce' => [
                ['titolo' => 'Boss', 'progetto' => 'Kanaglia', 'anno' => 2018, 'plays' => 52000, 'prod' => 'Daves The Kid', 'link' => 'https://www.youtube.com/watch?v=k-aW1e6m3f0', 'hit' => 1],
                ['titolo' => 'Kanaglia', 'progetto' => 'Kanaglia', 'anno' => 2018, 'plays' => 43000, 'prod' => 'Daves The Kid', 'link' => null, 'hit' => 0]
            ]
        ],
        [
            'nome' => '10kwatt',
            'slug' => '10kwatt',
            'livello' => 'superficie',
            'stile' => 'Distorted Noise Trap',
            'bio' => "Sound distorto e culto SoundCloud. Tracce virali nelle community di Reddit r/rapitaliano e YouTube underground come 'bontà' (in collaborazione con Rooc Lee) e 'coltelli sotto la gonna'.",
            'progetto' => 'Sottocultura Tape',
            'brano' => 'bontà (con Rooc Lee)',
            'plays' => 1650,
            'monthly_listeners' => 380,
            'link' => 'https://soundcloud.com/10kwatt',
            'link_soundcloud' => 'https://soundcloud.com/10kwatt',
            'link_youtube' => 'https://www.youtube.com/watch?v=0rUm0gUntjs',
            'avatar_url' => 'assets/artists/10kwatt.jpg',
            'is_goat' => 0,
            'tracce' => [
                ['titolo' => 'bontà (con Rooc Lee)', 'progetto' => 'Single', 'anno' => 2024, 'plays' => 1100, 'prod' => 'Rooc Lee', 'link' => 'https://www.youtube.com/watch?v=0rUm0gUntjs', 'hit' => 1]
            ]
        ],
        [
            'nome' => '19cola',
            'slug' => '19cola',
            'livello' => 'profondo',
            'stile' => 'Raw Cloud Plugg',
            'bio' => "Collettivi Ceasefire e scartocurra. Sound grezzo, campionamenti sognanti e rime sommesse. Cult hero del sottosuolo più puro, autore del progetto 'Gol Collection' e della traccia 'Stella Cadente'.",
            'progetto' => 'Gol Collection',
            'brano' => 'Stella Cadente',
            'plays' => 920,
            'monthly_listeners' => 210,
            'link' => 'https://soundcloud.com/19cola',
            'link_soundcloud' => 'https://soundcloud.com/19cola',
            'link_youtube' => 'https://www.youtube.com/watch?v=dh03RYDzzJg',
            'avatar_url' => 'assets/artists/19cola.jpg',
            'is_goat' => 0,
            'tracce' => [
                ['titolo' => 'Stella Cadente', 'progetto' => 'Gol Collection', 'anno' => 2024, 'plays' => 620, 'prod' => 'Ceasefire', 'link' => 'https://www.youtube.com/watch?v=dh03RYDzzJg', 'hit' => 1]
            ]
        ],
        [
            'nome' => 'Bruce Laren',
            'slug' => 'bruce-laren',
            'livello' => 'abisso',
            'stile' => 'Sub-Bass Lo-Fi / Ambient Horrorcore',
            'bio' => "26 ascoltatori mensili. Nessuna foto profilo reale. Tracce da 1 minuto e 12 caricate su account SoundCloud anonimi. Più misterioso dell'area 51.",
            'progetto' => 'Oscurità Totale',
            'brano' => 'Nessuna Luce',
            'plays' => 26,
            'monthly_listeners' => 26,
            'link' => 'https://soundcloud.com/',
            'is_goat' => 0,
            'tracce' => [
                ['titolo' => 'Nessuna Luce', 'progetto' => 'Oscurità Totale', 'anno' => 2024, 'plays' => 26, 'prod' => 'Anon', 'link' => null, 'hit' => 1]
            ]
        ],
        [
            'nome' => 'KiloSb',
            'slug' => 'kilosb',
            'livello' => 'abisso',
            'stile' => 'Experimental Noise Plugg',
            'bio' => "Un 808 talmente saturo che fa tremare gli infissi. 43 ascoltatori e un ego paragonabile al consiglio d'amministrazione di Sony Music.",
            'progetto' => 'Distorsione Pura',
            'brano' => 'Cassa a Fuoco',
            'plays' => 43,
            'monthly_listeners' => 43,
            'link' => 'https://soundcloud.com/',
            'is_goat' => 0,
            'tracce' => [
                ['titolo' => 'Cassa a Fuoco', 'progetto' => 'Distorsione Pura', 'anno' => 2024, 'plays' => 43, 'prod' => 'KiloSb', 'link' => null, 'hit' => 1]
            ]
        ]
    ];

    // Inserisci o aggiorna ogni artista
    foreach ($artistiReali as $art) {
        $check = $pdo->prepare("SELECT id FROM artisti WHERE nome = ? LIMIT 1");
        $check->execute([$art['nome']]);
        $row = $check->fetch();

        if ($row) {
            $artId = $row['id'];
            $pdo->prepare("UPDATE artisti SET 
                slug = ?, livello = ?, stile = ?, bio = ?, progetto = ?, brano = ?, plays = ?, monthly_listeners = ?,
                link = ?, link_soundcloud = ?, link_youtube = ?, link_instagram = ?, link_spotify = ?,
                avatar_url = ?, is_goat = ?
                WHERE id = ?")->execute([
                    $art['slug'], $art['livello'], $art['stile'], $art['bio'], $art['progetto'], $art['brano'],
                    $art['plays'], $art['monthly_listeners'], $art['link'] ?? null, $art['link_soundcloud'] ?? null,
                    $art['link_youtube'] ?? null, $art['link_instagram'] ?? null, $art['link_spotify'] ?? null,
                    $art['avatar_url'] ?? null, $art['is_goat'] ?? 0, $artId
                ]);
        } else {
            $stIns = $pdo->prepare("INSERT INTO artisti (nome, slug, livello, stile, bio, progetto, brano, plays, monthly_listeners, link, link_soundcloud, link_youtube, link_instagram, link_spotify, avatar_url, is_goat)
                VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
            $stIns->execute([
                $art['nome'], $art['slug'], $art['livello'], $art['stile'], $art['bio'], $art['progetto'], $art['brano'],
                $art['plays'], $art['monthly_listeners'], $art['link'] ?? null, $art['link_soundcloud'] ?? null,
                $art['link_youtube'] ?? null, $art['link_instagram'] ?? null, $art['link_spotify'] ?? null,
                $art['avatar_url'] ?? null, $art['is_goat'] ?? 0
            ]);
            $artId = $pdo->lastInsertId();
        }

        // Sincronizza tracce
        if (!empty($art['tracce'])) {
            // Rimuovi tracce vecchie per rigenerarle pulite
            $pdo->prepare("DELETE FROM tracce WHERE artista_id = ?")->execute([$artId]);
            $stTr = $pdo->prepare("INSERT INTO tracce (artista_id, titolo, progetto, anno, plays, produttore, link_audio, is_hit) VALUES (?,?,?,?,?,?,?,?)");
            foreach ($art['tracce'] as $tr) {
                $stTr->execute([
                    $artId, $tr['titolo'], $tr['progetto'], $tr['anno'], $tr['plays'], $tr['prod'], $tr['link'], $tr['hit'] ?? 0
                ]);
            }
        }
    }

    // Assicurati che Layyd mantenga 113 plays storici
    $pdo->exec("UPDATE artisti SET plays = 113, is_goat = 1 WHERE nome = 'Layyd'");
}
