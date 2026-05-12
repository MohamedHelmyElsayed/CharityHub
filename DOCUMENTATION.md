# CharityHub - Comprehensive Documentation

**Version:** 1.0 | **Last Updated:** May 12, 2026

---

## Table of Contents

1. [Database Schema](#database-schema)
2. [Payment Flow Diagram](#payment-flow-diagram)
3. [API Documentation](#api-documentation)
4. [Donor-Facing User Guide](#donor-facing-user-guide)
5. [Admin Operations Manual](#admin-operations-manual)
6. [Compliance & Financial Data Security](#compliance--financial-data-security)

---

## Database Schema

### Overview

CharityHub uses a relational database structure organized around three primary domains:
- **Fundraising**: Campaigns, Donations, Subscriptions, Donors
- **Volunteering**: Volunteers, Events, Shifts, Applications, Hours
- **Operations**: Financial Logs, Refunds, Webhooks, Audit Trails

### Core Tables

#### **1. Users Table**
Central authentication and role management system.

```
users
├── id (PK)
├── name (string)
├── email (string, unique)
├── password (hashed)
├── role (enum: 'admin', 'employee', 'user')
├── subtype (nullable)
├── google_id (nullable) - Google OAuth integration
├── avatar (nullable)
├── email_verified_at (datetime)
├── created_at, updated_at (timestamps)
```

**Role Hierarchy:**
- `admin`: Full system access
- `employee`: Staff member with limited permissions
- `user`: Regular user (donor/volunteer)

---

#### **2. Donors Table**
Individual donor profiles with GDPR compliance tracking.

```
donors
├── id (PK)
├── user_id (FK) - nullable, soft-deletes friendly
├── name (string)
├── email (string, unique)
├── phone (nullable)
├── address (nullable)
├── city (nullable)
├── country (nullable)
├── anonymous (boolean) - Hide name on public lists
├── gdpr_consent (boolean) - GDPR consent given
├── gdpr_consent_at (datetime) - When consent was given
├── marketing_opt_in (boolean) - Marketing communications preference
├── deleted_at (datetime) - Soft delete for GDPR erasure
├── created_at, updated_at (timestamps)
```

**Key Features:**
- Soft deletes for GDPR compliance
- GDPR consent tracking with timestamps
- Marketing opt-in preference management
- Anonymous donation support

---

#### **3. Campaigns Table**
Fundraising campaign management.

```
campaigns
├── id (PK)
├── title (string)
├── slug (string, unique) - URL-friendly identifier
├── short_description (text)
├── description (longtext)
├── cover_image (string, nullable)
├── image (string, nullable)
├── goal_amount (decimal: 12,2)
├── current_amount (decimal: 12,2) - Running total from donations
├── deadline (date)
├── status (enum: 'active', 'paused', 'completed', 'ended')
├── featured (boolean) - Show on homepage
├── category (string, nullable)
├── og_title (string, nullable) - OpenGraph for social sharing
├── og_description (string, nullable)
├── lat, long (double, nullable) - Campaign location
├── created_at, updated_at (timestamps)
```

**Relationships:**
```
Campaign
  ├── hasMany: Donations
  ├── hasMany: VolunteerSchedules
  ├── hasMany: ImpactReports
  └── hasMany: Subscriptions
```

---

#### **4. Donations Table**
Individual and recurring donation records.

```
donations
├── id (PK)
├── user_id (FK, nullable)
├── donor_id (FK)
├── campaign_id (FK)
├── subscription_id (FK, nullable) - Links to recurring subscription
├── amount (decimal: 12,2)
├── currency (string: 'EGP', 'USD')
├── type (enum: 'one_time', 'recurring')
├── is_recurring (boolean)
├── status (enum: 'pending', 'completed', 'failed', 'refunded')
├── gateway (string: 'stripe', 'paypal', etc.)
├── gateway_transaction_id (string, unique)
├── gateway_refund_id (string, nullable)
├── stripe_payment_intent_id (string, nullable)
├── payment_id (string, unique, nullable)
├── idempotency_key (string) - Prevents duplicate charges
├── anonymous (boolean) - Hide donor name
├── message (text, nullable) - Optional donor message
├── ip_address (string, nullable) - For fraud detection
├── certificate_uuid (uuid) - Links to tax certificate
├── certificate_path (string, nullable)
├── certificate_generated_at (datetime, nullable)
├── refunded_at (datetime, nullable)
├── created_at, updated_at (timestamps)
```

**Audit Logging:**
- Tracks changes to: status, amount, refunded_at
- Uses Spatie ActivityLog

---

#### **5. Subscriptions Table**
Recurring donation management.

```
subscriptions
├── id (PK)
├── user_id (FK, nullable)
├── donor_id (FK, nullable)
├── campaign_id (FK, nullable)
├── gateway (string: 'stripe')
├── gateway_subscription_id (string, unique)
├── gateway_customer_id (string, nullable)
├── gateway_plan_id (string, nullable)
├── status (enum: 'active', 'trialing', 'paused', 'canceled', 'incomplete')
├── quantity (integer, nullable)
├── amount (decimal: 12,2)
├── currency (string: 'EGP', 'USD')
├── billing_interval (string: 'monthly', 'yearly')
├── next_billing_date (datetime)
├── trial_ends_at (datetime, nullable)
├── ends_at (datetime, nullable)
├── cancelled_at (datetime, nullable)
├── created_at, updated_at (timestamps)
```

**Relationships:**
```
Subscription
  ├── belongsTo: User
  ├── belongsTo: Donor
  ├── belongsTo: Campaign
  └── hasMany: Donations
```

---

#### **6. Financial Logs Table**
Immutable audit trail for all financial transactions (GDPR & Compliance).

```
financial_logs
├── id (PK)
├── user_id (FK, nullable)
├── donor_id (FK, nullable)
├── donation_id (FK, nullable)
├── campaign_id (FK, nullable)
├── batch_uuid (uuid) - Groups related transactions
├── transaction_type (enum: 'donation', 'refund', 'subscription_created', 
│                           'subscription_cancelled', 'subscription_renewed')
├── amount (decimal: 12,2)
├── currency (string)
├── status (string: 'initiated', 'processing', 'completed', 'failed', 'reversed')
├── gateway (string)
├── gateway_transaction_id (string)
├── idempotency_key (string) - Duplicate prevention
├── metadata (json) - Additional transaction data
├── old_values (json) - Previous values (for updates)
├── new_values (json) - New values (for updates)
├── ip_address (string) - Source IP for fraud detection
├── user_agent (string) - Browser information
├── hash (string) - SHA-256 HMAC for tampering detection
├── event (string) - Event type that triggered the log
├── created_at (timestamp, no updates allowed)
```

**Critical Features:**
- **Append-Only:** Cannot be updated or deleted
- **Cryptographic Hash:** Detects tampering
- **Comprehensive Metadata:** Full transaction context
- **User Tracking:** IP and user agent for compliance
- **No Update Timestamp:** Ensures immutability

---

#### **7. Refunds Table**
Refund tracking and management.

```
refunds
├── id (PK)
├── donation_id (FK)
├── user_id (FK) - Admin who initiated refund
├── amount (decimal: 12,2)
├── currency (string)
├── reason (string) - Reason for refund
├── gateway_refund_id (string, unique)
├── status (enum: 'pending', 'completed', 'failed')
├── metadata (json) - Gateway response data
├── created_at, updated_at (timestamps)
```

---

#### **8. Payment Webhooks Table**
Tracks incoming payment gateway webhooks for compliance and debugging.

```
payment_webhooks
├── id (PK)
├── gateway (string: 'stripe')
├── event_type (string: 'charge.succeeded', 'charge.failed', etc.)
├── gateway_event_id (string, unique)
├── payload (json) - Full webhook payload
├── signature (string) - HMAC signature for verification
├── processed_at (datetime, nullable)
├── status (enum: 'pending', 'processed', 'failed')
├── error_message (text, nullable)
├── created_at, updated_at (timestamps)
```

---

#### **9. Activity Logs Table (Spatie)**
Audit trail for user actions and model changes.

```
activity_log
├── id (PK)
├── log_name (string)
├── description (string) - What changed
├── subject_type (string) - Model class
├── subject_id (nullable)
├── causer_type (string, nullable) - Who made the change
├── causer_id (nullable)
├── properties (json) - Old/new values
├── batch_uuid (uuid) - Groups related actions
├── event (string) - created, updated, deleted
├── created_at, updated_at (timestamps)
```

---

#### **10. Volunteers Table**
Volunteer profiles and hour tracking.

```
volunteers
├── id (PK)
├── user_id (FK)
├── name (string)
├── email (string)
├── phone (string)
├── date_of_birth (date)
├── gender (string, nullable)
├── address (string, nullable)
├── skills (json) - Array of skills
├── interests (json) - Array of interests
├── availability (json) - Availability schedule
├── bio (text, nullable)
├── emergency_contact (string, nullable)
├── emergency_contact_name (string, nullable)
├── emergency_contact_phone (string, nullable)
├── profile_photo (string, nullable)
├── status (enum: 'pending', 'approved', 'active', 'suspended', 'inactive')
├── total_hours (float) - Total hours logged
├── total_approved_hours (float) - Admin-approved hours
├── approved_at (datetime, nullable)
├── approved_by (FK, nullable) - Admin who approved
├── internal_notes (text, nullable)
├── created_at, updated_at (timestamps)
```

---

#### **11. Volunteer Events Table**
Public volunteering opportunities.

```
volunteer_events
├── id (PK)
├── title (string)
├── slug (string, unique)
├── description (longtext)
├── image (string, nullable)
├── event_date (datetime)
├── location (string)
├── latitude, longitude (double, nullable)
├── max_volunteers (integer)
├── current_volunteers (integer) - Running count
├── status (enum: 'draft', 'published', 'cancelled', 'completed')
├── hours_per_slot (decimal: 5,2)
├── skills_required (json, nullable)
├── created_at, updated_at (timestamps)
```

---

#### **12. Volunteer Applications Table**
Volunteer interest and application tracking.

```
volunteer_applications
├── id (PK)
├── volunteer_id (FK)
├── event_id (FK)
├── status (enum: 'pending', 'approved', 'rejected', 'withdrawn')
├── motivation (text) - Why volunteer wants to help
├── experience (text, nullable) - Relevant experience
├── applied_at (datetime)
├── reviewed_at (datetime, nullable)
├── reviewed_by (FK, nullable) - Admin reviewer
├── created_at, updated_at (timestamps)
```

---

#### **13. Volunteer Shifts Table**
Time slots for volunteer events.

```
volunteer_shifts
├── id (PK)
├── event_id (FK)
├── shift_date (datetime)
├── start_time (time)
├── end_time (time)
├── max_volunteers (integer)
├── current_volunteers (integer)
├── created_at, updated_at (timestamps)
```

---

#### **14. Volunteer Slot Requests Table**
Volunteer requests for specific shifts (new system).

```
volunteer_slot_requests
├── id (PK)
├── volunteer_id (FK)
├── shift_id (FK, nullable) - Legacy system
├── event_id (FK)
├── status (enum: 'pending', 'approved', 'rejected', 'completed', 'no_show')
├── hours_requested (decimal: 5,2)
├── approved_hours (decimal: 5,2, nullable)
├── notes (text, nullable)
├── requested_at (datetime)
├── request_time_start (datetime, nullable)
├── request_time_end (datetime, nullable)
├── approved_at (datetime, nullable)
├── approved_by (FK, nullable)
├── created_at, updated_at (timestamps)
```

---

#### **15. Hour Logs & Attendance Logs Tables**
Detailed volunteer hour and attendance tracking.

```
hour_logs
├── id (PK)
├── volunteer_id (FK)
├── event_id (FK, nullable)
├── hours (decimal: 5,2)
├── date_logged (date)
├── description (text)
├── status (enum: 'pending', 'approved', 'rejected')
├── created_at, updated_at (timestamps)

attendance_logs
├── id (PK)
├── volunteer_id (FK)
├── event_id (FK)
├── check_in_time (datetime)
├── check_out_time (datetime, nullable)
├── status (enum: 'checked_in', 'checked_out', 'absent')
├── created_at, updated_at (timestamps)
```

---

### Database Relationships Diagram

```
┌─────────────────────────────────────────────────────────────────┐
│                          USER (Auth)                             │
└──────────┬──────────────┬──────────────┬────────────────────────┘
           │              │              │
     ┌─────▼──────┐  ┌────▼──────┐  ┌───▼────────┐
     │  VOLUNTEER │  │   DONOR    │  │ ACTIVITY   │
     └─────┬──────┘  └────┬──────┘  │   LOGS     │
           │              │         └────────────┘
     ┌─────▼──────────────▼──────────────────┐
     │      FINANCIAL OPERATIONS              │
     │                                        │
     │  ┌──────────────┐    ┌──────────────┐ │
     │  │  CAMPAIGNS   │    │  DONATIONS   │ │
     │  │      │       │    │      │       │ │
     │  └──────┼───────┘    │  ┌───┴──────┐│ │
     │         │            │  │REFUNDS   ││ │
     │  ┌──────▼───────┐   │  └──────────┘│ │
     │  │SUBSCRIPTIONS ├──►│FINANCIAL    ││ │
     │  │              │   │  LOGS       ││ │
     │  └──────────────┘   │(Audit Trail)││ │
     │                     └──────────────┘ │
     └────────────────────────────────────────┘

     ┌──────────────────────────────────────┐
     │   VOLUNTEERING MANAGEMENT             │
     │                                       │
     │  ┌──────────────┐  ┌──────────────┐ │
     │  │ EVENTS       │  │APPLICATIONS  │ │
     │  │      │       │  │              │ │
     │  │  ┌───▼──────┐├─►│              │ │
     │  │  │ SHIFTS   ││  └──────────────┘ │
     │  │  └──────────┤│                    │
     │  └─────────────┘│ ┌──────────────┐ │
     │                └─┤SLOT REQUESTS │ │
     │                  │(New System)  │ │
     │                  └──────┬───────┘ │
     │                         │          │
     │              ┌──────────▼────────┐ │
     │              │  ATTENDANCE LOGS  │ │
     │              │  HOUR LOGS        │ │
     │              └───────────────────┘ │
     └──────────────────────────────────────┘
```

---

## Payment Flow Diagram

### 1. One-Time Donation Flow

```
┌──────────────┐
│   Donor      │
│  Completes   │
│ Donation     │
│   Form       │
└──────┬───────┘
       │
       ▼
┌──────────────────────────────────────┐
│ Idempotency Check                    │
│ (Prevent duplicate charges)          │
│ Key: SHA256(email + amount + time)   │
└──────┬───────────────────────────────┘
       │
       ▼
┌──────────────────────────────────────┐
│ Find or Create Donor Record          │
│ Store GDPR Consent (timestamp)       │
│ Track IP Address & User Agent        │
└──────┬───────────────────────────────┘
       │
       ▼
┌──────────────────────────────────────┐
│ Create Stripe Checkout Session       │
│ gateway.createOneTimeCharge()        │
│ Return sessionId for redirect        │
└──────┬───────────────────────────────┘
       │
       ▼
┌──────────────────────────────────────┐
│ Create Pending Donation Record       │
│ status: 'pending'                    │
│ Store payment_intent_id              │
└──────┬───────────────────────────────┘
       │
       ▼ (Donor redirected to Stripe)
┌──────────────────────────────────────┐
│   Stripe Checkout Page               │
│   (PCI Compliant - Not CharityHub)   │
└──────┬───────────────────────────────┘
       │
   ┌───┴────────────────┬─────────────┐
   │ Success            │ Cancelled   │
   ▼                    ▼             
┌────────────────┐  ┌────────────────┐
│Redirect to     │  │Redirect to     │
│Success Page    │  │Cancel Page     │
└────┬───────────┘  └────────────────┘
     │
     ▼
┌──────────────────────────────────────┐
│ Stripe Webhook: charge.succeeded     │
│ POST /stripe/webhook                 │
│ Signature: X-Stripe-Signature        │
└──────┬───────────────────────────────┘
       │
       ▼
┌──────────────────────────────────────┐
│ Verify Webhook Signature             │
│ Prevent replay attacks               │
└──────┬───────────────────────────────┘
       │
       ▼
┌──────────────────────────────────────┐
│ Update Donation Status → 'completed' │
│ Emit: DonationReceived Event         │
│ Store: gateway_transaction_id        │
└──────┬───────────────────────────────┘
       │
       ▼
┌──────────────────────────────────────┐
│ Create Financial Log Entry (Audit)   │
│ transaction_type: 'donation'         │
│ Immutable record with hash           │
│ Log: amount, gateway, timestamp, IP  │
└──────┬───────────────────────────────┘
       │
       ▼
┌──────────────────────────────────────┐
│ Update Campaign current_amount       │
│ Queue: Generate Tax Certificate      │
│ Update Statistics                    │
└──────┬───────────────────────────────┘
       │
       ▼
┌──────────────────────────────────────┐
│ Send Donor Confirmation Email        │
│ Include: Certificate UUID, Amount    │
│ Call Queue: SendDonationEmail Job    │
└──────────────────────────────────────┘
```

### 2. Recurring Donation (Subscription) Flow

```
┌──────────────┐
│   Donor      │
│  Chooses     │
│ Recurring    │
│   Donation   │
└──────┬───────┘
       │
       ▼
┌──────────────────────────────────────┐
│ Idempotency Check                    │
└──────┬───────────────────────────────┘
       │
       ▼
┌──────────────────────────────────────┐
│ Find or Create Donor Record          │
│ Store GDPR Consent                   │
└──────┬───────────────────────────────┘
       │
       ▼
┌──────────────────────────────────────┐
│ Create Stripe Customer (if new)      │
│ gateway.createSubscription()         │
│ Return sessionId for setup           │
└──────┬───────────────────────────────┘
       │
       ▼ (Donor completes payment)
┌──────────────────────────────────────┐
│ Redirect to Success Page             │
└──────┬───────────────────────────────┘
       │
       ▼
┌──────────────────────────────────────┐
│ Stripe Webhook: customer.subscription│
│ created                              │
│ POST /stripe/webhook                 │
└──────┬───────────────────────────────┘
       │
       ▼
┌──────────────────────────────────────┐
│ Create Subscription Record           │
│ status: 'active' or 'trialing'       │
│ Store: gateway_subscription_id       │
│ next_billing_date calculation        │
└──────┬───────────────────────────────┘
       │
       ▼
┌──────────────────────────────────────┐
│ Create Financial Log Entry           │
│ transaction_type:                    │
│   'subscription_created'             │
└──────┬───────────────────────────────┘
       │
       ▼ (Monthly/Yearly on billing date)
┌──────────────────────────────────────┐
│ Stripe Webhook: invoice.payment_     │
│ succeeded                            │
└──────┬───────────────────────────────┘
       │
       ▼
┌──────────────────────────────────────┐
│ Create Donation Record for Charge    │
│ subscription_id: linked              │
│ is_recurring: true                   │
│ status: 'completed'                  │
└──────┬───────────────────────────────┘
       │
       ▼
┌──────────────────────────────────────┐
│ Create Financial Log Entry           │
│ transaction_type:                    │
│   'subscription_renewed'             │
│ Update next_billing_date             │
└──────┬───────────────────────────────┘
       │
       ▼
┌──────────────────────────────────────┐
│ Send Renewal Confirmation Email      │
└──────────────────────────────────────┘
```

### 3. Refund Flow

```
┌──────────────┐
│ Admin        │
│ Initiates    │
│ Refund       │
└──────┬───────┘
       │
       ▼
┌──────────────────────────────────────┐
│ Create Refund Record                 │
│ status: 'pending'                    │
│ Store: donation_id, reason           │
│ Record admin_user_id                 │
└──────┬───────────────────────────────┘
       │
       ▼
┌──────────────────────────────────────┐
│ Call Stripe Refund API               │
│ gateway.processRefund(transaction_id)│
│ Capture: gateway_refund_id           │
└──────┬───────────────────────────────┘
       │
       ▼
┌──────────────────────────────────────┐
│ Update Refund Record                 │
│ status: 'completed' or 'failed'      │
│ Store metadata from Stripe           │
└──────┬───────────────────────────────┘
       │
       ▼
┌──────────────────────────────────────┐
│ Update Donation                      │
│ status: 'refunded'                   │
│ refunded_at: now()                   │
│ Track change in ActivityLog          │
└──────┬───────────────────────────────┘
       │
       ▼
┌──────────────────────────────────────┐
│ Create Financial Log Entry           │
│ transaction_type: 'refund'           │
│ status: 'completed'                  │
│ Emit: RefundIssued Event             │
└──────┬───────────────────────────────┘
       │
       ▼
┌──────────────────────────────────────┐
│ Send Refund Confirmation to Donor    │
│ Amount, Reason, Tracking Info        │
└──────────────────────────────────────┘
```

### 4. Webhook Processing Flow

```
┌──────────────────────┐
│ Stripe Sends Webhook │
│ POST /stripe/webhook │
└──────┬───────────────┘
       │
       ▼
┌──────────────────────────────────────┐
│ Extract X-Stripe-Signature Header    │
│ Extract Webhook Payload              │
└──────┬───────────────────────────────┘
       │
       ▼
┌──────────────────────────────────────┐
│ Verify HMAC Signature                │
│ HMAC-SHA256(body, stripe_secret)     │
│ Security: Prevent spoofed webhooks   │
└──────┬───────────────────────────────┘
       │ ✓ Valid
       ▼
┌──────────────────────────────────────┐
│ Store PaymentWebhook Record          │
│ status: 'pending'                    │
│ Store full payload (json)            │
└──────┬───────────────────────────────┘
       │
       ▼
┌──────────────────────────────────────┐
│ Process Based on Event Type          │
│ • charge.succeeded                   │
│ • charge.failed                      │
│ • customer.subscription.created      │
│ • customer.subscription.deleted      │
│ • invoice.payment_succeeded          │
│ • invoice.payment_failed             │
└──────┬───────────────────────────────┘
       │
       ▼
┌──────────────────────────────────────┐
│ Update Webhook Record Status         │
│ status: 'processed'                  │
│ processed_at: now()                  │
└──────┬───────────────────────────────┘
       │
       ▼
┌──────────────────────────────────────┐
│ Return 200 OK to Stripe              │
│ Idempotent: Safe to receive again    │
└──────────────────────────────────────┘
```

---

## API Documentation

### Authentication

All API endpoints require:
- **Authentication:** Laravel session or Bearer token
- **CSRF Protection:** Except for `/stripe/webhook` (configured in bootstrap/app.php)
- **Email Verification:** Most authenticated endpoints require verified email

### Request/Response Format

```json
{
  "success": true/false,
  "message": "string",
  "data": { /* response data */ },
  "errors": { /* validation errors */ }
}
```

---

### Donation Endpoints

#### 1. GET `/donate`
**Purpose:** Display donation page with available campaigns

**Authentication:** Not required (public)

**Query Parameters:**
- `campaign_id` (optional): Pre-select a campaign

**Response:**
```json
{
  "campaigns": [
    {
      "id": 1,
      "title": "Emergency Relief",
      "slug": "emergency-relief-abc123",
      "short_description": "Help those in need",
      "goal_amount": "10000.00",
      "current_amount": "5234.50",
      "progress_percentage": 52,
      "deadline": "2026-06-12"
    }
  ],
  "selectedCampaign": { /* campaign object */ }
}
```

---

#### 2. POST `/donate/checkout`
**Purpose:** Create Stripe checkout session for donation

**Authentication:** Required (auth middleware)
**Idempotency:** Yes (via middleware)

**Request Body:**
```json
{
  "campaign_id": 1,
  "amount": 100.00,
  "type": "one_time",
  "name": "John Doe",
  "email": "john@example.com",
  "anonymous": false,
  "message": "Keep up the great work!",
  "gdpr_consent": true,
  "idempotency_key": "sha256_hash_here"
}
```

**Validation Rules:**
- `campaign_id`: required, exists in campaigns table
- `amount`: required, numeric, 1-1,000,000
- `type`: required, one_time|recurring
- `name`: required, string, max 255
- `email`: required, email, max 255
- `gdpr_consent`: required, accepted
- `idempotency_key`: required, string, max 64

**Response (201 Created):**
```json
{
  "sessionId": "cs_test_...",
  "clientSecret": "...",
  "publicKey": "pk_test_..."
}
```

**Error Response (400):**
```json
{
  "success": false,
  "message": "Campaign not found or inactive",
  "errors": {
    "campaign_id": ["Campaign must be active"]
  }
}
```

---

#### 3. GET `/donate/success`
**Purpose:** Redirect after successful Stripe payment

**Query Parameters:**
- `session_id` (Stripe checkout session)

**Response:**
- Redirects to dashboard with success message
- Displays donation certificate UUID for verification

---

#### 4. GET `/donate/cancel`
**Purpose:** Redirect if donor cancels payment

**Response:**
- Redirects to donate page with message

---

### Donation Management (Admin)

#### 5. GET `/admin/manage-donations`
**Purpose:** List all donations with filters

**Authentication:** Required (admin/employee)

**Query Parameters:**
- `status`: pending|completed|failed|refunded
- `campaign_id`: filter by campaign
- `donor_id`: filter by donor
- `date_from`: start date (YYYY-MM-DD)
- `date_to`: end date (YYYY-MM-DD)
- `page`: pagination (default: 1)
- `per_page`: items per page (default: 15)

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "donor_name": "John Doe",
      "amount": "100.00",
      "currency": "EGP",
      "type": "one_time",
      "status": "completed",
      "campaign": { "id": 1, "title": "..." },
      "created_at": "2026-05-12T10:30:00Z",
      "refunded_at": null
    }
  ],
  "pagination": {
    "current_page": 1,
    "total": 150,
    "per_page": 15,
    "last_page": 10
  }
}
```

---

#### 6. GET `/admin/manage-donations/{id}`
**Purpose:** View donation details

**Authentication:** Required (admin/employee)

**Response:**
```json
{
  "donation": {
    "id": 1,
    "donor": { /* full donor object */ },
    "campaign": { /* campaign object */ },
    "amount": "100.00",
    "currency": "EGP",
    "status": "completed",
    "gateway": "stripe",
    "gateway_transaction_id": "ch_1234567890",
    "payment_intent_id": "pi_1234567890",
    "anonymous": false,
    "message": "Great work!",
    "certificate": { "uuid": "...", "path": "..." },
    "created_at": "2026-05-12T10:30:00Z",
    "updated_at": "2026-05-12T10:35:00Z"
  },
  "financial_logs": [ /* related financial logs */ ],
  "activity_log": [ /* audit trail */ ]
}
```

---

#### 7. POST `/admin/manage-donations/{id}/refund`
**Purpose:** Process refund for donation

**Authentication:** Required (admin/employee)

**Request Body:**
```json
{
  "reason": "Donor requested refund",
  "metadata": { "notes": "Additional notes" }
}
```

**Response (201 Created):**
```json
{
  "refund": {
    "id": 1,
    "donation_id": 1,
    "amount": "100.00",
    "reason": "Donor requested refund",
    "status": "completed",
    "gateway_refund_id": "re_1234567890",
    "created_at": "2026-05-12T11:00:00Z"
  },
  "message": "Refund processed successfully"
}
```

---

### Subscription Endpoints

#### 8. POST `/donor/subscriptions/{subscription}/cancel`
**Purpose:** Cancel recurring donation subscription

**Authentication:** Required (auth, verified email)

**Request Body (optional):**
```json
{
  "reason": "Too frequent"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Subscription cancelled successfully",
  "subscription": {
    "id": 1,
    "status": "canceled",
    "cancelled_at": "2026-05-12T12:00:00Z",
    "last_billing_amount": "50.00"
  }
}
```

---

### Donor Dashboard Endpoints

#### 9. GET `/donor/dashboard`
**Purpose:** Get donor dashboard overview

**Authentication:** Required (auth, verified email)

**Response:**
```json
{
  "donor": {
    "name": "John Doe",
    "email": "john@example.com",
    "total_donated": "500.00",
    "donation_count": 5
  },
  "active_subscriptions": [
    {
      "id": 1,
      "campaign": { "title": "..." },
      "amount": "50.00",
      "billing_interval": "monthly",
      "next_billing_date": "2026-06-12",
      "status": "active"
    }
  ],
  "recent_donations": [ /* array of recent donations */ ],
  "tax_certificates": [ /* array of certificates */ ]
}
```

---

#### 10. GET `/donor/donations`
**Purpose:** Get donation history

**Authentication:** Required (auth, verified email)

**Query Parameters:**
- `type`: one_time|recurring
- `status`: completed|failed|refunded
- `page`: pagination

**Response:**
```json
{
  "donations": [ /* array of donations */ ],
  "pagination": { /* pagination info */ }
}
```

---

### Campaign Endpoints

#### 11. GET `/campaigns`
**Purpose:** List all active campaigns

**Authentication:** Not required (public)

**Query Parameters:**
- `category`: filter by category
- `featured`: 1/0
- `page`: pagination

**Response:**
```json
{
  "campaigns": [
    {
      "id": 1,
      "title": "Emergency Relief",
      "slug": "emergency-relief-abc123",
      "short_description": "...",
      "cover_image": "/images/campaign-1.jpg",
      "goal_amount": "10000.00",
      "current_amount": "5234.50",
      "deadline": "2026-06-12",
      "progress_percentage": 52,
      "category": "emergency"
    }
  ],
  "pagination": { /* pagination info */ }
}
```

---

#### 12. GET `/campaigns/{slug}`
**Purpose:** View campaign details

**Authentication:** Not required (public)

**Response:**
```json
{
  "campaign": {
    "id": 1,
    "title": "Emergency Relief",
    "description": "Full HTML description",
    "goal_amount": "10000.00",
    "current_amount": "5234.50",
    "deadline": "2026-06-12",
    "status": "active",
    "donations_count": 23,
    "top_donors": [
      { "name": "Anonymous", "amount": "500.00" },
      { "name": "Jane Smith", "amount": "250.00" }
    ],
    "impact_reports": [ /* array of reports */ ]
  }
}
```

---

### Volunteer Endpoints

#### 13. GET `/volunteering`
**Purpose:** List volunteering opportunities

**Authentication:** Not required (public)

**Response:**
```json
{
  "events": [
    {
      "id": 1,
      "title": "Community Cleanup",
      "slug": "community-cleanup-123",
      "description": "Help clean our local park",
      "location": "Central Park",
      "event_date": "2026-05-20T09:00:00Z",
      "hours_per_slot": "4.0",
      "max_volunteers": 30,
      "current_volunteers": 18,
      "skills_required": ["physical activity"],
      "status": "published"
    }
  ]
}
```

---

#### 14. POST `/volunteering/{event:slug}/apply`
**Purpose:** Submit volunteer application

**Authentication:** Required (auth)

**Request Body:**
```json
{
  "motivation": "I want to help the community",
  "experience": "3 years volunteering at local shelters"
}
```

**Response (201 Created):**
```json
{
  "success": true,
  "message": "Application submitted successfully",
  "application": {
    "id": 1,
    "event_id": 1,
    "status": "pending",
    "applied_at": "2026-05-12T12:00:00Z"
  }
}
```

---

### Stripe Webhook Endpoint

#### 15. POST `/stripe/webhook`
**Purpose:** Receive Stripe webhook events

**Authentication:** No auth (signature verification instead)

**Header Verification:**
```
X-Stripe-Signature: t=timestamp,v1=signature,v0=old_signature
```

**Supported Events:**
- `charge.succeeded` → Update donation to completed
- `charge.failed` → Update donation to failed, emit event
- `customer.subscription.created` → Create subscription record
- `customer.subscription.deleted` → Mark subscription as cancelled
- `invoice.payment_succeeded` → Create recurring donation record
- `invoice.payment_failed` → Emit renewal failure event
- `customer.source.deleted` → Clean up customer data

**Response:**
```json
{
  "success": true,
  "message": "Webhook processed"
}
```

**Error Response (401):**
```json
{
  "success": false,
  "message": "Invalid webhook signature"
}
```

---

### Certificate Verification Endpoints

#### 16. GET `/verify`
**Purpose:** Display certificate verification page

**Authentication:** Not required (public)

**Response:**
- HTML form to enter certificate UUID

---

#### 17. GET `/verify/{uuid}`
**Purpose:** Verify and display donation certificate

**Authentication:** Not required (public)

**Response:**
```json
{
  "valid": true,
  "certificate": {
    "uuid": "550e8400-e29b-41d4-a716-446655440000",
    "donor_name": "John Doe",
    "amount": "100.00",
    "campaign": "Emergency Relief",
    "donation_date": "2026-05-12",
    "certificate_url": "/certificates/550e8400.pdf"
  }
}
```

---

### Impact Report Endpoints

#### 18. GET `/impact`
**Purpose:** List published impact reports

**Authentication:** Not required (public)

**Response:**
```json
{
  "reports": [
    {
      "id": 1,
      "title": "Q1 2026 Impact Report",
      "slug": "q1-2026-impact-report",
      "excerpt": "In the first quarter...",
      "cover_image": "/images/report-1.jpg",
      "published_at": "2026-04-01"
    }
  ]
}
```

---

#### 19. GET `/impact/{report:slug}`
**Purpose:** View impact report details

**Authentication:** Not required (public)

**Response:**
```json
{
  "report": {
    "id": 1,
    "title": "Q1 2026 Impact Report",
    "content": "Full HTML content",
    "metrics": {
      "total_donations": "50000.00",
      "lives_impacted": 500,
      "volunteers_engaged": 45
    },
    "photos": [ /* array of impact photos */ ]
  }
}
```

---

### Admin Dashboard Endpoints

#### 20. GET `/admin`
**Purpose:** Admin dashboard overview

**Authentication:** Required (admin/employee)

**Response:**
```json
{
  "statistics": {
    "total_donations_today": "5000.00",
    "total_donations_month": "50000.00",
    "active_campaigns": 3,
    "active_donors": 150,
    "total_volunteers": 45,
    "pending_applications": 12
  },
  "recent_transactions": [ /* last 10 donations */ ],
  "pending_approvals": [
    "volunteer_hours": 5,
    "volunteer_applications": 12,
    "slot_requests": 8
  ]
}
```

---

#### 21. GET `/admin/ledger`
**Purpose:** View financial audit ledger

**Authentication:** Required (admin/employee)

**Query Parameters:**
- `date_from`: YYYY-MM-DD
- `date_to`: YYYY-MM-DD
- `transaction_type`: donation|refund|subscription_created|etc
- `status`: initiated|processing|completed|failed|reversed

**Response:**
```json
{
  "entries": [
    {
      "id": 1,
      "date": "2026-05-12T10:30:00Z",
      "transaction_type": "donation",
      "amount": "100.00",
      "currency": "EGP",
      "status": "completed",
      "gateway": "stripe",
      "gateway_transaction_id": "ch_1234567890",
      "donor": "John Doe",
      "campaign": "Emergency Relief",
      "ip_address": "192.168.1.1",
      "hash": "sha256_hash_here",
      "verified": true
    }
  ],
  "summary": {
    "total_amount": "10000.00",
    "transaction_count": 100,
    "timestamp_first": "2026-05-01T00:00:00Z",
    "timestamp_last": "2026-05-12T23:59:59Z"
  }
}
```

---

## Donor-Facing User Guide

### Getting Started

#### 1. Creating an Account
1. Click **"Sign Up"** on the homepage
2. Enter your email and create a secure password
3. Verify your email by clicking the link sent to your inbox
4. Complete your profile information

**OR**

Use **"Sign in with Google"** for quick registration:
1. Click **"Continue with Google"**
2. Select your Google account
3. Your CharityHub account is created instantly

---

#### 2. Making a One-Time Donation

**Steps:**
1. Navigate to the **"Donate"** page
2. **Select a Campaign:** Choose from featured or all available campaigns
3. **Enter Donation Details:**
   - Amount (minimum £1)
   - Your name
   - Your email address
4. **Personalize (Optional):**
   - Add an anonymous donation flag
   - Include a message of encouragement
5. **Privacy & Consent:**
   - Review our Privacy Policy
   - Confirm GDPR data consent
6. **Review & Confirm:**
   - Click "Proceed to Payment"
   - You'll be redirected to Stripe's secure checkout
7. **Payment:**
   - Enter card details
   - Complete the payment
8. **Confirmation:**
   - You'll receive a confirmation email
   - A **tax-deductible certificate** will be generated (downloadable from your dashboard)

**Supported Currencies:**
- EGP (Egyptian Pound) - Primary
- USD (US Dollar) - International donors

**Supported Payment Methods:**
- Credit cards (Visa, Mastercard, American Express)
- Digital wallets (Apple Pay, Google Pay - where available)

---

#### 3. Setting Up a Recurring Donation

**Steps:**
1. Go to **"Donate"** page
2. Select **"Make it a recurring donation"**
3. Choose your billing frequency:
   - **Monthly:** Charge on the same day each month
   - **Yearly:** Charge on the same date annually
4. Enter donation details (same as one-time)
5. Proceed to Stripe checkout
6. Complete payment

**Important:**
- Your first charge occurs immediately
- Subsequent charges occur automatically on the scheduled date
- You can cancel anytime from your donor dashboard
- No lock-in period - cancel immediately after first charge if you wish

**What You Get:**
- Monthly email receipts
- Individual certificates for each donation
- Special recognition on our "Sustaining Donors" page (opt-in)

---

#### 4. Your Donor Dashboard

**Access:** Click **"My Dashboard"** after logging in

**Dashboard Features:**

**Overview Card:**
- Total amount donated
- Number of donations
- Active subscriptions
- Donation status summary

**My Donations:**
- View all past donations
- Filter by campaign or date
- Download receipts
- View tax certificates

**Active Subscriptions:**
- List of recurring donations
- Next billing date
- Billing amount and frequency
- Quick cancel button

**Tax Certificates:**
- Download PDF certificates
- Verify certificate authenticity
- Share verification link

**Account Settings:**
- Update email address
- Change password
- Update privacy preferences
- Download account data (GDPR)
- Delete account option

---

#### 5. Tax Certificates

**Automatic Generation:**
- Generated immediately after payment completion
- Available for download in your dashboard
- Unique UUID for verification

**Certificate Details Include:**
- Your name (or "Anonymous" if you chose that option)
- Donation amount and date
- Campaign name
- CharityHub tax registration number
- Certificate UUID (for public verification)

**How to Use:**
1. Download from your dashboard
2. Use for tax filing with your accountant/tax authority
3. Keep the UUID for verification purposes

**Public Verification:**
- Share certificate UUID with anyone
- They can verify authenticity at `/verify/{uuid}`
- No personal data is shown in public verification

---

#### 6. Cancelling a Subscription

**Steps:**
1. Go to **"My Dashboard"**
2. Find the subscription in "Active Subscriptions"
3. Click **"Cancel Subscription"** button
4. Confirm cancellation (optional: provide reason)
5. You'll receive a cancellation confirmation email

**Important:**
- Cancellation is immediate
- No further charges will occur
- Current period remains valid
- You can resubscribe anytime

**Refund Policy:**
- We don't refund the current billing period
- If you had a trial period, you won't be charged
- For disputes, contact our support team

---

#### 7. Donation Privacy & Anonymity

**Anonymous Donations:**
- Your name will not appear on public donor lists
- Tax certificate will show "Anonymous"
- Email will only be used by CharityHub (no third parties)
- Receipt will be sent to your email only

**Public Recognition:**
- If not anonymous, you may appear on:
  - Campaign page donor list (amount only)
  - "Sustaining Donors" page (if monthly donor)
  - Impact reports (aggregated data)
- You can opt-out of public recognition anytime

---

#### 8. Contacting Support

**Email Support:** support@charityhub.example.com

**Response Time:** Within 24 business hours

**Common Issues:**

**Q: I didn't receive my tax certificate**
A: Check your dashboard under "My Donations." If missing, email support with your transaction ID.

**Q: Can I change my donation amount?**
A: For recurring donations, you'll need to cancel and create a new subscription.

**Q: Is my payment information stored?**
A: No. Payments are processed through Stripe's secure PCI-compliant servers. CharityHub never stores card details.

**Q: Can I get a refund?**
A: Contact support with your transaction ID. Refunds are considered case-by-case.

**Q: How do I delete my account?**
A: Visit your profile settings and select "Delete Account." Your personal data will be anonymized per GDPR.

---

## Admin Operations Manual

### Admin Access & Roles

**Admin Roles:**
- **Admin:** Full system access (create accounts, manage content, refund donations, etc.)
- **Employee:** Limited access (view reports, manage volunteers, approve hours, etc.)

**Access:**
- URL: `/admin` (requires login + admin/employee role)
- Filament Panel: Additional configuration interface

---

### Dashboard Overview

**Admin Dashboard (`/admin`):**

Displays:
- **Key Metrics:**
  - Total donations (today, this month, all-time)
  - Active campaigns count
  - Total donors
  - Active subscribers
  - Pending approvals
  
- **Charts:**
  - Donation trends (line chart)
  - Campaign progress (bar chart)
  - Volunteer engagement (pie chart)

- **Quick Actions:**
  - Create new campaign
  - Process refund
  - Approve volunteer hours
  - Review applications

---

### Campaign Management

#### Creating a Campaign

**Steps:**
1. Go to **Admin > Campaigns > Create New**
2. **Basic Information:**
   - Title: Campaign name (e.g., "Emergency Relief Fund")
   - Short Description: One-liner for lists
   - Description: Full HTML description with impact details
   - Category: Select category (emergency, community, education, etc.)
   
3. **Financial Settings:**
   - Goal Amount: Target fundraising amount
   - Deadline: End date for campaign
   - Status: active, paused, or completed
   
4. **Media:**
   - Cover Image: Large banner image (1920x1080px recommended)
   - Images: Additional campaign photos
   
5. **Location (Optional):**
   - Latitude & Longitude: For map display
   
6. **Social Sharing (Optional):**
   - OpenGraph Title: Custom social media title
   - OpenGraph Description: Custom social media preview
   
7. **Advanced:**
   - Featured: Show on homepage
   - Slug: URL-friendly identifier (auto-generated)

8. Click **"Create Campaign"** to save

**Permissions:** Admin only

---

#### Editing Campaigns

1. Go to **Admin > Campaigns**
2. Find campaign in table
3. Click **"Edit"** button
4. Modify any field
5. Click **"Update"** to save

**Note:** Once campaign has donations, you cannot lower the goal amount below current amount.

---

#### Campaign Status Lifecycle

```
active (accepting donations)
   ↓
completed (goal reached, donations still accepted)
   ↓
paused (donations halted temporarily)
   ↓
ended (campaign finished, no more donations)
```

**Automatic Status Changes:**
- When donations equal goal amount: Status changes to "ended"
- After deadline passes: Status changes to "completed"

---

#### Viewing Campaign Analytics

**Click Campaign > "View Analytics":**

Shows:
- Total donations: Amount and count
- Donation breakdown: One-time vs recurring
- Top donors: Names and amounts
- Donation timeline: Graph by date
- Volunteer contributions: If applicable
- Recent activity: Last 10 transactions

---

### Financial Management

#### Donations Ledger

**Access:** **Admin > Manage Donations**

**Table Shows:**
- Donation ID
- Donor Name (or "Anonymous")
- Campaign
- Amount & Currency
- Type (One-time/Recurring)
- Status (Pending/Completed/Failed/Refunded)
- Payment Gateway
- Date

**Filters:**
- Status
- Campaign
- Donor
- Date range
- Amount range

**Actions (Click Row):**
- View details
- Process refund
- View audit trail

---

#### Viewing Donation Details

**Click Donation ID > Details:**

Shows:
- **Donor Information:**
  - Name, email, phone
  - Address (if provided)
  - Donor history (total donated, frequency)
  
- **Donation Details:**
  - Amount, currency, type
  - Campaign
  - Payment status
  - Payment method (gateway)
  - Gateway transaction ID
  - Donation message (if provided)
  - Anonymous flag
  
- **Related Records:**
  - Certificate (if generated)
  - Refunds (if any)
  - Financial logs (audit trail)
  - Activity log (changes)

---

#### Processing Refunds

**Steps:**

1. Go to **Admin > Manage Donations**
2. Find donation to refund
3. Click **"Process Refund"** button
4. Refund Form:
   - Reason: Select predefined reason or custom text
   - Amount: Defaults to full donation amount
   - Notes: Internal notes about refund
5. Click **"Confirm Refund"**
6. System will:
   - Call Stripe API to reverse charge
   - Create refund record
   - Update donation status to "refunded"
   - Create financial log entry
   - Send confirmation email to donor
   - Subtract amount from campaign total

**Important Notes:**
- Refunds process to original payment method
- Takes 3-5 business days to appear in donor's account
- Creates an immutable financial log entry
- Donor receives automated email with refund details

**Refund Reasons:**
- Accidental duplicate donation
- Donor requested cancellation
- Technical error
- Fraudulent transaction
- Other (requires custom text)

---

#### Financial Audit Ledger

**Access:** **Admin > Ledger**

**Purpose:** View immutable financial audit trail

**Shows:**
- All transactions in chronological order
- Transaction type (donation, refund, etc.)
- Amount and currency
- Status
- Payment gateway
- Gateway transaction ID
- Donor ID (if applicable)
- Campaign ID (if applicable)
- IP address (for fraud detection)
- Hash verification status
- Timestamp

**Features:**
- **Verify Integrity:** System automatically verifies SHA-256 hash
- **Export:** Download as CSV for external audit
- **Filter:** By date, type, status, gateway
- **Search:** Transaction ID, donor, campaign

**Important:**
- Entries are append-only (cannot be modified/deleted)
- Hash tampering is detected automatically
- All IP addresses logged (GDPR compliant)
- Regulatory requirement for financial compliance

---

### Donor Management

#### Viewing All Donors

**Access:** **Admin > Donors**

**Table Shows:**
- Donor Name/Email
- Total Donated
- Number of Donations
- Active Subscriptions
- Last Donation Date
- Status (Active/Inactive/Anonymous)

**Actions:**
- Click to view details
- View donation history
- View subscription details
- Send email

---

#### Donor Details

**Shows:**
- Profile information
- GDPR consent status and date
- Marketing opt-in status
- Donation history (all donations)
- Active subscriptions
- Financial metrics
- Download personal data (GDPR)

---

#### GDPR Data Requests

**Data Subject Access Request (DSAR):**

1. Go to **Admin > Donors**
2. Find donor
3. Click **"Export Personal Data"**
4. System generates ZIP containing:
   - All personal data (JSON)
   - Donation records
   - Subscription history
   - Communication records

**Data Deletion Request:**

1. Click **"Delete Personal Data"** (Admin only)
2. Confirm action (cannot be undone)
3. System will:
   - Anonymize donor record: Name → "Anonymized", Email → "anonymized_123@deleted.local"
   - Soft delete record (kept for legal compliance)
   - Create audit log entry
   - Cannot be recovered

---

### Volunteer Management

#### Volunteer Dashboard

**Access:** **Admin > Manage Volunteers**

**Table Shows:**
- Volunteer Name/Email
- Status (Pending/Approved/Active/Suspended)
- Total Hours (logged)
- Approved Hours (verified by admin)
- Email
- Phone
- Date Joined

---

#### Reviewing Volunteer Applications

**Access:** **Admin > Manage Volunteers > Applications**

**Tab: "Pending Applications"**

Shows volunteers awaiting approval. For each:
- Name, contact info
- Bio and interests
- Skills
- Availability
- Emergency contact

**Approval Process:**

1. Click application
2. Review volunteer details
3. Click **"Approve"** or **"Reject"**
4. Add internal notes (optional)
5. System sends notification to volunteer

**Approved volunteers get:**
- Email confirmation
- Access to volunteering opportunities
- Invitation to upcoming events

---

#### Managing Volunteer Hours

**Access:** **Admin > Volunteer Hours**

**Tab: "Pending Approval"**

Shows submitted hour logs awaiting verification.

**For Each Entry:**
- Volunteer name
- Event name
- Hours claimed
- Date submitted
- Description

**Approval:**
1. Click to view details
2. Review submission
3. Click **"Approve Hours"** or **"Reject"**
4. If rejecting, provide reason
5. Click confirm

**Updates:**
- Volunteer's total_approved_hours updated
- Financial impact (hours used for impact metrics)
- Notification sent to volunteer

---

#### Volunteer Slot Requests

**Access:** **Admin > Volunteer Slots**

**Shows:**
- Volunteer name
- Event name
- Requested hours
- Status (Pending/Approved/Rejected)
- Request date

**Management:**
1. Click request
2. Review volunteer and event details
3. **Approve:** Volunteer confirmed for slot
4. **Reject:** Provide reason
5. Notification sent to volunteer

---

#### Volunteer Events

**Create Event:**

1. Go to **Admin > Volunteering > Create Event**
2. Fill form:
   - Title (event name)
   - Description (what volunteers will do)
   - Event date & time
   - Location
   - Maximum volunteers
   - Hours per slot
   - Required skills (optional)
3. Save

**Manage Events:**
- Edit event details
- Update volunteer capacity
- View applications
- Mark as completed

---

### Impact Reports

#### Creating Impact Report

**Access:** **Admin > Impact Reports > Create**

**Form Fields:**
- Title (e.g., "Q1 2026 Impact Report")
- Description: Main narrative content
- Metrics (JSON):
  ```json
  {
    "lives_impacted": 500,
    "funds_distributed": 50000,
    "volunteers_engaged": 45,
    "hours_volunteered": 360
  }
  ```
- Cover image
- Additional photos
- Visibility: Published/Draft

**Steps:**
1. Complete form
2. Add photos
3. Click **"Preview"** to review
4. Click **"Publish"** to go live
5. Automatically shared on `/impact` page

---

#### Report Analytics

**Click Report > Analytics:**
- Page views
- Downloads
- Shares
- Public engagement

---

### Settings & Configuration

#### Financial Settings

**Access:** **Admin > Settings > Financial**

- Minimum donation amount (default: £1)
- Supported currencies
- Tax registration number (for certificates)
- Refund policy text
- Default donation message template

---

#### Email Configuration

**Access:** **Admin > Settings > Email**

- Send test emails
- Configure email templates
- Set notification recipients
- Manage unsubscribe list

---

#### Activity Logs

**Access:** **Admin > Activity Logs**

**Shows:**
- All user actions (logins, edits, deletions)
- Who made change
- What changed (before/after)
- Timestamp
- IP address

**Use Cases:**
- Audit trail compliance
- Troubleshooting issues
- Investigating unusual activity

---

#### Backup & Export

**Access:** **Admin > Tools > Backup**

**Export Options:**
- All donations (CSV/JSON)
- All volunteers (CSV/JSON)
- Financial ledger (CSV)
- Donors (anonymized for security)

**Backup Schedule:**
- Daily automatic backups
- Manual backup on demand
- 30-day retention

---

## Compliance & Financial Data Security

### GDPR Compliance

#### Data Collection & Consent

**CharityHub Collects:**
- Name, email, phone (for donation receipts)
- Address (if provided)
- Payment information (sent directly to Stripe, not stored)
- IP address (for fraud detection)
- User agent (for security)

**Explicit Consent:**
- GDPR checkbox must be checked before donation
- Timestamp recorded (`gdpr_consent_at`)
- Can be revoked anytime

**Lawful Basis for Processing:**
- **Consent:** For marketing communications
- **Contract:** For donation processing and fulfillment
- **Legal Obligation:** For tax reporting and financial records
- **Legitimate Interest:** For fraud prevention and security

---

#### Data Retention

**Donation Records:**
- Keep for **7 years** (tax compliance)
- After 7 years: Archive to cold storage

**Donor Personal Data:**
- Keep for duration of relationship
- Plus 1 year after last interaction
- After that: Delete on request or annual purge

**Activity/Audit Logs:**
- Keep for **3 years** (compliance)
- Immutable (cannot be deleted for audit trail)

**Payment Information:**
- **Never stored** (Stripe handles directly)
- Transaction IDs logged (not card details)

---

#### Data Subject Rights

**Right to Access (DSAR):**
- Donors can request export of their data
- Process: Click in dashboard or email support
- Response time: 30 days
- Includes: All personal data, donations, communications

**Right to Rectification:**
- Update own profile data (name, email, address)
- Contact admin for changes to historical records

**Right to Erasure ("Right to be Forgotten"):**
- Available after completing current donation cycle
- Process: Dashboard > Settings > "Delete Account"
- Result: Anonymization + soft delete
- Legal records (financial logs) retained for 7 years

**Right to Data Portability:**
- Export data in machine-readable format (JSON/CSV)
- Process: Dashboard > Download My Data

**Right to Object:**
- Opt-out of marketing emails: Subscription link in email
- Opt-out of analytics: Contact support

---

#### Cookie Policy

**Cookies Used:**
- **Session cookie:** Maintain login session (removed on logout)
- **CSRF token:** Prevent cross-site request forgery
- **Preference cookie:** Remember donor preferences

**No Tracking Cookies:**
- CharityHub does not use third-party tracking
- Google Analytics (optional): Can be disabled

**Donors Can:**
- Accept/reject cookies on first visit
- Change preferences anytime in settings

---

### Payment Security

#### PCI Compliance

**CharityHub Status:** PCI DSS Level 1 compliant (through Stripe)

**Implementation:**
- All payments handled by Stripe (PCI certified)
- CardElement loaded directly from Stripe
- Card data never touches CharityHub servers
- Payment Intent IDs logged (not card details)

**Security Measures:**
- HTTPS/TLS 1.2+ (all traffic encrypted)
- No card storage on our servers
- Tokenization (payment method stored with Stripe)

**Donor Data Protection:**
- Card details: Encrypted by Stripe
- Name/email: Encrypted in transit
- IP address: Logged for fraud detection

---

#### Idempotency for Payment Processing

**Implementation:**
- `idempotency_key` generated client-side
- Middleware checks for duplicate submissions
- Prevents accidental double-charges
- Key format: SHA256(email + amount + timestamp)

**Flow:**
1. Client generates idempotency_key
2. Submission with key
3. Server checks if key exists
4. If exists: Return previous result
5. If new: Process payment
6. Store key for 24 hours

---

#### Fraud Detection

**Mechanisms:**
- IP address tracking (flag suspicious locations)
- Velocity checks (block rapid successive donations)
- Email verification (confirm real email)
- Amount limits (max £1,000,000 per transaction)
- User agent monitoring (detect bots)

**Admin Alert Triggers:**
- Same IP: Multiple donations in 1 hour
- Same email: Multiple failed payments
- Unusual locations: Geographic anomalies
- Large amounts: Donations >£5,000

**Admin Actions:**
- Review flagged transactions manually
- Block suspicious payment methods
- Request additional verification
- Contact donor if concerned

---

### Financial Audit Trail

#### Immutable Logging (FinancialLog)

**Purpose:** Tamper-proof record of all transactions

**What's Logged:**
- Transaction type (donation, refund, subscription event)
- Amount and currency
- Status (initiated → completed/failed)
- Payment gateway and gateway ID
- IP address and user agent
- Timestamp
- Idempotency key
- Metadata (additional context)
- Cryptographic hash (for tampering detection)

**Key Properties:**
- **Append-only:** New records added, never updated or deleted
- **Immutable:** SQL triggers prevent modifications
- **Hashed:** SHA256-HMAC detects tampering
- **Auditable:** Included in regulatory audits

**Hash Verification:**
```php
hash_hmac('sha256', json_encode([
    'transaction_type',
    'amount',
    'currency',
    'gateway_transaction_id',
    'idempotency_key',
    'status'
]), config('app.key'))
```

**Admin Verification:**
- Dashboard shows hash status (✓ Valid / ⚠ Tampered)
- Export includes hash for external audit

---

#### Financial Report Generation

**Available Reports:**

1. **Daily Ledger:**
   - All transactions for specific date
   - Totals by status (completed/failed/pending)
   - CSV export for accounting software

2. **Monthly Summary:**
   - Total donations (one-time + recurring)
   - Refunds issued
   - Net income
   - Revenue by campaign

3. **Annual Report:**
   - Yearly totals
   - Trend analysis
   - Donor metrics
   - Cost analysis

4. **Audit Report:**
   - Full ledger with all fields
   - Includes all IP addresses
   - Hash verification status
   - Suitable for external auditors

**Export Formats:**
- CSV (for Excel)
- JSON (for systems integration)
- PDF (for printing/archiving)

---

### Tax Compliance

#### Tax Certificate Generation

**Automatic Process:**
1. Donation completed (status = "completed")
2. Certificate generated immediately
3. Stored as PDF in `/storage/certificates/`
4. UUID created for public verification

**Certificate Contents:**
- Donor name (or "Anonymous")
- Donation amount and date
- Campaign name
- CharityHub tax registration number
- Charity number (if applicable)
- Statement of tax-deductibility
- Unique certificate UUID

**Public Verification:**
- UUID can be shared publicly
- Verification page shows: Name, amount, date, campaign
- No sensitive data exposed (no email/phone)
- No access to donor records

#### Tax Reporting

**For Administrators:**
- Generate donor lists for tax authority
- Export donation records by tax year
- Calculate total charitable donations
- Automated tax report generation

**For Donors:**
- Download certificate anytime
- Use for tax deduction (consult accountant)
- Keep UUID for verification

**Currency Handling:**
- Donations in multiple currencies tracked separately
- Convert to local currency for tax reporting
- Exchange rate recorded at transaction time
- Separate reporting lines per currency

---

### Data Security Measures

#### Encryption

**In Transit:**
- HTTPS/TLS 1.2+ enforced on all endpoints
- HSTS headers (force HTTPS for 1 year)
- Certificate pinning for critical APIs

**At Rest:**
- Database encryption: At rest (hosting-level)
- Sensitive fields encrypted (passwords hashed with bcrypt)
- Payment data: Not stored (Stripe handles)
- Audit logs: Encrypted database-level

#### Authentication & Authorization

**Session Management:**
- Session tokens: Secure, HttpOnly cookies
- Session timeout: 24 hours of inactivity
- Force logout on password change
- Prevent concurrent sessions (1 per user)

**Password Policy:**
- Minimum 8 characters
- Must include: uppercase, lowercase, number, special char
- Cannot reuse last 5 passwords
- Automatic expiry: 90 days (admin users)

**Two-Factor Authentication (Admin):**
- Required for admin accounts
- Methods: TOTP (Google Authenticator), SMS
- Backup codes provided
- Optional for regular users

**Role-Based Access Control:**
- Admin: Full access
- Employee: Limited access (view reports, approve hours)
- User: Personal data only
- No access to others' data

#### Logging & Monitoring

**Security Logs Track:**
- Failed login attempts
- Permission denied errors
- Data exports
- Account deletions
- Settings changes

**Monitoring Alerts:**
- Multiple failed logins (3+ in 5 minutes)
- Unusual access patterns
- Large data exports
- Bulk operations

**Log Retention:**
- Keep for 1 year
- Archived to cold storage after 90 days
- Accessible by admins and auditors

---

### Compliance Standards

#### Standards Met

1. **GDPR (General Data Protection Regulation)**
   - EU data protection regulation
   - Applies to: All EU donors + UK donors
   - Requirements: Consent, transparency, data rights

2. **UK Data Protection Act 2018**
   - UK implementation of GDPR
   - Applies to: UK organizations + UK donors

3. **PCI DSS (Payment Card Industry Data Security Standard)**
   - Payment industry standard
   - Level: Compliant through Stripe
   - Requirements: Secure payment handling

4. **UK Gift Aid**
   - UK tax relief on charitable donations
   - Requires: Donor consent, record retention
   - Certificate generation: Automatic

5. **AML/KYC (Anti-Money Laundering / Know Your Customer)**
   - Financial regulations
   - Implemented by: Payment processors (Stripe)
   - CharityHub role: No KYC (handled by payment gateway)

---

### Incident Response

#### Data Breach Protocol

**If Data Breach Occurs:**

1. **Immediate (within 1 hour):**
   - Isolate affected systems
   - Stop unauthorized access
   - Document evidence

2. **Within 24 hours:**
   - Assess scope (which data affected)
   - Notify security team and leadership
   - Begin investigation

3. **Within 72 hours (GDPR requirement):**
   - Notify ICO (Information Commissioner's Office)
   - Notify affected donors (if high risk)
   - Publish transparency report

4. **Follow-up:**
   - Root cause analysis
   - Corrective actions
   - Enhanced monitoring
   - Breach report to auditors

**Donor Notification:**
- Email: Clear, non-technical explanation
- Action items: What donors should do
- Support: Contact info for questions
- Timeline: When notification sent + investigation completion

**Transparent Communication:**
- Public statement on website
- Social media updates
- Press release (if significant)
- Full disclosure to regulators

---

#### Security Updates & Patching

**Schedule:**
- Daily: Monitor for critical vulnerabilities
- Weekly: Security patches applied
- Monthly: Major version upgrades tested

**Process:**
1. Identify vulnerability
2. Patch developed and tested
3. Applied to production (minimal downtime)
4. Verified and monitored
5. Documented in change log

**Critical Vulnerabilities:**
- Applied immediately (emergency maintenance)
- Donor notification if data exposed
- Full incident report generated

---

### Third-Party Integrations

#### Stripe Payment Gateway

**Data Shared:**
- Payment card information (encrypted)
- Transaction amount and currency
- Donor name and email
- IP address

**Stripe Privacy Policy:** https://stripe.com/privacy

**Data Processing:**
- Stripe processes under sub-processor agreement
- PCI DSS Level 1 certified
- Data retained: Per PCI standards
- Data location: US (check Stripe terms)

#### Google OAuth

**Data Shared:**
- Email address
- Name
- Profile picture

**Google Privacy Policy:** https://policies.google.com/privacy

**Data Processing:**
- Used for authentication only
- No data stored from Google (except email/name)
- Can disconnect anytime

---

### Audit & Compliance Checklist

**For Internal Auditors:**

- [ ] Financial logs: All transactions recorded (immutable)
- [ ] Donor consent: GDPR consent dates recorded
- [ ] Data retention: Old data purged per schedule
- [ ] Access logs: All admin actions logged
- [ ] Security updates: Patches applied timely
- [ ] Password policies: Enforced for all users
- [ ] Backups: Daily backups verified
- [ ] Incident reports: Documented properly
- [ ] Third-party contracts: DPAs signed with processors
- [ ] Breach register: Updated with any incidents

**For External Auditors:**

Request access to:
- Financial ledger (export from admin dashboard)
- Activity logs (for period being audited)
- Backup verification
- Incident reports (if any)
- DPAs with third parties
- Privacy policy and terms
- GDPR compliance documentation

---

## Appendices

### A. Glossary

- **Donation:** Monetary contribution to a campaign (one-time or recurring)
- **Subscription:** Recurring donation (charged monthly or yearly)
- **Refund:** Reversal of charge; funds returned to donor
- **Campaign:** Fundraising initiative with goal amount and deadline
- **Financial Log:** Immutable audit trail of financial transaction
- **Idempotency:** Prevention of duplicate transactions
- **GDPR Consent:** Explicit permission to collect personal data
- **PCI DSS:** Payment security standard
- **Stripe:** Third-party payment processor
- **Webhook:** HTTP callback from Stripe to notify of events
- **Certificate:** Tax receipt for donor (proof of donation)
- **Soft Delete:** Logical deletion (data kept but hidden)
- **Activity Log:** Audit trail of user actions
- **IP Address:** Internet identifier for fraud detection

---

### B. Useful Endpoints

| Purpose | Endpoint | Method |
|---------|----------|--------|
| Donate | `/donate` | GET |
| Create Payment | `/donate/checkout` | POST |
| Donation Success | `/donate/success` | GET |
| Verify Certificate | `/verify/{uuid}` | GET |
| Donor Dashboard | `/donor/dashboard` | GET |
| Admin Dashboard | `/admin` | GET |
| Admin Ledger | `/admin/ledger` | GET |
| Manage Donations | `/admin/manage-donations` | GET |
| Manage Volunteers | `/admin/manage-volunteers` | GET |
| Stripe Webhook | `/stripe/webhook` | POST |

---

### C. Contact & Support

**For Donors:**
- Email: support@charityhub.example.com
- Phone: +44 (0)123 456 7890
- Hours: Monday-Friday, 9am-5pm GMT

**For Administrators:**
- Internal Slack Channel: #charityhub-admin
- On-call Support: [contact info]
- Documentation: [internal wiki URL]

**For Security Issues:**
- Email: security@charityhub.example.com
- Do not open public issues
- Responsible disclosure welcome

---

**Document Version:** 1.0 | **Last Updated:** May 12, 2026 | **Next Review:** May 12, 2027
