# CRM working flow

This document describes how the current education CRM actually works: roles, menus, permissions, modules, helpers, and the path from lead to placed student.

Use it to build a new CRM with the same working flow. Do not copy the current folder layout. Today each course is a separate controller, view, and permission string. In the new system, course is a record. Mentor, faculty, support, and finance lists are one screen filtered by course and desk.

## How a student moves

A person enters as a **lead**. A telecaller works the call. The student fills a course registration form. Conversion creates a **student** with a fee. That student then moves through finance, academics, support, mentor, faculty, post-sales, and placement.

### 1. Capture

A lead is created with phone, country code, course, source, team, and telecaller. It starts as **Un Touched**. The first source, first course, and first status are stored once and are not overwritten later.

Entry points:

- Telecaller, admin, or general manager adds one lead, or uploads a sheet
- Meta ads (`by_meta`) and the Meta WhatsApp webhook
- Marketing **D2D form**, which first creates a marketing lead
- A public registration link for that course (NIOS, BOSSE, GMVSS, UG/PG, EduMaster, and the skill courses)

Duplicate rule: same country code + phone + course is the same lead.

### 2. Own and call

The telecaller updates status, interest, rating, remarks, and a follow-up date. Each change is an activity. Click-to-call goes through Voxbay. The Call app and NatX later sync recordings.

| Status | What it means |
|---|---|
| Un Touched Leads | Nobody has worked it |
| Follow-up | Call again on a date. This is the Follow-up Leads menu |
| DNP | Did not pick up |
| Demo | Demo given or scheduled |
| Interested to Buy | Warm enough to register or convert |
| Positive | Strong intent |
| May Buy Later | Parked |
| Not-interested IN FULL COURSE | Closed, not buying |
| Disqualified | Not a valid prospect |

### 3. Assign and pull back

A lead belongs to `telecaller_id` and `team_id`.

- A team lead sees the team.
- A senior manager, general manager, admin, or super admin sees a wider set.
- Pullback sets `is_pullbacked`, hides the lead from the normal list, and parks it on **Pullbacked Leads** until a general manager assigns it again.

### 4. Register

Staff sends the public form for that course. The student submits subjects, batch, and documents. The lead then appears on **Registration Form Submitted**.

### 5. Convert

Super admin, admin, telecaller, and general manager can convert. Admission counsellor, academic assistant, finance, post-sales, and auditor cannot.

Conversion confirms name, phone, email, date of birth, board, and batch. EduMaster does not require a batch.

Fee rules:

- Normal lead: course amount + batch amount
- UG/PG: also adds the university UG or PG amount
- B2B lead: only the batch B2B amount
- Optional first payment needs type, amount, date, and a proof file

The system marks the lead converted, creates the student, an invoice, and the payment.

### 6. After conversion

Six desks share one student:

| Desk | What they do |
|---|---|
| Finance | Collects the rest, stores proofs, sets finance approval, runs sales reports |
| Admission counsellor / academic assistant | Documents, register number, academic verification, course change, ID card |
| Support | Support details, support flag, course mail, WhatsApp, feedback, support verification |
| Mentor, faculty, HOD | Course track: class, flags, mentor columns |
| Post-sales | Owner, call status, follow-up, batch postpone, or cancel with a remark |
| Placement | After “move to placement”: resume check, mock test, interview, remarks |
| LMS | Course mapping, then “share to LMS” |

Cancel is a flag with who, when, and a remark. It does not delete the student.

Lead write actions (status, reassign, convert, delete) are only for super admin, admin, telecaller, and general manager.

## Roles

Role is `users.role_id`. Extra power is a flag on the same user, not a new role.

Seeded in `database/seeders/UserRoleSeeder.php`. Checked in `app/Helpers/RoleHelper.php`. Menu access is decided in `app/Helpers/PermissionHelper.php`.

