# CharityHub Volunteer System Architecture

## Overview

The volunteer system has been completely redesigned to provide a modern, opportunity-driven workflow similar to real nonprofit platforms and volunteer portals.

**Key Philosophy:** Volunteers discover opportunities, apply, get approved for specific opportunities, then request shifts within those approved opportunities.

---

## 1. System Flow

### Stage 1: Opportunity Discovery
```
Public Visitor
    ↓
Browse Volunteering Opportunities Page
    ↓
View Opportunity Details
    ↓
[Unauthenticated → Login/Register]
    ↓
Click "Apply Now"
```

### Stage 2: Opportunity Application
```
Authenticated User
    ↓
Complete Application Form
    (motivation, skills, experience, availability)
    ↓
Submit Application
    ↓
Status: PENDING
    ↓
Admin Reviews Application
```

### Stage 3: Application Decision
```
Admin Reviews
    ↓
[Approve | Reject]
    ↓
If APPROVED: Volunteer unlocks Shift Selection
If REJECTED: Volunteer cannot request shifts
```

### Stage 4: Shift/Slot Selection
```
Approved Volunteer
    ↓
Visits Approved Opportunity Details
    ↓
Sees Available Shifts (ONLY for this opportunity)
    ↓
Requests Shift
    ↓
[CONFLICT DETECTION RUNS]
    ├─ Overlapping times?
    ├─ Duplicate request?
    ├─ Shift full?
    ├─ Deadline passed?
    └─ Still valid → Create PENDING slot request
    ↓
Admin Reviews Slot Request
    ↓
[Approve | Reject]
```

### Stage 5: Attendance & Hour Tracking
```
Shift Approved
    ↓
Volunteer Checks In
    ↓
Volunteer Checks Out
    ↓
System Calculates Hours
    ↓
Admin Reviews Hours
    ↓
Hours Added to Profile
```

---

## 2. Database Tables

### `volunteer_opportunities` (renamed from `volunteer_events`)
- Stores public volunteer opportunities
- **Key Fields:**
  - `id`, `created_by`, `campaign_id`
  - `title`, `slug`, `description`
  - `location`, `latitude`, `longitude`
  - `event_type`, `category`
  - `required_skills` (JSON array)
  - `max_volunteers`, `cover_image`, `banner_image`
  - `gallery` (JSON array)
  - `registration_deadline`, `start_date`, `end_date`
  - `status` (open, full, completed, cancelled, draft)
  - `requirements`, `benefits`

### `volunteer_applications`
- Links users to opportunities they've applied for
- **Key Fields:**
  - `id`, `event_id`, `user_id`
  - `motivation`, `skills_offered`, `experience`
  - `availability`, `notes`
  - `status` (pending, approved, rejected)
  - `admin_notes`, `reviewed_by`, `reviewed_at`
  - **Constraint:** One application per user per event (unique)

### `volunteer_shifts`
- Individual shifts within an opportunity
- **Key Fields:**
  - `id`, `event_id`
  - `title`, `description`
  - `shift_date`, `start_time`, `end_time`
  - `required_volunteers`, `assigned_count`
  - `location`, `qr_code`, `qr_token`, `qr_expires_at`
  - `status` (open, full)

### `volunteer_slot_requests`
- Volunteer's request to join a shift
- **Key Fields:**
  - `id`, `volunteer_id`, `shift_id`, `campaign_id`
  - `requested_date`, `requested_start_time`, `requested_end_time`
  - `notes`, `admin_notes`
  - `status` (pending, approved, rejected, cancelled)
  - `requested_at`, `approved_at`, `approved_by`
  - `rejected_at`, `rejected_by`, `rejection_reason`
  - `cancelled_at`, `completed_at`

### `attendance_logs`
- Check-in/check-out records
- **Key Fields:**
  - `id`, `volunteer_id`, `shift_id`
  - `check_in`, `check_out`
  - `status` (checked_in, checked_out, verified)

### `hour_logs`
- Approved volunteer hours
- **Key Fields:**
  - `id`, `volunteer_id`, `attendance_log_id`
  - `calculated_hours`, `adjusted_hours`
  - `status` (pending_review, approved, adjusted, rejected)
  - `reviewed_by`, `reviewed_at`, `approval_notes`

---

## 3. Models & Relationships

