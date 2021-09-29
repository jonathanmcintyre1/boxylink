<?php
/*
 * @copyright Copyright (c) 2021 AltumCode (https://altumcode.com/)
 *
 * This software is exclusively sold through https://altumcode.com/ by the AltumCode author.
 * Downloading this product from any other sources and running it without a proper license is illegal,
 *  except the official ones linked from https://altumcode.com/.
 */

namespace Altum\Models;

class Payments extends Model {

    public function codes_payment_check($code, $user) {
        /* Make sure the code exists */
        $codes_code = db()->where('code', $code)->where('type', 'discount')->getOne('codes');

        if($codes_code) {
            /* Check if we should insert the usage of the code or not */
            if(!db()->where('user_id', $user->user_id)->where('code_id', $codes_code->code_id)->has('redeemed_codes')) {

                /* Update the code usage */
                db()->where('code_id', $codes_code->code_id)->update('codes', ['redeemed' => db()->inc()]);

                /* Add log for the redeemed code */
                db()->insert('redeemed_codes', [
                    'code_id'   => $codes_code->code_id,
                    'user_id'   => $user->user_id,
                    'date'      => \Altum\Date::$date
                ]);
            }

            return $codes_code;
        }

        return null;
    }

    public function affiliate_payment_check($payment_id, $payment_total, $user) {
        if(\Altum\Plugin::is_active('affiliate') && settings()->affiliate->is_enabled && $user->referred_by) {
            if((settings()->affiliate->commission_type == 'once' && !$user->referred_by_has_converted) || settings()->affiliate->commission_type == 'forever') {
                $referral_user = db()->where('user_id', $user->referred_by)->getOne('users', ['user_id', 'email', 'active']);

                /* Make sure the referral user is active and existing */
                if($referral_user && $referral_user->active == 1) {
                    $amount = number_format($payment_total * (float) settings()->affiliate->commission_percentage / 100, 2, '.', '');

                    /* Insert the affiliate commission */
                    db()->insert('affiliates_commissions', [
                        'user_id' => $referral_user->user_id,
                        'referred_user_id' => $user->user_id,
                        'payment_id' => $payment_id,
                        'amount' => $amount,
                        'currency' => settings()->payment->currency,
                        'datetime' => \Altum\Date::$date
                    ]);

                    /* Update the referred user */
                    db()->where('user_id', $user->user_id)->update('users', ['referred_by_has_converted' => 1]);
                }
            }
        }
    }

}