| ID | Role | What they do |
|---|---|---|
| 1 | Super Admin | Every menu, including website settings and telecaller tracking. Deny list is empty, so every permission key is allowed |
| 2 | Admin | Almost everything. Denied: website settings, and advanced reports except Stage Movement |
| 3 | Telecaller | Own leads, follow-ups, registration submitted, converted list, lead report, payments, profile |
| 4 | Admission Counsellor | Converted students, master data, many user lists, NatX. No lead write actions |
| 5 | Academic Assistant | Leads and converted students, universities, notifications, calls, B2B, departments. No lead write actions |
| 6 | Finance | Course student lists, payments, courses, batches, universities, finance reports and post-sales reports |
| 7 | Post-sales | Converted list, post-sales students, calls, payments |
| 8 | Support Team | Support list and support students |
| 9 | Mentor | Dashboard, leads, converted students, mentor track |
| 10 | Teacher | Seeded only. No permission method, so the web menu is empty |
| 11 | General Manager | Leads, follow-ups, pullback, registration, converted students, marketing, telecallers, post-sales, reports |
| 12 | Auditor | View only: leads, students, every report, telecaller tracking |
| 13 | Marketing | D2D form and marketing leads |
| 14 | HOD | Converted students, mentor and faculty lists, online faculty, call analytics |
| 15 | Placement Manager | Placement list only |
| 16 | Faculty | Faculty student lists |

### Flags on a user

| Flag | Used on | Extra behavior |
|---|---|---|
| `is_team_lead` | Telecaller | Sees the team. Converted leads report. App can switch to team-lead mode |
| `is_team_manager` | Telecaller | Mobile app team-manager permissions: teams, members, reports |
| `is_senior_manager` | Telecaller | Call analytics and converted leads report. Sees all team filters |
| `is_head` | Mentor or post-sales | Mentor head, or post-sales head. Post-sales head also gets post-sales reports |
| `is_b2b` | Telecaller or lead | B2B telecaller hides Payments. B2B lead fee uses only the batch B2B amount |
| `current_role` | Telecaller app | Switches the mobile session between telecaller, team lead, and team manager |

### Permission style

- Super admin: allow everything (`has_permission_super_admin` returns true unless the key is in an empty deny list).
- Admin: allow everything except these keys:
  - `admin/settings/index`
  - `admin/website/settings`
  - `admin/reports/lead-efficiency`
  - `admin/reports/lead-aging`
  - `admin/reports/team-wise`
  - `admin/reports/course-summary`
- Every other role: an allow-list of menu keys such as `leads/index`. If the key is not in the list, the menu and the check fail.
- `has_lead_action_permission()` is separate from the menu. It controls status update, reassign, convert, and delete.

### Mobile app feature flags

`PermissionHelper::has_permission_app()` returns on/off flags for the CRM app. Only roles 1, 2, and 3 are defined. Other roles fall through to the telecaller app set.

| Flag | Super admin | Admin | Team manager | Team lead | Telecaller |
|---|---|---|---|---|---|
| teams | 1 | 1 | 1 | 0 | 0 |
| members | 1 | 1 | 1 | 0 | 0 |
| leads | 1 | 1 | 1 | 1 | 1 |
| follow_ups | 1 | 1 | 1 | 1 | 1 |
| call | 1 | 1 | 1 | 1 | 1 |
| candidate | 1 | 1 | 1 | 1 | 1 |
| invoice | 1 | 0 | 0 | 0 | 0 |
| enrollments | 1 | 1 | 0 | 0 | 0 |
| admin_panel | 1 | 1 | 0 | 0 | 0 |
| user_roles | 1 | 0 | 0 | 0 | 0 |
| reports | 1 | 1 | 1 | 1 | 0 |

## Menus

The sidebar is `resources/views/layouts/parts/sidebar.blade.php`. Each item calls `has_permission('...')` unless noted.

### Daily work

