-- Run this in phpMyAdmin to enable manual loan assign permission key for staff.
-- Admins can already assign, this permission controls which staff are eligible for assignment workflows.

INSERT INTO `permissions` (`perm_key`, `description`)
SELECT 'loan_manual_assign', 'Manually assign loans to staff'
WHERE NOT EXISTS (
    SELECT 1 FROM `permissions` WHERE `perm_key` = 'loan_manual_assign'
);

-- Optional backfill:
-- If role 1 currently has loan_process, also grant loan_manual_assign to role 1.
INSERT INTO `role_permissions` (`role_id`, `permission_id`)
SELECT 1, p_new.id
FROM `permissions` p_new
WHERE p_new.perm_key = 'loan_manual_assign'
  AND EXISTS (
      SELECT 1
      FROM `role_permissions` rp
      INNER JOIN `permissions` p_old ON p_old.id = rp.permission_id
      WHERE rp.role_id = 1 AND p_old.perm_key = 'loan_process'
  )
  AND NOT EXISTS (
      SELECT 1
      FROM `role_permissions` rp2
      WHERE rp2.role_id = 1 AND rp2.permission_id = p_new.id
  );

-- Optional backfill:
-- Any staff with individual loan_process permission also gets loan_manual_assign.
INSERT INTO `staff_permissions` (`staff_id`, `permission_id`)
SELECT sp.staff_id, p_new.id
FROM `staff_permissions` sp
INNER JOIN `permissions` p_old ON p_old.id = sp.permission_id
INNER JOIN `permissions` p_new ON p_new.perm_key = 'loan_manual_assign'
LEFT JOIN `staff_permissions` sp2
       ON sp2.staff_id = sp.staff_id AND sp2.permission_id = p_new.id
WHERE p_old.perm_key = 'loan_process'
  AND sp2.staff_id IS NULL;
