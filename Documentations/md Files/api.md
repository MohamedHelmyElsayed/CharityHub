# API Documentation — CharityHub

There is no RESTful API or `routes/api.php`. All endpoints are web routes returning Blade views or redirects. The only machine-facing endpoint is the payment webhook.

---

## Webhook

### `POST /stripe/webhook`
**CSRF excluded.** Receives payment events from Stripe and Paymob.

**Headers:**
- Stripe: `Stripe-Signature`
- Paymob: `?hmac=` query parameter

**Stripe payload:**
```json
{
  "id": "evt_xxx",
  "type": "checkout.session.completed",
  "data": {
    "object": {
      "id": "cs_xxx",
      "payment_intent": "pi_xxx",
      "subscription": "sub_xxx",
      "metadata": {
        "idempotency_key": "uuid",
        "donor_id": "1",
        "campaign_id": "1",
        "amount": "100.00",
        "type": "one_time"
      }
    }
  }
}
```

**Supported event types:**
| Event | Handler |
|-------|---------|
| `checkout.session.completed` | Mark donation completed, create subscription if recurring |
| `invoice.paid` / `invoice.payment_succeeded` | Create renewal donation, update subscription |
| `invoice.payment_failed` | Mark subscription past_due |
| `charge.refunded` | Reverse donation, revoke certificate |
| `customer.subscription.deleted` | Cancel subscription |
| `customer.subscription.updated` | Sync subscription status |

**Response:** `200 { "status": "ok" }` or `200 { "status": "already_processed" }`

---

## Web Routes (Key Endpoints)

### Public
| Method | URI | Description |
|--------|-----|-------------|
| GET | `/` | Home page with featured campaigns |
| GET | `/campaigns` | Campaign listing |
| GET | `/campaigns/{slug}` | Campaign detail |
| GET | `/donate` | Donation form |
| GET | `/donate/success` | Donation success page |
| GET | `/donate/cancel` | Donation cancellation |
| GET | `/verify/{uuid}` | Certificate verification |
| GET | `/certificates/{uuid}/download` | Download certificate PDF |
| GET | `/volunteering` | Volunteer opportunities |
| GET | `/volunteering/{slug}` | Opportunity detail |
| GET | `/impact` | Impact reports |
| POST | `/stripe/webhook` | Payment webhook (no auth) |

### Requires Authentication
| Method | URI | Description |
|--------|-----|-------------|
| POST | `/donate/checkout` | Create checkout session |
| GET | `/volunteering/{slug}/apply` | Application form |
| POST | `/volunteering/{slug}/apply` | Submit application |
| GET | `/my-dashboard` | User dashboard |
| GET | `/my-profile` | User profile |
| PUT | `/my-profile` | Update profile |
| PUT | `/my-profile/password` | Update password |
| DELETE | `/my-profile` | Delete account (GDPR) |

### Donor Dashboard
| Method | URI | Description |
|--------|-----|-------------|
| GET | `/donor/dashboard` | Donor dashboard |
| GET | `/donor/donations` | Donation history |
| POST | `/donor/subscriptions/{sub}/cancel` | Cancel subscription |

### Volunteer
| Method | URI | Description |
|--------|-----|-------------|
| GET | `/volunteer/dashboard` | Volunteer dashboard |
| POST | `/volunteer/attendance/check-in` | Self check-in |
| POST | `/volunteer/attendance/{log}/check-out` | Self check-out |

### Admin
| Method | URI | Description |
|--------|-----|-------------|
| GET | `/admin` | Admin dashboard (KPIs) |
| GET | `/admin/ledger` | Financial ledger |
| GET | `/admin/manage-donations` | Donation management |
| POST | `/admin/manage-donations/{id}/refund` | Process refund |
| GET | `/admin/manage-volunteers` | Volunteer management |
| PATCH | `/admin/manage-volunteers/{id}/status` | Approve/reject volunteer |
| GET | `/admin/volunteer-hours` | Hour logs for approval |
| POST | `/admin/volunteer-hours/{log}/approve` | Approve hours |
| POST | `/admin/volunteer-hours/{log}/decline` | Decline hours |
| GET | `/admin/manage-schedules` | Schedule management |
| GET | `/admin/manage-impact-reports` | Impact reports CRUD |
| GET | `/admin/manage-campaigns` | Campaign CRUD |

### Filament Panel
All CRUD operations available at `/admin` via Filament for: Campaigns, Donations, Donors, Subscriptions, Financial Logs, Impact Reports, Volunteers, Events, Shifts, Applications, Slot Requests, Attendance Logs, Hour Logs.
