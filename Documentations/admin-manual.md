# Admin Operations Manual — CharityHub

**Access:** All admin routes at `/admin/*`. Requires `role: admin` or `role: employee`.

**Sidebar navigation:** Dashboard, Donations Ledger, Campaigns, Donors, Volunteers, Volunteering Opportunities, Slot Requests, Hour Logs, Impact Reports, Filament Panel.

---

## Dashboard

`GET /admin`

KPI cards:
- **Financial:** Total raised, active campaigns, total donors
- **Volunteer:** Total volunteers, pending/hour logs/slot requests, active events/attendees

Tables:
- Recent pending volunteer applications (approve/reject)
- Recent pending hour logs (approve/decline)
- Recent donations

---

## Campaign Management

**Legacy panel** at `/admin/manage-campaigns` or **Filament** at `/admin` → Campaigns.

Fields: title, description, goal amount, deadline, status (active/completed/paused/ended), category, cover image, location (lat/lng), featured toggle, social links.

**Create:** Set title, goal, deadline, category, description, upload image.
**Edit:** Update any field, change status.
**Delete:** Removes campaign and cascades to donations.

---

## Donations

**Legacy panel** at `/admin/manage-donations` or **Filament** at `/admin` → Donations.

**Refund a donation:**
1. View donation details
2. Click **"Refund"**
3. Enter amount and reason
4. System processes via Stripe (or local manual refund if no gateway ID)
5. Donation status changes to `refunded`
6. Certificate is **revoked**
7. Campaign progress is **recalculated**

---

## Financial Ledger

`GET /admin/ledger`

Append-only audit log of all financial transactions. Filter by:
- Transaction type (payment, refund, failed, adjustment)
- Status
- Gateway (Stripe, Paymob)
- Date range

---

## Volunteer Management

### Applications
`GET /admin/manage-volunteers`
- **Pending** section: review applicant details (motivation, skills, experience)
- Click **Approve** — creates Volunteer profile, sets status to `active`
- Click **Reject** — sends rejection

### Status Changes
Change volunteer status: pending, approved, active, rejected, suspended, inactive.

---

## Volunteering Opportunities & Shifts

**Create opportunity** at `/admin/manage-schedules/create`:
- Title, description, location, start/end dates
- Category, required skills (JSON), max volunteers
- Registration deadline, status (draft/open)

**Shifts** are managed within an opportunity:
- Add shifts with date, start/end time, required volunteer count
- Remove shifts as needed

---

## Hour Logs Approval

`GET /admin/volunteer-hours`

**Statuses:**
- **Pending Review** (yellow) — awaiting action
- **Approved** (green) — accepted as calculated
- **Adjusted** (blue) — accepted with hours override
- **Declined** (red) — rejected

**Actions per pending log:**
1. **Approve** (green button) — accepts calculated hours as-is → status `approved`
2. **Override hours** in input box + **Approve** → status `adjusted`
3. **Decline** (red button) → status `rejected`, hours not added to volunteer total

---

## Slot Requests

`GET /admin/volunteer-slots`

Volunteer shift requests requiring admin action:
- **Approve** — assigns volunteer to the shift, increments assigned count
- **Reject** — with optional admin notes

---

## Impact Reports

`GET /admin/manage-impact-reports`

Create and publish impact reports:
- Title, outcomes narrative, beneficiary count, funds used
- Add **beneficiary locations** with lat/lng and per-location beneficiary counts
- Upload **impact photos** with captions
- Toggle between **draft** and **published**
- **Download PDF** — auto-generated report

---

## Certificate Management

**Filament** at `/admin` → Certificates.

View all certificates with: donor, campaign, amount, date, status (pending/generated/emailed/revoked).
Download links available.

---

## Donor Management

`GET /admin/donors`

Searchable directory with total donations per donor.
View donor details, donation history, subscriptions.

---

## Filament Panel

Full CRUD panel at `/admin` for all major resources:
Campaigns, Donations, Donors, Subscriptions, Financial Logs, Impact Reports, Volunteers, Volunteer Events, Shifts, Applications, Slot Requests, Attendance Logs, Hour Logs.