| Menu | Permission key | Who |
|---|---|---|
| Dashboard | `dashboard/index` | Almost every role that has a menu |
| Leads | `leads/index` | Telecaller, admission, academic, finance, post-sales, support, mentor, faculty, GM, auditor. Admin and super admin by allow-all |
| Pullbacked Leads | `leads/pullbacked` | General manager (and admin / super admin) |
| Follow-up Leads | `leads/followup` | Telecaller, general manager, auditor |
| Registration Form Submitted | `leads/registration-form-submitted` | Telecaller, admission counsellor, academic assistant, general manager |
| Converted Leads | `admin/converted-leads/index` | Most operational roles. Support and HOD also forced on in the sidebar |
| Support List | same block as converted leads | Shown with Converted Leads |
| Placement List | `admin/placement-list/index` | Placement manager, admission counsellor |
| Post-sales Converted Students | `admin/post-sales-converted-leads/index` | Post-sales, general manager |
| Payments | `admin/payments/list` | Telecaller (hidden if `is_b2b`), finance, post-sales |

### Marketing and calls

| Menu | Permission key | Who |
|---|---|---|
| D2D Form | `admin/marketing/d2d-form` | Marketing, general manager |
| Marketing Leads | `admin/marketing/marketing-leads` | Marketing, general manager |
| Online Teaching Faculty | `admin/online-teaching-faculties/index` | Admission counsellor, academic assistant, HOD |
| Call Analytics | `admin/call-analytics/index` | Senior-manager telecaller, admission, academic, post-sales, GM, HOD |
| NatX Analytics | `admin/natx-analytics/index` | Admin, super admin, admission counsellor |
| Converted Leads Report | `admin/reports/converted-leads-report` | Team lead, senior manager, general manager, auditor |
| Call Logs | `admin/call-logs/index` | Admin and super admin (not on the narrower allow-lists) |

### User management

Shown when any of these index keys is allowed. Admin and super admin see the set. Admission counsellor sees a subset. General manager sees telecallers and post-sales. Auditor sees auditors.

| Menu | Permission key |
|---|---|
| Telecallers | `admin/telecallers/index` |
| Marketing | `admin/marketing/index` |
| Teachers | `admin/teachers/index` |
| General Managers | `admin/general-managers/index` |
| Auditors | `admin/auditors/index` |
| Placement Officers | `admin/placement-officers/index` |
| Admission Counsellors | `admin/admission-counsellors/index` |
| Academic Assistants | `admin/academic-assistants/index` |
| Finance | `admin/finance/index` |
| HOD | `admin/hod/index` |
| Support Team | `admin/support-team/index` |
| Mentor | `admin/mentor/index` |
| Faculty | `admin/faculty/index` |
| Post-sales | `admin/post-sales/index` |
| Admin Users | `admin/admins/index` |

### Lead setup

| Menu | Permission key |
|---|---|
| Lead Status | `admin/lead-statuses/index` |
| Lead Source | `admin/lead-sources/index` |

Admin and super admin. Not on the narrower role allow-lists.

### Reports

| Menu | Permission key | Who |
|---|---|---|
| Lead Reports | `admin/reports/leads` | Telecaller, general manager, auditor |
| Stage Movement | `admin/reports/lead-stage-movement` | Admin, super admin, auditor |
| Source Efficiency | `admin/reports/lead-efficiency` | Super admin, auditor. Admin is denied |
| Lead Aging | `admin/reports/lead-aging` | Super admin, auditor. Admin is denied |
| Team-Wise Report | `admin/reports/team-wise` | Super admin, auditor. Admin is denied |
| Course Reports | `admin/reports/course-summary` | Admission counsellor, auditor. Admin is denied. Super admin allowed |

These groups are opened by role helpers in the sidebar, not only by `has_permission`:

| Group | Who sees it | Pages |
|---|---|---|
| Post Sales Reports | Finance, admin, super admin, post-sales head | Month ways, total monthly, BDE collected amount |
| Finance Reports | Finance, admin, super admin | Telecaller sales, Thanzeels and Eschool, course-wise sales |
| Telecaller Tracking | Super admin and auditor | Tracking dashboard, task management |
| Teams menu extra | Finance, even without the teams permission string | Teams |

### Master data

Admin and super admin see the full set. Admission counsellor sees most of it. Finance sees courses, batches, universities, university courses. Academic assistant sees universities, B2B services, departments.

