<?php
// src/models/RecordManager.php

class RecordManager {
    private $db;

    public function __construct() {
        try {
            $this->db = new PDO("mysql:host=localhost;dbname=operational_db", "root", "");
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            throw new Exception("Database connection failed: " . $e->getMessage());
        }
    }

    public function calculateSiteCosts() {
        try {
            $query = "
                SELECT 
                    j.location AS site_name,
                    COUNT(j.job_id) AS total_jobs,
                    SUM(e.amount) AS total_cost,
                    AVG(e.amount) AS avg_cost_per_job
                FROM jobs j
                LEFT JOIN operational_expenses e ON j.job_id = e.job_id
                WHERE e.amount IS NOT NULL
                GROUP BY j.location
                ORDER BY total_cost DESC
            ";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error calculating site costs: " . $e->getMessage());
        }
    }
}