### VolunteerEvent (Opportunity)
```php
- belongsTo(Campaign)
- belongsTo(User, 'created_by') // creator
- hasMany(VolunteerShift, 'event_id')
- hasMany(VolunteerApplication, 'event_id')
- hasMany(VolunteerApplication, 'event_id')->where('status', 'approved')
```

### VolunteerApplication
```php
- belongsTo(VolunteerEvent, 'event_id')
- belongsTo(User)
- belongsTo(User, 'reviewed_by') // reviewer
- Methods: approve(), reject()
```

### VolunteerShift
```php
- belongsTo(VolunteerEvent, 'event_id')
- hasMany(VolunteerSlotRequest, 'shift_id')
- hasMany(VolunteerSlotRequest, 'shift_id')->where('status', 'approved')
- hasMany(AttendanceLog, 'shift_id')
- Methods: 
  - generateQrToken(), isQrTokenValid()
  - incrementAssignedCount(), decrementAssignedCount()
```

### VolunteerSlotRequest
```php
- belongsTo(Volunteer)
- belongsTo(VolunteerShift, 'shift_id')
- belongsTo(Campaign)
- belongsTo(User, 'approved_by')
- belongsTo(User, 'rejected_by')
```

### Volunteer
```php
- belongsTo(User)
- belongsTo(User, 'approved_by')
- hasMany(VolunteerHour)
- hasMany(HourLog)
- hasMany(AttendanceLog)
- hasMany(VolunteerSlotRequest)
- belongsToMany(VolunteerSchedule)
- Methods:
  - hasShiftConflict(VolunteerShift): bool
  - hasConflict(VolunteerSchedule): bool
  - approve(), reject(), suspend(), reactivate()
  - addApprovedHours()
```

---

## 4. Services

### ShiftConflictService
**Location:** `app/Services/ShiftConflictService.php`

Performs comprehensive validation before a volunteer can request a shift:

```php
validate(Volunteer $volunteer, VolunteerShift $shift): ?string
```

**Checks:**
1. ✅ Is shift full? → "This shift is at full capacity"
2. ✅ Is registration expired? → "Deadline has passed"
3. ✅ Duplicate request? → "Already have request for this shift"
4. ✅ Approved for opportunity? → "Must be approved first"
5. ✅ Overlapping shifts? → "Conflicts with existing shift"

**Related Methods:**
- `hasConflict()` - Boolean check
- `findConflict()` - Returns conflicting request
- `isShiftFull()` - Capacity check
- `isRegistrationExpired()` - Deadline check
- `hasDuplicateRequest()` - Duplicate check
- `isApprovedForOpportunity()` - Scoping check

---

## 5. Controllers

### VolunteerController
**Location:** `app/Http/Controllers/VolunteerController.php`

**Public Routes:**
- `opportunities()` - List all open opportunities (public)
- `showOpportunity()` - Show opportunity detail with shifts (if approved)
- `profile()` - Edit volunteer profile
- `register()` - Create/update volunteer registration
- `dashboard()` - Volunteer dashboard with stats & history

**Data Provided to Dashboard:**
- Total approved hours, pending hours, completed shifts
- My applications (approved, pending, rejected)
- Upcoming approved shifts
- Active check-ins
- Attendance history
- Hour logs
- Available opportunities to browse

### ApplicationController (Volunteer)
**Location:** `app/Http/Controllers/Volunteer/ApplicationController.php`

**Routes:**
- `create()` - Show application form for opportunity
- `store()` - Submit application

**Validations:**
- Registration deadline passed?
- Already applied?
- All required fields present (motivation, skills, availability)

### ShiftRequestController (Volunteer)
**Location:** `app/Http/Controllers/Volunteer/ShiftRequestController.php`

**Routes:**
- `store()` - Request a shift (uses ShiftConflictService)
- `cancel()` - Cancel pending request
- `approve()` - Admin approves request
- `reject()` - Admin rejects request

### AttendanceController (Volunteer)
**Location:** `app/Http/Controllers/Volunteer/AttendanceController.php`

**Routes:**
- `selfCheckIn()` - Volunteer checks in to shift
- `selfCheckOut()` - Volunteer checks out (creates HourLog)

---

## 6. Views/Pages

### Public Pages

#### `/volunteering`
**File:** `resources/views/pages/volunteering-opportunities.blade.php`

Modern responsive grid/card layout showing all open opportunities.