| Menu | Permission key |
|---|---|
| Courses | `admin/courses/index` |
| Addon Course | `admin/addon-courses/index` |
| Subjects | `admin/subjects/index` |
| Subject Areas | `admin/subject-areas/index` |
| Mail | `admin/mails/index` |
| Flag | `admin/flags/index` |
| Support Flag | `admin/support-flags/index` |
| Course Flag | `admin/course-flags/index` |
| Class Times | `admin/class-times/index` |
| Course Types | `admin/course-types/index` |
| Stream / Specializations | `admin/stream-specializations/index` |
| Offline Places | `admin/offline-places/index` |
| Sub Courses | `admin/sub-courses/index` |
| Countries | `admin/countries/index` |
| Boards | `admin/boards/index` |
| Batches | `admin/batches/index` |
| Admission Batches | `admin/admission-batches/index` |
| Teams | `admin/teams/index` |
| B2B Services | `admin/b2b-services/index` |
| Departments | `admin/departments/index` |
| Universities | `admin/universities/index` |
| University Courses | `admin/university-courses/index` |
| Registration Links | `admin/registration-links/index` |
| Academic Delivery Structure | `admin/academic-delivery-structures/index` (also academic counsellor) |

Subject areas, mails, and flags can be managed and deleted by admin, super admin, or admission counsellor (`can_manage_subject_areas_mails_flags`).

### LMS and settings

| Menu | Who |
|---|---|
| Course Mapping (`admin/course-mapping/index`) | Admission counsellor, admin, super admin |
| Website Settings (`admin/website/settings`) | Super admin. Admin is explicitly denied |
| Call App Settings | Admin and super admin (sidebar role check) |
| CRM App Settings | Admin and super admin |
| NatX App Settings | Admin and super admin |
| Profile (`profile/index`) | Any role whose allow-list includes it |

## Role workflows

### Super admin

1. Log in. Session stores user id, role id 1, and role title.
2. Full sidebar: daily work, user management, lead setup, all reports, tracking, master data, LMS, website and app settings.
3. Can create every user type, change master data, convert and reassign leads, approve the business, and read telecaller sessions and tasks.

### Admin

Same daily operation as super admin, with three limits:

- No website settings.
- Advanced reports: Stage Movement only. Source efficiency, aging, team-wise, and course summary are denied.
- Mobile app: no invoice flag and no user-roles flag.

### Telecaller

1. Sees own leads (team lead sees the team).
2. Calls, sets status and follow-up, sends the registration link.
3. Converts when the student is ready, including optional first payment.
4. Opens converted students, lead report, and payments. Payments are hidden when `is_b2b` is set.
5. Team lead or senior manager also gets the converted-leads report. Senior manager also gets call analytics.

### General manager

Works leads like a telecaller across teams: follow-ups, registration submitted, pullback and reassignment, converted students, lead reports, marketing D2D and marketing leads, telecaller users, and post-sales students.

### Admission counsellor

Does not write leads. Owns the student after conversion and the catalog around it: documents, master data (courses, subjects, flags, mails, batches, universities, registration links), user lists for teachers, assistants, support, mentors, faculty, and placement, plus NatX analytics and course reports.

### Academic assistant

Reads leads and registration-submitted leads. Works converted students, academic verification, universities, notifications, online teaching faculty, call analytics, B2B services, and departments.

### Finance

Opens each course’s converted-student list, payments, courses, batches, and universities. Runs finance reports and post-sales reports. Also sees Teams from the sidebar even though `admin/teams/index` is not on the finance allow-list.

### Post-sales

Opens converted students and the post-sales student list. Assigns a post-sales owner, updates call status and follow-up, postpones a batch, or cancels with a remark. Can see payments and call analytics. If `is_head` is set, also sees post-sales reports.

### Support team

Opens the support list and the per-course support student pages. Updates support details, sends course mail and WhatsApp, records feedback, and toggles support verification.

### Mentor and faculty

Mentor (role 9) opens dashboard, leads, and converted students, then the mentor track for the course. Faculty (role 16) opens the faculty course lists. HOD (role 14) oversees both mentor and faculty lists, online faculty, and call analytics. `is_head` on a mentor marks the mentor head.

### Marketing

Only D2D form and marketing leads, plus profile. A D2D submission is a marketing lead. It can later become a CRM lead (`marketing_leads_id` on the lead).

