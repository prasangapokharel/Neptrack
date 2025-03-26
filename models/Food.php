<?php
class Food {
    private $conn;
    public $id;
    public $user_id;
    public $food_name;
    public $protein_grams;
    public $date;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Create food entry
    public function create() {
        $query = "INSERT INTO food_entries (user_id, food_name, protein_grams, date) VALUES (:user_id, :food_name, :protein_grams, :date)";
        $stmt = $this->conn->prepare($query);

        // Clean data
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        $this->food_name = htmlspecialchars(strip_tags($this->food_name));
        $this->protein_grams = htmlspecialchars(strip_tags($this->protein_grams));
        $this->date = htmlspecialchars(strip_tags($this->date));

        // Bind data
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':food_name', $this->food_name);
        $stmt->bindParam(':protein_grams', $this->protein_grams);
        $stmt->bindParam(':date', $this->date);

        // Execute query
        if($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Get today's food entries
    public function getTodayEntries() {
        $query = "SELECT * FROM food_entries WHERE user_id = :user_id AND date = :date ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':date', $this->date);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get entries by date
    public function getEntriesByDate() {
        $query = "SELECT * FROM food_entries WHERE user_id = :user_id AND date = :date ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':date', $this->date);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Delete food entry
    public function delete() {
        $query = "DELETE FROM food_entries WHERE id = :id AND user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        
        // Clean data
        $this->id = htmlspecialchars(strip_tags($this->id));
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        
        // Bind data
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':user_id', $this->user_id);
        
        // Execute query
        if($stmt->execute()) {
            return true;
        }
        
        return false;
    }

    // Get average daily protein
    public function getAverageDailyProtein() {
        $query = "SELECT AVG(daily_total) as avg_daily FROM (
                    SELECT date, SUM(protein_grams) as daily_total 
                    FROM food_entries 
                    WHERE user_id = :user_id 
                    GROUP BY date
                  ) as daily_totals";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['avg_daily'] ? round($row['avg_daily']) : 0;
    }

    // Get highest protein day
    public function getHighestProteinDay() {
        $query = "SELECT date, SUM(protein_grams) as protein_grams 
                  FROM food_entries 
                  WHERE user_id = :user_id 
                  GROUP BY date 
                  ORDER BY protein_grams DESC 
                  LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $row : ['date' => '-', 'protein_grams' => 0];
    }

    // Get total entries count
    public function getTotalEntriesCount() {
        $query = "SELECT COUNT(*) as count FROM food_entries WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['count'];
    }
}
?>

