# Local changes pending GitHub update

Branch: main
HEAD: fdcd4bf (fix inventory)
Cached origin/main comparison (behind ahead): 0	0

Git fetch could not refresh origin/main because .git/FETCH_HEAD is not writable in this session. Remote status below uses the cached reference.

Hosting status: not verified. No live hosting source or deployed commit was compared.

Modified tracked files: 53
Staged files: 0
Untracked files before creating this report: 18

## Modified tracked files

- app/Http/Controllers/InventoryController.php
- app/Http/Controllers/InvoiceController.php
- app/Http/Controllers/OrderController.php
- app/Http/Controllers/PaymentController.php
- app/Http/Controllers/ProductController.php
- app/Http/Controllers/PurchaseController.php
- app/Http/Controllers/ReportController.php
- app/Http/Controllers/SalespersonController.php
- app/Http/Requests/StoreOrderRequest.php
- app/Http/Requests/StoreProductRequest.php
- app/Http/Requests/UpdateOrderRequest.php
- app/Models/InventoryMovement.php
- app/Models/Invoice.php
- app/Models/Order.php
- app/Models/OrderItem.php
- app/Models/Product.php
- database/migrations/2026_06_29_155426_add_allow_custom_price_to_products_table.php
- database/migrations/2026_07_01_145953_add_allow_free_to_products_table.php
- database/migrations/2026_07_22_170235_add_discount_percent_to_order_items_table.php
- database/migrations/2026_08_01_000001_add_printed_at_to_invoices_table.php
- database/migrations/2026_08_01_171027_add_show_invoice_info_to_deliveries_table.php
- database/migrations/2026_08_01_180000_add_soft_deletes_to_invoices_table.php
- database/migrations/2026_08_01_190000_add_soft_deletes_to_orders_table.php
- database/migrations/2026_08_26_150955_add_payment_method_to_purchases_table.php
- database/migrations/2026_08_26_154122_add_currency_to_purchases_table.php
- database/migrations/2026_08_26_155738_add_salesperson_id_to_customers_table.php
- database/migrations/2026_08_27_145301_create_salespersons_table.php
- database/migrations/2026_08_27_145546_update_customer_salesperson_foreign_key.php
- database/migrations/2026_09_07_143833_add_delete_reason_to_invoices_and_orders_table.php
- resources/views/inventory/index.blade.php
- resources/views/inventory/pdf.blade.php
- resources/views/inventory/show.blade.php
- resources/views/invoices/index.blade.php
- resources/views/invoices/show.blade.php
- resources/views/layouts/app.blade.php
- resources/views/orders/create.blade.php
- resources/views/orders/edit.blade.php
- resources/views/orders/show.blade.php
- resources/views/packing/index.blade.php
- resources/views/packing/sticker-prep.blade.php
- resources/views/payments/index.blade.php
- resources/views/products/create.blade.php
- resources/views/products/edit.blade.php
- resources/views/products/index.blade.php
- resources/views/products/show.blade.php
- resources/views/purchases/index.blade.php
- resources/views/reports/customers.blade.php
- resources/views/reports/inventory.blade.php
- resources/views/reports/sales-pdf.blade.php
- resources/views/reports/sales.blade.php
- resources/views/salespersons/index.blade.php
- resources/views/salespersons/show.blade.php
- routes/web.php

## New untracked files

- app/Services/InventoryAnalyticsService.php
- app/Services/InventoryReportService.php
- app/Services/OrderStockService.php
- database/migrations/2026_10_16_000001_classify_inventory_order_items.php
- database/migrations/2026_10_23_000001_add_packing_completed_by_to_invoices.php
- database/migrations/2026_10_23_000002_add_packing_checklist_to_invoices.php
- database/migrations/2026_10_23_000003_add_print_templates_to_orders.php
- docs/MY_PIZZA_System_Documentation_KM.docx
- docs/SYSTEM_DOCUMENTATION_KM.md
- docs/~$_PIZZA_System_Documentation_KM.docx
- resources/views/inventory/partials/activity.blade.php
- resources/views/inventory/partials/analytics.blade.php
- resources/views/inventory/partials/modal-styles.blade.php
- resources/views/products/partials/catalog-style.blade.php
- resources/views/products/partials/edit-style.blade.php
- resources/views/reports/partials/print-styles.blade.php
- tests/Feature/InventoryAnalyticsTest.php
- tests/Feature/OrderStockTest.php

## Notes

- These working tree changes are not included in the current HEAD commit.
- Files may already have been manually copied to hosting; Git status cannot establish that.
- The accompanying patch includes tracked modifications only; it excludes new untracked files.
- This report and patch are also newly created local artifacts.
