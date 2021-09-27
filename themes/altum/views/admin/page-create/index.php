<?php defined('ALTUMCODE') || die() ?>

<div class="d-flex justify-content-between mb-4">
    <h1 class="h3"><i class="fa fa-fw fa-xs fa-file-alt text-primary-900 mr-2"></i> <?= language()->admin_page_create->header ?></h1>
</div>

<?= \Altum\Alerts::output_alerts() ?>

<div class="card <?= \Altum\Alerts::has_field_errors() ? 'border-danger' : null ?>">
    <div class="card-body">
        <form action="" method="post" role="form">
            <input type="hidden" name="token" value="<?= \Altum\Middlewares\Csrf::get() ?>" />

            <div class="form-group">
                <label for="type"><?= language()->admin_pages->input->type ?></label>
                <select id="type" name="type" class="form-control form-control-lg">
                    <option value="internal" <?= $data->values['type'] == 'internal' ? 'selected="selected"' : null ?>><?= language()->admin_pages->input->type_internal ?></option>
                    <option value="external" <?= $data->values['type'] == 'external' ? 'selected="selected"' : null ?>><?= language()->admin_pages->input->type_external ?></option>
                </select>
            </div>

            <div class="form-group" data-type="internal">
                <label for="url"><?= language()->admin_pages->input->url ?></label>
                <div class="input-group">
                    <div id="url_prepend" class="input-group-prepend">
                        <span class="input-group-text"><?= SITE_URL . 'page/' ?></span>
                    </div>

                    <input id="url" type="text" name="url" class="form-control form-control-lg <?= \Altum\Alerts::has_field_errors('url') ? 'is-invalid' : null ?>" value="<?= $data->values['url'] ?>" placeholder="<?= language()->admin_pages->input->url_internal_placeholder ?>" required="required" />
                    <?= \Altum\Alerts::output_field_error('url') ?>
                </div>
            </div>

            <div class="form-group" data-type="external">
                <label for="url"><?= language()->admin_pages->input->url ?></label>
                <input id="url" type="url" name="url" class="form-control form-control-lg <?= \Altum\Alerts::has_field_errors('url') ? 'is-invalid' : null ?>" value="<?= $data->values['url'] ?>" placeholder="<?= language()->admin_pages->input->url_external_placeholder ?>" required="required" />
                <?= \Altum\Alerts::output_field_error('url') ?>
            </div>

            <div class="form-group">
                <label for="title"><?= language()->admin_pages->input->title ?></label>
                <input id="title" type="text" name="title" class="form-control form-control-lg <?= \Altum\Alerts::has_field_errors('title') ? 'is-invalid' : null ?>" value="<?= $data->values['title'] ?>" required="required" />
                <?= \Altum\Alerts::output_field_error('title') ?>
            </div>

            <div class="form-group" data-type="internal">
                <label for="description"><?= language()->admin_pages->input->description ?></label>
                <input id="description" type="text" name="description" class="form-control form-control-lg" value="<?= $data->values['description'] ?>" />
            </div>

            <div class="form-group" data-type="internal">
                <label for="editor"><?= language()->admin_pages->input->editor ?></label>
                <select id="editor" name="editor" class="form-control form-control-lg">
                    <option value="wysiwyg" <?= $data->values['editor'] == 'wysiwyg' ? 'selected="selected"' : null ?>><?= language()->admin_pages->input->editor_wysiwyg ?></option>
                    <option value="raw" <?= $data->values['editor'] == 'raw' ? 'selected="selected"' : null ?>><?= language()->admin_pages->input->editor_raw ?></option>
                </select>
            </div>

            <div class="form-group" data-type="internal">
                <label for="content"><?= language()->admin_pages->input->content ?></label>
                <div id="quill_container">
                    <div id="quill" style="height: 15rem;"></div>
                </div>
                <textarea name="content" id="content" class="form-control form-control-lg d-none" style="height: 15rem;"><?= $data->values['content'] ?></textarea>
            </div>

            <div class="form-group">
                <label for="pages_category_id"><?= language()->admin_pages->input->pages_category_id ?></label>
                <select id="pages_category_id" name="pages_category_id" class="form-control form-control-lg">
                    <?php foreach($data->pages_categories as $row): ?>
                        <option value="<?= $row->pages_category_id ?>" <?= $data->values['pages_category_id'] == $row->pages_category_id ? 'selected="selected"' : null ?>><?= $row->title ?></option>
                    <?php endforeach ?>

                    <option value="" <?= $data->values['pages_category_id'] == '' ? 'selected="selected"' : null ?>><?= language()->admin_pages->input->pages_category_id_null ?></option>
                </select>
            </div>

            <div class="form-group">
                <label for="position"><?= language()->admin_pages->input->position ?></label>
                <select id="position" name="position" class="form-control form-control-lg">
                    <option value="bottom" <?= $data->values['position'] == 'bottom' ? 'selected="selected"' : null ?>><?= language()->admin_pages->input->position_bottom ?></option>
                    <option value="top" <?= $data->values['position'] == 'top' ? 'selected="selected"' : null ?>><?= language()->admin_pages->input->position_top ?></option>
                    <option value="hidden" <?= $data->values['position'] == 'hidden' ? 'selected="selected"' : null ?>><?= language()->admin_pages->input->position_hidden ?></option>
                </select>
            </div>

            <div class="form-group">
                <label for="order"><?= language()->admin_pages->input->order ?></label>
                <input id="order" type="number" name="order" class="form-control form-control-lg" value="<?= $data->values['order'] ?>" />
                <small class="form-text text-muted"><?= language()->admin_pages->input->order_help ?></small>
            </div>

            <button type="submit" name="submit" class="btn btn-lg btn-block btn-primary mt-4"><?= language()->global->create ?></button>
        </form>
    </div>
