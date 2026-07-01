export type OptionItem = {
    value: string;
    label: string;
    description?: string;
};

export type FormOptions = {
    requestTypes: OptionItem[];
};

export type RequestStatusValue =
    'recibido' | 'en_revision' | 'atendido' | 'cerrado' | 'descartado';

export type RequestSummary = {
    id: number;
    folio: string;
    request_type: string;
    request_type_label: string;
    department_label: string;
    full_name: string | null;
    contact_info: string | null;
    status: RequestStatusValue;
    status_label: string;
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
    full_name: string | null;
    contact_info: string | null;
    department: string;
    department_label: string;
    incident_date: string | null;
    description: string;
    involved_people: string | null;
    has_evidence: boolean;
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
    key?: string;
    dateFrom?: string;
    dateTo?: string;
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
