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

    /**
     * Constructor for the class.
     *
     * @param Database $connection The database connection object.
     */
    public function __construct(Database $connection)
    {
        $this->conn = $connection->connect();
    }

    /**
     * Retrieves all available couriers from the database.
     *
     * @return array An array of couriers.
     */
    public function getAll(): array
    {
        // Build the SQL query to select all rows from the table
        $query = 'SELECT * FROM ' . $this->table;

        // Execute the query and retrieve the result set
        $stmt = $this->conn->query($query);

        // Initialize an empty array to store the retrieved data
        $data = [];

        // Loop through each row in the result set
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // Add the row to the data array
            array_push($data, $row);
        }

        // Return the data array
        return $data;
    }

    /**
     * Retrieve a specific courier from the database.
     *
     * @param int $courierId The ID of the courier to retrieve.
     *
     * @return array|false The data of the courier if found, false otherwise.
     */
    public function get($courierId): array | false
    {
        // Prepare the SQL query
        $query = "SELECT * FROM {$this->table} WHERE NEng = :courierId";

        // Prepare the statement
        $stmt = $this->conn->prepare($query);

        // Bind the parameters
        $stmt->bindParam(':courierId', $courierId, PDO::PARAM_INT);

        // Execute the statement
        $stmt->execute();

        // Fetch the data
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        // Check if data exists
        if ($data !== false) {
            return $data;
        }

        return false;
    }

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
                        NiveauImportance = :niveau,
                        Indexe = :indexe,
                        IdDate = :iddate,
                        IDHeureDepot = :idheure,
                        DateTime = :datetime,
                        IDDateTime = :iddatetime,
                        CodeAgence = :codeagence,
                        Synchronization = :synchronization,
                        LastUpDateTime = :lastupdatetime,
                        IDLastUpDate = :idlastupdate,
                        UserDateTime = :userdatetime
                        -- Statut = :status
                ";
        $stmt = $this->conn->prepare($query);



        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $login = $_SESSION["login"] ?? "NONO";

        $setting_query = "SELECT Site FROM utilisateurs WHERE nom = '{$login}'";
        $setting_stmt = $this->conn->query($setting_query);
        $site = $setting_stmt->fetch(PDO::FETCH_ASSOC);

        $code_agence_query = "SELECT `CodeAgence` FROM settings WHERE `Site` = '{$site['Site']}'";
        $code_agence_stmt = $this->conn->query($code_agence_query);
        $codeAgence = $code_agence_stmt->fetch(PDO::FETCH_ASSOC);

        //data
        $this->type = htmlspecialchars(strip_tags($data['InOutCourier']));
        $this->ref = htmlspecialchars(strip_tags($data['ReferenceCourier']));
        $this->obj = htmlspecialchars(strip_tags($data['ObjetCourier']));
        $this->source = htmlspecialchars(strip_tags($data['SourceCourier']));
        $this->desti = htmlspecialchars(strip_tags($data['Destinataire']));
        $this->niveau = htmlspecialchars(strip_tags($data['NiveauImportance']));
        // $this->status = htmlspecialchars(strip_tags($data['status']));
        
        $this->date = htmlspecialchars(strip_tags($data['DateDepot'] ?? date('Y/m/d')));

        $this->heure = htmlspecialchars(strip_tags($data['HeureDepot'] ?? date('H:i')));

        return ($this->heure) ;

        $i = 0;
        $tmp = mt_rand(0, 9);
        do {
            $tmp .= mt_rand(0, 9);
        } while (++$i < 24);
        $uniqueId = $codeAgence["CodeAgence"] . $tmp;

        $iddate = strtotime($this->date);
        $idheure = strtotime($this->heure);
        $datetime = $this->date . " " . date('H:i:s', time() - 1 * 60 * 60);
        $iddatetime = strtotime($datetime);
        $txt = "New";
        $synchronization = "$txt;$codeAgence[CodeAgence];";
        $lastupdatetime = $datetime;
        $idlastupdate = $iddatetime;
        $userdatetime = $login;

        //Bind data
        $stmt->bindParam(':site', $site['Site'], PDO::PARAM_STR);
        $stmt->bindParam(':type', $this->type, PDO::PARAM_STR);
        $stmt->bindParam(':ref', $this->ref, PDO::PARAM_STR);
        $stmt->bindParam(':obj', $this->obj, PDO::PARAM_STR);
        $stmt->bindParam(':source', $this->source, PDO::PARAM_STR);
        $stmt->bindParam(':desti', $this->desti, PDO::PARAM_STR);
        $stmt->bindParam(':date', $this->date, PDO::PARAM_STR);
        $stmt->bindParam(':heure', $this->heure, PDO::PARAM_STR);
        $stmt->bindParam(':niveau', $this->niveau, PDO::PARAM_STR);
        $stmt->bindParam(':indexe', $uniqueId, PDO::PARAM_STR);
        $stmt->bindParam(':iddate', $iddate, PDO::PARAM_INT);
        $stmt->bindParam(':idheure', $idheure, PDO::PARAM_INT);
        $stmt->bindParam(':datetime', $datetime, PDO::PARAM_STR);
        $stmt->bindParam(':iddatetime', $iddatetime, PDO::PARAM_INT);
        $stmt->bindParam(':codeagence', $codeAgence["CodeAgence"], PDO::PARAM_STR);
        $stmt->bindParam(':synchronization', $synchronization, PDO::PARAM_STR);
        $stmt->bindParam(':lastupdatetime', $lastupdatetime, PDO::PARAM_STR);
        $stmt->bindParam(':idlastupdate', $idlastupdate, PDO::PARAM_INT);
        $stmt->bindParam(':userdatetime', $userdatetime, PDO::PARAM_STR);
        // $stmt->bindParam(':status', $this->status, PDO::PARAM_STR);

        //Execute statement
        $stmt->execute();

        return $this->conn->lastInsertId();
    }

    // Update a courrier
    public function update(array $current, array $new_data): int | bool
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
            NiveauImportance = :niveau,
            Synchronization = :synchronization,
            LastUpDateTime = :lastupdatetime,
            IDLastUpDate = :idlastupdate,
            UserLastUpDateTime = :userlastupdatetime
            -- Statut = :statut
        WHERE NEng = :courrierId";
        $stmt = $this->conn->prepare($query);

        //data
        $this->site = htmlspecialchars(strip_tags($new_data['Site'] ?? $current['Site']));
        $this->type = htmlspecialchars(strip_tags($new_data['InOutCourier'] ?? $current['InOutCourier']));
        $this->ref = htmlspecialchars(strip_tags($new_data['ReferenceCourier'] ?? $current['ReferenceCourier']));
        $this->obj = htmlspecialchars(strip_tags($new_data['ObjetCourier'] ?? $current['ObjetCourier']));
        $this->source = htmlspecialchars(strip_tags($new_data['SourceCourier'] ?? $current['SourceCourier']));
        $this->desti = htmlspecialchars(strip_tags($new_data['Destinataire'] ?? $current['Destinataire']));
        $this->date = htmlspecialchars(strip_tags($new_data['DateDepot'] ?? $current['DateDepot']));
        $this->heure = htmlspecialchars(strip_tags($new_data['HeureDepot'] ?? $current['HeureDepot']));
        $this->niveau = htmlspecialchars(strip_tags($new_data['NiveauImportance'] ?? $current['NiveauImportance']));
        $this->courrierId = htmlspecialchars(strip_tags($current["NEng"]));

        if ($current['Statut'] == 'Archivé') {
            return false;
        }

        $txt = "Modified";
        $synchronization = "$txt;$current[CodeAgence];";
        $lastupdatetime = date("Y/m/d H:i:s", time() - 1 * 60 * 60);
        $idlastupdate = strtotime($lastupdatetime);
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $login = $_SESSION["login"] ?? "NONO";
        $userlastupdatetime = $login;

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
        $stmt->bindParam(':synchronization', $synchronization, PDO::PARAM_STR);
        $stmt->bindParam(':lastupdatetime', $lastupdatetime, PDO::PARAM_STR);
        $stmt->bindParam(':idlastupdate', $idlastupdate, PDO::PARAM_INT);
        $stmt->bindParam(':userlastupdatetime', $userlastupdatetime, PDO::PARAM_STR);

        //Execute statement
        $stmt->execute();

        return $stmt->rowCount();
    }

    /**
     * Deletes a courier by ID.
     *
     * @param int $id The ID of the courier to delete.
     * @return int The number of rows affected by the delete operation.
     */
    public function delete($id): int | false
    {
        // Define the query to delete the courier by ID
        $query = "DELETE FROM {$this->table} WHERE NEng = :courierId";

        // Sanitize and assign the courier ID
        $courierId = htmlspecialchars(strip_tags($id));

        // Prepare the SQL statement
        $stmt = $this->conn->prepare($query);


        // make a backup in table `backupcourrier`
        $backup_query = "INSERT INTO backupcourier
                        SELECT *
                        FROM {$this->table} 
                        WHERE `NEng` = {$courierId}";
        $backup_stmt = $this->conn->prepare($backup_query);
        $backup_stmt->execute();

        $done = $backup_stmt->rowCount();

        if ($done != 1) {
            return false;
        }


        // Bind the courier ID parameter
        $stmt->bindParam(':courierId', $courierId, PDO::PARAM_INT);

        // Execute the SQL statement
        $stmt->execute();

        // Return the number of rows affected by the delete operation
        return $stmt->rowCount();
    }
}
