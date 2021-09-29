<?php
/*
 * @copyright Copyright (c) 2021 AltumCode (https://altumcode.com/)
 *
 * This software is exclusively sold through https://altumcode.com/ by the AltumCode author.
 * Downloading this product from any other sources and running it without a proper license is illegal,
 *  except the official ones linked from https://altumcode.com/.
 */

namespace Altum\Models;

class Project extends Model {

    public function get_projects($user_id) {

        $result = database()->query("SELECT * FROM `projects` WHERE `user_id` = {$user_id}");
        $data = [];

        while($row = $result->fetch_object()) {
            $data[$row->project_id] = $row;
        }

        return $data;
    }

}
