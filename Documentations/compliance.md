# Compliance Notes — CharityHub

## Financial Data Handling

### Payment Processing
- All payments are processed through **Stripe** or **Paymob** hosted checkouts
- **No raw card data** (PAN, CVC) ever touches CharityHub servers
- Payment data is tokenized by the gateways — only `payment_intent_id` / `transaction_id` references are stored

### Sensitive Data Masking
The `LogFinancialTransaction` listener masks sensitive fields in audit logs:
- Keys containing: `card`, `cvc`, `secret`, `token`, `api_key`, `password`, `number`
- Masking replaces values with `'***MASKED***'`
- This applies to all `financial_logs.metadata` and webhook payloads stored in `payment_webhooks.payload`

### Append-Only Financial Audit
**`financial_logs`** table:
- **No update or delete** operations allowed — model throws exceptions
- `updated_at` column is `null` (removed from timestamps)
- Records include: `old_values` / `new_values` JSON snapshots before/after changes
- Includes: `ip_address`, `user_agent`, `hash` (integrity verification)
- All financial events are logged:
  - Donation received, payment failed, refund issued
  - Subscription created, renewed, cancelled, renewal failed
  - Webhook received, webhook failed
  - Certificate generated, email sent

### Idempotency
- Every checkout request includes a UUID `idempotency_key`
- `idempotency_keys` table is **append-only** (no updated_at)
- Prevents duplicate charges from retries or network issues
- Webhooks are idempotent by `gateway_event_id` — no double processing

### Refund Safety
- Refunds check for **existing refunds** to prevent double-refund
- Gateway refunds fail gracefully → falls back to manual (local) refund
- Campaign progress is **recalculated** (not decremented) on refund to avoid inconsistencies
- Refund records are stored with gateway reference IDs

---

## GDPR Compliance

### Consent Capture
- **Explicit consent** required before any donation processing:
  - `gdpr_consent` checkbox on the donation form (required, validated server-side)
  - Recorded as `gdpr_consent = true` with `gdpr_consent_at` timestamp in the `donors` table
- Separate `marketing_opt_in` toggle (optional)

### Data Collected
| Data | Purpose | Retention |
|------|---------|-----------|
| Name, email | Donation receipts, certificates | Until account deletion |
| Donation amount, campaign | Public transparency, tax receipts | Indefinitely (anonymized) |
| IP address | Fraud prevention, audit | 90 days in logs |
| Payment token | Gateway reference | Indefinitely (no raw card data) |

### Right to Erasure ("Right to be Forgotten")
**Account deletion** at `/my-profile`:
1. User types "DELETE" to confirm
2. Session is invalidated
3. User record is **permanently deleted**

**Donor anonymization** (handled in `Donor` model via `anonymizeForGdpr()`):
- Name → `'Anonymized'`
- Email → `'anonymized_{id}@deleted.local'`
- Phone, address, city, country → `null`
- Anonymous flag → `true`
- Record is then **soft-deleted** (`deleted_at` timestamp)

**Note:** Donation records (amount, campaign) are **retained** for financial audit and campaign transparency, but are disassociated from the user identity.

### Right to Access
- Users can view all their data on their **dashboard** (`/my-dashboard`)
- Donation history, certificates, subscriptions are visible
- Profile data accessible at `/my-profile`
- No formal data export endpoint exists

### Right to Rectify
- Users can update **name, email, password** from `/my-profile`

### Data Portability
- Certificates can be downloaded as PDFs
- No formal data export function exists

### Certificate Transparency
Public certificate verification at `/verify/{uuid}`:
- Displays **masked donor name** (e.g., "J*** Smith")
- Anonymous donations display "Anonymous Donor"
- Shows: amount, campaign title, date, certificate status
- QR code on certificate links to verification page

### Privacy Policy
Available at `/privacy` — covers:
- Data collected and purpose
- Use of data (certificates, volunteering, updates)
- Transparency of donation metadata
- User rights (access, correct, delete)

### Third-Party Data Processors
| Processor | Purpose | Data Shared |
|-----------|---------|-------------|
| Stripe | Payment processing | Amount, currency, idempotency key |
| Paymob | Payment processing (fallback) | Amount, currency, customer email |
| Google (OAuth) | Social login | Email, name, avatar (optional) |
| Google Maps | Impact report location maps | None (client-side) |

### Security Measures
- CSRF protection on all web routes (except webhook)
- Idempotency key prevents duplicate charges
- Role-based access control (`admin`, `employee`, `user`)
- Activity logging via `spatie/laravel-activitylog` for sensitive models
- Financial audit trail with data masking
- Environment-based configuration for secrets (`.env`)
