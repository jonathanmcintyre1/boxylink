<?php
/*
 * @copyright Copyright (c) 2021 AltumCode (https://altumcode.com/)
 *
 * This software is exclusively sold through https://altumcode.com/ by the AltumCode author.
 * Downloading this product from any other sources and running it without a proper license is illegal,
 *  except the official ones linked from https://altumcode.com/.
 */

namespace Altum;

class Captcha {

    /* Configuration Variables */
    private $image_width = 120;
    private $image_height = 30;
    private $text_length = 6;
    private $lines = 6;
    private $background_color = [255, 255, 255];
    private $text_color = [0, 0, 0];
    private $lines_color = [63, 63, 63];

    public function __construct() {
        /* :) */
    }


    /* Custom valid function for both the normal captcha and the recaptcha */
    public function is_valid() {

        if(settings()->captcha->type == 'recaptcha' && settings()->captcha->recaptcha_public_key && settings()->captcha->recaptcha_private_key) {

            $recaptcha = new \ReCaptcha\ReCaptcha(settings()->captcha->recaptcha_private_key);
            $response = $recaptcha->verify($_POST['g-recaptcha-response'], $_SERVER['REMOTE_ADDR']);

            return ($response->isSuccess());

        }

        else if(settings()->captcha->type == 'hcaptcha' && settings()->captcha->hcaptcha_site_key && settings()->captcha->hcaptcha_secret_key) {

            $response = \Unirest\Request::post('https://hcaptcha.com/siteverify', [], [
                'secret' => settings()->captcha->hcaptcha_secret_key,
                'response' => $_POST['h-captcha-response'],
            ]);

            return isset($response->body) && isset($response->body->success) && $response->body->success;

        }

        else {

            return ($_POST['captcha'] == $_SESSION['captcha']);

        }
    }

    /* Display function based on the captcha settings ( normal captcha or recaptcha ) */
    public function display() {

        if(settings()->captcha->type == 'recaptcha' && settings()->captcha->recaptcha_public_key && settings()->captcha->recaptcha_private_key) {

            echo '<div class="g-recaptcha" data-sitekey="' . settings()->captcha->recaptcha_public_key . '"></div>';
            echo '<script src="https://www.google.com/recaptcha/api.js" async defer></script>';
            echo '<input type="hidden" name="captcha" class="form-control ' . (\Altum\Alerts::has_field_errors('captcha') ? 'is-invalid' : null) . '">';
            echo \Altum\Alerts::output_field_error('captcha');

        }

        else if(settings()->captcha->type == 'hcaptcha' && settings()->captcha->hcaptcha_site_key && settings()->captcha->hcaptcha_secret_key) {

            echo '<div class="h-captcha" data-sitekey="' . settings()->captcha->hcaptcha_site_key . '"></div>';
            echo '<script src="https://hcaptcha.com/1/api.js" async defer></script>';
            echo '<input type="hidden" name="captcha" class="form-control ' . (\Altum\Alerts::has_field_errors('captcha') ? 'is-invalid' : null) . '">';
            echo \Altum\Alerts::output_field_error('captcha');

        }

        else {
            echo '
            <img src="data:image/png;base64,' . base64_encode($this->create_simple_captcha()) . '" class="mb-2" id="captcha" alt="' . language()->global->accessibility->captcha_alt . '" />
            <input type="text" name="captcha" class="form-control ' . (\Altum\Alerts::has_field_errors('captcha') ? 'is-invalid' : null) . '" placeholder="' . language()->global->captcha_placeholder . '" aria-label="' . language()->global->accessibility->captcha_input . '" required="required" autocomplete="off" />
            ' . \Altum\Alerts::output_field_error('captcha') . '
            ';
        }

    }

    /* Generating the captcha image */
    public function create_simple_captcha() {

        /* Generate the text */
        $text = null;

        for($i = 1; $i <= $this->text_length; $i++) $text .= mt_rand(1, 9) . ' ';

        /* Store the generated text in Sessions */
        $_SESSION['captcha'] = str_replace(' ', '', $text);

        /* Create the image */
        $image = imagecreate($this->image_width, $this->image_height);

        /* Define the background color */
        imagecolorallocate($image, $this->background_color[0], $this->background_color[1], $this->background_color[2]);

        /* Start writing the text */
        imagestring($image, 5, 7, 7, $text, imagecolorallocate($image, $this->text_color[0], $this->text_color[1], $this->text_color[2]));

        /* Generate lines */
        for($i = 1; $i <= $this->lines; $i++) imageline($image, mt_rand(1, $this->image_width), mt_rand(1, $this->image_height), mt_rand(1, $this->image_width), mt_rand(1, $this->image_height), imagecolorallocate($image, $this->lines_color[0], $this->lines_color[1], $this->lines_color[2]));

        /* Output the image */
        ob_start();

        imagepng($image, null, 9);

        $image_data = ob_get_clean();

        return $image_data;

    }


}
