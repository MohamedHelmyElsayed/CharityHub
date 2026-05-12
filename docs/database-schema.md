# Database Schema — CharityHub

## Overview

28 tables across 43 migrations. Key domains: users & auth, donations & payments, campaigns, volunteering, certificates, financial audit.

---

## Core Tables

### `users`
| Column | Type | Notes |
|--------|------|-------|
| id | bigint PK | |
| name | varchar(255) | |
| email | varchar(255) | UNIQUE |
| google_id | varchar(255) | NULLABLE, UNIQUE |
| avatar | varchar(255) | NULLABLE |
| email_verified_at | timestamp | NULLABLE |
| password | varchar(255) | NULLABLE (nullable for OAuth) |
| role | enum('admin','employee','user') | DEFAULT 'user' |
| subtype | enum('donor','volunteer') | NULLABLE |

### `donors`
| Column | Type | Notes |
|--------|------|-------|
| id | bigint PK | |
| user_id | bigint FK->users | SET NULL on delete |
| name | varchar(255) | |
| email | varchar(255) | UNIQUE |
| phone / address / city / country | varchar(255) | NULLABLE |
| anonymous | boolean | DEFAULT false |
| gdpr_consent | boolean | |
| gdpr_consent_at | timestamp | NULLABLE |
| deleted_at | timestamp | Soft deletes for GDPR erasure |

### `campaigns`
| Column | Type | Notes |
|--------|------|-------|
| id | bigint PK | |
| title | varchar(255) | |
| slug | varchar(255) | UNIQUE |
| description | text | |
| goal_amount | decimal(12,2) | |
| current_amount | decimal(12,2) | DEFAULT 0 |
| deadline | date | |
| status | enum('active','completed','paused','ended') | DEFAULT 'active' |
| featured | boolean | |
| category | varchar(255) | |

### `donations`
| Column | Type | Notes |
|--------|------|-------|
| id | bigint PK | |
| user_id | bigint FK->users | SET NULL |
| donor_id | bigint FK->donors | SET NULL |
| campaign_id | bigint FK->campaigns | CASCADE |
| subscription_id | bigint FK->subscriptions | SET NULL |
| amount | decimal(12,2) | |
| currency | varchar(3) | DEFAULT 'EGP' |
| type | enum('one_time','recurring') | |
| status | varchar(255) | pending/completed/failed/refunded |
| gateway | varchar(255) | stripe / paymob |
| gateway_transaction_id | varchar(255) | NULLABLE |
| idempotency_key | varchar(255) | UNIQUE |
| anonymous | boolean | |
| certificate_uuid | varchar(255) | UNIQUE |

### `subscriptions`
| Column | Type | Notes |
|--------|------|-------|
| id | bigint PK | |
| user_id / donor_id | bigint FK | SET NULL |
| campaign_id | bigint FK | SET NULL |
| gateway_subscription_id | varchar(255) | NULLABLE |
| status | varchar(255) | active/canceled/past_due |
| amount | decimal(15,2) | |
| next_billing_date | timestamp | NULLABLE |
| cancelled_at | timestamp | NULLABLE |

### `hour_logs`
| Column | Type | Notes |
|--------|------|-------|
| id | bigint PK | |
| volunteer_id | bigint FK->volunteers | CASCADE |
| attendance_log_id | bigint FK->attendance_logs | CASCADE |
| calculated_hours | decimal(6,2) | |
| approved_hours | decimal(6,2) | NULLABLE |
| status | enum('pending_review','approved','adjusted','rejected') | DEFAULT 'pending_review' |
| approved_by | bigint FK->users | SET NULL |
| approved_at | timestamp | NULLABLE |

### `financial_logs`
Append-only audit table. No updates or deletes.
| Column | Type | Notes |
|--------|------|-------|
| id | bigint PK | |
| user_id / donor_id / donation_id / campaign_id | bigint FK | SET NULL |
| transaction_type | varchar(255) | payment/refund/failed/adjustment |
| amount | decimal(12,2) | |
| status | varchar(255) | |
| gateway | varchar(255) | NULLABLE |
| metadata | json | NULLABLE |
| old_values / new_values | json | NULLABLE |
| hash | varchar(255) | Integrity hash |
| created_at | timestamp | INDEX, no updated_at |

---

## Key Relationships

```
User ──hasMany──> Donation
User ──hasOne──> Volunteer
User ──hasMany──> Subscription

Donor ──hasMany──> Donation
Donor ──hasMany──> Subscription
Donor ──hasMany──> Certificate

Campaign ──hasMany──> Donation
Campaign ──hasMany──> ImpactReport

Donation ──belongsTo──> Campaign
Donation ──hasOne──> Certificate
Donation ──hasOne──> Refund

Subscription ──hasMany──> Donation

Volunteer ──hasMany──> HourLog
Volunteer ──hasMany──> AttendanceLog
Volunteer ──belongsToMany──> VolunteerSchedule (pivot)

AttendanceLog ──hasOne──> HourLog
VolunteerShift ──hasMany──> AttendanceLog
VolunteerEvent ──hasMany──> VolunteerShift
```

---

## Full Table List

users, password_reset_tokens, sessions, donors, campaigns, donations,
subscriptions, volunteers, volunteer_hours, volunteer_schedules,
volunteer_schedule_user, volunteer_events, volunteer_shifts,
volunteer_applications, volunteer_slot_requests, attendance_logs,
hour_logs, certificates, refunds, payment_webhooks, impact_reports,
beneficiary_locations, impact_photos, financial_logs, idempotency_keys,
activity_log, notifications, cache, cache_locks, jobs, job_batches, failed_jobs
