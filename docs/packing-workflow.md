# Packing workflow

Office staff send an invoice to packing once. The packing list reads the original send date directly from `invoices.packing_sent_at`, displays the current invoice details, and shows each invoice once. Clicking send again does not change its send date or create another entry.

Editing an already-sent order only marks its invoice as edited. Its send time, completion state and checklist remain unchanged. An edit does not require resending. Revision checks prevent a checklist opened before an edit from saving stale changes.

Inventory staff open the current invoice, check every sale/free item, and mark it completed. The invoice records the completion time and actor. Date and status filters and summary counts all use the same invoice query.

The optional edit/revision columns have a fallback in the existing checklist JSON for older deployments.

## Deployment

The dispatch history model, service, commands and queries have been removed. No history table or backfill command is needed to use packing.

For databases where the history table was already created, run:

```sh
php artisan migrate --force
php artisan optimize:clear
```

The removal migration archives the obsolete table to a private JSONL file under `storage/app/private/packing-backups` before dropping it. If the table is already absent, the migration does nothing. Invoice and payment data are unchanged. The original creation migration remains as deployment history; the later removal migration leaves the table absent on fresh installations as well. Automatic rollback of this removal is disabled; restore the archive manually if required.
