export type OptionItem = {
    value: string;
    label: string;
    description?: string;
};

export type FormOptions = {
    requestTypes: OptionItem[];
    departments: OptionItem[];
};

export type RequestStatusValue =
    'recibido' | 'en_revision' | 'atendido' | 'cerrado' | 'descartado';

export type RequestSummary = {
    id: number;
    folio: string;
    request_type: string;
    request_type_label: string;
    department_label: string;
    location?: string | null;
    urgency_level?: string;
    urgency_level_label: string;
    status: RequestStatusValue;
    status_label: string;
    is_anonymous: boolean;
    has_evidence?: boolean;
    attachments_count?: number;
    created_at: string;
};

export type RequestAttachment = {
    id: number;
    original_name: string;
    mime_type: string | null;
    size: number;
    human_size: string;
    is_image: boolean;
};

export type RequestDetail = {
    id: number;
    folio: string;
    request_type: string;
    request_type_label: string;
    is_anonymous: boolean;
    full_name: string | null;
    department: string;
    department_label: string;
    location: string | null;
    incident_date: string | null;
    description: string;
    involved_people: string | null;
    urgency_level: string;
    urgency_level_label: string;
    has_evidence: boolean;
    wants_follow_up: boolean;
    contact_info: string | null;
    status: RequestStatusValue;
    status_label: string;
    internal_notes: string | null;
    reviewed_by: string | null;
    reviewed_at: string | null;
    closed_at: string | null;
    created_at: string;
    attachments: RequestAttachment[];
};

export type ChartPoint = {
    label: string;
    value: number;
};

export type PageLink = {
    url: string | null;
    label: string;
    active: boolean;
};

export type Paginated<T> = {
    data: T[];
    links: PageLink[];
    from: number | null;
    to: number | null;
    total: number;
    current_page: number;
    last_page: number;
};

export const URGENCY_COLORS: Record<string, string> = {
    bajo: 'emerald',
    medio: 'amber',
    alto: 'orange',
    critico: 'red',
};

export const STATUS_COLORS: Record<RequestStatusValue, string> = {
    recibido: 'blue',
    en_revision: 'amber',
    atendido: 'emerald',
    cerrado: 'gray',
    descartado: 'red',
};
