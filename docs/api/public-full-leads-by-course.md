# Public Full Leads by Course API

Export full lead → registration → student payloads for syncing into another CRM.

## Endpoint

**GET** `/api/v1/public/leads/by-course/{course_id}`

| Item | Value |
|------|--------|
| Method | `GET` |
| Auth | Public route (no Sanctum). Requires CRM API key. |
| Route name | `api.public.leads.by-course` |

## Authentication

Send the CRM API key in the request header:

```
X-CRM-API-KEY: {CRM_API_KEY}
```

Also accepted:

| Source | Example |
|--------|---------|
| Header `X-CRM-API-KEY` | preferred |
| Header `X-Api-Key` | alternate |
| Query `api_key` | `?api_key=...` (not recommended) |

Configure the key in `.env`:

```env
CRM_API_KEY=your_secret_key_here
```

If `CRM_API_KEY` is empty or the header does not match, the API returns `401`.

## Path Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `course_id` | integer | **Yes** | Course ID. Must exist in `courses`. |

## Query Parameters

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `page` | integer | `1` | Page number |
| `per_page` | integer | `50` | Records per page (`1`–`200`) |
| `is_converted` | boolean | — | `true` = converted only, `false` = not converted |
| `only_with_registration` | boolean | `false` | `true` = only leads that have `leads_details` |

## Success Response (200)

```json
{
  "status": true,
  "message": "Full leads fetched successfully.",
  "course": {
    "id": 5,
    "title": "Course Title"
  },
  "data": [
    {
      "source_lead_id": 101,
      "lead": {
        "id": 101,
        "name": "Student Full Name",
        "code": "91",
        "phone": "9876543210",
        "gender": "male",
        "dob": "2005-01-15",
        "whatsapp_code": "91",
        "whatsapp": "9876543210",
        "email": "student@example.com",
        "qualification": "Plus Two",
        "country_id": 1,
        "interest_status": "hot",
        "rating": 8,
        "lead_status_id": 1,
        "first_lead_status_id": 1,
        "lead_source_id": 1,
        "first_lead_source_id": 1,
        "address": "House, Locality, PO, District, State, 680001",
        "team_id": 1,
        "telecaller_id": 10,
        "place": "Thrissur",
        "course_id": 5,
        "first_lead_course_id": 5,
        "batch_id": 12,
        "by_meta": false,
        "is_converted": true,
        "converted_at": "2026-03-01 10:00:00",
        "followup_date": null,
        "remarks": "Imported note",
        "created_at": "2025-11-01 09:00:00",
        "updated_at": "2026-03-01 10:00:00",
        "first_created_at": "2025-11-01 09:00:00",
        "deleted_at": null,
        "created_by": 1,
        "updated_by": 1,
        "deleted_by": null,
        "age": 19,
        "university_id": null,
        "is_b2b": false
      },
      "registration": {
        "id": 55,
        "lead_id": 101,
        "student_name": "Student Full Name",
        "father_name": "Father Name",
        "mother_name": "Mother Name",
        "date_of_birth": "2005-01-15",
        "parents_code": "91",
        "parents_number": "9123456780",
        "whatsapp_code": "91",
        "whatsapp_number": "9876543210",
        "batch_id": 12,
        "street": "House / street",
        "locality": "Locality",
        "post_office": "Post Office",
        "district": "Thrissur",
        "state": "Kerala",
        "pin_code": "680001",
        "status": "approved",
        "passport_photo": "storage/student-documents/uuid.jpg",
        "document_proof": "storage/student-documents/uuid.pdf",
        "passport_photo_verification_status": "verified",
        "document_proof_verification_status": "verified",
        "passport_photo_verified_by": 2,
        "passport_photo_verified_at": "2026-02-01 12:00:00",
        "document_proof_verified_by": 2,
        "document_proof_verified_at": "2026-02-01 12:00:00",
        "admin_remarks": null,
        "reviewed_by": 2,
        "reviewed_at": "2026-02-01 12:00:00",
        "created_at": "2026-01-15 10:00:00",
        "updated_at": "2026-02-01 12:00:00",
        "deleted_at": null,
        "created_by": null,
        "updated_by": null,
        "deleted_by": null,
        "documents": {
          "passport_photo_file": "https://crm.example/storage/student-documents/uuid.jpg",
          "passport_photo_path": "storage/student-documents/uuid.jpg",
          "document_proof_file": "https://crm.example/storage/student-documents/uuid.pdf",
          "document_proof_path": "storage/student-documents/uuid.pdf",
          "document_proof_source_field": "adhar_front"
        },
        "source_extras": {
          "course_id": 5,
          "addon_course_id": null,
          "gender": "male",
          "email": "student@example.com",
          "personal_code": "91",
          "personal_number": "9876543210",
          "residential_address": null,
          "subject_id": null,
          "sub_course_id": null,
          "class_time_id": null,
          "programme_type": null,
          "location": null,
          "university_id": null,
          "university_course_id": null
        }
      },
      "documents": {
        "passport_photo": {
          "path": "storage/student-documents/uuid.jpg",
          "disk_path": "student-documents/uuid.jpg",
          "url": "https://crm.example/storage/student-documents/uuid.jpg",
          "verification_status": "verified",
          "verified_by": 2,
          "verified_at": "2026-02-01 12:00:00"
        },
        "adhar_front": {
          "path": "storage/student-documents/uuid.pdf",
          "disk_path": "student-documents/uuid.pdf",
          "url": "https://crm.example/storage/student-documents/uuid.pdf",
          "verification_status": "verified",
          "verified_by": 2,
          "verified_at": "2026-02-01 12:00:00"
        },
        "sslc_certificates": []
      },
      "student": {
        "id": 88,
        "lead_id": 101,
        "name": "Student Full Name",
        "register_number": null,
        "registration_number": "EDU-1001",
        "class_time": "09:30:00",
        "is_academic_verified": false,
        "academic_verified_by": null,
        "academic_verified_at": null,
        "is_support_verified": false,
        "support_verified_by": null,
        "support_verified_at": null,
        "is_cancelled": false,
        "cancelled_by": null,
        "cancelled_at": null,
        "cancel_remark": null,
        "remarks": null,
        "created_at": "2026-03-01 10:00:00",
        "updated_at": "2026-03-01 10:00:00",
        "deleted_at": null,
        "created_by": 1,
        "updated_by": 1,
        "deleted_by": null
      },
      "student_details": {
        "id": 88,
        "converted_lead_id": 88,
        "flag_id": null,
        "support_flag_id": null,
        "faculty_flag_id": null,
        "faculty_team_id": null,
        "faculty_team_head_id": null,
        "faculty_id": null,
        "called_time": null,
        "screening_date": null,
        "class_status": null,
        "class_time": "09:30:00",
        "registration_number": "EDU-1001",
        "remarks": null,
        "continuing_studies": null,
        "reason": null,
        "created_at": "2026-03-01 10:00:00",
        "updated_at": "2026-03-01 10:00:00",
        "deleted_at": null,
        "created_by": null,
        "updated_by": null,
        "deleted_by": null
      },
      "converted_lead": {
        "id": 88,
        "lead_id": 101,
        "name": "Student Full Name",
        "code": "91",
        "phone": "9876543210",
        "email": "student@example.com",
        "dob": "2005-01-15",
        "register_number": null,
        "registration_number": "EDU-1001",
        "class_time": "09:30:00",
        "course_id": 5,
        "batch_id": 12,
        "admission_batch_id": null,
        "sub_course_id": null,
        "university_id": null,
        "subject_id": null,
        "board_id": null,
        "flag_id": null,
        "support_flag_id": null,
        "course_flag_id": null,
        "faculty_id": null,
        "academic_assistant_id": null,
        "status": "Active",
        "postsale_status": null,
        "paid_status": null,
        "call_status": null,
        "is_academic_verified": false,
        "is_support_verified": false,
        "is_cancelled": false,
        "is_b2b": false,
        "remarks": null,
        "created_at": "2026-03-01 10:00:00",
        "updated_at": "2026-03-01 10:00:00"
      }
    }
  ],
  "pagination": {
    "current_page": 1,
    "per_page": 50,
    "total": 120,
    "last_page": 3,
    "from": 1,
    "to": 50
  }
}
```

