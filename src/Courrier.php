<?php
class Courrier
{
    //DB require stuff
    private $conn;
    private $table = 'courier';

    //Properties for courriers
    public $courrierId;
    public $site;
    public $type;
    public $ref;
    public $obj;
    public $source;
    public $desti;
    public $date;
    public $heure;
    public $niveau;
    public $status;

    public function __construct(Database $connection)
    {
        $this->conn = $connection->connect();
    }

    // Display all available courriers
    public function getAll(): array
    {
        $query = 'SELECT * FROM ' . $this->table;
        $stmt = $this->conn->query($query);

        $data = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            array_push($data, $row);
        }
        return $data;
    }

    // Display a particular courrier
    public function get($courrierId): array | false
    {
        $query = "SELECT * FROM {$this->table} WHERE NEng = :courrierId";
        $stmt = $this->conn->prepare($query);

        // bind parameters
        $stmt->bindParam(':courrierId', $courrierId, PDO::PARAM_INT);

        $stmt->execute();

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data !== false) {
            return $data;
        }
        return false;
    }

    // search by name
    // public function getByName($name): array | false
    // {
    //     $query = "SELECT * FROM {$this->table} WHERE nom = :name";
    //     $stmt = $this->conn->prepare($query);

    //     //data 
    //     $this->nom = htmlspecialchars(strip_tags($name));

    //     // bind parameter
    //     $stmt->bindParam(':name', $this->nom, PDO::PARAM_STR);

    //     $stmt->execute();

    //     $data = $stmt->fetch(PDO::FETCH_ASSOC);

    //     if ($data !== false) {
    //         return $data;
    //     }
    //     return false;
    // }

    // Create a new courrier
    public function create(array $data): string | false
    {
        $query = "INSERT INTO {$this->table} 
                    SET 
                        Site = :site, 
                        InOutCourier = :type, 
                        ReferenceCourier = :ref, 
                        ObjetCourier = :obj, 
                        SourceCourier = :source, 
                        Destinataire = :desti, 
                        DateDepot = :date, 
                        HeureDepot = :heure, 
                        NiveauImportance = :niveau
                        -- Statut = :statut
                ";
        $stmt = $this->conn->prepare($query);

        //data
        $this->site = htmlspecialchars(strip_tags($data['site']));
        $this->type = htmlspecialchars(strip_tags($data['type']));
        $this->ref = htmlspecialchars(strip_tags($data['reference']));
        $this->obj = htmlspecialchars(strip_tags($data['objet']));
        $this->source = htmlspecialchars(strip_tags($data['source']));
        $this->desti = htmlspecialchars(strip_tags($data['destinataire']));
        $this->niveau = htmlspecialchars(strip_tags($data['niveau']));
        // $this->status = htmlspecialchars(strip_tags($data['status']));
        $this->date = htmlspecialchars(strip_tags($data['date'] ?? date('Y-m-d')));
        $this->heure = htmlspecialchars(strip_tags($data['heure'] ?? date('H:i:s', time() + 1 * 60 * 60)));

        //Bind data
        $stmt->bindParam(':site', $this->site, PDO::PARAM_STR);
        $stmt->bindParam(':type', $this->type, PDO::PARAM_STR);
        $stmt->bindParam(':ref', $this->ref, PDO::PARAM_STR);
        $stmt->bindParam(':obj', $this->obj, PDO::PARAM_STR);
        $stmt->bindParam(':source', $this->source, PDO::PARAM_STR);
        $stmt->bindParam(':desti', $this->desti, PDO::PARAM_STR);
        $stmt->bindParam(':date', $this->date, PDO::PARAM_STR);
        $stmt->bindParam(':heure', $this->heure, PDO::PARAM_STR);
        $stmt->bindParam(':niveau', $this->niveau, PDO::PARAM_STR);
        // $stmt->bindParam(':status', $this->status, PDO::PARAM_STR);

        //Execute statement
        $stmt->execute();

        return $this->conn->lastInsertId();
    }

    // Update a courrier
    public function update(array $current, array $new_data): int
    {
        $query = "UPDATE {$this->table} 
        SET 
            Site = :site, 
            InOutCourier = :type, 
            ReferenceCourier = :ref, 
            ObjetCourier = :obj, 
            SourceCourier = :source, 
            Destinataire = :desti, 
            DateDepot = :date, 
            HeureDepot = :heure, 
            NiveauImportance = :niveau
            -- Statut = :statut
        WHERE NEng = :courrierId";
        $stmt = $this->conn->prepare($query);

        //data
        $this->site = htmlspecialchars(strip_tags($new_data['site'] ?? $current['Site']));
        $this->type = htmlspecialchars(strip_tags($new_data['type'] ?? $current['InOutCourier']));
        $this->ref = htmlspecialchars(strip_tags($new_data['reference'] ?? $current['ReferenceCourier']));
        $this->obj = htmlspecialchars(strip_tags($new_data['objet'] ?? $current['ObjetCourier']));
        $this->source = htmlspecialchars(strip_tags($new_data['source'] ?? $current['SourceCourier']));
        $this->desti = htmlspecialchars(strip_tags($new_data['destinataire'] ?? $current['Destinataire']));
        $this->date = htmlspecialchars(strip_tags($new_data['date'] ?? $current['DateDepot']));
        $this->heure = htmlspecialchars(strip_tags($new_data['heure'] ?? $current['HeureDepot']));
        $this->niveau = htmlspecialchars(strip_tags($new_data['niveau'] ?? $current['NiveauImportance']));
        $this->courrierId = htmlspecialchars(strip_tags($current["NEng"]));

        //Bind data
        $stmt->bindParam(':site', $this->site, PDO::PARAM_STR);
        $stmt->bindParam(':type', $this->type, PDO::PARAM_STR);
        $stmt->bindParam(':ref', $this->ref, PDO::PARAM_STR);
        $stmt->bindParam(':obj', $this->obj, PDO::PARAM_STR);
        $stmt->bindParam(':source', $this->source, PDO::PARAM_STR);
        $stmt->bindParam(':desti', $this->desti, PDO::PARAM_STR);
        $stmt->bindParam(':date', $this->date, PDO::PARAM_STR);
        $stmt->bindParam(':heure', $this->heure, PDO::PARAM_STR);
        $stmt->bindParam(':niveau', $this->niveau, PDO::PARAM_STR);
        $stmt->bindParam(':courrierId', $this->courrierId, PDO::PARAM_INT);

        //Execute statement
        $stmt->execute();

        return $stmt->rowCount();
    }

    // Delete a courrier
    public function delete($id): int
    {
        $query = "DELETE FROM {$this->table} WHERE NEng = :courrierId";

        $this->courrierId = htmlspecialchars(strip_tags($id));

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':courrierId', $this->courrierId, PDO::PARAM_INT);

        //Execute statement
        $stmt->execute();

        return $stmt->rowCount();
    }
}