**Features:**
- Hero section with search
- Category filters
- Search functionality
- Opportunity cards with:
  - Image/banner
  - Title & description
  - Location & dates
  - Spots remaining
  - Category badge
  - Status badge (Open/Full/Closed)
  - Required skills tags
  - Campaign association
  - "View Details" CTA

**UI/UX:**
- Gradient hero background
- Smooth hover animations
- Responsive grid (1→2→3 columns)
- Live search & filter
- Modern design pattern (nonprofit platforms style)

#### `/volunteering/{slug}`
**File:** `resources/views/pages/volunteering-opportunity-detail.blade.php`

Detailed opportunity page with full information.

**Features:**
- Full description & requirements
- Volunteer benefits
- Gallery images
- Location & map info
- Required skills
- Registration deadline countdown
- "Apply Now" CTA (if not applied)
- Application status display
- Available shifts (if approved)
- Related campaign link
- Key details sidebar

**Application States:**
- Not applied → "Apply Now" button
- Application pending → "Under review" message
- Application approved → Can request shifts
- Application rejected → Can't request shifts

### Authenticated Pages

#### `/volunteer/dashboard`
**File:** `resources/views/pages/volunteer-dashboard.blade.php`

Volunteer's personal dashboard.

**Sections:**
1. **Header:** Welcome, status badge, quick actions
2. **Stats:** Approved hours, pending hours, completed shifts, donations
3. **My Opportunity Applications:**
   - List of all applications
   - Status badge (pending/approved/rejected)
   - "View Shifts" link if approved
   - Applied date
4. **Upcoming Approved Shifts:**
   - List of shifts from approved opportunities
   - Date/time with visual calendar cell
   - Event title & shift description
   - Check-in button (if upcoming)
5. **Active Check-Ins:**
   - Any ongoing shifts
   - Check-out button
   - Duration so far
6. **Attendance History:**
   - Past 10 attended shifts
   - Date, duration, event
7. **Hour Logs:**
   - Pending hours for review
   - Approved hours
   - Rejected hours with reason
8. **Available Opportunities:**
   - Browse more opportunities to apply
   - Quick apply shortcuts

#### `/volunteer/profile`
**File:** `resources/views/pages/volunteer-profile-edit.blade.php`

Edit volunteer profile information.

**Editable Fields:**
- Name, email, phone
- Skills (tags/array)
- Bio
- Emergency contact info
- Date of birth
- Gender, address
- Profile photo

---

## 7. Filament Admin Resources

### VolunteerEventResource
**Location:** `app/Filament/Resources/VolunteerEventResource.php`

Create and manage volunteer opportunities.

**Form Sections:**
1. **Opportunity Details:**
   - Title, slug (auto-generated)
   - Description (rich editor)
   - Type & category
   - Requirements & benefits
2. **Location & Schedule:**
   - Location
   - Max volunteers
   - Start/end dates
   - Registration deadline
3. **Skills & Media:**
   - Required skills (tags)
   - Status (draft/open/full/completed/cancelled)
   - Cover image, banner image
   - Gallery images (multiple)

**Table Features:**
- Search by title
- Filter by status
- Show applications count
- Show shifts count
- Direct link to public page
- Bulk delete

### VolunteerApplicationResource
**Location:** `app/Filament/Resources/VolunteerApplicationResource.php`

Review and manage opportunity applications.

**Table Columns:**
- Applicant name (searchable)
- Opportunity title
- Availability
- Status badge (pending/approved/rejected)
- Applied date
- Reviewed date (toggleable)

**Filters:**
- By status
- By opportunity

**Actions:**
- **Approve** (with optional notes)
- **Reject** (requires reason)
- View full application
- Edit
- Bulk approve

**Notifications:** Email sent to volunteer on approve/reject

### VolunteerShiftResource
**Location:** `app/Filament/Resources/VolunteerShiftResource.php`

Create and manage shifts for opportunities.

**Form:**
- Event (opportunity) selection
- Shift title & description
- Date & time (start/end)
- Required volunteers count
- Location
- QR code generation

**Table Features:**
- Show date & time
- Show assigned vs required count
- Show status (open/full)
- Quick edit

### SlotRequestResource
**Location:** `app/Filament/Resources/SlotRequestResource.php`

Review volunteer's shift requests.

**Table Columns:**
- Volunteer name
- Opportunity & shift
- Requested date & time
- Status badge
- Requested date

