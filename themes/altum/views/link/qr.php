<?php defined('ALTUMCODE') || die() ?>

<div class="d-flex flex-column flex-md-row justify-content-between mb-3">
    <h2 class="h4 mr-3"><?= language()->link->qr->header ?></h2>
</div>

<div class="row">
    <div class="col-12 col-lg-6 mb-4 mb-lg-0 d-print-none">
        <div class="card">
            <div class="card-body">

                <div class="form-group">
                    <label for="foreground_color"><?= language()->link->qr->configurator->foreground_color ?></label>
                    <input type="hidden" id="foreground_color" name="foreground_color" class="form-control" value="#000000" />
                    <div id="foreground_color_pickr"></div>
                </div>

                <div class="form-group">
                    <label for="background_color"><?= language()->link->qr->configurator->background_color ?></label>
                    <input type="hidden" id="background_color" name="background_color" class="form-control" value="#ffffff" />
                    <div id="background_color_pickr"></div>
                </div>

                <div class="form-group">
                    <label for="corner_radius"><?= language()->link->qr->configurator->corner_radius ?></label>
                    <input type="range" id="corner_radius" class="form-control-range" min="0" max="0.5" step="0.1" value="0" />
                </div>

                <div class="form-group">
                    <label for="type"><?= language()->link->qr->configurator->type ?></label>
                    <select name="type" id="type" class="form-control">
                        <option value="normal" selected="selected"><?= language()->link->qr->configurator->type_normal ?></option>
                        <option value="text"><?= language()->link->qr->configurator->type_text ?></option>
                        <option value="image"><?= language()->link->qr->configurator->type_image ?></option>
                    </select>
                </div>

                <div id="type_text" class="d-none">
                    <div class="form-group">
                        <label for="text"><?= language()->link->qr->configurator->text ?></label>
                        <input type="text" id="text" name="text" class="form-control" value=":)" />
                    </div>

                    <div class="form-group">
                        <label for="text_color"><?= language()->link->qr->configurator->text_color ?></label>
                        <input type="hidden" id="text_color" name="text_color" class="form-control" value="#000000" />
                        <div id="text_color_pickr"></div>
                    </div>

                    <div class="form-group">
                        <label for="text_size"><?= language()->link->qr->configurator->text_size ?></label>
                        <input type="range" id="text_size" class="form-control-range" min="0.05" max="0.1" step="0.005" value="0.005" />
                    </div>
                </div>

                <div id="type_image" class="d-none">
                    <div class="form-group">
                        <label for="image"><?= language()->link->qr->configurator->image ?></label>
                        <input id="image" type="file" name="image" accept=".png, .jpg, .jpeg" class="form-control-file" />
                        <img id="image-buffer" src="" class="d-none" />
                    </div>

                    <div class="form-group">
                        <label for="image_size"><?= language()->link->qr->configurator->image_size ?></label>
                        <input type="range" id="image_size" class="form-control-range" min="0.05" max="0.1" step="0.005" value="0.005" />
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-6">
        <div class="card mb-4">
            <div id="qr"></div>
        </div>

        <div class="row mb-4">
            <div class="col-12 col-lg-6 mb-3 mb-lg-0">
                <button type="button" onclick="window.print()" class="btn btn-block btn-outline-secondary d-print-none">
                    <i class="fa fa-fw fa-sm fa-file-pdf"></i> <?= language()->link->qr->print ?>
                </button>
            </div>

            <div class="col-12 col-lg-6">
                <button id="download" type="button" class="btn btn-block btn-primary d-print-none">
                    <i class="fa fa-fw fa-sm fa-download"></i> <?= language()->link->qr->download ?>
                </button>
            </div>
        </div>
    </div>
</div>


<?php ob_start() ?>
<link href="<?= ASSETS_FULL_URL . 'css/pickr.min.css' ?>" rel="stylesheet" media="screen,print">
<?php \Altum\Event::add_content(ob_get_clean(), 'head') ?>

<?php ob_start() ?>
<script src="<?= ASSETS_FULL_URL . 'js/libraries/jquery-qrcode.min.js' ?>"></script>
<script src="<?= ASSETS_FULL_URL . 'js/libraries/pickr.min.js' ?>"></script>