</div>

<?php ob_start() ?>
<link href="<?= ASSETS_FULL_URL . 'css/quill.snow.css?v=' . PRODUCT_CODE ?>" rel="stylesheet" media="screen,print">
<?php \Altum\Event::add_content(ob_get_clean(), 'head') ?>

<?php ob_start() ?>
<script src="<?= ASSETS_FULL_URL . 'js/libraries/quill.min.js' ?>"></script>

<script>
    'use strict';

    let quill = new Quill('#quill', {
        theme: 'snow',
        modules: {
            toolbar: [
                [{ "font": [] }, { "size": ["small", false, "large", "huge"] }],
                ["bold", "italic", "underline", "strike"],
                [{ "color": [] }, { "background": [] }],
                [{ "script": "sub" }, { "script": "super" }],
                [{ "header": 1 }, { "header": 2 }, "blockquote", "code-block"],
                [{ "list": "ordered" }, { "list": "bullet" }, { "indent": "-1" }, { "indent": "+1" }],
                [{ "direction": "rtl" }, { "align": [] }],
                ["link", "image", "video", "formula"],
                ["clean"]
            ]
        },
    });

    quill.root.innerHTML = document.querySelector('#content').value;

    document.querySelector('form').addEventListener('submit', event => {
        let editor = document.querySelector('#editor').value;

        if(editor == 'wysiwyg') {
            document.querySelector('#content').value = quill.root.innerHTML;
        }
    });

    document.querySelector('#editor').addEventListener('change', event => {
        let editor = document.querySelector('#editor').value;

        switch(editor) {
            case 'wysiwyg':
                document.querySelector('#quill_container').classList.remove('d-none');
                quill.enable(true);
                quill.root.innerHTML = document.querySelector('#content').value;
                document.querySelector('#content').classList.add('d-none');
                break;

            case 'raw':
                document.querySelector('#content').value = quill.root.innerHTML;
                document.querySelector('#quill_container').classList.add('d-none');
                quill.enable(false);
                document.querySelector('#content').classList.remove('d-none');
                break;
        }
    })

    /* Type handler */
    let type_handler = () => {
        let type = document.querySelector('select[name="type"]').value;

        document.querySelectorAll(`[data-type]:not([data-type="${type}"])`).forEach(element => {
            element.classList.add('d-none');
            let input = element.querySelector('input');

            if(input) {
                input.setAttribute('disabled', 'disabled');
                if(input.getAttribute('required')) {
                    input.setAttribute('data-is-required', 'true');
                }
                input.removeAttribute('required');
            }
        });

        document.querySelectorAll(`[data-type="${type}"]`).forEach(element => {
            element.classList.remove('d-none');
            let input = element.querySelector('input');

            if(input) {
                input.removeAttribute('disabled');
                if(input.getAttribute('data-is-required')) {
                    input.setAttribute('required', 'required')
                }
            }
        });
    }

    type_handler();

    document.querySelector('select[name="type"]') && document.querySelector('select[name="type"]').addEventListener('change', type_handler);
</script>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>
