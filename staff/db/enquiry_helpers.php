<?php
function staffHasAccess($conn, $perm_key, $staff_id) {
    $staff_id = (int)$staff_id;
    $perm_key = mysqli_real_escape_string($conn, $perm_key);
    $query = "
        SELECT p.id FROM permissions p 
        INNER JOIN role_permissions rp ON p.id = rp.permission_id 
        INNER JOIN staff s ON s.role_id = rp.role_id
        WHERE s.id = $staff_id AND p.perm_key = '$perm_key'
        UNION
        SELECT p.id FROM permissions p
        INNER JOIN staff_permissions sp ON p.id = sp.permission_id
        WHERE sp.staff_id = $staff_id AND p.perm_key = '$perm_key'
    ";
    $result = mysqli_query($conn, $query);
    return (mysqli_num_rows($result) > 0);
}

function staffCanAccessEnquiry($conn, $staff_id, $enquiry_id) {
    $staff_id = (int)$staff_id;
    $enquiry_id = (int)$enquiry_id;
    if (staffHasAccess($conn, 'enquiry_view_all', $staff_id)) {
        return true;
    }
    if (staffHasAccess($conn, 'enquiry_view_assigned', $staff_id)) {
        $res = mysqli_query($conn, "SELECT id FROM enquiries WHERE id = $enquiry_id AND assigned_staff_id = $staff_id LIMIT 1");
        return ($res && mysqli_num_rows($res) > 0);
    }
    return false;
}
?>