### Auditor

Opens leads, follow-ups, converted students, every lead report, the converted-leads report, all advanced reports, telecaller tracking, and task management. `has_lead_action_permission()` is false. Do not give this role write buttons.

### Placement manager

Only dashboard and placement list: resume verify, mock test, scheduled interview, interview status, specialization, and remarks.

### Teacher

Role 10 exists in the seeder. `PermissionHelper` has no teacher branch, so `has_permission` returns false. Leave this role unused until it has a real menu.

## Modules

| Module | Job | Main records |
|---|---|---|
| Auth | Session login for web. Sanctum token for CRM, Call, and NatX apps | `users`, session, tokens |
| Leads | List, add, bulk upload, status, reassign, pullback, convert, duplicates | `leads`, `lead_activities`, `lead_details` |
| Sources | Meta ads fetch, WhatsApp webhook, D2D marketing form, public registration | `meta_leads`, `marketing_leads` |
| Calls | Voxbay click-to-call and CDR. Call app recording sync. NatX call sync | `voxbay_call_logs`, call recordings |
| Converted students | Course lists, documents, register number, ID card, course change, cancel, LMS share | `converted_leads`, invoices, payments |
| Support desk | Per-course support view, feedback, course mail, WhatsApp, support verification | `converted_student_support_details` |
| Mentor and faculty | Course track columns, flags, head oversight | `converted_student_mentor_details` |
| Post-sales | Assign owner, status, follow-up, postponed batches, cancel | `post_sales_user_id` on the converted lead |
| Finance | Payments, proofs, receipts, finance approval, sales reports | `payments`, `payment_proofs`, `invoices` |
| Placement | Move student, resume verify, mock test, interview status | placement remarks and interviews |
| Master data | Courses, subjects, batches, universities, flags, mails, teams, countries | courses and lookup tables |
| Users | One user table. Separate admin screens per role today | `users`, `user_roles`, `teams` |
| Notifications | In-app notices by role | `notifications` |
| LMS | Map CRM course to LMS course, then share the student | `lms_course_mappings` |
| Tracking | Telecaller sessions, idle time, tasks. Super admin and auditor | `telecaller_sessions`, `telecaller_tasks` |

### Three apps on the same users

| App | API prefix | What the user does |
|---|---|---|
| CRM mobile | `/api/v1` | Home, leads, follow-ups, registration convert, invoices, payments, marketing leads |
| Call app | `/api/v1/call` | Login, sync calls, upload recordings |
| NatX | `/api/v1/natx` | Mentor student list, work status, call sync, notifications |

Web login is a custom session in `AuthHelper` and `AuthMiddleware`, not Laravel’s default web guard.

## Folders

| Path | What lives there |
|---|---|
| `app/Http/Controllers` | Web screens. `LeadController` and `ConvertedLeadController` hold most of the flow |
| `app/Http/Controllers/Public` | One registration form controller per course |
| `app/Http/Controllers/API` | CRM mobile API |
| `app/Http/Controllers/API/Call_Api` | Call app login and recording sync |
| `app/Http/Controllers/API/NatX_Api` | Mentor students, work status, call sync |
| `app/Helpers` | Roles, permissions, phone, mail, payments, registration URLs |
| `app/Support` | Per-course table columns, flags, and mail formatting |
| `app/Models` | `Lead`, `ConvertedLead`, `User`, invoices, payments, courses, teams |
| `app/Http/Middleware/AuthMiddleware.php` | Redirects to login when the session is missing |
| `resources/views/layouts/parts/sidebar.blade.php` | The only menu |
| `resources/views/admin` | Admin screens, split again by course under `converted-leads` |
| `resources/views/public` | Public registration forms |
| `routes/web.php` | Web routes, public forms, Voxbay |
| `routes/api.php` | The three app APIs |
| `database/seeders/UserRoleSeeder.php` | The 16 roles |
| `database/seeders/LeadStatusSeeder.php` | The nine lead statuses |

## Helpers

Global Blade wrappers live in `app/helpers.php`: `has_permission()`, `can_access_menu()`, `is_super_admin()`, `is_admin()`, `is_telecaller()`, and the other role checks, plus `get_country_code()` and `get_phone_code()`.

