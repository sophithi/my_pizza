# KHR payment reconciliation

Payment entry, balances, settlement status and payment summaries now use original integer KHR amounts. USD is derived for display at 4,000 KHR/USD. Payments cannot exceed the exact remaining KHR balance, including when editing an installment. KHR amounts are not rounded to hundreds.

Historical normalization was applied on November 18, 2026. It converted 83 receipts containing USD lines to KHR and corrected payment 6220 (ORD-6612) from 109,501 KHR to 109,500 KHR. Original receipts and their lines are preserved in:

`storage/app/private/payment-khr-backups/20261118-082556-aa31e81a.json`

The repair command previews changes by default:

```sh
php artisan payments:normalize-khr
```

`--apply` saves a private backup before updating receipts in a transaction. Explicit, verified single-line corrections use `--correction=PAYMENT_ID:KHR_AMOUNT`. Full invoice USD rounding corrections apply only to a single receipt containing one USD line whose amount matches the rounded original KHR invoice. Other historical USD receipts retain their recorded exchange-rate equivalent. Receipt dates remain unchanged.

Verified ABA totals:

| Period | Current-order collections, KHR | Old-debt collections, KHR | Total ABA, KHR |
| --- | ---: | ---: | ---: |
| November 13, 2026 | 5,718,000 | 0 | 5,718,000 |
| November 2026 | 93,803,000 | 1,219,500 | 95,022,500 |

## Historical discrepancies requiring receipt confirmation

These four August receipts already contain conflicting KHR amounts. They are separate from currency rounding. They were left unchanged pending confirmation of the actual amount received.

| Order | Payment | Date | Stored payment total, KHR | Receipt lines and notes, KHR |
| --- | ---: | --- | ---: | ---: |
| ORD-817 | 653 | 2026-08-10 | 133,000 | 139,000 |
| ORD-865 | 707 | 2026-08-11 | 307,500 | 297,500 |
| ORD-928 | 787 | 2026-08-12 | 222,500 | 207,500 |
| ORD-1548 | 1389 | 2026-08-21 | 119,000 | 120,000 |

Six orphan USD lines reference missing payments (15–19). They have no parent receipts and are excluded from payment reports; they were preserved rather than assigned invented amounts.
