CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER
VIEW `recon`.`viewreconk2` AS
    select `s`.`code` AS `store_code`,
           `s`.`name` AS `store_name`,
           `h`.`date` AS `recdate`,
           if(isnull(`it1t1`.`amount`),0.00,`it1t1`.`amount`) AS `cash`,
           if(isnull(`it1t0`.`amount`),0.00,`it1t0`.`amount`) AS `cash_rp`,
                                         (`cash` - `cash_rp`) AS `cash_var`,
           
           if(isnull(`it2t1`.`amount`),0.00,`it2t1`.`amount`) AS `gcsales_t1`,
           if(isnull(`it2t2`.`amount`),0.00,`it2t2`.`amount`) AS `gcsales_t2`,
           if(isnull(`it2t3`.`amount`),0.00,`it2t3`.`amount`) AS `gcsales_t3`,
           if(isnull(`it2t4`.`amount`),0.00,`it2t4`.`amount`) AS `gcsales_t4`,
           if(isnull(`it2t0`.`amount`),0.00,`it2t0`.`amount`) AS `gcsales_rp`,
           ((`gcsales_t1` + `gcsales_t2` + `gcsales_t3` + `gcsales_t4`) - `gcsales_rp`) AS `gcsales_var`,
           
           if(isnull(`it3t1`.`amount`),0.00,`it3t1`.`amount`) AS `gcredeemed_t1`,
           if(isnull(`it3t2`.`amount`),0.00,`it3t2`.`amount`) AS `gcredeemed_t2`,
           if(isnull(`it3t3`.`amount`),0.00,`it3t3`.`amount`) AS `gcredeemed_t3`,
           if(isnull(`it3t4`.`amount`),0.00,`it3t4`.`amount`) AS `gcredeemed_t4`,
           if(isnull(`it3t0`.`amount`),0.00,`it3t0`.`amount`) AS `gcredeemed_rp`,
           ((`gcredeemed_t1` + `gcredeemed_t2` + `gcredeemed_t3` + `gcredeemed_t4`) - `gcredeemed_rp`) AS `gcredeemed_var`,

           if(isnull(`it4t1`.`amount`),0.00,`it4t1`.`amount`) AS `echeck_t1`,
           if(isnull(`it4t2`.`amount`),0.00,`it4t2`.`amount`) AS `echeck_t2`,
           if(isnull(`it4t3`.`amount`),0.00,`it4t3`.`amount`) AS `echeck_t3`,
           if(isnull(`it4t4`.`amount`),0.00,`it4t4`.`amount`) AS `echeck_t4`,
           if(isnull(`it5t1`.`amount`),0.00,`it5t1`.`amount`) AS `checks`,
           if(isnull(`it5t0`.`amount`),0.00,`it5t0`.`amount`) AS `checks_rp`,
                                     (`checks` - `checks_rp`) AS `checks_var`,

           if(isnull(`it7t1`.`amount`),0.00,`it7t1`.`amount`) AS `visa_t1`,
           if(isnull(`it7t2`.`amount`),0.00,`it7t2`.`amount`) AS `visa_t2`,
           if(isnull(`it7t3`.`amount`),0.00,`it7t3`.`amount`) AS `visa_t3`,
           if(isnull(`it7t4`.`amount`),0.00,`it7t4`.`amount`) AS `visa_t4`,
           if(isnull(`it7t0`.`amount`),0.00,`it7t0`.`amount`) AS `visa_rp`,
           ((`visa_t1` + `visa_t2` + `visa_t3` + `visa_t4`) - `visa_rp`) AS `visa_var`,
           
           if(isnull(`it8t1`.`amount`),0.00,`it8t1`.`amount`) AS `mc_t1`,
           if(isnull(`it8t2`.`amount`),0.00,`it8t2`.`amount`) AS `mc_t2`,
           if(isnull(`it8t3`.`amount`),0.00,`it8t3`.`amount`) AS `mc_t3`,
           if(isnull(`it8t4`.`amount`),0.00,`it8t4`.`amount`) AS `mc_t4`,
           if(isnull(`it8t0`.`amount`),0.00,`it8t0`.`amount`) AS `mc_rp`,
            ((`mc_t1` + `mc_t2` + `mc_t3` + `mc_t4`) - `mc_rp`) AS `mc_var`,
           
           if(isnull(`it9t1`.`amount`),0.00,`it9t1`.`amount`) AS `discover_t1`,
           if(isnull(`it9t2`.`amount`),0.00,`it9t2`.`amount`) AS `discover_t2`,
           if(isnull(`it9t3`.`amount`),0.00,`it9t3`.`amount`) AS `discover_t3`,
           if(isnull(`it9t4`.`amount`),0.00,`it9t4`.`amount`) AS `discover_t4`,
           if(isnull(`it9t0`.`amount`),0.00,`it9t0`.`amount`) AS `discover_rp`,
           ((`discover_t1` + `discover_t2` + `discover_t3` + `discover_t4`) - `discover_rp`) AS `discover_var`,
           
           if(isnull(`it10t1`.`amount`),0.00,`it10t1`.`amount`) AS `amex_t1`,
           if(isnull(`it10t2`.`amount`),0.00,`it10t2`.`amount`) AS `amex_t2`,
           if(isnull(`it10t3`.`amount`),0.00,`it10t3`.`amount`) AS `amex_t3`,
           if(isnull(`it10t4`.`amount`),0.00,`it10t4`.`amount`) AS `amex_t4`,
           if(isnull(`it10t0`.`amount`),0.00,`it10t0`.`amount`) AS `amex_rp`,
           ((`amex_t1` + `amex_t2` + `amex_t3` + `amex_t4`) - `amex_rp`) AS `amex_var`,
           
           if(isnull(`it11t1`.`amount`),0.00,`it11t1`.`amount`) AS `debit_t1`,
           if(isnull(`it11t2`.`amount`),0.00,`it11t2`.`amount`) AS `debit_t2`,
           if(isnull(`it11t3`.`amount`),0.00,`it11t3`.`amount`) AS `debit_t3`,
           if(isnull(`it11t4`.`amount`),0.00,`it11t4`.`amount`) AS `debit_t4`,
           if(isnull(`it11t0`.`amount`),0.00,`it11t0`.`amount`) AS `debit_rp`,
           ((`debit_t1` + `debit_t2` + `debit_t3` + `debit_t4`) - `debit_rp`) AS `debit_var`,
           
           if(isnull(`it12t1`.`amount`),0.00,`it12t1`.`amount`) AS `ge_money_t1`,
           if(isnull(`it12t2`.`amount`),0.00,`it12t2`.`amount`) AS `ge_money_t2`,
           if(isnull(`it12t3`.`amount`),0.00,`it12t3`.`amount`) AS `ge_money_t3`,
           if(isnull(`it12t4`.`amount`),0.00,`it12t4`.`amount`) AS `ge_money_t4`,
           if(isnull(`it12t0`.`amount`),0.00,`it12t0`.`amount`) AS `ge_money_rp`,
           ((`ge_money_t1` + `ge_money_t2` + `ge_money_t3` + `ge_money_t4`) - `ge_money_rp`) AS `ge_money_var`,
           
           if(isnull(`it33t1`.`amount`),0.00,`it33t1`.`amount`) AS `amazon_t1`,
           if(isnull(`it33t2`.`amount`),0.00,`it33t2`.`amount`) AS `amazon_t2`,
           if(isnull(`it33t3`.`amount`),0.00,`it33t3`.`amount`) AS `amazon_t3`,
           if(isnull(`it33t4`.`amount`),0.00,`it33t4`.`amount`) AS `amazon_t4`,
           if(isnull(`it33t0`.`amount`),0.00,`it33t0`.`amount`) AS `amazon_rp`,
           ((`amazon_t1` + `amazon_t2` + `amazon_t3` + `amazon_t4`) - `amazon_rp`) AS `amazon_var`,

           if(isnull(`it34t1`.`amount`),0.00,`it34t1`.`amount`) AS `paypal_t1`,
           if(isnull(`it34t2`.`amount`),0.00,`it34t2`.`amount`) AS `paypal_t2`,
           if(isnull(`it34t3`.`amount`),0.00,`it34t3`.`amount`) AS `paypal_t3`,
           if(isnull(`it34t4`.`amount`),0.00,`it34t4`.`amount`) AS `paypal_t4`,
           if(isnull(`it34t0`.`amount`),0.00,`it34t0`.`amount`) AS `paypal_rp`,
           ((`amazon_t1` + `amazon_t2` + `amazon_t3` + `amazon_t4`) - `amazon_rp`) AS `paypal_var`,

           if(isnull(`it35t1`.`amount`),0.00,`it35t1`.`amount`) AS `wire_t1`,
           if(isnull(`it35t2`.`amount`),0.00,`it35t2`.`amount`) AS `wire_t2`,
           if(isnull(`it35t3`.`amount`),0.00,`it35t3`.`amount`) AS `wire_t3`,
           if(isnull(`it35t4`.`amount`),0.00,`it35t4`.`amount`) AS `wire_t4`,
           if(isnull(`it35t0`.`amount`),0.00,`it35t0`.`amount`) AS `wire_rp`,
           ((`wire_t1` + `wire_t2` + `wire_t3` + `wire_t4`) - `wire_rp`) AS `wire_var`,

           if(isnull(`it37t1`.`amount`),0.00,`it37t1`.`amount`) AS `misc_t1`,
           if(isnull(`it37t2`.`amount`),0.00,`it37t2`.`amount`) AS `misc_t2`,
           if(isnull(`it37t3`.`amount`),0.00,`it37t3`.`amount`) AS `misc_t3`,
           if(isnull(`it37t4`.`amount`),0.00,`it37t4`.`amount`) AS `misc_t4`,
           if(isnull(`it37t0`.`amount`),0.00,`it37t0`.`amount`) AS `misc_rp`,
           ((`misc_t1` + `misc_t2` + `misc_t3` + `misc_t4`) - `misc_rp`) AS `misc_var`,

           ((
           `cash` +
           `gcsales_t1` +
           `visa_t1` +
           `mc_t1` +
           `discover_t1` +
           `amex_t1` +
           `debit_t1` +
           `ge_money_t1` +
           `amazon_t1` +
           `paypal_t1` +
           `wire_t1` +
           `misc_t1`) - `gcredeemed_t1`) AS `total_t1`,
           
           ((
           `gcsales_t2` +
           `visa_t2` +
           `mc_t2` +
           `discover_t2` +
           `amex_t2` +
           `debit_t2` +
           `ge_money_t2` +
           `amazon_t2` +
           `paypal_t2` +
           `wire_t2` +
           `misc_t2`) - `gcredeemed_t2`) AS `total_t2`,
           
           ((
           `gcsales_t3` +
           `visa_t3` +
           `mc_t3` +
           `discover_t3` +
           `amex_t3` +
           `debit_t3` +
           `ge_money_t3` +
           `amazon_t3` +
           `paypal_t3` +
           `wire_t3` +
           `misc_t3`) - `gcredeemed_t3`) AS `total_t3`,
           
           ((
           `gcsales_t4` +
           `visa_t4` +
           `mc_t4` +
           `discover_t4` +
           `amex_t4` +
           `debit_t4` +
           `ge_money_t4` +
           `amazon_t4` +
           `paypal_t4` +
           `wire_t4` +
           `misc_t4`) - `gcredeemed_t4`) AS `total_t4`,
           
           ((
           `gcsales_rp` +
           `visa_rp` +
           `mc_rp` +
           `discover_rp` +
           `amex_rp` +
           `debit_rp` +
           `ge_money_rp` +
           `amazon_rp` +
           `paypal_rp` +
           `wire_rp` +
           `misc_rp`) - `gcredeemed_rp`) AS `total_rp`,
           
          ((
          `cash_var` +
          `gcsales_var` +
          `checks_var` +
          `visa_var` +
          `mc_var` +
          `discover_var` +
          `amex_var` +
          `debit_var` +
          `ge_money_var` +
          `amazon_var` +
          `paypal_var` +
          `wire_var` +
          `misc_var`) - `gcredeemed_var`) AS `total_var`
          
          `h`.`note` AS `notes`,
          `h`.`update_ts` AS `recupd`

