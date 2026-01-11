<?php

require_once 'core/Database.php';

class ParticipantModel {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function getAll() {
        $this->db->query("SELECT * FROM participants ORDER BY id DESC");
        return $this->db->resultSet();
    }

    public function getById($id) {
        $this->db->query("SELECT * FROM participants WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function create($data)
        {
            try {
                $this->db->query("INSERT INTO participants (name, age, gender, district, contact, age_group, weight_category, event_type)
                                  VALUES (:name, :age, :gender, :district, :contact, :age_group, :weight_category, :event_type)");
        
                $this->db->bind(':name', $data['name']);
                $this->db->bind(':age', $data['age']);
                $this->db->bind(':gender', $data['gender']);
                $this->db->bind(':district', $data['district']);
                $this->db->bind(':contact', $data['contact']);
                $this->db->bind(':age_group', $data['age_group']);
                $this->db->bind(':weight_category', $data['weight_category']);
                $this->db->bind(':event_type', $data['event_type']);
        
                return $this->db->execute();
        
            } catch (PDOException $e) {
                // 23000 = Integrity constraint violation (e.g., duplicate unique key)
                if ($e->getCode() === '23000') {
                    header("Location: https://mahathangta.in?error=participant_exists");
                    exit;
                }
        
                throw $e; // rethrow unexpected errors
            }
        }


    public function update($id, $data) {
        $this->db->query("UPDATE participants 
                          SET name = :name, age = :age, gender = :gender, district = :district, 
                              contact = :contact, age_group = :age_group, weight_category = :weight_category, event_type = :event_type 
                          WHERE id = :id");
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':age', $data['age']);
        $this->db->bind(':gender', $data['gender']);
        $this->db->bind(':district', $data['district']);
        $this->db->bind(':contact', $data['contact']);
        $this->db->bind(':age_group', $data['age_group']);
        $this->db->bind(':weight_category', $data['weight_category']);
        $this->db->bind(':event_type', $data['event_type']);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    public function delete($id) {
        $this->db->query("DELETE FROM participants WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    public function getFilteredParticipants($ageGroup, $weightCategory, $eventType, $gender, $district) {
        $query = "SELECT * FROM participants WHERE 1=1 ";
        
        if ($ageGroup) {
            $query .= "AND age_group = :age_group ";
        }
        if ($weightCategory) {
            $query .= "AND weight_category = :weight_category ";
        }
        if ($eventType) {
            $query .= "AND event_type = :event_type ";
        }
        if ($gender) {
            $query .= "AND gender = :gender ";
        }
        if ($district) {
            $query .= "AND district = :district ";
        }
    
        $this->db->query($query);
    
        if ($ageGroup) {
            $this->db->bind(':age_group', $ageGroup);
        }
        if ($weightCategory) {
            $this->db->bind(':weight_category', $weightCategory);
        }
        if ($eventType) {
            $this->db->bind(':event_type', $eventType);
        }
        if ($gender) {
            $this->db->bind(':gender', $gender);
        }
        if ($district) {
            $this->db->bind(':district', $district);
        }
    
        return $this->db->resultSet();
    }

    
}
