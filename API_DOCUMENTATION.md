# CRM API Documentation

## Authentication

### Register

**POST** `/api/auth/register`

Request Body:
- `name`
- `email`
- `password`

### Login

**POST** `/api/auth/login`

Request Body:
- `email`
- `password`

### Logout

**POST** `/api/auth/logout`

Authentication:
- Bearer Token Required

## Location APIs

### Country

**POST** `/api/country/save`

**POST** `/api/country/list`

**POST** `/api/country/detail`

**POST** `/api/country/update`

**POST** `/api/country/delete`

### State

**POST** `/api/state/save`

**POST** `/api/state/list`

**POST** `/api/state/detail`

**POST** `/api/state/update`

**POST** `/api/state/delete`

### City

**POST** `/api/city/save`

**POST** `/api/city/list`

**POST** `/api/city/detail`

**POST** `/api/city/update`

**POST** `/api/city/delete`

## Category APIs

### Category

**POST** `/api/category/save`

**POST** `/api/category/list`

**POST** `/api/category/detail`

**POST** `/api/category/update`

**POST** `/api/category/delete`

**POST** `/api/category/dropdown`

## Lead APIs

### Lead

**POST** `/api/lead/save`

**POST** `/api/lead/list`

**POST** `/api/lead/detail`

**POST** `/api/lead/update`

**POST** `/api/lead/delete`

**POST** `/api/lead/convert`

**POST** `/api/lead/my-leads`

**POST** `/api/lead/my-leads-summary`

## Customer Contact APIs

### Customer Contact

**POST** `/api/customer-contact/save`

**POST** `/api/customer-contact/list`

**POST** `/api/customer-contact/detail`

**POST** `/api/customer-contact/update`

**POST** `/api/customer-contact/delete`

**POST** `/api/customer-contact/set-primary`

## Follow-Up APIs

### Follow-Up

**POST** `/api/follow-up/save`

**POST** `/api/follow-up/list`

**POST** `/api/follow-up/detail`

**POST** `/api/follow-up/update`

**POST** `/api/follow-up/delete`

**POST** `/api/follow-up/my-follow-ups`

**POST** `/api/follow-up/my-follow-ups-summary`

**POST** `/api/follow-up/upcoming`

**POST** `/api/follow-up/overdue`

## Activity APIs

### Activity

**POST** `/api/activity/save`

**POST** `/api/activity/list`

**POST** `/api/activity/detail`

**POST** `/api/activity/update`

**POST** `/api/activity/delete`

**POST** `/api/activity/my-activities`

**POST** `/api/activity/my-activities-summary`

## Notes APIs

### Note

**POST** `/api/note/save`

**POST** `/api/note/list`

**POST** `/api/note/detail`

**POST** `/api/note/update`

**POST** `/api/note/delete`

**POST** `/api/note/my-notes`

**POST** `/api/note/my-notes-summary`

## Task APIs

### Task

**POST** `/api/task/save`

**POST** `/api/task/list`

**POST** `/api/task/detail`

**POST** `/api/task/update`

**POST** `/api/task/delete`

**POST** `/api/task/my-tasks`

**POST** `/api/task/my-tasks-summary`

## Notification APIs

### Notification

**POST** `/api/notification/list`

**POST** `/api/notification/detail`

**POST** `/api/notification/mark-as-read`

**POST** `/api/notification/mark-all-as-read`

**POST** `/api/notification/unread-count`

**POST** `/api/notification/my-notifications`

**POST** `/api/notification/my-unread-count`

**POST** `/api/notification/my-mark-all-as-read`

## Deal APIs

### Deal

**POST** `/api/deal/save`

**POST** `/api/deal/list`

**POST** `/api/deal/detail`

**POST** `/api/deal/update`

**POST** `/api/deal/delete`

**POST** `/api/deal/my-deals`

**POST** `/api/deal/my-deals-summary`

**POST** `/api/deal/pipeline-summary`

## Deal Stage History APIs

### Deal Stage History

**POST** `/api/deal-stage-history/list`

