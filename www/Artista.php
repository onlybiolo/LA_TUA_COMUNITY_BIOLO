<?php
    //classe Artista.php cosi non devo scrivere sempre le stesse cose
    class Artista {
        public $id;
        public $nome_arte;
        public $nome;
        public $cognome;
        public $anno_nascita;
        public $anno_debutto;
        public $provenienza;
        public $bio;
        public $link_foto;
        public $data_inserimento;

        private $host = 'db';
        private $db = 'comunity';
        private $user = 'user';
        private $pass = 'password';

        private function connect() {
            return new PDO(
                "mysql:host={$this->host};dbname={$this->db};charset=utf8mb4",
                $this->user,
                $this->pass,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
        }

        public function getArtisti() {
            try {
                $pdo = $this->connect();
                $stmt = $pdo->query("SELECT * FROM artista");
                return $stmt->fetchAll(PDO::FETCH_CLASS, 'Artista');
            } catch (Exception $e) {
                echo "Errore di connessione: " . $e->getMessage();
                return [];
            }
        }

        public function getArtistaById($id) {
            try {
                $pdo = $this->connect();
                $stmt = $pdo->prepare("SELECT * FROM artista WHERE id = :id");
                $stmt->execute([':id' => $id]);
                $stmt->setFetchMode(PDO::FETCH_CLASS, 'Artista');
                return $stmt->fetch() ?: null;
            } catch (Exception $e) {
                echo "Errore di connessione: " . $e->getMessage();
                return null;
            }
        }

        public function addArtista(Artista $artista) {
            try {
                $pdo = $this->connect();
                $sql = "INSERT INTO artista (nome_arte, nome, cognome, anno_nascita, anno_debutto, provenienza, bio, link_foto) 
                        VALUES (:nome_arte, :nome, :cognome, :anno_nascita, :anno_debutto, :provenienza, :bio, :link_foto)";
                $stmt = $pdo->prepare($sql);
                return $stmt->execute([
                    ':nome_arte' => $artista->nome_arte,
                    ':nome' => $artista->nome,
                    ':cognome' => $artista->cognome,
                    ':anno_nascita' => $artista->anno_nascita,
                    ':anno_debutto' => $artista->anno_debutto,
                    ':provenienza' => $artista->provenienza,
                    ':bio' => $artista->bio,
                    ':link_foto' => $artista->link_foto
                ]);
            } catch (Exception $e) {
                echo "Errore di connessione: " . $e->getMessage();
                return false;
            }
        }

        public function updateArtista(Artista $artista) {
            try {
                $pdo = $this->connect();
                $sql = "UPDATE artista 
                        SET nome_arte = :nome_arte, nome = :nome, cognome = :cognome, anno_nascita = :anno_nascita, 
                            anno_debutto = :anno_debutto, provenienza = :provenienza, bio = :bio, link_foto = :link_foto 
                        WHERE id = :id";
                $stmt = $pdo->prepare($sql);
                return $stmt->execute([
                    ':id' => $artista->id,
                    ':nome_arte' => $artista->nome_arte,
                    ':nome' => $artista->nome,
                    ':cognome' => $artista->cognome,
                    ':anno_nascita' => $artista->anno_nascita,
                    ':anno_debutto' => $artista->anno_debutto,
                    ':provenienza' => $artista->provenienza,
                    ':bio' => $artista->bio,
                    ':link_foto' => $artista->link_foto
                ]);
            } catch (Exception $e) {
                echo "Errore di connessione: " . $e->getMessage();
                return false;
            }
        }

        public function deleteArtista($id) {
            try {
                $pdo = $this->connect();
                $stmt = $pdo->prepare("DELETE FROM artista WHERE id = :id");
                return $stmt->execute([':id' => $id]);
            } catch (Exception $e) {
                echo "Errore di connessione: " . $e->getMessage();
                return false;
            }
        }
    }
?>