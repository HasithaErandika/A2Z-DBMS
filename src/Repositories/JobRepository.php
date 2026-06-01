<?php
// src/repositories/JobRepository.php

namespace App\Repositories;

use PDO;
use Exception;
use PDOException;

class JobRepository extends BaseRepository {
    protected $table = 'jobs';
    protected $primaryKey = 'job_id';

    public function getActiveJobsCount() {
        try {
            $stmt = $this->db->query("SELECT COUNT(*) FROM jobs WHERE completion < 1.0");
            return intval($stmt->fetchColumn());
        } catch (PDOException $e) {
            error_log("Error in getActiveJobsCount: " . $e->getMessage());
            return 0;
        }
    }

    public function getTodaysJobsCount() {
        try {
            $today = date('Y-m-d');
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM jobs WHERE DATE(date_started) = ?");
            $stmt->execute([$today]);
            return intval($stmt->fetchColumn());
        } catch (PDOException $e) {
            error_log("Error in getTodaysJobsCount: " . $e->getMessage());
            return 0;
        }
    }

    public function updateJobStatus($jobId, $newCompletion) {
        try {
            $stmt = $this->db->prepare("UPDATE jobs SET completion = ? WHERE job_id = ?");
            $stmt->execute([$newCompletion, $jobId]);
            return ['success' => true];
        } catch (PDOException $e) {
            error_log("Error in updateJobStatus: " . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function getJobCompletion($jobId) {
        try {
            $stmt = $this->db->prepare("SELECT completion FROM jobs WHERE job_id = ?");
            $stmt->execute([$jobId]);
            $val = $stmt->fetchColumn();
            return $val !== false ? floatval($val) : 0.0;
        } catch (PDOException $e) {
            error_log("Error in getJobCompletion: " . $e->getMessage());
            return 0.0;
        }
    }

    public function getInvoiceDetailsByJobId($jobId) {
        try {
            $stmt = $this->db->prepare("SELECT invoice_no, invoice_value, received_amount FROM invoice_data WHERE job_id = ? LIMIT 1");
            $stmt->execute([$jobId]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (PDOException $e) {
            error_log("Error in getInvoiceDetailsByJobId: " . $e->getMessage());
            return null;
        }
    }

    public function calculateTotalJobCapacity() {
        try {
            $stmt = $this->db->query("SELECT SUM(capacity) FROM jobs");
            return floatval($stmt->fetchColumn());
        } catch (PDOException $e) {
            error_log("Error in calculateTotalJobCapacity: " . $e->getMessage());
            return 0.0;
        }
    }

    public function getJobDetails($jobId = null) {
        if (!empty($jobId)) {
            try {
                $stmt = $this->db->prepare("
                    SELECT j.job_id, j.engineer, j.customer_reference, p.project_description, p.company_reference
                    FROM jobs j
                    LEFT JOIN projects p ON j.project_id = p.project_id
                    WHERE j.job_id = ?
                ");
                $stmt->execute([$jobId]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                if (!$result) return 'Job Not Found';
                return sprintf(
                    '%s - %s - %s',
                    htmlspecialchars($result['project_description'] ?? 'No Description'),
                    htmlspecialchars($result['company_reference'] ?? 'No Company'),
                    htmlspecialchars($result['customer_reference'] ?? 'No Customer Ref')
                );
            } catch (PDOException $e) {
                error_log("Error in getJobDetails (details): " . $e->getMessage());
                return 'Error fetching job details';
            }
        }
        try {
            $stmt = $this->db->query("
                SELECT j.job_id, j.engineer, j.customer_reference, p.project_description, p.company_reference
                FROM jobs j
                LEFT JOIN projects p ON j.project_id = p.project_id
                ORDER BY j.job_id DESC
            ");
            $options = ['<option value="">Select Job</option>'];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $options[] = sprintf(
                    '<option value="%s">(%s - %s - %s - %s - %s)</option>',
                    htmlspecialchars($row['job_id']),
                    htmlspecialchars($row['job_id']),
                    htmlspecialchars($row['project_description'] ?? 'No Description'),
                    htmlspecialchars($row['company_reference'] ?? 'No Company'),
                    htmlspecialchars($row['customer_reference'] ?? 'No Customer Ref'),
                    htmlspecialchars($row['engineer'] ?? 'No Engineer')
                );
            }
            return implode('', $options);
        } catch (PDOException $e) {
            error_log("Error in getJobDetails (options): " . $e->getMessage());
            return '<option value="">Error loading jobs</option>';
        }
    }
}
