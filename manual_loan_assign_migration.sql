-- Permission split for staff manual loan assignment
-- 1) loan_manual_assign: staff can create manual loan and self-assign
-- 2) loan_manual_assign_others: staff can assign/reassign to other staff

INSERT INTO `permissions` (`perm_key`, `description`)
SELECT 'loan_manual_assign', 'Manually assign loans to staff'
WHERE NOT EXISTS (
    SELECT 1 FROM `permissions` WHERE `perm_key` = 'loan_manual_assign'
);

INSERT INTO `permissions` (`perm_key`, `description`)
SELECT 'loan_manual_assign_others', 'Allow staff to assign/reassign loans to other staff'
WHERE NOT EXISTS (
    SELECT 1 FROM `permissions` WHERE `perm_key` = 'loan_manual_assign_others'
);

-- Optional: if role 1 already has loan_manual_assign, keep it as-is (self create permission).
-- Optional: do NOT auto-grant loan_manual_assign_others; grant it only to selected staff/roles.

-- Example: grant loan_manual_assign_others to role_id = 1 (all staff)
-- INSERT INTO `role_permissions` (`role_id`, `permission_id`)
-- SELECT 1, p.id
-- FROM `permissions` p
-- WHERE p.perm_key = 'loan_manual_assign_others'
--   AND NOT EXISTS (
--       SELECT 1 FROM `role_permissions` rp WHERE rp.role_id = 1 AND rp.permission_id = p.id
--   );

-- Example: grant loan_manual_assign_others to one staff (replace 5 with actual staff id)
-- INSERT INTO `staff_permissions` (`staff_id`, `permission_id`)
-- SELECT 5, p.id
-- FROM `permissions` p
-- WHERE p.perm_key = 'loan_manual_assign_others'
--   AND NOT EXISTS (
--       SELECT 1 FROM `staff_permissions` sp WHERE sp.staff_id = 5 AND sp.permission_id = p.id
--   );
