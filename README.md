# CharityHub 🌍

**CharityHub** is a comprehensive, modern fundraising and volunteer management platform designed to streamline non-profit operations, enhance donor engagement, and ensure financial transparency.

---

## 🚀 Key Features

### 💰 Fundraising & Donations
*   **Flexible Donations**: Support for one-time and recurring donations (Monthly/Yearly).
*   **Integrated Payment Gateways**: Secure processing via **Stripe** and **Paymob**.
*   **Donor Dashboard**: Personal portal for donors to manage subscriptions, view history, and download tax certificates.
*   **Anonymous Giving**: Option for donors to hide their identity from public lists.

### 🤝 Volunteer Management
*   **Opportunity Portal**: Public-facing list of volunteering events and opportunities.
*   **Application Workflow**: Multi-step application and approval process for volunteers.
*   **Shift Tracking**: Integrated shift management with conflict detection (`ShiftConflictService`).
*   **Attendance & Hours**: Real-time check-in/out and automated hours processing.

### 📊 Operations & Transparency
*   **Immutable Ledger**: An append-only financial audit trail secured with SHA-256 HMAC hashing to prevent tampering.
*   **Impact Reporting**: Detailed reports with **Google Maps** integration to visualize beneficiary locations.
*   **GDPR Compliance**: Built-in tools for data portability, consent tracking, and the "right to be forgotten."
*   **Automated Notifications**: Real-time updates for donation confirmations, volunteer approvals, and shift reminders.

---

## 🛠️ Technical Stack

*   **Framework**: [Laravel 11](https://laravel.com)
*   **Frontend**: Vanilla CSS, Blade Templates, Tailwind CSS
*   **Database**: MySQL / MariaDB
*   **Payments**: Stripe API, Paymob API
*   **Maps**: Google Maps JavaScript API
*   **Authentication**: Laravel Fortify / Breeze (Customized)

---

## ⚙️ Installation & Setup

### Prerequisites
*   PHP 8.2+
*   Composer
*   Node.js & NPM
*   MySQL/MariaDB

### Steps
1.  **Clone the repository**:
    ```bash
    git clone https://github.com/your-repo/CharityHub.git
    cd CharityHub
    ```

2.  **Install dependencies**:
    ```bash
    composer install
    npm install
    npm run build
    ```

3.  **Environment Setup**:
    ```bash
    cp .env.example .env
    php artisan key:generate
    ```
    *Update `.env` with your database and API credentials (Stripe, Paymob, Google Maps).*

4.  **Database Migration**:
    ```bash
    php artisan migrate --seed
    ```

5.  **Run the application**:
    ```bash
    php artisan serve
    ```

---

## 🛡️ Security & Compliance

CharityHub is built with security as a top priority:
*   **Financial Integrity**: All financial logs are signed with HMAC hashes. Any tampering is immediately flagged in the Admin Ledger.
*   **Data Privacy**: Full GDPR compliance suite including encrypted data storage and consent management.
*   **PCI-DSS Compliance**: No sensitive card data touches our servers; all payments are tokenized via Stripe/Paymob.

---

## 📄 Documentation

For more detailed technical information, please refer to the files in the `Documentations/md Files/` directory:
*   [Core Documentation](Documentations/md%20Files/DOCUMENTATION.md)
*   [Volunteer System Architecture](Documentations/md%20Files/VOLUNTEER_SYSTEM_ARCHITECTURE.md)
*   [Payment Flow Details](Documentations/md%20Files/payment-flow.md)
*   [Database Schema](Documentations/md%20Files/database-schema.md)

---

## 👥 Contributors

*   **Mohamed Helmy Elsayed**
*   **Amir Khaled Yousif**
*   **Mostafa Salah Eldin Ali**
*   **Mahmoud Mohamed Mahmoud**

---

## 📄 License

The CharityHub platform is open-sourced software licensed under the [SUT license](LICENSE).