**Filters:**
- By status
- By opportunity
- By volunteer

**Actions:**
- **Approve** (confirms volunteering for this shift)
- **Reject** (with optional reason)
- View details

**Notifications:** Email sent to volunteer on approve/reject

### AttendanceLogResource
**Location:** `app/Filament/Resources/AttendanceLogResource.php`

Track check-in/check-out records.

**Features:**
- List all attendance records
- Manual check-in/check-out (admin)
- Duration calculation
- Link to hour log review

### HourLogResource
**Location:** `app/Filament/Resources/HourLogResource.php`

Approve volunteer hours.

**Workflow:**
1. Attendance log created (check-out)
2. Hours automatically calculated
3. Admin reviews
4. Admin approves/adjusts/rejects
5. Volunteer's `total_approved_hours` updated

**Actions:**
- Approve
- Adjust hours (with reason)
- Reject (with reason)

---

## 8. Routes

### Public Routes
```php
GET  /volunteering                          → volunteering.index
GET  /volunteering/{slug}                   → volunteering.show
GET  /volunteering/{slug}/apply             → volunteering.apply (requires auth)
POST /volunteering/{slug}/apply             → volunteering.apply.store (requires auth)
```

### Legacy Redirect
```php
GET  /volunteer                             → redirects to volunteering.index
```

### Authenticated Routes
```php
GET  /volunteer/dashboard                   → volunteer.dashboard
GET  /volunteer/profile                     → volunteer.profile.edit
POST /volunteer/register                    → volunteer.register

POST /volunteer/shifts/request              → volunteer.shifts.request
PATCH /volunteer/shifts/requests/{id}/cancel → volunteer.shifts.requests.cancel

POST /volunteer/attendance/check-in         → volunteer.attendance.check-in
POST /volunteer/attendance/{log}/check-out  → volunteer.attendance.check-out
```

---

## 9. Key Features

### ✅ Conflict Detection
- **Automatic validation** when volunteer requests shift
- **Prevents overlapping shifts** across opportunities
- **Prevents duplicate requests** for same shift
- **Validates capacity limits**
- **Checks registration deadlines**
- **Enforces opportunity approval requirement**

**Example Scenario:**
```
Volunteer requests:
  - Food Distribution: 10 AM → 2 PM (approved)
  
Then requests:
  - Medical Support: 11 AM → 1 PM (same day)
  
Result: BLOCKED
Error: "Conflicts with Food Distribution Shift"
```

### ✅ Opportunity Scoping
- Volunteers can **only see shifts** from opportunities they're approved for
- Prevents confusion & abuse
- Improves UX (only relevant shifts visible)

### ✅ Modern UI/UX
- **Opportunity grid** mimics job boards (AngelList, LinkedIn)
- **Responsive design** (mobile-first)
- **Smooth animations** & transitions
- **Intuitive CTAs** ("Apply Now", "Request Slot")
- **Status badges** provide clarity
- **Clear deadlines** & requirements

### ✅ Multi-Stage Workflow
1. Opportunity Discovery
2. Application Submission
3. Admin Review
4. Shift Selection (if approved)
5. Attendance Tracking
6. Hour Approval
7. Certificate Generation (downstream)

### ✅ Notifications
- Application submitted → Admin notified
- Application approved/rejected → Volunteer notified
- Shift request submitted → Admin notified
- Shift request approved/rejected → Volunteer notified
- Hours approved → Volunteer notified

### ✅ Admin Dashboard
- All resources accessible from Filament admin panel
- Create opportunities & manage shifts
- Review applications & slot requests
- Approve volunteer hours
- Bulk actions for efficiency
- Dashboard stats & charts (future)

---

## 10. Data Flow Diagram

