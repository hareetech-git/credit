# Permissions Reference

This document describes what each permission does in the system.

- `cust_create`: Create new customers in the admin/staff panel.
- `cust_read`: View customer list and customer details.
- `cust_update`: Edit customer information.
- `cust_delete`: Delete customer records.

- `loan_view`: View loan applications (staff sees assigned apps; admin sees all).
- `loan_process`: Approve or reject loan applications and update loan status.
- `loan_delete`: Delete loan applications (and related docs).

- `enquiry_view_assigned`: View only enquiries assigned to the staff member.
- `enquiry_view_all`: View all enquiries (not just assigned ones).
- `enquiry_delete`: Delete enquiries (and related messages/notes).
- `enquiry_status_change`: Change enquiry status (convert/close). Also unlocks staff email/WhatsApp actions.