from ((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((`recon`.`headers` `h`
    left join `recon`.`stores` `s`     on((`h`.`store_id` = `s`.`id`)))
    left join `recon`.`items` `it1t0`  on(((`it1t0`.`header_id` = `h`.`id`)  and (`it1t0`.`itemtype_id` = 1)   and (`it1t0`.`term_num` = 0))))
    left join `recon`.`items` `it1t1`  on(((`it1t1`.`header_id` = `h`.`id`)  and (`it1t1`.`itemtype_id` = 1)   and (`it1t1`.`term_num` = 1))))
    left join `recon`.`items` `it2t1`  on(((`it2t1`.`header_id` = `h`.`id`)  and (`it2t1`.`itemtype_id` = 2)   and (`it2t1`.`term_num` = 1))))
    left join `recon`.`items` `it2t2`  on(((`it2t2`.`header_id` = `h`.`id`)  and (`it2t2`.`itemtype_id` = 2)   and (`it2t2`.`term_num` = 2))))
    left join `recon`.`items` `it2t3`  on(((`it2t3`.`header_id` = `h`.`id`)  and (`it2t3`.`itemtype_id` = 2)   and (`it2t3`.`term_num` = 3))))
    left join `recon`.`items` `it2t4`  on(((`it2t4`.`header_id` = `h`.`id`)  and (`it2t4`.`itemtype_id` = 2)   and (`it2t4`.`term_num` = 4))))
    left join `recon`.`items` `it3t0`  on(((`it3t0`.`header_id` = `h`.`id`)  and (`it3t0`.`itemtype_id` = 3)   and (`it3t0`.`term_num` = 0))))
    left join `recon`.`items` `it3t1`  on(((`it3t1`.`header_id` = `h`.`id`)  and (`it3t1`.`itemtype_id` = 3)   and (`it3t1`.`term_num` = 1))))
    left join `recon`.`items` `it3t2`  on(((`it3t2`.`header_id` = `h`.`id`)  and (`it3t2`.`itemtype_id` = 3)   and (`it3t2`.`term_num` = 2))))
    left join `recon`.`items` `it3t3`  on(((`it3t3`.`header_id` = `h`.`id`)  and (`it3t3`.`itemtype_id` = 3)   and (`it3t3`.`term_num` = 3))))
    left join `recon`.`items` `it3t4`  on(((`it3t4`.`header_id` = `h`.`id`)  and (`it3t4`.`itemtype_id` = 3)   and (`it3t4`.`term_num` = 4))))
    left join `recon`.`items` `it4t1`  on(((`it4t1`.`header_id` = `h`.`id`)  and (`it4t1`.`itemtype_id` = 4)   and (`it4t1`.`term_num` = 1))))
    left join `recon`.`items` `it4t2`  on(((`it4t2`.`header_id` = `h`.`id`)  and (`it4t2`.`itemtype_id` = 4)   and (`it4t2`.`term_num` = 2))))
    left join `recon`.`items` `it4t3`  on(((`it4t3`.`header_id` = `h`.`id`)  and (`it4t3`.`itemtype_id` = 4)   and (`it4t3`.`term_num` = 3))))
    left join `recon`.`items` `it4t4`  on(((`it4t4`.`header_id` = `h`.`id`)  and (`it4t4`.`itemtype_id` = 4)   and (`it4t4`.`term_num` = 4))))
    left join `recon`.`items` `it5t0`  on(((`it5t0`.`header_id` = `h`.`id`)  and (`it5t0`.`itemtype_id` = 5)   and (`it5t0`.`term_num` = 0))))
    left join `recon`.`items` `it5t1`  on(((`it5t1`.`header_id` = `h`.`id`)  and (`it5t1`.`itemtype_id` = 5)   and (`it5t1`.`term_num` = 1))))
    left join `recon`.`items` `it7t1`  on(((`it7t1`.`header_id` = `h`.`id`)  and (`it7t1`.`itemtype_id` = 7)   and (`it7t1`.`term_num` = 1))))
    left join `recon`.`items` `it7t2`  on(((`it7t2`.`header_id` = `h`.`id`)  and (`it7t2`.`itemtype_id` = 7)   and (`it7t2`.`term_num` = 2))))
    left join `recon`.`items` `it7t3`  on(((`it7t3`.`header_id` = `h`.`id`)  and (`it7t3`.`itemtype_id` = 7)   and (`it7t3`.`term_num` = 3))))
    left join `recon`.`items` `it7t4`  on(((`it7t4`.`header_id` = `h`.`id`)  and (`it7t4`.`itemtype_id` = 7)   and (`it7t4`.`term_num` = 4))))
    left join `recon`.`items` `it7t0`  on(((`it7t0`.`header_id` = `h`.`id`)  and (`it7t0`.`itemtype_id` = 7)   and (`it7t0`.`term_num` = 0))))
    left join `recon`.`items` `it8t1`  on(((`it8t1`.`header_id` = `h`.`id`)  and (`it8t1`.`itemtype_id` = 8)   and (`it8t1`.`term_num` = 1))))
    left join `recon`.`items` `it8t2`  on(((`it8t2`.`header_id` = `h`.`id`)  and (`it8t2`.`itemtype_id` = 8)   and (`it8t2`.`term_num` = 2))))
    left join `recon`.`items` `it8t3`  on(((`it8t3`.`header_id` = `h`.`id`)  and (`it8t3`.`itemtype_id` = 8)   and (`it8t3`.`term_num` = 3))))
    left join `recon`.`items` `it8t4`  on(((`it8t4`.`header_id` = `h`.`id`)  and (`it8t4`.`itemtype_id` = 8)   and (`it8t4`.`term_num` = 4))))
    left join `recon`.`items` `it8t0`  on(((`it8t0`.`header_id` = `h`.`id`)  and (`it8t0`.`itemtype_id` = 8)   and (`it8t0`.`term_num` = 0))))
    left join `recon`.`items` `it9t1`  on(((`it9t1`.`header_id` = `h`.`id`)  and (`it9t1`.`itemtype_id` = 9)   and (`it9t1`.`term_num` = 1))))
    left join `recon`.`items` `it9t2`  on(((`it9t2`.`header_id` = `h`.`id`)  and (`it9t2`.`itemtype_id` = 9)   and (`it9t2`.`term_num` = 2))))
    left join `recon`.`items` `it9t3`  on(((`it9t3`.`header_id` = `h`.`id`)  and (`it9t3`.`itemtype_id` = 9)   and (`it9t3`.`term_num` = 3))))
    left join `recon`.`items` `it9t4`  on(((`it9t4`.`header_id` = `h`.`id`)  and (`it9t4`.`itemtype_id` = 9)   and (`it9t4`.`term_num` = 4))))
    left join `recon`.`items` `it9t0`  on(((`it9t0`.`header_id` = `h`.`id`)  and (`it9t0`.`itemtype_id` = 9)   and (`it9t0`.`term_num` = 0))))
    left join `recon`.`items` `it10t1` on(((`it10t1`.`header_id` = `h`.`id`) and (`it10t1`.`itemtype_id` = 10) and (`it10t1`.`term_num` = 1))))
    left join `recon`.`items` `it10t2` on(((`it10t2`.`header_id` = `h`.`id`) and (`it10t2`.`itemtype_id` = 10) and (`it10t2`.`term_num` = 2))))
    left join `recon`.`items` `it10t3` on(((`it10t3`.`header_id` = `h`.`id`) and (`it10t3`.`itemtype_id` = 10) and (`it10t3`.`term_num` = 3))))
    left join `recon`.`items` `it10t4` on(((`it10t4`.`header_id` = `h`.`id`) and (`it10t4`.`itemtype_id` = 10) and (`it10t4`.`term_num` = 4))))
    left join `recon`.`items` `it10t0` on(((`it10t0`.`header_id` = `h`.`id`) and (`it10t0`.`itemtype_id` = 10) and (`it10t0`.`term_num` = 0))))
    left join `recon`.`items` `it11t1` on(((`it11t1`.`header_id` = `h`.`id`) and (`it11t1`.`itemtype_id` = 11) and (`it11t1`.`term_num` = 1))))
    left join `recon`.`items` `it11t2` on(((`it11t2`.`header_id` = `h`.`id`) and (`it11t2`.`itemtype_id` = 11) and (`it11t2`.`term_num` = 2))))
    left join `recon`.`items` `it11t3` on(((`it11t3`.`header_id` = `h`.`id`) and (`it11t3`.`itemtype_id` = 11) and (`it11t3`.`term_num` = 3))))
    left join `recon`.`items` `it11t4` on(((`it11t4`.`header_id` = `h`.`id`) and (`it11t4`.`itemtype_id` = 11) and (`it11t4`.`term_num` = 4))))
    left join `recon`.`items` `it11t0` on(((`it11t0`.`header_id` = `h`.`id`) and (`it11t0`.`itemtype_id` = 11) and (`it11t0`.`term_num` = 0))))
    left join `recon`.`items` `it12t1` on(((`it12t1`.`header_id` = `h`.`id`) and (`it12t1`.`itemtype_id` = 12) and (`it12t1`.`term_num` = 1))))
    left join `recon`.`items` `it12t2` on(((`it12t2`.`header_id` = `h`.`id`) and (`it12t2`.`itemtype_id` = 12) and (`it12t2`.`term_num` = 2))))
    left join `recon`.`items` `it12t3` on(((`it12t3`.`header_id` = `h`.`id`) and (`it12t3`.`itemtype_id` = 12) and (`it12t3`.`term_num` = 3))))
    left join `recon`.`items` `it12t4` on(((`it12t4`.`header_id` = `h`.`id`) and (`it12t4`.`itemtype_id` = 12) and (`it12t4`.`term_num` = 4))))
    left join `recon`.`items` `it12t0` on(((`it12t0`.`header_id` = `h`.`id`) and (`it12t0`.`itemtype_id` = 12) and (`it12t0`.`term_num` = 0))))
    left join `recon`.`items` `it33t0`  on(((`it33t0`.`header_id` = `h`.`id`)  and (`it33t0`.`itemtype_id` = 33)   and (`it33t0`.`term_num` = 0))))
    left join `recon`.`items` `it33t1`  on(((`it33t1`.`header_id` = `h`.`id`)  and (`it33t1`.`itemtype_id` = 33)   and (`it33t1`.`term_num` = 1))))
    left join `recon`.`items` `it33t2`  on(((`it33t2`.`header_id` = `h`.`id`)  and (`it33t2`.`itemtype_id` = 33)   and (`it33t2`.`term_num` = 2))))
    left join `recon`.`items` `it33t3`  on(((`it33t3`.`header_id` = `h`.`id`)  and (`it33t3`.`itemtype_id` = 33)   and (`it33t3`.`term_num` = 3))))
    left join `recon`.`items` `it33t4`  on(((`it33t4`.`header_id` = `h`.`id`)  and (`it33t4`.`itemtype_id` = 33)   and (`it33t4`.`term_num` = 4))))
    left join `recon`.`items` `it34t0`  on(((`it34t0`.`header_id` = `h`.`id`)  and (`it34t0`.`itemtype_id` = 33)   and (`it34t0`.`term_num` = 0))))
    left join `recon`.`items` `it34t1`  on(((`it34t1`.`header_id` = `h`.`id`)  and (`it34t1`.`itemtype_id` = 33)   and (`it34t1`.`term_num` = 1))))
    left join `recon`.`items` `it34t2`  on(((`it34t2`.`header_id` = `h`.`id`)  and (`it34t2`.`itemtype_id` = 33)   and (`it34t2`.`term_num` = 2))))
    left join `recon`.`items` `it34t3`  on(((`it34t3`.`header_id` = `h`.`id`)  and (`it34t3`.`itemtype_id` = 33)   and (`it34t3`.`term_num` = 3))))
    left join `recon`.`items` `it34t4`  on(((`it34t4`.`header_id` = `h`.`id`)  and (`it34t4`.`itemtype_id` = 33)   and (`it34t4`.`term_num` = 4))))
    left join `recon`.`items` `it35t0`  on(((`it35t0`.`header_id` = `h`.`id`)  and (`it35t0`.`itemtype_id` = 33)   and (`it35t0`.`term_num` = 0))))
    left join `recon`.`items` `it35t1`  on(((`it35t1`.`header_id` = `h`.`id`)  and (`it35t1`.`itemtype_id` = 33)   and (`it35t1`.`term_num` = 1))))
    left join `recon`.`items` `it35t2`  on(((`it35t2`.`header_id` = `h`.`id`)  and (`it35t2`.`itemtype_id` = 33)   and (`it35t2`.`term_num` = 2))))
    left join `recon`.`items` `it35t3`  on(((`it35t3`.`header_id` = `h`.`id`)  and (`it35t3`.`itemtype_id` = 33)   and (`it35t3`.`term_num` = 3))))
    left join `recon`.`items` `it35t4`  on(((`it35t4`.`header_id` = `h`.`id`)  and (`it35t4`.`itemtype_id` = 33)   and (`it35t4`.`term_num` = 4))))
    left join `recon`.`items` `it37t0`  on(((`it37t0`.`header_id` = `h`.`id`)  and (`it37t0`.`itemtype_id` = 33)   and (`it37t0`.`term_num` = 0))))
    left join `recon`.`items` `it37t1`  on(((`it37t1`.`header_id` = `h`.`id`)  and (`it37t1`.`itemtype_id` = 33)   and (`it37t1`.`term_num` = 1))))
    left join `recon`.`items` `it37t2`  on(((`it37t2`.`header_id` = `h`.`id`)  and (`it37t2`.`itemtype_id` = 33)   and (`it37t2`.`term_num` = 2))))
    left join `recon`.`items` `it37t3`  on(((`it37t3`.`header_id` = `h`.`id`)  and (`it37t3`.`itemtype_id` = 33)   and (`it37t3`.`term_num` = 3))))
    left join `recon`.`items` `it37t4`  on(((`it37t4`.`header_id` = `h`.`id`)  and (`it37t4`.`itemtype_id` = 33)   and (`it37t4`.`term_num` = 4))))