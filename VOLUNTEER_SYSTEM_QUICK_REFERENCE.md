# Quick Reference Guide - Volunteer System

## 🎯 System Overview

The volunteer system is now a **complete opportunity-driven platform** with discovery, application, shift management, and attendance tracking.

---

## 🌐 User Journeys

### Visitor → Volunteer Journey
```
1. Visit /volunteering
   └─ Browse opportunities, search, filter

2. Click "View Details" on opportunity
   └─ See full description, requirements, dates, skills

3. Click "Apply Now"
   └─ If not logged in → Redirect to login
   └─ If logged in → Show application form

4. Fill application form
   └─ Why volunteer? (motivation)
   └─ What skills? (skills_offered)
   └─ Experience? (optional)
   └─ When available? (availability)
   └─ Notes? (optional)

5. Submit application
   └─ Status: PENDING
   └─ Page: Shows "Application under review"
   └─ Admin: Receives notification

6. Wait for approval
   └─ Admin reviews in Filament panel
   └─ Admin approves/rejects
   └─ Email notification sent to volunteer

7. If APPROVED → See available shifts
   └─ Click "Request Slot"
   └─ System checks for conflicts (ShiftConflictService)
   └─ Slot request: PENDING

8. Admin reviews slot request
   └─ Approves → Status: APPROVED
   └─ Rejects → Status: REJECTED

9. If APPROVED → Ready to volunteer
   └─ On shift day: Click "Check In"
   └─ During shift: Work
   └─ After shift: Click "Check Out"
   └─ Hours calculated automatically

10. Admin reviews hours
    └─ Approves → Status: APPROVED
    └─ Hours added to volunteer profile
    └─ Stats update on dashboard
```

---

## 📍 Key URLs (Volunteer)

| Action | URL | Method | Auth |
|--------|-----|--------|------|
| Browse opportunities | `/volunteering` | GET | No |
| View opportunity | `/volunteering/{slug}` | GET | No |
| Start application | `/volunteering/{slug}/apply` | GET | Yes |
| Submit application | `/volunteering/{slug}/apply` | POST | Yes |
| Dashboard | `/volunteer/dashboard` | GET | Yes |
| Request shift | `/volunteer/shifts/request` | POST | Yes |
| Cancel shift request | `/volunteer/shifts/requests/{id}/cancel` | PATCH | Yes |
| Check in | `/volunteer/attendance/check-in` | POST | Yes |
| Check out | `/volunteer/attendance/{log}/check-out` | POST | Yes |

---

## 🎛️ Admin URLs (Filament)

| Resource | URL | Purpose |
|----------|-----|---------|
| Opportunities | `/admin/volunteer-events` | Create/manage volunteer opportunities |
| Applications | `/admin/volunteer-applications` | Review & approve/reject applications |
| Shifts | `/admin/volunteer-shifts` | Create shifts for opportunities |
| Slot Requests | `/admin/slot-requests` | Approve/reject shift requests |
| Attendance | `/admin/attendance-logs` | View check-in/check-out records |
| Hours | `/admin/hour-logs` | Approve volunteer hours |

---

## 📊 Database Tables

```
volunteer_opportunities ← VolunteerEvent (main opportunity)
├─ volunteer_applications (user applies to opportunity)
├─ volunteer_shifts (shifts within opportunity)
│  └─ volunteer_slot_requests (volunteer requests slot)
│     └─ attendance_logs (check-in/out)
│        └─ hour_logs (hours worked)
```

### Key Fields

**volunteer_opportunities**
- title, slug, description, location
- event_type, category, status (open, full, completed, cancelled, draft)
- start_date, end_date, registration_deadline
- required_skills (JSON), max_volunteers
- cover_image, banner_image, gallery (JSON)

**volunteer_applications**
- event_id, user_id
- motivation, skills_offered, experience, availability, notes
- status (pending, approved, rejected)
- admin_notes, reviewed_by, reviewed_at

**volunteer_shifts**
- event_id
- title, description, shift_date, start_time, end_time
- required_volunteers, assigned_count
- status (open, full)

**volunteer_slot_requests**
- volunteer_id, shift_id
- status (pending, approved, rejected, cancelled, completed)
- requested_date, requested_start_time, requested_end_time
- approved_at, rejected_at, rejection_reason

**attendance_logs**
- volunteer_id, shift_id
- check_in, check_out
- status (checked_in, checked_out, verified)

**hour_logs**
- volunteer_id, attendance_log_id
- calculated_hours, adjusted_hours
- status (pending_review, approved, adjusted, rejected)
- reviewed_by, reviewed_at

---

## 🔄 Admin Workflow

### Create Opportunity
```
1. Go to /admin/volunteer-events
2. Click "Create"
3. Fill form:
   - Title (auto-generates slug)
   - Type & Category
   - Description, Requirements, Benefits
   - Location, Dates, Deadline
   - Max volunteers, Skills
   - Images (cover, banner, gallery)
   - Status: "Open" (makes public)
4. Save
```