<script>
    'use strict';

    /* Download handler */
    document.querySelector('#download').addEventListener('click', () => {
        let a = document.createElement('a');
        a.href = document.querySelector('#qr img').getAttribute('src');
        a.download = 'qr.png';
        a.click();
    });

    let generate_qr = () => {
        let qr_url = <?= json_encode($data->link->full_url . '?referrer=qr') ?>;

        let mode = 0;
        let mode_size = 0.1;

        switch(document.querySelector('#type').value) {
            case 'normal':
                mode = 0;
                break;

            case 'text':
                mode = 2;
                mode_size = parseFloat(document.querySelector('#text_size').value)
                break;

            case 'image':
                mode = 4;
                mode_size = parseFloat(document.querySelector('#image_size').value)
                break;
        }


        let default_options = {
            // render method: 'canvas', 'image' or 'div'
            render: 'image',

            // version range somewhere in 1 .. 40
            minVersion: 1,
            maxVersion: 40,

            // error correction level: 'L', 'M', 'Q' or 'H'
            ecLevel: 'H',

            // offset in pixel if drawn onto existing canvas
            left: 0,
            top: 0,

            // size in pixel
            size: 1000,

            // code color or image element
            fill: document.querySelector('#foreground_color').value,

            // background color or image element, null for transparent background
            background: document.querySelector('#background_color').value,

            // content
            text: qr_url,

            // corner radius relative to module width: 0.0 .. 0.5
            radius: document.querySelector('#corner_radius').value,

            // quiet zone in modules
            quiet: 0,

            // modes
            // 0: normal
            // 1: label strip
            // 2: label box
            // 3: image strip
            // 4: image box
            mode: mode,

            mSize: mode_size,
            mPosX: 0.5,
            mPosY: 0.5,

            label: document.querySelector('#text').value,
            fontname: 'arial',
            fontcolor: document.querySelector('#text_color').value,

            image: document.querySelector('#image-buffer')
        };

        /* Delete already existing image generated */
        document.querySelector('#qr img') && document.querySelector('#qr img').remove();

        $('#qr').qrcode(default_options);
    }

    generate_qr();

    /* Initiate the color picker */
    let pickr_options = {
        comparison: false,

        components: {
            preview: true,
            opacity: false,
            hue: true,
            comparison: false,
            interaction: {
                hex: true,
                rgba: false,
                hsla: false,
                hsva: false,
                cmyk: false,
                input: true,
                clear: false,
                save: true
            }
        }
    };

    /* Timer for selecting the color */
    let timer = null;

    /* Foreground color */
    let foreground_color_pickr = Pickr.create({
        el: document.querySelector('#foreground_color_pickr'),
        default: document.querySelector('#foreground_color').value,
        theme: 'nano',
        ...pickr_options
    });

    foreground_color_pickr.off().on('change', hsva => {
        document.querySelector('#foreground_color').value = hsva.toHEXA().toString();

        clearTimeout(timer);
        timer = setTimeout(generate_qr, 100);
    });

    /* Background color */
    let background_color_pickr = Pickr.create({
        el: document.querySelector('#background_color_pickr'),
        default: document.querySelector('#background_color').value,
        theme: 'nano',
        ...pickr_options
    });

    background_color_pickr.off().on('change', hsva => {
        document.querySelector('#background_color').value = hsva.toHEXA().toString();

        clearTimeout(timer);
        timer = setTimeout(generate_qr, 100);
    });

    /* Corner radius */
    document.querySelector('#corner_radius').addEventListener('change', generate_qr);

    /* Type */
    document.querySelector('#type').addEventListener('change', event => {
        let type = document.querySelector('#type').value;

        switch(type) {
            case 'normal':
                document.querySelector('#type_text').classList.add('d-none');
                document.querySelector('#type_image').classList.add('d-none');
                break;

            case 'text':
                document.querySelector('#type_text').classList.remove('d-none');
                document.querySelector('#type_image').classList.add('d-none')
                break;

            case 'image':
                document.querySelector('#type_text').classList.add('d-none');
                document.querySelector('#type_image').classList.remove('d-none')
                break;
        }

        generate_qr();

    });

    /* Text */
    document.querySelector('#text').addEventListener('change', generate_qr);

    /* Text size */
    document.querySelector('#text_size').addEventListener('change', generate_qr);

    /* Text color */
    let text_color_pickr = Pickr.create({
        el: document.querySelector('#text_color_pickr'),
        default: document.querySelector('#text_color').value,
        theme: 'nano',
        ...pickr_options
    });

    text_color_pickr.off().on('change', hsva => {
        document.querySelector('#text_color').value = hsva.toHEXA().toString();

        clearTimeout(timer);
        timer = setTimeout(generate_qr, 100);
    });

    /* Image */
    document.querySelector('#image').addEventListener('change', () => {
        const input = document.querySelector('#image');

        if(input.files && input.files[0]) {
            const reader = new window.FileReader();

            reader.onload = event => {
                document.querySelector('#image-buffer').setAttribute('src', event.target.result);

                setTimeout(generate_qr, 250);
            };

            reader.readAsDataURL(input.files[0]);
        }
    });

    /* Image size */
    document.querySelector('#image_size').addEventListener('change', generate_qr);

</script>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>

