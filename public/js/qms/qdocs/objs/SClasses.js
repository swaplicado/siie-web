class SSection {
    id_section = 0;
    title = '*';
    dt_section = '2019-01-01';
    comments = '**';
    is_deleted = false;
    created_by_id = 1;
    updated_by_id = 1;
}

class SElement {
    id_element = 0;
    element = '';
    n_values = 1;
    is_deleted = false;
    element_type_id = 1;
    created_by_id = 1;
    updated_by_id = 1;

    fileds = [];
}

class SField {
    id_field = 0;
    field_name = '';
    is_deleted = false;
    element_id = 0;
}