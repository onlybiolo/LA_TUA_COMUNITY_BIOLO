<!-- TABELLA ARTISTA
id Primary 	int 			No 	None 		AUTO_INCREMENT 	Change Change 	Drop Drop 	
	2 	nome_arte 	varchar(100) 	utf8mb4_0900_ai_ci 		No 	None 			Change Change 	Drop Drop 	
	3 	nome 	varchar(100) 	utf8mb4_0900_ai_ci 		Yes 	NULL 			Change Change 	Drop Drop 	
	4 	cognome 	varchar(100) 	utf8mb4_0900_ai_ci 		Yes 	NULL 			Change Change 	Drop Drop 	
	5 	anno_nascita 	smallint 		UNSIGNED 	Yes 	NULL 			Change Change 	Drop Drop 	
	6 	anno_debutto 	smallint 		UNSIGNED 	Yes 	NULL 			Change Change 	Drop Drop 	
	7 	provenienza 	varchar(100) 	utf8mb4_0900_ai_ci 		Yes 	NULL 			Change Change 	Drop Drop 	
	8 	bio 	text 	utf8mb4_0900_ai_ci 		Yes 	NULL 			Change Change 	Drop Drop 	
	9 	link_foto 	varchar(255) 	utf8mb4_0900_ai_ci 		Yes 	NULL 			Change Change 	Drop Drop 	
	10 	data_inserimento 	timestamp 			Yes 	CURRENT_TIMESTAMP 		DEFAULT_GENERATED 	Change Change 	Drop Drop 	

-->
<?php
    require_once 'Artista.php';
    $artistaRepo = new Artista();
    $artisti = $artistaRepo->getArtisti();
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SoundCloud Trappers Underground Cave</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header class="topbar">
            <div>
                <h1>SoundCloud Trappers Underground Cave</h1>
                <h2>Tutti gli artisti presenti nella nostra community</h2>
            </div>
            <button onclick="window.location.href='aggiungi_artista.php'">Aggiungi un nuovo artista</button>
        </header>

        <ul class="grid">
            <?php foreach ($artisti as $artista): ?>
                <li class="card">
                    <div class="card-img">
                        <img src="<?php echo htmlspecialchars($artista->link_foto ?? ''); ?>" alt="Foto di <?php echo htmlspecialchars($artista->nome_arte ?? ''); ?>">
                    </div>

                    <div class="card-head">
                        <h3><?php echo htmlspecialchars($artista->nome_arte ?? ''); ?></h3>
                        <span class="origin"><?php echo htmlspecialchars($artista->provenienza ?: '—'); ?></span>
                    </div>

                    <div class="meta-grid">
                        <div class="meta-cell">
                            <span>Nome</span>
                            <strong><?php echo htmlspecialchars(trim(($artista->nome ?? '') . ' ' . ($artista->cognome ?? '')) ?: '—'); ?></strong>
                        </div>
                        <div class="meta-cell">
                            <span>Nato</span>
                            <strong><?php echo htmlspecialchars($artista->anno_nascita ?: '—'); ?></strong>
                        </div>
                        <div class="meta-cell">
                            <span>Debutto</span>
                            <strong><?php echo htmlspecialchars($artista->anno_debutto ?: '—'); ?></strong>
                        </div>
                    </div>

                    <p class="bio"><?php echo nl2br(htmlspecialchars($artista->bio ?? '')); ?></p>

                    <div class="card-foot">
                        <?php echo htmlspecialchars($artista->data_inserimento ?? ''); ?>
                    </div>
                    <div class="update-buttons">
                        <button class="update-button" onclick="window.location.href='modifica_artista.php?id=<?php echo $artista->id; ?>'">Modifica</button>
                        <button class="delete-button" onclick="if(confirm('Sei sicuro di voler eliminare questo artista?')) { window.location.href='elimina_artista.php?id=<?php echo $artista->id; ?>'; }">Elimina</button>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</body>
</html>