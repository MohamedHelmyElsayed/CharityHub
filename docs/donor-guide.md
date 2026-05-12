# Donor-Facing User Guide — CharityHub

---

## Making a Donation

1. **Browse campaigns** at `/campaigns` to find a cause
2. Click **"Donate"** on a campaign
3. Fill the donation form:
   - Select **one-time** or **monthly recurring**
   - Choose a preset amount (EGP 250/500/1000/2500) or enter custom
   - Enter your **name** and **email**
   - Optionally add a **message** and toggle **anonymous**
   - **Check the GDPR consent box** (required)
4. Click **"Donate Now"** — you are redirected to the payment gateway
5. Complete payment on Stripe or Paymob's secure page
6. You are redirected back to the **success page** with your transaction details

---

## Managing Your Donations

Access your **Donor Dashboard** at `/donor/dashboard`:
- View **total donated** and **lifetime impact**
- See your **giving history** with dates, amounts, campaigns
- Download **donation certificates**
- Manage **recurring subscriptions**:
  - View active subscriptions
  - **Cancel** a subscription (takes effect immediately)

---

## Certificates

Each completed donation generates a digital certificate:
- Receive it via **email**
- Download from your **dashboard**
- **Verify publicly** at `/verify/{uuid}` — donors see masked name (e.g., "J*** Smith") unless anonymous
- Certificates include a **QR code** linking to the verification page
- If a donation is refunded, the certificate is **revoked**

---

## Volunteering

1. Browse opportunities at `/volunteering`
2. Filter by **category**, **location**, or search by keyword
3. Click an opportunity to view details (dates, requirements, skills needed)
4. Click **"Apply Now"** — fill the application form:
   - **Motivation** (why you want to volunteer, min 30 characters)
   - **Skills you can contribute**
   - **Previous experience** (optional)
   - **Availability** (e.g., weekends, mornings)
5. Application is reviewed by admin
6. Once **approved**, you can request shifts from the opportunity page
7. **Check in** on shift day via the volunteer dashboard
8. Hours are calculated upon **check-out** and submitted for admin approval

---

## Profile & Account

Access at `/my-profile`:
- **Update** your name and email
- **Change password** (requires current password)
- **Delete account** — type "DELETE" to confirm. Your data is erased immediately.