### Create Shifts
```
1. Go to /admin/volunteer-shifts
2. Click "Create"
3. Select Event (opportunity)
4. Fill:
   - Title & Description
   - Date, Start Time, End Time
   - Required Volunteers
   - Location
5. Save
6. Repeat for each shift time
```

### Review Applications
```
1. Go to /admin/volunteer-applications
2. Filter by Status: "Pending"
3. Click "View" to see full application
4. Click "Approve" or "Reject"
5. Add optional notes
6. System sends email to volunteer
```

### Approve Shifts
```
1. Go to /admin/slot-requests
2. Filter by Status: "Pending"
3. Click "Approve" or "Reject"
4. If reject, add reason
5. System sends email & updates dashboard
```

### Approve Hours
```
1. Go to /admin/hour-logs
2. Filter by Status: "Pending Review"
3. Click "Approve" (or "Adjust" if needed)
4. System updates volunteer's total_approved_hours
5. Volunteer notified via email
```

---

## 🛡️ Conflict Detection (ShiftConflictService)

Located: `app/Services/ShiftConflictService.php`

### Validation Sequence
When volunteer requests a shift:

```
Check 1: Is shift full?
  └─ if yes → ERROR: "Shift at full capacity"

Check 2: Is registration deadline passed?
  └─ if yes → ERROR: "Deadline passed"

Check 3: Already requested this shift?
  └─ if yes → ERROR: "Already requested this shift"

Check 4: Approved for this opportunity?
  └─ if no → ERROR: "Must be approved for opportunity first"

Check 5: Overlapping shifts on same day?
  └─ if yes → ERROR: "Conflicts with [other shift]"

ALL PASS → Request created with status: PENDING
```

### Overlap Logic
```
Overlap occurs when:
  Start1 < End2 AND Start2 < End1

Examples:
  09:00-13:00 vs 13:00-17:00 → NO overlap (end=start is OK)
  09:00-13:00 vs 12:00-14:00 → OVERLAP (12:00-13:00)
  09:00-13:00 vs 14:00-18:00 → NO overlap (different times)
```

---

## 🎨 Frontend Structure

### Public Pages (Blade Templates)
- `resources/views/pages/volunteering-opportunities.blade.php` - List page ✅
- `resources/views/pages/volunteering-opportunity-detail.blade.php` - Detail page ✅
- `resources/views/pages/volunteering-apply.blade.php` - Application form ✅

### Authenticated Pages
- `resources/views/pages/volunteer-dashboard.blade.php` - Dashboard ✅
- `resources/views/pages/volunteer-profile-edit.blade.php` - Profile ✅

### Controllers (Request Handling)
- `app/Http/Controllers/VolunteerController.php`
  - `opportunities()` - List page
  - `showOpportunity()` - Detail page
  - `dashboard()` - Dashboard
  - `profile()` - Edit profile
  - `register()` - Save profile

- `app/Http/Controllers/Volunteer/ApplicationController.php`
  - `create()` - Show form
  - `store()` - Submit form

- `app/Http/Controllers/Volunteer/ShiftRequestController.php`
  - `store()` - Request shift (uses ShiftConflictService)
  - `cancel()` - Cancel request
  - `approve()` - Admin approves
  - `reject()` - Admin rejects

- `app/Http/Controllers/Volunteer/AttendanceController.php`
  - `selfCheckIn()` - Check in
  - `selfCheckOut()` - Check out

### Admin Resources (Filament)
- `app/Filament/Resources/VolunteerEventResource.php` - Opportunities
- `app/Filament/Resources/VolunteerApplicationResource.php` - Applications
- `app/Filament/Resources/VolunteerShiftResource.php` - Shifts
- `app/Filament/Resources/SlotRequestResource.php` - Slot requests
- `app/Filament/Resources/AttendanceLogResource.php` - Attendance
- `app/Filament/Resources/HourLogResource.php` - Hours

---

## 📋 Model Methods Reference

### VolunteerEvent
```php
$event->shifts()                    // Get shifts
$event->applications()              // Get applications
$event->approvedApplications()      // Get approved only
$event->getTotalAssignedAttribute() // Total assigned count
$event->getIsFullAttribute()        // Is full?
$event->scopeOpen($query)           // Open status only
$event->scopeUpcoming($query)       // Future & open
```

### VolunteerApplication
```php
$app->approve($adminId, $notes)     // Mark as approved
$app->reject($adminId, $notes)      // Mark as rejected
$app->getIsApprovedAttribute()      // Boolean
$app->getStatusColorAttribute()     // Color for badge
$app->scopeApproved($query)         // Filter approved
$app->scopePending($query)          // Filter pending
```

### VolunteerShift
```php
$shift->slotRequests()              // Get requests
$shift->approvedRequests()          // Get approved only
$shift->attendanceLogs()            // Get attendance records
$shift->incrementAssignedCount()    // Add 1 volunteer
$shift->decrementAssignedCount()    // Remove 1 volunteer
$shift->getIsFullAttribute()        // Is full?
$shift->getDurationHoursAttribute() // Hours duration
$shift->getAvailableSpotsAttribute() // Spots left
$shift->generateQrToken()           // Create QR code
$shift->isQrTokenValid($token)      // Validate QR
```

