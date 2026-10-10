# Leads API

Authentication for every endpoint below is Laravel Sanctum. Send the bearer token on each request:

```
Authorization: Bearer {your_token}
```

Base path: `/api/v1`

A team lead sees their own unconverted leads plus leads assigned to telecallers on the same team. A telecaller sees only their own leads. Admin, super admin, senior manager, and roles `4`–`16` see all leads.

Post-sale telecallers with **Hide from Team Lead** are left out of a team lead’s lead list, filter dropdown, and team-member list. The team lead is always included.

---

## List leads

**GET** `/api/v1/leads`

Unconverted leads, newest first.

### Query parameters

All parameters are optional and can be combined.

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `page` | integer | 1 | Page number |
| `per_page` | integer | 15 | Records per page |
| `lead_status_id` | integer | | Filter by lead status |
| `lead_source_id` | integer | | Filter by lead source |
| `course_id` | integer | | Filter by course |
| `rating` | integer | | Filter by rating (`1`–`10`) |
| `telecaller_id` | integer | | Filter by assigned telecaller |
| `search` | string | | Match name, phone, or email |
| `date_from` | date | | Start of `created_at` (`YYYY-MM-DD`). Applied only when `date_to` is also sent |
| `date_to` | date | | End of `created_at` (`YYYY-MM-DD`). Applied only when `date_from` is also sent |

### Team lead `telecaller_id` filter

A team lead who cannot see every lead in the system can pass `telecaller_id` to show one telecaller’s leads. The id must be the team lead or a telecaller on that team. Anyone else returns `403`.

```
GET /api/v1/leads?telecaller_id=42&per_page=15
```

Users who can see all leads can filter by any telecaller id. A regular telecaller already only sees their own leads.

### Success response (200)

```json
{
    "status": true,
    "data": [
        {
            "id": 101,
            "name": "Anita Rao",
            "profile_completed_percentage": 80,
            "lead_status_id": 2,
            "phone": "+91 9876543210",
            "email": "anita@example.com",
            "lead_status": "Followup",
            "rating": 7,
            "lead_source": "Website",
            "course_name": "Digital Marketing",
            "course_id": 4,
            "telecaller_id": 42,
            "telecaller_name": "Ravi Kumar",
            "remarks": "Asked to call tomorrow",
            "marketing_remarks": "",
            "date": "10-10-2026",
            "time": "09:15 AM",
            "follow_up_date": "11-10-2026",
            "registration_details_status": "",
            "can_convert": 0,
            "created_at": "10-10-2026 09:15 AM",
            "is_lead_reg_form_submitted": 0,
            "show_lead_reg_form_link": 1,
            "reg_form_link": "https://example.com/register/101",
            "registration_link": "https://example.com/register/101",
            "registration_form_title": "Digital Marketing Registration",
            "show_plus_two_follow_up_form_link": 0,
            "plus_two_follow_up_form_link": "",
            "is_plus_two_follow_up_form_submitted": 0
        }
    ],
    "leads_count": 1,
    "pagination": {
        "current_page": 1,
        "per_page": 15,
        "total": 1,
        "last_page": 1,
        "from": 1,
        "to": 1
    }
}
```

`telecaller_id` is included on each lead so the app can tell which telecaller owns the row.

### Error responses

| Status | When | Message |
|--------|------|---------|
| 401 | Missing or invalid token | `Unauthorized` |
| 403 | Team lead filters by a telecaller outside their team | `You can only filter leads for telecallers on your team.` |

---

## Lead filter options

**GET** `/api/v1/leads/filters`

Dropdown data for the leads screen. `telecallers` is limited to the caller’s visibility: all telecallers for admins, the team for a team lead, and only themselves for a telecaller.

`can_filter_by_telecaller` is `true` for a team lead and for users who can see all leads. Use it to show or hide the telecaller dropdown.

### Success response (200)

```json
{
    "status": true,
    "data": {
        "lead_statuses": [
            { "id": 2, "title": "Followup" }
        ],
        "lead_sources": [
            { "id": 1, "title": "Website" }
        ],
        "courses": [
            { "id": 4, "title": "Digital Marketing" }
        ],
        "ratings": [
            { "value": 1, "label": "1/10" }
        ],
        "telecallers": [
            { "id": 42, "name": "Ravi Kumar" }
        ],
        "can_filter_by_telecaller": true,
        "registration_form_courses": [
            {
                "course_id": 4,
                "title": "Digital Marketing Registration",
                "route_name": "public.lead.digital-marketing.register"
            }
        ]
    }
}
```

---

## Team members

**GET** `/api/v1/team-members`

Team lead only. Returns the team lead and the telecallers on their team, ordered by name. Hidden post-sale telecallers are omitted. If the team lead has no team, the list contains only themselves.

### Success response (200)

```json
{
    "status": true,
    "data": [
        {
            "id": 15,
            "name": "Meera Nair",
            "phone": "+91 9000000001",
            "email": "meera@example.com",
            "team_id": 3,
            "team_name": "Team Alpha",
            "is_team_lead": 1,
            "is_active": 1
        },
        {
            "id": 42,
            "name": "Ravi Kumar",
            "phone": "+91 9000000002",
            "email": "ravi@example.com",
            "team_id": 3,
            "team_name": "Team Alpha",
            "is_team_lead": 0,
            "is_active": 1
        }
    ]
}
```

### Error responses

| Status | When | Message |
|--------|------|---------|
| 401 | Missing or invalid token | `Unauthorized` |
| 403 | Caller is not a team lead | `Only a team lead can view team members.` |

---

## Leads by telecaller

**GET** `/api/v1/leads/by-telecaller/{telecaller_id}`

Same lead list as `GET /api/v1/leads`, locked to one telecaller. Optional query parameters are the same as the leads list (`page`, `per_page`, `lead_status_id`, `lead_source_id`, `course_id`, `rating`, `search`, `date_from`, `date_to`). Do not send `telecaller_id` as a query parameter; the path id is used.

```
GET /api/v1/leads/by-telecaller/42?lead_status_id=2&per_page=15
```

Who can call it:

| Caller | Allowed `{telecaller_id}` |
|--------|---------------------------|
| Team lead | Themselves or a telecaller on their team |
| User who can see all leads | Any existing user |
| Telecaller | Their own id only |

### Success response (200)

The body matches the leads list, with an extra `telecaller` object:

```json
{
    "status": true,
    "data": [],
    "leads_count": 0,
    "pagination": {
        "current_page": 1,
        "per_page": 15,
        "total": 0,
        "last_page": 1,
        "from": null,
        "to": null
    },
    "telecaller": {
        "id": 42,
        "name": "Ravi Kumar"
    }
}
```

### Error responses

| Status | When | Message |
|--------|------|---------|
| 401 | Missing or invalid token | `Unauthorized` |
| 403 | Team lead requests someone outside the team | `You can only filter leads for telecallers on your team.` |
| 403 | Telecaller requests another user’s leads | `You can only view your own leads.` |
| 404 | No user with that id | `Telecaller not found.` |
