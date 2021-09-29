<?php defined('ALTUMCODE') || die() ?>
<!DOCTYPE html>
<html>
    <head></head>

    <body>
        <?= $this->views['pixels'] ?>

        <script>
             window.location.href = <?= json_encode($data->location_url) ?>;
        </script>
    </body>
</html>
