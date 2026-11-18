# Daily packing dispatch history

The packing page shows each currently sent invoice once, with its current contents and current packing status. Editing a sent invoice only sets the “បានកែវិក្កយបត្រ” badge. It retains its original send time, completion status, checklist and place in the queue. No resend is required. The separate revision number prevents a checklist opened before an edit from writing stale changes without moving the invoice to another day.

The ledger remains in the background to preserve the actual dispatch days. Date filters use these known send times; the screen displays the latest matching send time and the current invoice. A monthly view counts each invoice once even if legacy data contains multiple dispatches. Clicking send again for an already-sent invoice does not create another dispatch.

Editing does not clear `packing_sent_at`, `packing_completed_at`, `packing_completed_by` or `packing_checklist`. Deleted or cancelled invoices leave the active packing screen, but their ledger history remains stored. The previous superseded/history cards, history rows and resend controls have been removed from the packing list.

Live dispatches still retain their original invoice/order snapshot internally. The packing screen and print actions always use the current invoice and edited quantities, rather than an older snapshot.

The send operation and its history entry are saved in one transaction. It locks the order before the invoice, matching order editing, and rechecks the current invoice to prevent duplicate submissions.

## Recovery of existing records

```sh
php artisan migrate --path=database/migrations/2026_11_18_000001_create_packing_dispatches_table.php
php artisan packing:backfill-dispatches
php artisan packing:backfill-dispatches --apply
php artisan migrate --path=database/migrations/2026_11_18_000002_add_packing_edit_marker_to_invoices_table.php
php artisan packing:restore-edited
php artisan packing:restore-edited --apply
```

Backfill is idempotent and only uses existing first-send and latest-send timestamps. It includes soft-deleted invoices so deleting an invoice does not erase a known dispatch. It recovers the first-send records of invoices whose current send field was cleared after editing.

The restore command takes a private backup, restores cleared send fields from known historical timestamps, and marks previously edited active invoices. It does not create a new dispatch for a restored send and does not change financial amounts. It is idempotent.

Earlier intermediate resends, their completion states, and their original item contents cannot be reconstructed when they were never recorded. Recovered records are marked `legacy_known_timestamp`; their snapshots reflect the data available at recovery, not a claim that historical contents were reconstructed. Only future `live` snapshots are presented as original dispatch contents.