```
┌─────────────────────────────────────────────────────────────────┐
│                    PUBLIC VISITOR                               │
└─────────────────────────────────────────────────────────────────┘
                            ↓
                 Browse Opportunities
                (GET /volunteering)
                            ↓
            ┌───────────────┴───────────────┐
            ↓                               ↓
      Apply Now              Unauthenticated?
      (GET /apply)           YES → Login
            ↓                     ↓
            └───────────────┬───────┘
                            ↓
                   Submit Application Form
                  (POST /volunteering/apply)
                            ↓
                 volunteer_applications table
                   (status: pending)
                            ↓
┌─────────────────────────────────────────────────────────────────┐
│                    ADMIN REVIEW                                  │
└─────────────────────────────────────────────────────────────────┘
                            ↓
                  Filament: Applications Panel
                            ↓
                    ┌───────┴────────┐
                    ↓                ↓
                APPROVE         REJECT
                    ↓                ↓
            Status: approved   Status: rejected
                    ↓
        Volunteer Notified (Email)
                    ↓
┌─────────────────────────────────────────────────────────────────┐
│                APPROVED VOLUNTEER                                │
└─────────────────────────────────────────────────────────────────┘
                            ↓
            Visit Opportunity Details
          (Can now see "Available Shifts")
                            ↓
            Request Shift (POST /shifts/request)
                            ↓
         ShiftConflictService.validate()
                            ↓
         ┌─────────────────┴────────────────┐
         ↓                                  ↓
    VALID                            CONFLICT/INVALID
         ↓                                  ↓
  volunteer_slot_requests           Error Message
   (status: pending)                (back to volunteer)
         ↓
    Admin Reviews (Filament)
         ↓
    ┌────────┴────────┐
    ↓                 ↓
 APPROVE          REJECT
    ↓                 ↓
Status: approved  Volunteer Notified
    ↓
┌─────────────────────────────────────────────────────────────────┐
│              SHIFT ATTENDANCE                                    │
└─────────────────────────────────────────────────────────────────┘
                            ↓
            Volunteer Checks In
         (POST /attendance/check-in)
                            ↓
            attendance_logs (check_in: timestamp)
                            ↓
            Volunteer Checks Out
         (POST /attendance/check-out)
                            ↓
       System Calculates Hours (duration)
       Creates hour_log (pending_review)
                            ↓
         Admin Reviews Hours (Filament)
                            ↓
              ┌──────┴──────┐
              ↓             ↓
          APPROVE      ADJUST/REJECT
              ↓             ↓
    volunteer.total_approved_hours
              += calculated_hours
```

---

## 11. Environment & Dependencies

### Key Packages
- **Filament v3** - Admin panel
- **Spatie Activity Log** - Audit trails
- **Laravel** - Framework
- **Livewire** - Real-time updates (as needed)
- **Stripe/Paymob** - Payment processing (for donations)

### Database Migrations
All tables have been created via migrations:
- `2026_05_12_000002_create_volunteer_applications_table.php`
- `2026_05_11_000003_create_volunteer_shifts_table.php`
- And others for attendance, hours, etc.

---

## 12. Next Steps & Future Enhancements

### Short-term
- [ ] Email notifications setup (verify templates)
- [ ] QR code generation for shift check-in
- [ ] Volunteer certificate generation
- [ ] Dashboard charts & analytics

### Medium-term
- [ ] Calendar view for shifts
- [ ] Volunteer search/filtering
- [ ] Impact metrics dashboard
- [ ] Volunteer hours leaderboard

### Long-term
- [ ] Mobile app (React Native)
- [ ] Integration with calendar systems
- [ ] Advanced scheduling algorithms
- [ ] Volunteer peer reviews
- [ ] Gamification (badges, points)

---

## 13. Security & Access Control

### Authentication
- All protected routes require `auth` middleware
- Email verification required for some routes

### Authorization
- Volunteers can only view their own data
- Admins access through Filament (role-based)
- Conflict detection prevents data abuse

### Data Protection
- Volunteer applications scoped to opportunities
- Slot requests scoped to individual volunteers
- Hour logs tied to specific attendance records

---

## Summary

The volunteer system is now **fully opportunity-driven**, providing:

✅ **Modern, public-facing opportunities page** (like AngelList, VolunteerMatch)
✅ **Multi-stage application workflow** (discover → apply → approve → shifts → attend → track hours)
✅ **Conflict detection** preventing scheduling conflicts
✅ **Opportunity scoping** so volunteers only see relevant shifts
✅ **Admin panel** for managing opportunities, reviewing applications, and approving hours
✅ **Volunteer dashboard** with comprehensive stats and history
✅ **Responsive, beautiful UI** with smooth animations
✅ **Email notifications** for key events
✅ **Attendance tracking** with automatic hour calculation
✅ **Hour approval workflow** for accurate volunteer tracking

This architecture aligns with real-world nonprofit and volunteer platforms, making it intuitive and professional for both volunteers and administrators.
