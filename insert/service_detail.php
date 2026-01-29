<?php
require_once 'includes/connection.php';

class ServiceDetail {
    private $pdo;
    
    public function __construct() {
        $this->pdo = getDBConnection();
    }
    
    /**
     * Get service basic information
     */
    public function getServiceInfo($service_id) {
        $stmt = $this->pdo->prepare("
            SELECT s.*, 
                   sc.sub_category_name,
                   scat.category_name
            FROM services s
            LEFT JOIN services_subcategories sc ON s.sub_category_id = sc.id
            LEFT JOIN services_categories scat ON s.category_id = scat.id
            WHERE s.id = ?
        ");
        $stmt->execute([$service_id]);
        return $stmt->fetch();
    }
    
    /**
     * Get service overview (for hero stats)
     */
    public function getServiceOverview($service_id) {
        $stmt = $this->pdo->prepare("
            SELECT * FROM service_overview 
            WHERE service_id = ? 
            ORDER BY id ASC
        ");
        $stmt->execute([$service_id]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get service documents (requirements)
     */
    public function getServiceDocuments($service_id) {
        $stmt = $this->pdo->prepare("
            SELECT * FROM service_documents 
            WHERE service_id = ? 
            ORDER BY id ASC
        ");
        $stmt->execute([$service_id]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get service features (deliverables)
     */
    public function getServiceFeatures($service_id) {
        $stmt = $this->pdo->prepare("
            SELECT * FROM service_features 
            WHERE service_id = ? 
            ORDER BY id ASC
        ");
        $stmt->execute([$service_id]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get service eligibility criteria (can be used for process flow)
     */
    public function getEligibilityCriteria($service_id) {
        $stmt = $this->pdo->prepare("
            SELECT * FROM service_eligibility_criteria 
            WHERE service_id = ? 
            ORDER BY id ASC
        ");
        $stmt->execute([$service_id]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get service fees and charges (for timeline/pricing info)
     */
    public function getFeesCharges($service_id) {
        $stmt = $this->pdo->prepare("
            SELECT * FROM service_fees_charges 
            WHERE service_id = ? 
            ORDER BY id ASC
        ");
        $stmt->execute([$service_id]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get service loan repayment options
     */
    public function getLoanRepayment($service_id) {
        $stmt = $this->pdo->prepare("
            SELECT * FROM service_loan_repayment 
            WHERE service_id = ? 
            ORDER BY id ASC
        ");
        $stmt->execute([$service_id]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get service banks (add-on services)
     */
    public function getServiceBanks($service_id) {
        $stmt = $this->pdo->prepare("
            SELECT * FROM service_banks 
            WHERE service_id = ? 
            ORDER BY id ASC
        ");
        $stmt->execute([$service_id]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get why choose us points
     */
    public function getWhyChooseUs($service_id) {
        $stmt = $this->pdo->prepare("
            SELECT * FROM service_why_choose_us 
            WHERE service_id = ? 
            ORDER BY id ASC
        ");
        $stmt->execute([$service_id]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get all service data at once
     */
    public function getAllServiceData($service_id) {
        return [
            'service' => $this->getServiceInfo($service_id),
            'overview' => $this->getServiceOverview($service_id),
            'documents' => $this->getServiceDocuments($service_id),
            'features' => $this->getServiceFeatures($service_id),
            'eligibility' => $this->getEligibilityCriteria($service_id),
            'fees' => $this->getFeesCharges($service_id),
            'repayment' => $this->getLoanRepayment($service_id),
            'banks' => $this->getServiceBanks($service_id),
            'why_choose' => $this->getWhyChooseUs($service_id)
        ];
    }
}

/**
 * Helper function to get icon class based on document name
 */
function getDocumentIcon($doc_name) {
    $icons = [
        'Identity Proof' => 'fa-id-card',
        'Address Proof' => 'fa-home',
        'Income Proof' => 'fa-rupee-sign',
        'Bank Statement' => 'fa-university',
        'Photograph' => 'fa-camera',
        'PAN Card' => 'fa-id-card',
        'Aadhaar Card' => 'fa-id-card-alt',
        'Business Proof' => 'fa-briefcase',
        'ITR' => 'fa-file-invoice',
        'Salary Slip' => 'fa-money-check',
    ];
    
    foreach ($icons as $key => $icon) {
        if (stripos($doc_name, $key) !== false) {
            return $icon;
        }
    }
    
    return 'fa-file-alt'; // Default icon
}

/**
 * Helper function to parse JSON keys and values from service_overview
 */
function parseOverviewData($overview_item) {
    $keys = json_decode($overview_item['keys'], true);
    $values = json_decode($overview_item['values'], true);
    
    if ($keys && $values && count($keys) === count($values)) {
        return array_combine($keys, $values);
    }
    
    return [];
}
?>