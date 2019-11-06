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
    field_default_value = '';
    is_reported = false;
    is_deleted = false;
    element_id = 0;
}

/***
 * Objeto base para MongoDB
 */
class SResult {
    id_configuration = 0;
    id_field = 0;
    result = null;
    data = null;

    constructor(conf, field, res) {
        this.id_configuration = conf;
        this.id_field = field;
        this.result = res;
    }

    id_tag = '';
    field_name = '';
    element_id = 0;
    element_type_id = 0;
    item_link_type_id = 0;
    item_link_id = 0;
    analysis_id = 0;
    is_table = false;
    table_name = '';
    dt_date = null;
    updated_at = null;
    usr_upd = 1;
}