| File | Decision it owns |
|---|---|
| `app/Helpers/AuthHelper.php` | Session user, role id, team, team member ids |
| `app/Helpers/RoleHelper.php` | `is_super_admin`, `is_telecaller`, `is_team_lead`, `is_senior_manager`, head desks |
| `app/Helpers/PermissionHelper.php` | Allow or deny each menu key per role. Mobile app feature flags |
| `app/Helpers/StatusHelper.php` | Badge color for the nine lead statuses |
| `app/Helpers/LeadRegistrationRouteHelper.php` | Which public form URL belongs to which course id |
| `app/Helpers/TeamTelecallerFilterHelper.php` | Which teams and telecallers a user may filter |
| `app/Helpers/PhoneNumberHelper.php` | Display, click-to-call, WhatsApp digits, duplicate matching |
| `app/Helpers/PaymentProofHelper.php` | Save one or many payment proof files |
| `app/Helpers/MailHelper.php` | `send_email` for course mails and notices |
| `app/Helpers/CourseTitleHelper.php` | Hard-coded titles for a few course ids |
| `app/Helpers/DateRangeHelper.php` | Today, week, month, custom filters on reports |
| `app/Helpers/CountryHelper.php` and `CountriesHelper.php` | Dial codes |
| `app/Helpers/PublicStorageHelper.php` | Public storage link and file writes |

`app/Support` holds course-specific table formatters and column maps (NIOS, BOSSE, GMVSS, mentor tracks, course flags, mail body). In a new CRM these become one formatter driven by course configuration.

## Public course forms

`LeadRegistrationRouteHelper` maps course id to the public registration route. Rebuild this as course configuration, not a new controller per id.

| Course id | Title |
|---|---|
| 1 | National Institute of Open Schooling |
| 2 | Board of Open Schooling and Skill Education |
| 3 | Certificate Course in Medical Coding |
| 4 | Diploma in Hospital Administration |
| 5 | E-School |
| 6 | Eduthanzeel |
| 7 | TTC |
| 8 | Hotel Management |
| 9 | UG/PG |
| 10 | Python |
| 11 | AI Integrated Digital Marketing |
| 12 | Diploma in Data Science |
| 13 | Web Development & Designing |
| 14 | Vibe Coding |
| 15 | Diploma in Graphic Designing |
| 16 | Grameen Mukt Vidhyalayi Shiksha Sansthan |
| 20 | Diploma in Machine Learning |
| 21 | Flutter |
| 23 | EduMaster |
| 25 | CreateX AI |
| 27 | RPA |
| 29 | AI-Integrated Sales & Marketing |
| 30 | AI-Integrated Video Editing |
| 31 | AI-Integrated Videography |
| 32 | AI-Integrated Photography |
| 33 | Robo Vibe |
| 34 | Prompt Engineering |

## What to create in the new CRM

Keep the path above. Change the shape.

| New module | Replaces | Keeps |
|---|---|---|
| Identity | 16 role screens and `RoleHelper` if-chains | Same roles and the five flags |
| Access | `PermissionHelper` string lists | One permission table: role, menu, can_view, can_edit |
| Lead | The large `LeadController` | Capture, assign, status, follow-up, pullback, convert |
| Intake | One public controller per course | One registration form whose fields come from the course |
| Student | `ConvertedLeadController` plus mentor, faculty, and support copies | One student record with a desk: finance, support, mentor, faculty, post-sales, placement |
| Catalog | Courses, batches, subjects, universities, flags, mails | Same master data. Course id is data |
| Money | Payments inside conversion and finance lists | Invoice, payment, proof, approval, reports |
| Comms | Voxbay, Call app, NatX, WhatsApp, mail | Call log attached to the lead or the student |

Permission rules to store as data:

- Super admin is allow-all.
- Admin is allow-all except website settings and the advanced reports other than stage movement.
- Telecaller edits only owned leads. Team lead edits the team. Senior manager and general manager see wider.
- Auditor is view on leads, students, and reports.
- Teacher stays unused until it has a menu.
