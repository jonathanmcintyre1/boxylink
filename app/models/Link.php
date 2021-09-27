<?php
/*
 * @copyright Copyright (c) 2021 AltumCode (https://altumcode.com/)
 *
 * This software is exclusively sold through https://altumcode.com/ by the AltumCode author.
 * Downloading this product from any other sources and running it without a proper license is illegal,
 *  except the official ones linked from https://altumcode.com/.
 */

namespace Altum\Models;

class Link extends Model {

    public function delete($link_id) {

        if(!$link = db()->where('link_id', $link_id)->getOne('links', ['user_id', 'link_id', 'type', 'settings'])) {
            return;
        }

        /* Process to delete the stored files of the link */
        if($link->type == 'biolink') {
            $link->settings = json_decode($link->settings);

            /* Offload deleting */
            if(\Altum\Plugin::is_active('offload') && settings()->offload->uploads_url) {
                $s3 = new \Aws\S3\S3Client(get_aws_s3_config());

                /* Delete favicon */
                $s3->deleteObject([
                    'Bucket' => settings()->offload->storage_name,
                    'Key' => 'uploads/favicons/' . $link->settings->image,
                ]);

                /* Delete background */
                if(is_string($link->settings->background)) {
                    $s3->deleteObject([
                        'Bucket' => settings()->offload->storage_name,
                        'Key' => 'uploads/backgrounds/' . $link->settings->background,
                    ]);
                }

                /* Delete seo opengraph image */
                $s3->deleteObject([
                    'Bucket' => settings()->offload->storage_name,
                    'Key' => 'uploads/block_images/' . $link->settings->seo->image,
                ]);
            }

            /* Local deleting */
            else {
                /* Delete favicon */
                if(!empty($link->settings->image) && file_exists(UPLOADS_PATH . 'favicons/' . $link->settings->image)) {
                    unlink(UPLOADS_PATH . 'favicons/' . $link->settings->image);
                }

                /* Delete background */
                if(is_string($link->settings->background) && !empty($link->settings->background) && file_exists(UPLOADS_PATH . 'backgrounds/' . $link->settings->background)) {
                    unlink(UPLOADS_PATH . 'backgrounds/' . $link->settings->background);
                }

                /* Delete seo opengraph image */
                if(is_string($link->settings->seo->image) && !empty($link->settings->seo->image) && file_exists(UPLOADS_PATH . 'backgrounds/' . $link->settings->seo->image)) {
                    unlink(UPLOADS_PATH . 'block_images/' . $link->settings->seo->image);
                }
            }

            /* Get all the available biolink blocks and iterate over them to delete the stored images */
            $result = database()->query("SELECT `biolink_block_id` FROM `biolinks_blocks` WHERE `link_id` = {$link->link_id}");
            while($row = $result->fetch_object()) {

                (new \Altum\Models\BiolinkBlock())->delete($row->biolink_block_id);

            }
        }

        /* Delete from database */
        db()->where('link_id', $link_id)->delete('links');

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItemsByTag('biolinks_links_user_' . $link->user_id);
        \Altum\Cache::$adapter->deleteItemsByTag('link_id=' . $link->link_id);

    }
}