### Volunteer
```php
$vol->hasShiftConflict($shift)      // Check conflict
$vol->hasConflict($schedule)        // Check conflict
$vol->approve($admin)               // Mark approved
$vol->reject($admin, $reason)       // Mark rejected
$vol->suspend($admin, $reason)      // Mark suspended
$vol->reactivate()                  // Mark approved again
$vol->addApprovedHours($hours)      // Add to total
$vol->scopeApproved($query)         // Filter approved
$vol->scopePending($query)          // Filter pending
```

---

## 📧 Email Events

**Sent To Admin:**
- Volunteer application submitted
- Volunteer shift request submitted

**Sent To Volunteer:**
- Application approved/rejected
- Shift request approved/rejected
- Hours approved/adjusted/rejected
- Daily digest (optional)

---

## 🔐 Access Control

| Route | Auth | Volunteer Status | Admin |
|-------|------|------------------|-------|
| `/volunteering` | No | N/A | Can create |
| `/volunteering/{slug}/apply` | Yes | Any | Can override |
| `/volunteer/dashboard` | Yes | Any | Own only |
| `/volunteer/shifts/request` | Yes | Approved | Own only |
| `/admin/*` | Yes | N/A | Only |

---

## 🚀 Deployment Checklist

```bash
# 1. Run migrations
php artisan migrate

# 2. Clear everything
php artisan cache:clear
php artisan view:clear
php artisan config:cache

# 3. Link storage
php artisan storage:link

# 4. Start queue (for emails)
php artisan queue:work &

# 5. Test URLs
curl http://yoursite.com/volunteering
curl http://yoursite.com/admin/volunteer-events
```

---

## 🎯 Common Tasks

### As Admin

**Create a new opportunity:**
```
1. /admin/volunteer-events → Create
2. Fill title, description, dates
3. Status: "Open"
4. Save
```

**Create shifts for opportunity:**
```
1. /admin/volunteer-shifts → Create (3x for 3 shifts)
2. Select event
3. Set date, time, required_volunteers
4. Save
```

**Review applications:**
```
1. /admin/volunteer-applications
2. Filter: Status = "Pending"
3. View each application
4. Approve/Reject with reason
```

**Approve hours:**
```
1. /admin/hour-logs
2. Filter: Status = "Pending Review"
3. View details
4. Approve (or Adjust)
```

### As Volunteer

**Find opportunity:**
```
1. /volunteering
2. Search or filter
3. Click "View Details"
```

**Apply:**
```
1. Click "Apply Now"
2. Fill form (motivation, skills, availability)
3. Submit
4. Wait for email
```

**Request shift:**
```
1. /volunteer/dashboard
2. Click "View Shifts"
3. Click "Request Slot"
4. Wait for approval
```

**Check in/out:**
```
1. /volunteer/dashboard
2. On shift day: "Check In"
3. After shift: "Check Out"
4. Hours calculated automatically
```

---

## 📊 Important Queries

### Get pending applications
```php
VolunteerApplication::pending()->get()
```

### Get volunteer's approved opportunities
```php
VolunteerApplication::where('user_id', $userId)
    ->where('status', 'approved')
    ->with('event')
    ->get()
```

### Get volunteer's approved shifts
```php
VolunteerSlotRequest::where('volunteer_id', $volId)
    ->where('status', 'approved')
    ->with('shift.event')
    ->get()
```

### Calculate total approved hours
```php
HourLog::where('volunteer_id', $volId)
    ->where('status', 'approved')
    ->sum('calculated_hours')
```

### Get upcoming shifts
```php
VolunteerShift::where('shift_date', '>=', today())
    ->with('event', 'slotRequests')
    ->orderBy('shift_date')
    ->get()
```

---

## 🐛 Common Issues & Fixes

| Issue | Cause | Fix |
|-------|-------|-----|
| Opportunity not showing | Status not "open" | Set status to "open" in admin |
| Can't request shift | Not approved for opportunity | Admin must approve application first |
| Conflict error | Time overlap detected | Check existing approved shifts |
| Hours not showing | Not checked out | Check AttendanceLog has check_out time |
| Admin can't see resource | Missing Filament resource | Check `app/Filament/Resources/` |
| Images not showing | Wrong path or no storage link | Run `php artisan storage:link` |

---

## 📞 Quick Reference

- **Architecture Doc:** `VOLUNTEER_SYSTEM_ARCHITECTURE.md`
- **Testing Guide:** `VOLUNTEER_SYSTEM_TESTING_CHECKLIST.md`
- **Complete Summary:** `VOLUNTEER_SYSTEM_COMPLETE.md` (this file)
- **ShiftConflictService:** `app/Services/ShiftConflictService.php`
- **Main Controller:** `app/Http/Controllers/VolunteerController.php`
- **Routes:** `routes/web.php` (search "volunteer" or "volunteering")

---

**Status: ✅ PRODUCTION READY**

All components implemented, tested, and documented. Ready for deployment!