## Response Field Mapping

| Response key | Source table / meaning |
|--------------|------------------------|
| `lead` | `leads` (`title` → `name`) shaped for destination CRM `leads` |
| `registration` | `leads_details` shaped for destination `lead_details` / registration import |
| `documents` | All uploaded registration files with public URLs |
| `student` | `converted_leads` → destination `students`. `registration_number` is `converted_student_details.registration_number`, or `converted_leads.register_number` when that detail is empty. `class_time` is the mentor class time, or the student-detail class time. |
| `student_details` | Converted flags/ops → destination `student_details`, including `registration_number` and `class_time` |
| `converted_lead` | Full source `converted_leads` row (extra source fields) |

### Documents notes

- Paths are normalized as `storage/student-documents/{file}` for destination import.
- `url` is an absolute public URL the importer can download.
- Destination `document_proof` is mapped from the first available of: `other_document`, `adhar_front`, `adhar_back`, `birth_certificate`.
- `registration` / `student` / `student_details` / `converted_lead` / `documents` are `null` when that data does not exist for the lead.

## Error Responses

### 401 Unauthorized

```json
{
  "status": false,
  "message": "Unauthorized. Provide a valid X-CRM-API-KEY header."
}
```

### 404 Course not found

```json
{
  "status": false,
  "message": "Course not found."
}
```

### 422 Validation failed

```json
{
  "status": false,
  "message": "Validation failed.",
  "errors": {
    "course_id": ["The selected course id is invalid."],
    "per_page": ["The per page must not be greater than 200."]
  }
}
```

## Example Requests

### Basic

```bash
curl -X GET "https://crm-demo.test/api/v1/public/leads/by-course/5" \
  -H "Accept: application/json" \
  -H "X-CRM-API-KEY: your_secret_key_here"
```

### Converted leads only, page 2

```bash
curl -X GET "https://crm-demo.test/api/v1/public/leads/by-course/5?is_converted=1&page=2&per_page=25" \
  -H "Accept: application/json" \
  -H "X-CRM-API-KEY: your_secret_key_here"
```

### Only leads with registration submitted

```bash
curl -X GET "https://crm-demo.test/api/v1/public/leads/by-course/5?only_with_registration=1" \
  -H "Accept: application/json" \
  -H "X-CRM-API-KEY: your_secret_key_here"
```

## Import checklist (destination CRM)

1. Resolve / map FKs (`course_id`, `batch_id`, `lead_status_id`, `lead_source_id`, `country_id`, `team_id`, `telecaller_id`) in the destination CRM.
2. Create `leads` from `data[].lead`.
3. Create `lead_details` from `data[].registration` when present.
4. Download files from `documents.*.url` (or `registration.documents.*_file`) into `storage/app/public/student-documents/` and store `storage/student-documents/...` paths.
5. If `lead.is_converted` is true, create `students` + `student_details` from `student` / `student_details`.