**POST** `/api/deal-stage-history/detail`

## Quotation APIs

### Quotation

**POST** `/api/quotation/save`

**POST** `/api/quotation/list`

**POST** `/api/quotation/detail`

**POST** `/api/quotation/update`

**POST** `/api/quotation/delete`

**POST** `/api/quotation/my-quotations`

**POST** `/api/quotation/convert-to-invoice`

## Invoice APIs

### Invoice

**POST** `/api/invoice/save`

**POST** `/api/invoice/list`

**POST** `/api/invoice/detail`

**POST** `/api/invoice/update`

**POST** `/api/invoice/delete`

**POST** `/api/invoice/my-invoices`

**POST** `/api/invoice/my-invoices-summary`

**POST** `/api/invoice/mark-overdue`

## Invoice Status History APIs

### Invoice Status History

**POST** `/api/invoice-status-history/list`

**POST** `/api/invoice-status-history/detail`

## Invoice Payment APIs

### Invoice Payment

**POST** `/api/invoice-payment/save`

**POST** `/api/invoice-payment/list`

**POST** `/api/invoice-payment/detail`

**POST** `/api/invoice-payment/update`

**POST** `/api/invoice-payment/delete`

**POST** `/api/invoice-payment/summary`

## Quotation Item APIs

### Quotation Item

**POST** `/api/quotation-item/save`

**POST** `/api/quotation-item/list`

**POST** `/api/quotation-item/detail`

**POST** `/api/quotation-item/update`

**POST** `/api/quotation-item/delete`

## Dashboard APIs

### Dashboard

**POST** `/api/dashboard/summary`

**POST** `/api/dashboard/sales-by-user`

**POST** `/api/dashboard/sales-by-customer`

**POST** `/api/dashboard/tasks-by-user`

**POST** `/api/dashboard/follow-ups-by-user`

**POST** `/api/dashboard/quotations-by-user`

**POST** `/api/dashboard/invoices-by-user`

**POST** `/api/dashboard/deal-forecast`

**POST** `/api/dashboard/deal-forecast-by-user`

**POST** `/api/dashboard/lead-source-summary`

**POST** `/api/dashboard/monthly-revenue`

**POST** `/api/dashboard/activities-by-user`

**POST** `/api/dashboard/notes-by-user`

**POST** `/api/dashboard/leads-by-user`

**POST** `/api/dashboard/quotation-conversion-rate`

**POST** `/api/dashboard/quotation-invoice-summary`

**POST** `/api/dashboard/invoice-aging`

**POST** `/api/dashboard/deal-win-rate`

## Global Search APIs

### Search

**POST** `/api/search`

Request Body:

- `keyword`
- `per_page`

`keyword` minimum 2 characters ka hona chahiye.

## Common Response Format

### Success Response

```json
{
    "status": 1,
    "msg": "success message",
    "error": "",
    "error_array": [],
    "data": {}
}

{
    "status": 0,
    "msg": "error message",
    "error": "",
    "error_array": [],
    "data": []
}

Authorization: Bearer YOUR_TOKEN
Accept: application/json

Save kar do.

Ho jaye to **done** bolo.

## Base URL

```text
http://127.0.0.1:8000

## Testing

Run all Laravel tests:

```bash
php artisan test

php artisan migrate:status
php artisan optimize:clear
composer dump-autoload

## Project Structure

```text
app/
├── Http/
│   └── Controllers/
├── Models/
└── Helpers/

database/
├── migrations/
└── seeders/

routes/
└── api.php

tests/
├── Feature/
└── Unit/

## Important Note

This CRM project does **not** contain a Product module.

Quotation Items are stored using:

- `quotation_id`
- `item_name`
- `quantity`
- `price`
- `total_amount`

Quotation Items do not use `product_id` or a `products` table.

## HTTP Status Codes

### 200 — Success

API request successfully complete hui.

### 401 — Unauthorized

Bearer token missing ya invalid hai.

### 422 — Validation Error

Request body me required/invalid data hai.

### 404 — Not Found

Requested record available nahi hai.



