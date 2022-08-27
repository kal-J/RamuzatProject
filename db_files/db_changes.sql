-- 09 -Sep - 2020
ALTER TABLE `fms_journal_transaction_line` ADD `transaction_date` DATE NOT NULL AFTER `credit_amount`, ADD `reference_no` VARCHAR(250) NULL DEFAULT NULL AFTER `transaction_date`, ADD `reference_id` VARCHAR(250) NULL DEFAULT NULL AFTER `reference_no`;
UPDATE `fms_journal_type` SET `type_name` = 'Savings deposits' WHERE `fms_journal_type`.`id` = 7; UPDATE `fms_journal_type` SET `type_name` = 'Savings withdraws' WHERE `fms_journal_type`.`id` = 8;
ALTER TABLE `fms_accounts_chart` CHANGE `opening_balance` `opening_balance` DECIMAL(15,2) NULL DEFAULT NULL;
ALTER TABLE `fms_journal_transaction` CHANGE `ref_id` `ref_id` VARCHAR(30) NULL DEFAULT NULL;

ALTER TABLE `fms_user` ADD `verification_code` INT(4) NULL DEFAULT NULL AFTER `comment`, ADD `verified` TINYINT(1) NOT NULL DEFAULT '0' AFTER `verification_code`;
ALTER TABLE `fms_journal_transaction_line` CHANGE `id` `id` BIGINT(20) NOT NULL AUTO_INCREMENT;

ALTER TABLE `fms_organisation` ADD `two_factor` TINYINT(1) NOT NULL DEFAULT '0' AFTER `savings_shares`, ADD `two_factor_choice` TINYINT(1) NULL DEFAULT NULL AFTER `two_factor`;
ALTER TABLE `fms_organisation` CHANGE `two_factor_choice` `two_factor_choice` TINYINT(1) NULL DEFAULT NULL COMMENT ' 1 = SMS, 2=EMAIL, 3=STAFF NO';

-- 10--NOV-2020
INSERT INTO `fms_modules` (`id`, `module_name`, `description`, `status_id`, `date_created`) VALUES (NULL, 'Emails', 'Send email notification', '1', current_timestamp());

-- 16 --Nov -- 2020
ALTER TABLE `fms_repayment_schedule` CHANGE `principle_amount` `principal_amount` DECIMAL(12,2) NOT NULL;
ALTER TABLE `fms_loan_installment_payment` CHANGE `paid_principle` `paid_principal` DECIMAL(12,2) NOT NULL, CHANGE `principle_over` `principal_over` DECIMAL(15,2) NULL DEFAULT NULL;

ALTER TABLE `fms_savings_product` ADD `interest_applicable` TINYINT(2) NOT NULL DEFAULT '0' AFTER `savings_fees_income_receivable_account_id`, ADD `interest_type` TINYINT(2) NOT NULL COMMENT '1=SIMPLE, 2= COMPOUND' AFTER `interest_applicable`;
ALTER TABLE `fms_savings_product` ADD `min_save_period` DECIMAL(6,2) NOT NULL AFTER `interest_type`, ADD `max_save_period` DECIMAL(6,2) NOT NULL AFTER `min_save_period`;
ALTER TABLE `fms_savings_product`
  DROP `min_save_period`,
  DROP `max_save_period`;
  
ALTER TABLE `fms_savings_product` DROP `defaulttermlength`;
ALTER TABLE `fms_savings_product` DROP `termtimeunit`;
ALTER TABLE `fms_savings_product` DROP `interest_type`;
ALTER TABLE `fms_savings_interest_setting` DROP `account_bal_for_Calc_interest`;

ALTER TABLE `fms_savings_interest_setting` ADD `product_id` INT(11) NOT NULL AFTER `id`, ADD `amount` DECIMAL(5,2) NOT NULL AFTER `product_id`, ADD `min_range` DECIMAL(6,2) NOT NULL AFTER `amount`, ADD `max_range` DECIMAL(6,2) NOT NULL AFTER `min_range`, ADD `date_created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `max_range`, ADD `created_by` INT(11) NOT NULL AFTER `date_created`;
ALTER TABLE `fms_savings_interest_setting` CHANGE `min_range` `min_range` DECIMAL(5,1) NOT NULL, CHANGE `max_range` `max_range` DECIMAL(5,1) NOT NULL;
ALTER TABLE `fms_savings_interest_setting` CHANGE `amount` `range_amount` DECIMAL(5,2) NOT NULL;
ALTER TABLE `fms_savings_interest_setting` CHANGE `id` `id` INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `fms_savings_interest_setting` CHANGE `range_amount` `range_amount` DECIMAL(5,1) NOT NULL;
ALTER TABLE `fms_savings_product`
  DROP `interest_applicable`;

  ALTER TABLE `fms_savings_product` CHANGE `defaultinterestrate` `defaultinterestrate` DECIMAL(4,1) NULL DEFAULT NULL;
  ALTER TABLE `fms_savings_account` ADD `date_opened` DATE NULL DEFAULT NULL AFTER `term_length`;
  ALTER TABLE `fms_savings_account` ADD `last_interest_cal_date` DATE NULL DEFAULT NULL AFTER `term_length`;
  ALTER TABLE `fms_savings_account` ADD `when_interest_paid` INT(2) NOT NULL DEFAULT '1' AFTER `interest_rate`;
  ALTER TABLE `fms_savings_product` CHANGE `allowarbitraryfees` `account_balance_for_interest_cal` TINYINT(1) NOT NULL;
  INSERT INTO `fms_journal_type` (`id`, `type_name`) VALUES ('30', 'Interest Payable'), ('31', 'Interest Paid');
ALTER TABLE `fms_journal_type` CHANGE `id` `id` INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `fms_savings_product` ADD `when_interest_paid_units` TINYINT(2) NOT NULL DEFAULT '3' AFTER `termtimeunit`;
ALTER TABLE `fms_savings_product` CHANGE `wheninterestispaid` `wheninterestispaid` INT(3) NULL DEFAULT NULL;


-- 08-dec-2020

ALTER TABLE `fms_member_fees` ADD `receivable_account_id` INT(11) NOT NULL AFTER `modified_by`;
ALTER TABLE `fms_member_fees` ADD `fee_paid` TINYINT(2) NOT NULL DEFAULT '0' AFTER `receivable_account_id`;
ALTER TABLE `fms_applied_member_fees` ADD `fee_paid` TINYINT(1) NOT NULL AFTER `amount`;
ALTER TABLE `fms_applied_member_fees` CHANGE `fee_paid` `fee_paid` TINYINT(1) NOT NULL DEFAULT '0';
ALTER TABLE `fms_member_fees` DROP `fee_paid`;
ALTER TABLE `fms_applied_member_fees` CHANGE `payment_id` `payment_id` INT(3) NULL DEFAULT NULL;
ALTER TABLE `fms_client_subscription` ADD `sub_fee_paid` TINYINT(1) NOT NULL DEFAULT '0' AFTER `payment_date`;
ALTER TABLE `fms_client_subscription` CHANGE `payment_id` `payment_id` INT(3) NULL DEFAULT NULL;

UPDATE `fms_account_sub_categories` SET `category_id` = '2', `sub_cat_name` = 'Dividends Payable' WHERE `fms_account_sub_categories`.`id` = 18;
UPDATE `fms_account_sub_categories` SET `category_id` = '2' WHERE `fms_account_sub_categories`.`id` = 20;
UPDATE `fms_account_sub_categories` SET `sub_cat_code` = '2-5' WHERE `fms_account_sub_categories`.`id` = 18;
UPDATE `fms_account_sub_categories` SET `sub_cat_code` = '2-6' WHERE `fms_account_sub_categories`.`id` = 20;
INSERT INTO `fms_account_sub_categories` (`id`, `category_id`, `sub_cat_name`, `sub_cat_code`, `description`) VALUES (NULL, '1', 'Accumulated Depreciation', '1-8', 'Accumulated Depreciation');

ALTER TABLE `fms_share_issuance` ADD `issuance_name` VARCHAR(400) NOT NULL FIRST, ADD `price_per_share` DECIMAL(15,2) NOT NULL AFTER `issuance_name`;

-- 15-01-2021
ALTER TABLE `fms_share_issuance` ADD `link_to_savings` TINYINT(1) NOT NULL DEFAULT '0' AFTER `allow_inactive_clients_dividends`;
ALTER TABLE `fms_share_fees` ADD `chargetrigger_id` TINYINT(2) NOT NULL AFTER `amount`;

INSERT INTO `fms_journal_type` (`id`, `type_name`) VALUES (NULL, 'Share Transaction Charge'), (NULL, 'Share Transfer Charge');
ALTER TABLE `fms_share_issuance_fees` ADD `share_fees_income_account_id` INT(11) NOT NULL AFTER `sharefee_id`, ADD `share_fees_income_receivable_account_id` INT(11) NOT NULL AFTER `share_fees_income_account_id`;
INSERT INTO `fms_transaction_type` (`id`, `type_name`, `description`) VALUES ('10', 'Share Transfer', 'Share Transfer');
INSERT INTO `fms_transaction_type` (`id`, `type_name`, `description`) VALUES ('11', 'Transaction Charge', 'Transaction Charge');
UPDATE `fms_transaction_type` SET `type_name` = 'Share Transaction Charge' WHERE `fms_transaction_type`.`id` = 11;

-- 27-01-2020
ALTER TABLE `fms_savings_interest_payment` ADD `qualifying_amount` DECIMAL(15,2) NOT NULL AFTER `savings_account_id`;

-- 06-02-2021
INSERT INTO `fms_account_sub_categories` (`id`, `category_id`, `sub_cat_name`, `sub_cat_code`, `description`) VALUES (NULL, '5', 'Taxes', '5-4', 'Taxes and Statutory obligations');
UPDATE `fms_account_sub_categories` SET `sub_cat_name` = 'Taxation' WHERE `fms_account_sub_categories`.`id` = 25;

-- 14-02-2021

ALTER TABLE `fms_journal_type` ADD `status` TINYINT(1) NOT NULL DEFAULT '0' AFTER `type_name`; 

-- 15- 02-2021
UPDATE `fms_interest_cal_method` SET `description` = 'Current Account Balance' WHERE `fms_interest_cal_method`.`id` = 1;
UPDATE `fms_interest_cal_method` SET `description` = 'Accumulated Account Balance including interest' WHERE `fms_interest_cal_method`.`id` = 2;
UPDATE `fms_interest_cal_method` SET `interest_method` = 'Simple Interest' WHERE `fms_interest_cal_method`.`id` = 1;
UPDATE `fms_interest_cal_method` SET `interest_method` = 'Compound Interest' WHERE `fms_interest_cal_method`.`id` = 2;

UPDATE `fms_journal_type` SET `type_name` = 'Withdraw charges' WHERE `fms_journal_type`.`id` = 9; 

INSERT INTO `fms_charge_trigger` (`id`, `charge_trigger_name`, `charge_trigger_description`, `status_id`) VALUES (NULL, 'Applicable on Cash Withdraw', '', '1'), (NULL, 'Applicable on Bank Withdraw', '', '1'); 

-- 15- 03-2021 Reagan
ALTER TABLE `fms_transaction` CHANGE `transaction_date` `transaction_date` DATETIME(6) NULL DEFAULT NULL; 

ALTER TABLE `fms_repayment_schedule` ADD CONSTRAINT `fk_loan_id_repayment_schedule` FOREIGN KEY (`client_loan_id`) REFERENCES `fms_client_loan`(`id`) ON DELETE CASCADE ON UPDATE CASCADE; 
ALTER TABLE `fms_loan_installment_payment` ADD CONSTRAINT `fk_loan_id_installment_payment` FOREIGN KEY (`client_loan_id`) REFERENCES `fms_client_loan`(`id`) ON DELETE CASCADE ON UPDATE CASCADE; 
ALTER TABLE `fms_loan_state` ADD CONSTRAINT `fk_loan_id_loan_state` FOREIGN KEY (`client_loan_id`) REFERENCES `fms_client_loan`(`id`) ON DELETE CASCADE ON UPDATE CASCADE; 
ALTER TABLE `fms_applied_loan_fee` ADD CONSTRAINT `fk_loan_id_applied_loan_fee` FOREIGN KEY (`client_loan_id`) REFERENCES `fms_client_loan`(`id`) ON DELETE CASCADE ON UPDATE CASCADE; 
ALTER TABLE `fms_client_loan_doc` ADD CONSTRAINT `fk_loan_id_client_loan_docs` FOREIGN KEY (`client_loan_id`) REFERENCES `fms_client_loan`(`id`) ON DELETE CASCADE ON UPDATE CASCADE; 
ALTER TABLE `fms_loan_approval` ADD CONSTRAINT `fk_loan_id_laon_approval` FOREIGN KEY (`client_loan_id`) REFERENCES `fms_client_loan`(`id`) ON DELETE CASCADE ON UPDATE CASCADE; 
ALTER TABLE `fms_loan_guarantor` ADD CONSTRAINT `fk_loan_id_loan_quarantor` FOREIGN KEY (`client_loan_id`) REFERENCES `fms_client_loan`(`id`) ON DELETE CASCADE ON UPDATE CASCADE; 
ALTER TABLE `fms_loan_collateral` ADD CONSTRAINT `fk_loan_id_loan_collateral` FOREIGN KEY (`client_loan_id`) REFERENCES `fms_client_loan`(`id`) ON DELETE CASCADE ON UPDATE CASCADE; 
ALTER TABLE `fms_loan_attached_saving_accounts` ADD CONSTRAINT `fk_loan_id_attached_savings` FOREIGN KEY (`loan_id`) REFERENCES `fms_client_loan`(`id`) ON DELETE CASCADE ON UPDATE CASCADE; 

-- 16-03-2021--
ALTER TABLE `fms_saving_fees` ADD `repayment_made_every` INT(5) NULL DEFAULT NULL AFTER `dateapplicationmethod_id`, ADD `repayment_frequency` INT(5) NULL DEFAULT NULL AFTER `repayment_made_every`;

--- 19-03-2021 -- Reagan
-- INSERT INTO fms_auto_savings (product_id, savings_account_id, last_payment_date,last_saving_penalty_pay_date) SELECT deposit_Product_id, id, IFNULL(date_opened,'2021-01-01'),IFNULL(date_opened,'2021-01-01') FROM fms_savings_account --

UPDATE `fms_journal_type` SET `type_name` = 'Account Maintenance Fees' WHERE `fms_journal_type`.`id` = 13; 
ALTER TABLE `fms_client_subscription` CHANGE `payment_date` `payment_date` DATE NULL DEFAULT NULL; 
ALTER TABLE `fms_organisation` ADD `loan_fees_payment_method` TINYINT(1) NOT NULL DEFAULT '0' AFTER `two_factor_choice`; 

ALTER TABLE `fms_savings_product` CHANGE `productname` `productname` VARCHAR(250) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL; 

-- 19-04-2021--- Joshua
ALTER TABLE `fms_dividend_declaration` ADD `total_computed_share` INT(15) NOT NULL AFTER `total_dividends`;
-- 15-04-2021-- Ambrose
ALTER TABLE `fms_user` ADD `login_attempt` TINYINT(2) NOT NULL DEFAULT '4' AFTER `verified`;
ALTER TABLE `fms_user` ADD `login_time` DATETIME(6) NULL DEFAULT NULL AFTER `login_attempt`;

ALTER TABLE `fms_loan_fees` CHANGE `amount` `amount` DECIMAL(12,4) NOT NULL; 

-- 20-04-2021 --
ALTER TABLE `fms_dividend_payment` ADD `payment_type` INT(15) NOT NULL AFTER `amount`;

-- 22-04-2021
ALTER TABLE `fms_dividend_declaration` CHANGE `total_computed_share` `total_computed_share` DECIMAL(15,2) NOT NULL;
ALTER TABLE `fms_dividend_declaration` ADD `share_issuance_id` INT(15) NOT NULL AFTER `fiscal_year_id`;
INSERT INTO `fms_loan_charge_trigger` (`id`, `charge_trigger_name`, `charge_trigger_description`, `status_id`) VALUES ('7', 'Pay Off', 'Fee is applied on pay off', '1');
-- 07-06-2021:Ambrose (appreciation table)
 
ALTER TABLE `fms_appreciation` CHANGE `status_id` `status_id` TINYINT(1) NULL DEFAULT '1';
 -- 10-June 2021
-- account format initials (organisation table)
ALTER TABLE `fms_organisation` ADD `fixed_dep_format_initials` VARCHAR(11) NOT NULL AFTER `fixed_dep_format`;
ALTER TABLE `fms_organisation` ADD `client_format_initials` VARCHAR(11) NOT NULL AFTER `client_format`;
ALTER TABLE `fms_organisation` ADD `staff_format_initials` VARCHAR(11) NOT NULL AFTER `staff_format`;
ALTER TABLE `fms_organisation` ADD `group_format_initials` VARCHAR(11) NOT NULL AFTER `group_format`;
ALTER TABLE `fms_organisation` ADD `share_format_initials` VARCHAR(11) NOT NULL AFTER `share_format`;
ALTER TABLE `fms_organisation` ADD `loan_format_initials` INT(11) NOT NULL AFTER `loan_format`;
ALTER TABLE `fms_organisation` ADD `account_format_initials` INT(11) NOT NULL AFTER `account_format`;
ALTER TABLE `fms_organisation` ADD `group_loan_format_initials` VARCHAR(11) NOT NULL AFTER `group_loan_format`;

-- Fixed assets 17/07/2021: Ambrose 
ALTER TABLE `fms_fixed_assets` ADD `depreciation_loss_account_id` INT(8) NOT NULL AFTER `depreciation_account_id`, ADD `depreciation_gain_account_id` INT(8) NOT NULL AFTER `depreciation_loss_account_id`;
ALTER TABLE `fms_fixed_assets` ADD `appreciation_loss_account_id` INT(11) NOT NULL AFTER `appreciation_account_id`, ADD `appreciation_gain_account_id` INT(11) NOT NULL AFTER `appreciation_loss_account_id`;
 
-- Asset payment: tracking gain or loss
ALTER TABLE `fms_asset_payment` ADD `loss_or_gain` INT(2) NOT NULL AFTER `transaction_type_id`;
ALTER TABLE `fms_asset_payment` CHANGE `loss_or_gain` `loss_or_gain` INT(2) NULL DEFAULT NULL COMMENT '1 loss and 2 gain';

-- 04-05-2021 --
ALTER TABLE `fms_savings_product` ADD `min_saving_amount` DECIMAL(15,2) NULL DEFAULT NULL AFTER `when_interest_paid_units`;
ALTER TABLE `fms_savings_product` ADD `penalty` TINYINT(2) NOT NULL AFTER `min_saving_amount`;
ALTER TABLE `fms_savings_product` ADD `penalty_calculated_as` TINYINT(2) NULL AFTER `penalty`;
ALTER TABLE `fms_savings_product` ADD `penalty_amount` DECIMAL(15,2) NULL AFTER `penalty_calculated_as`;
ALTER TABLE `fms_auto_savings` ADD `last_saving_penalty_pay_date` DATE NULL AFTER `last_payment_date`;

-- 06-05-2021--
ALTER TABLE `fms_savings_product` ADD `penalty_income_account_id` INT(11) NOT NULL AFTER `penalty_amount`;

-- 16-05-2021-- Kalujja
ALTER TABLE `fms_savings_account` ADD `child_id` INT(11) NULL DEFAULT NULL AFTER `status_id`;
INSERT INTO `fms_member_collateral`(`id`, `member_id`, `collateral_type_id`,`description`,`file_name`,`item_value`, `status_id`, `date_created`, `created_by`, `date_modified`, `modified_by`) SELECT l.id, a.member_id, `collateral_type_id`,`description`,`file_name`,`item_value`, l.status_id, l.date_created, l.created_by, l.date_modified, l.modified_by FROM fms_loan_collateral l JOIN fms_client_loan a ON l.client_loan_id=a.id;
-- 21-05-2021 Kalujja
ALTER TABLE `fms_loan_collateral`
  DROP `collateral_type_id`,
  DROP `item_name`,
  DROP `description`,
  DROP `file_name`;

  -- 26-05-2021 Kalujja
  ALTER TABLE `fms_loan_collateral` ADD `member_collateral_id` INT(11) NOT NULL AFTER `id`;
  UPDATE `fms_loan_collateral` SET member_collateral_id=id;

  INSERT INTO `fms_deposit_product_type` (`id`, `typeName`, `description`) VALUES (NULL, 'Junior Accounts', 'For Children or infants\r\n');

  -- 27-05-2021 Kalujja
  ALTER TABLE `fms_guarantor` ADD `status_id` INT(11) NOT NULL AFTER `client_loan_id`;
  -- 28-05-2021 Joshua--
  ALTER TABLE `fms_fixed_savings` CHANGE `start_date` `start_date` DATE NULL DEFAULT NULL;
  ALTER TABLE `fms_fixed_savings` CHANGE `end_date` `end_date` DATE NULL DEFAULT NULL;

ALTER TABLE `fms_organisation` ADD `fixed_dep_format_initials` VARCHAR(11) NOT NULL AFTER `fixed_dep_format`;
ALTER TABLE `fms_organisation` ADD `client_format_initials` VARCHAR(11) NOT NULL AFTER `client_format`;
ALTER TABLE `fms_organisation` ADD `staff_format_initials` VARCHAR(11) NOT NULL AFTER `staff_format`;
ALTER TABLE `fms_organisation` ADD `group_format_initials` VARCHAR(11) NOT NULL AFTER `group_format`;
ALTER TABLE `fms_organisation` ADD `share_format_initials` VARCHAR(11) NOT NULL AFTER `share_format`;
ALTER TABLE `fms_organisation` ADD `loan_format_initials` INT(11) NOT NULL AFTER `loan_format`;
ALTER TABLE `fms_organisation` ADD `account_format_initials` INT(11) NOT NULL AFTER `account_format`;
ALTER TABLE `fms_organisation` ADD `group_loan_format_initials` VARCHAR(11) NOT NULL AFTER `group_loan_format`;

-- Fixed assets 17/06/2021: Ambrose  

ALTER TABLE `fms_fixed_assets` ADD `depreciation_loss_account_id` INT(8) NOT NULL AFTER `depreciation_account_id`, ADD `depreciation_gain_account_id` INT(8) NOT NULL AFTER `depreciation_loss_account_id`;
ALTER TABLE `fms_fixed_assets` ADD `appreciation_loss_account_id` INT(11) NOT NULL AFTER `appreciation_account_id`, ADD `appreciation_gain_account_id` INT(11) NOT NULL AFTER `appreciation_loss_account_id`;
 
-- Asset payment: tracking gain or loss
ALTER TABLE `fms_asset_payment` ADD `loss_or_gain` INT(2) NOT NULL AFTER `transaction_type_id`;
ALTER TABLE `fms_asset_payment` CHANGE `loss_or_gain` `loss_or_gain` INT(2) NULL DEFAULT NULL COMMENT '1 loss and 2 gain';
ALTER TABLE `fms_organisation` CHANGE `loan_format_initials` `loan_format_initials` VARCHAR(11) NOT NULL, CHANGE `account_format_initials` `account_format_initials` VARCHAR(11) NOT NULL;
 
 -- 14-06-2021 Reagan
ALTER TABLE `fms_applied_loan_fee` ADD CONSTRAINT `uq_applied_loan_fee` UNIQUE(`client_loan_id`, `loan_product_fee_id`,`status_id`);

ALTER TABLE `fms_applied_loan_fee` ADD `payment_mode` INT(1) NOT NULL AFTER `paid_or_not`;

-- 16-06-2021 kalujja
ALTER TABLE `fms_repayment_schedule` ADD `demanded_penalty` DECIMAL(12,2) NOT NULL DEFAULT '0.00' COMMENT 'Field to track un paid penalty' AFTER `principal_amount`;
-- 21-06-21 kalujja
ALTER TABLE `fms_loan_installment_payment` ADD `forgiven_penalty` DECIMAL(12,2) NOT NULL AFTER `paid_penalty`;
ALTER TABLE `fms_loan_installment_payment` ADD `forgiven_interest` DECIMAL(12,2) NOT NULL AFTER `forgiven_penalty`;

-- 29-06-2021 kalujja
ALTER TABLE `fms_loan_installment_payment` ADD `receipt_amount` DECIMAL(12,2) NULL DEFAULT NULL AFTER `status_id`;

INSERT INTO `fms_relationship_type` (`id`, `relationship_type`) VALUES (NULL, 'Boyfriend'), (NULL, 'Girlfriend');
INSERT INTO `fms_relationship_type` (`id`, `relationship_type`) VALUES (NULL, 'Pastor');

-- 30-06-2021 kalujja
ALTER TABLE `fms_lock_savings_amount`
  DROP `loan_id`;

-- 02-07-2021 kalujja
ALTER TABLE `fms_loan_attached_saving_accounts` ADD `amount_locked` DECIMAL(12,2) NOT NULL DEFAULT '0.00' AFTER `status_id`;

-- 05-07-21
ALTER TABLE `fms_loan_product` ADD `use_shares_as_security` INT(2) NOT NULL AFTER `interest_receivable_account_id`;
ALTER TABLE `fms_loan_product` ADD `use_savings_as_security` INT(2) NOT NULL AFTER `use_shares_as_security`;
ALTER TABLE `fms_loan_product` ADD `mandatory_sv_or_sh` INT(2) NOT NULL AFTER `use_shares_as_security`;


-- 12-07-21
ALTER TABLE `fms_organisation` ADD `max_loans_to_guarantee` INT(11) NULL DEFAULT NULL AFTER `loan_app_comp`;

-- 18-07-21 Reagan
ALTER TABLE `fms_subscription_schedule` CHANGE `amount` `amount` DECIMAL(15,2) NOT NULL;


-- 19-07-21 joshua
ALTER TABLE `fms_member_fees` ADD `repayment_made_every` INT(11) NULL DEFAULT NULL AFTER `income_account_id`;
ALTER TABLE `fms_member_fees` ADD `repayment_frequency` INT(11) NULL DEFAULT NULL AFTER `repayment_made_every`;

-- 22-07-2021 Kalujja
ALTER TABLE `fms_organisation` ADD `loan_curtailment` INT(2) NOT NULL DEFAULT '0' AFTER `loan_format_initials`;


-- 24-07-2021 Reagan
INSERT INTO `fms_journal_type` (`id`, `type_name`, `status`) VALUES (NULL, 'Interest Written Off', '0');

-- 28-07-2021 Kalujja
ALTER TABLE `fms_share_account` ADD `client_type` INT(2) NOT NULL DEFAULT '1' AFTER `member_id`;

-- 29-07-2021 kalujja
ALTER TABLE `fms_group` ADD `group_client_type` INT(2) NOT NULL DEFAULT '2' AFTER `group_no`;

-- 30-07-2021 kalujja (Reversal changes)
ALTER TABLE `fms_loan_installment_payment` ADD `prev_payment_status` INT(11) NULL AFTER `modified_by`;
ALTER TABLE `fms_loan_installment_payment` ADD `prev_demanded_penalty` DECIMAL(15,2) NULL AFTER `prev_payment_status`;
ALTER TABLE `fms_loan_installment_payment` ADD `prev_payment_date` DATETIME NULL AFTER `prev_demanded_penalty`, ADD `reversal_reason` VARCHAR(200) NULL AFTER `prev_payment_date`;
ALTER TABLE `fms_loan_installment_payment` ADD `reversed_by` INT(11) NULL AFTER `reversal_reason`;


-- 02-08-2021 kalujja
ALTER TABLE `fms_loan_installment_payment` ADD `unique_id` VARCHAR(200) NOT NULL AFTER `reversed_by`;
ALTER TABLE `fms_repayment_schedule` ADD `unique_id` VARCHAR(200) NOT NULL AFTER `modified_by`;
ALTER TABLE `fms_client_loan` ADD `unique_id` VARCHAR(200) NULL AFTER `date_modified`;
ALTER TABLE `fms_group_loan` ADD `unique_id` VARCHAR(200) NULL AFTER `date_modified`;
ALTER TABLE `fms_journal_transaction` ADD `unique_id` VARCHAR(200) NOT NULL AFTER `reversed_date`;
ALTER TABLE `fms_journal_transaction_line` ADD `unique_id` VARCHAR(200) NOT NULL AFTER `status_id`;
ALTER TABLE `fms_applied_loan_fee` ADD `unique_id` VARCHAR(200) NULL AFTER `modified_by`;
ALTER TABLE `fms_client_loan_doc` ADD `unique_id` VARCHAR(200) NULL AFTER `modified_by`;
ALTER TABLE `fms_client_loan_monthly_expense` ADD `unique_id` VARCHAR(200) NULL AFTER `modified_by`;
ALTER TABLE `fms_client_loan_monthly_income` ADD `unique_id` VARCHAR(200) NULL AFTER `modified_by`;
ALTER TABLE `fms_loan_approval` ADD `unique_id` VARCHAR(200) NULL AFTER `modified_by`;
ALTER TABLE `fms_loan_attached_saving_accounts` ADD `unique_id` VARCHAR(200) NULL AFTER `modified_by`;
ALTER TABLE `fms_loan_attached_share_accounts` ADD `unique_id` VARCHAR(200) NULL AFTER `modified_by`;
ALTER TABLE `fms_loan_collateral` ADD `unique_id` VARCHAR(200) NULL AFTER `modified_by`;
ALTER TABLE `fms_loan_fees` ADD `unique_id` VARCHAR(200) NULL AFTER `modified_by`;
ALTER TABLE `fms_loan_guarantor` ADD `unique_id` VARCHAR(200) NULL AFTER `modified_by`;
ALTER TABLE `fms_loan_state` ADD `unique_id` VARCHAR(200) NOT NULL AFTER `modified_by`;
ALTER TABLE `fms_share_loan_guarantor` ADD `unique_id` VARCHAR(200) NULL AFTER `modified_by`;


--01-08-2021

ALTER TABLE `fms_guarantor` ADD `firstname` VARCHAR(100) NOT NULL AFTER `member_id`, ADD `lastname` VARCHAR(100) NOT NULL AFTER `firstname`, ADD `mobile_number` VARCHAR(50) NOT NULL AFTER `lastname`, ADD `email` VARCHAR(200) NULL DEFAULT NULL AFTER `mobile_number`;
ALTER TABLE `fms_guarantor` ADD `gender` TINYINT(2) NOT NULL AFTER `lastname`;
ALTER TABLE `fms_guarantor` ADD `attachment` TEXT NOT NULL AFTER `email`;
ALTER TABLE `fms_guarantor` ADD `nin` VARCHAR(50) NOT NULL AFTER `email`;
ALTER TABLE `fms_guarantor` ADD `comments` TEXT NOT NULL AFTER `attachment`;
ALTER TABLE `fms_emails` ADD `mgs_status` TINYINT(2) NOT NULL DEFAULT '0' COMMENT '0=Not sent; 1=sent' AFTER `status_id`;
ALTER TABLE `fms_emails` ADD `message_id` TINYINT(2) NOT NULL COMMENT '1=members;2=staff' AFTER `mgs_status`;
 
--Ambrose 05-08-2021 -07/08/2021
ALTER TABLE `fms_guarantor` CHANGE `comments` `comment` TEXT CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;
ALTER TABLE `fms_emails` ADD `modified_by` TINYINT(2) NOT NULL AFTER `created_by`;
ALTER TABLE `fms_alert_setting` CHANGE `number_of_reminder` `number_of_reminder_in_days` INT(6) NOT NULL;

ALTER TABLE `fms_alert_setting` CHANGE `type_of_reminder` `interval_of_reminder` VARCHAR(254) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;
ALTER TABLE `fms_alert_setting` ADD `number_of_alerts_to_send` VARCHAR(11) NOT NULL DEFAULT '0' AFTER `interval_of_reminder`;
ALTER TABLE `fms_alert_setting` CHANGE `number_of_alerts_to_send` `alert_sent_count` VARCHAR(11) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT '0';
ALTER TABLE `fms_emails` ADD `alert_method` TINYINT(11) NOT NULL DEFAULT '1' COMMENT '1=Email,2=SMS,3=Both' AFTER `id`, ADD `alert_type_id` TINYINT(11) NOT NULL AFTER `alert_method`, ADD `mobile_number` VARCHAR(50) NOT NULL AFTER `alert_type_id`;
 ALTER TABLE `fms_alert_types` CHANGE `description` `description` VARCHAR(45) NOT NULL;
 ALTER TABLE `fms_alert_setting` CHANGE `number_of_reminder_in_days` `number_of_days_to_duedate` INT(11) NOT NULL;
 ALTER TABLE `fms_alert_setting` ADD `alert_id` INT(11) NOT NULL AFTER `alert_type`;
-- 05-08-2021 kalujja
INSERT INTO `fms_action_type` (`id`, `description`, `date_created`, `date_modified`, `created_by`, `modified_by`, `status_id`) VALUES ('4', 'Loan Pay Off Action', '2021-08-05 13:36:00', '2021-08-05 13:36:00', '0', '0', '1');

-- 06-08-2021 kalujja (Loan Reschedule reversal)
ALTER TABLE `fms_trans_tracking` ADD `base_installment` INT(11) NULL AFTER `repayment_schedule_id`;
ALTER TABLE `fms_trans_tracking` ADD `loan_approved_installments` INT(11) NULL AFTER `base_installment`;
ALTER TABLE `fms_trans_tracking` ADD `loan_interest_rate` DECIMAL(15,2) NULL AFTER `loan_approved_installments`;
ALTER TABLE `fms_trans_tracking` ADD `loan_approved_repayment_made_every` INT(11) NULL AFTER `loan_interest_rate`, ADD `loan_approved_repayment_frequency` INT(11) NULL AFTER `loan_approved_repayment_made_every`, ADD `loan_status_id` INT(11) NULL AFTER `loan_approved_repayment_frequency`, ADD `loan_modified_by` INT(11) NULL AFTER `loan_status_id`;
INSERT INTO `fms_action_type` (`id`, `description`, `date_created`, `date_modified`, `created_by`, `modified_by`, `status_id`) VALUES ('1', 'Single Installment Loan Payment', '2021-08-05 13:36:00', '2021-08-05 13:36:00', '0', '0', '1');
INSERT INTO `fms_action_type` (`id`, `description`, `date_created`, `date_modified`, `created_by`, `modified_by`, `status_id`) VALUES ('2', 'Multiple Installment Loan Payment', '2021-08-05 13:36:00', '2021-08-05 13:36:00', '0', '0', '1');
INSERT INTO `fms_action_type` (`id`, `description`, `date_created`, `date_modified`, `created_by`, `modified_by`, `status_id`) VALUES ('3', 'Loan Curtailment', '2021-08-05 13:36:00', '2021-08-05 13:36:00', '0', '0', '1');
INSERT INTO `fms_action_type` (`id`, `description`, `date_created`, `date_modified`, `created_by`, `modified_by`, `status_id`) VALUES ('5', 'write Off', '2021-08-05 13:36:00', '2021-08-05 13:36:00', '0', '0', '1');
INSERT INTO `fms_action_type` (`id`, `description`, `date_created`, `date_modified`, `created_by`, `modified_by`, `status_id`) VALUES ('6', 'Loan reschedule', '2021-08-05 13:36:00', '2021-08-05 13:36:00', '0', '0', '1');

-- 08-08-2021 kalujja
ALTER TABLE `fms_trans_tracking` ADD `client_loan_id` INT(11) NULL AFTER `id`;

-- 10-08-2021 kalujja
ALTER TABLE `fms_transaction` ADD `unique_id` VARCHAR(200) NULL AFTER `reversed_date`;
ALTER TABLE `fms_transaction_charges` ADD `unique_id` VARCHAR(200) NULL AFTER `modified_by`;

 --09-08-2021
 ALTER TABLE `fms_emails` ADD `member_id` INT(11) NOT NULL AFTER `alert_type_id`;
 ALTER TABLE `fms_emails` ADD `date_sent` DATETIME NULL AFTER `mgs_status`;
 -- Product code db changes 
ALTER TABLE `fms_share_issuance` ADD `issuance_code` VARCHAR(50) NULL DEFAULT NULL AFTER `issuance_name`;
ALTER TABLE `fms_savings_product` ADD `product_code` VARCHAR(50) NULL DEFAULT NULL AFTER `productname`;
ALTER TABLE `fms_loan_product` ADD `product_code` VARCHAR(50) NULL DEFAULT NULL AFTER `product_name`;

-- 11-08-2021 joshua 
ALTER TABLE `fms_repayment_schedule` ADD `unique_id` VARCHAR(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL AFTER `id`;

-- 13-08-2021 kalujja
ALTER TABLE `fms_journal_transaction_line` ADD `reversed_by` INT(11) NULL AFTER `status_id`;
ALTER TABLE `fms_journal_transaction_line` ADD `reversed_date` DATETIME NULL AFTER `reversed_by`, ADD `reverse_msg` VARCHAR(200) NULL AFTER `reversed_date`;

-- 01-08-2021

ALTER TABLE `fms_guarantor` ADD `firstname` VARCHAR(100) NOT NULL AFTER `member_id`, ADD `lastname` VARCHAR(100) NOT NULL AFTER `firstname`, ADD `mobile_number` VARCHAR(50) NOT NULL AFTER `lastname`, ADD `email` VARCHAR(200) NULL DEFAULT NULL AFTER `mobile_number`;
ALTER TABLE `fms_guarantor` ADD `gender` TINYINT(2) NOT NULL AFTER `lastname`;
ALTER TABLE `fms_guarantor` ADD `attachment` TEXT NOT NULL AFTER `email`;
ALTER TABLE `fms_guarantor` ADD `nin` VARCHAR(50) NOT NULL AFTER `email`;
ALTER TABLE `fms_guarantor` ADD `comments` TEXT NOT NULL AFTER `attachment`;
ALTER TABLE `fms_emails` ADD `mgs_status` TINYINT(2) NOT NULL DEFAULT '0' COMMENT '0=Not sent; 1=sent' AFTER `status_id`;
ALTER TABLE `fms_emails` ADD `message_id` TINYINT(2) NOT NULL COMMENT '1=members;2=staff' AFTER `mgs_status`;
 
-- Ambrose 05-08-2021 -07/08/2021
ALTER TABLE `fms_guarantor` CHANGE `comments` `comment` TEXT CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;
ALTER TABLE `fms_emails` ADD `modified_by` TINYINT(2) NOT NULL AFTER `created_by`;
ALTER TABLE `fms_alert_setting` CHANGE `number_of_reminder` `number_of_reminder_in_days` INT(6) NOT NULL;

ALTER TABLE `fms_alert_setting` CHANGE `type_of_reminder` `interval_of_reminder` VARCHAR(254) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;
ALTER TABLE `fms_alert_setting` ADD `number_of_alerts_to_send` VARCHAR(11) NOT NULL DEFAULT '0' AFTER `interval_of_reminder`;
ALTER TABLE `fms_alert_setting` CHANGE `number_of_alerts_to_send` `alert_sent_count` VARCHAR(11) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT '0';
ALTER TABLE `fms_emails` ADD `alert_method` TINYINT(11) NOT NULL DEFAULT '1' COMMENT '1=Email,2=SMS,3=Both' AFTER `id`, ADD `alert_type_id` TINYINT(11) NOT NULL AFTER `alert_method`, ADD `mobile_number` VARCHAR(50) NOT NULL AFTER `alert_type_id`;
 ALTER TABLE `fms_alert_types` CHANGE `description` `description` VARCHAR(45) NOT NULL;
 ALTER TABLE `fms_alert_setting` CHANGE `number_of_reminder_in_days` `number_of_days_to_duedate` INT(11) NOT NULL;
 ALTER TABLE `fms_alert_setting` ADD `alert_id` INT(11) NOT NULL AFTER `alert_type`;

 -- 09-08-2021
 ALTER TABLE `fms_emails` ADD `member_id` INT(11) NOT NULL AFTER `alert_type_id`;
 ALTER TABLE `fms_emails` ADD `date_sent` DATETIME NULL AFTER `mgs_status`;
 ALTER TABLE `fms_emails` ADD `date_sent` DATETIME NULL AFTER `mgs_status`


UPDATE `fms_account_sub_categories` SET `sub_cat_name` = 'Receivable Loan Interest' WHERE `fms_account_sub_categories`.`id` = 2;
UPDATE `fms_account_sub_categories` SET `description` = 'For Tracking Loan Interest' WHERE `fms_account_sub_categories`.`id` = 2;

 -- 04-09-2021 reagan
INSERT INTO `fms_state` (`id`, `state_name`, `description`) VALUES (20, 'Pending Payment', 'Pending Payment');
ALTER TABLE `fms_organisation` ADD `mobile_payments` TINYINT(2) NOT NULL DEFAULT '0' AFTER `loan_fees_payment_method`;


-- 23-09-2021
ALTER TABLE `fms_journal_transaction_line` CHANGE `narrative` `narrative` TEXT CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;
ALTER TABLE `fms_auto_savings` ADD `opening_fee_paid` TINYINT(2) NOT NULL DEFAULT '0' AFTER `last_saving_penalty_pay_date`;
ALTER TABLE `fms_auto_savings` ADD `opening_fee_pay_date` DATE NULL DEFAULT NULL AFTER `opening_fee_paid`;
UPDATE `fms_charge_trigger` SET `charge_trigger_name` = 'Account Openning' WHERE `fms_charge_trigger`.`id` = 7;
UPDATE `fms_journal_type` SET `type_name` = 'Savings Account Fees' WHERE `fms_journal_type`.`id` = 13;
 -- 27-09-2021
ALTER TABLE `fms_transaction` ADD `unique_id` VARCHAR(200) NULL AFTER `reversed_date`;
ALTER TABLE `fms_client_loan` ADD `disbursed_amount` DECIMAL(15,2) NULL DEFAULT NULL AFTER `amount_approved`;
UPDATE fms_client_loan SET disbursed_amount = amount_approved;

-- 06-10-2021
ALTER TABLE `fms_loan_installment_payment` CHANGE `unique_id` `unique_id` VARCHAR(200) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;



-- 29-10-2021 Joshua
ALTER TABLE `fms_sales_transactions` ADD `income_account_id` INT(15) NULL AFTER `savings_account_id`;

-- 02-11-2021
ALTER TABLE `fms_loan_product` ADD `penalty_applicable_after_due_date` TINYINT(2) NOT NULL DEFAULT '0' AFTER `penalty_applicable`;
ALTER TABLE `fms_loan_product` ADD `fixed_penalty_amount` DECIMAL(15,2) NULL AFTER `penalty_rate_chargedPer`;

INSERT INTO `fms_repayment_made_every` (`id`, `made_every_name`, `date_created`, `date_modified`, `created_by`, `modified_by`) VALUES (4, 'Once (One time)', '1535528969', '2018-08-31 13:16:41', '1', '0');



-- 04-11-2021 Joshua
ALTER TABLE `fms_sales_transactions` CHANGE `item` `item_id` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL;
ALTER TABLE `fms_sales_transactions` CHANGE `item_id` `item_id` INT NOT NULL;


-- 09-11-2021 reagan
ALTER TABLE `fms_organisation` ADD `deduct_loan_fees_from_loan` TINYINT(1) NOT NULL DEFAULT '0' AFTER `mobile_payments`;
ALTER TABLE `fms_client_loan` CHANGE `interest_rate` `interest_rate` DECIMAL(10,2) NULL DEFAULT NULL;
-- 09/11/2021 Ambrose // Attaching a code to share,savings and loan products or category
ALTER TABLE `fms_share_issuance` ADD `issuance_code` VARCHAR(50) NULL DEFAULT NULL AFTER `issuance_name`;
ALTER TABLE `fms_savings_product` ADD `product_code` VARCHAR(50) NULL DEFAULT NULL AFTER `productname`;
ALTER TABLE `fms_loan_product` ADD `product_code` VARCHAR(50) NULL DEFAULT NULL AFTER `product_name`;
-- Ambrose  2021-11-05 (Share refund )
INSERT INTO `fms_transaction_type` (`id`, `type_name`, `description`) VALUES ('13', 'Refund', 'Refund');

-- 10-11-2021 Kalujja (Loan disbursement reversal)
ALTER TABLE `fms_trans_tracking` CHANGE `date_modified` `date_modified` DATETIME on update CURRENT_TIMESTAMP NOT NULL;
ALTER TABLE `fms_trans_tracking` ADD `loan_approval_note` TEXT NULL AFTER `status_id`, ADD `loan_amount_approved` DECIMAL(15,2) NULL AFTER `loan_approval_note`, ADD `loan_suggested_disbursement_date` DATE NULL AFTER `loan_amount_approved`, ADD `loan_approval_date` DATE NULL AFTER `loan_suggested_disbursement_date`, ADD `loan_approved_by` INT(11) NULL AFTER `loan_approval_date`, ADD `loan_source_fund_account_id` INT(11) NULL AFTER `loan_approved_by`, ADD `loan_disbursed_amount` DECIMAL(15,2) NULL AFTER `loan_source_fund_account_id`;
INSERT INTO `fms_action_type` (`id`, `description`, `date_created`, `date_modified`, `created_by`, `modified_by`, `status_id`) VALUES (NULL, 'Loan Disbursement', '2021-08-05 13:36:00', '2021-08-05 13:36:00', '0', '0', '1');
ALTER TABLE `fms_trans_tracking` CHANGE `payment_mode` `payment_mode` INT(11) NULL;
ALTER TABLE `fms_trans_tracking` CHANGE `repayment_schedule_id` `repayment_schedule_id` INT(11) NULL;
ALTER TABLE `fms_mobile_money_transactions` ADD `unique_id` VARCHAR(200) NULL AFTER `modified_by`;
ALTER TABLE `fms_mobile_money_transactions` ADD `status_id` TINYINT(2) NULL AFTER `unique_id`;
ALTER TABLE `fms_trans_tracking` ADD `linked_loan_id` INT(11) NULL AFTER `client_loan_id`;
ALTER TABLE `fms_trans_tracking` ADD `linked_loan_state_id` INT(11) NULL AFTER `linked_loan_id`;

-- reagan 19-11-2021 
ALTER TABLE `fms_transaction_channel` ADD `staff_id` INT(11) NULL DEFAULT NULL AFTER `modified_by`;
INSERT INTO `fms_modules` (`id`, `module_name`, `description`, `status_id`, `date_created`) VALUES ('26', 'Till', 'Till', '1', current_timestamp());
ALTER TABLE `fms_journal_transaction_line` ADD `member_id` INT(11) NULL DEFAULT NULL AFTER `reference_id`, ADD `reference_key` VARCHAR(200) NULL DEFAULT NULL AFTER `member_id`;
UPDATE `fms_journal_type` SET `type_name` = 'Savings deposits' WHERE `fms_journal_type`.`id` = 7;
UPDATE `fms_journal_type` SET `type_name` = 'Savings withdraws' WHERE `fms_journal_type`.`id` = 8;
UPDATE `fms_journal_type` SET `type_name` = 'Bought Shares' WHERE `fms_journal_type`.`id` = 22;

-- 06-12-2021 kalujja (Fixed Penalty)
ALTER TABLE `fms_client_loan` CHANGE `penalty_rate` `penalty_rate` DECIMAL(10,2) NULL DEFAULT NULL;
INSERT INTO `fms_penalty_calculation_method` (`id`, `method_description`, `date_created`, `created_by`, `date_modified`, `modified_by`) VALUES (2, 'Fixed Amount', '1498487362', '1', '2018-08-20 16:27:59', '1')

-- Ambrose 20-01-2022
ALTER TABLE `fms_member` ADD `introduced_by` INT(12) NULL DEFAULT NULL COMMENT 'Tracks which member introduced the the member ' AFTER `occupation`;

-- Ambrose 24-04-2022
ALTER TABLE `fms_guarantor` ADD `status_id` INT(2) NOT NULL DEFAULT '1' AFTER `date_created`;
-- Ambrose 27-01-2022
ALTER TABLE `fms_organisation` ADD `member_referral` TINYINT(2) NOT NULL DEFAULT '0' AFTER `loans_to_savings`;
ALTER TABLE `fms_member_referral` CHANGE `organisation_id` `organisation_id` INT(2) NULL DEFAULT NULL;

-- Ambrose 02-02-2022
ALTER TABLE `fms_member` ADD `introduced_by_id` INT(11) NULL DEFAULT NULL AFTER `date_modified`;
INSERT INTO `fms_penalty_calculation_method` (`id`, `method_description`, `date_created`, `created_by`, `date_modified`, `modified_by`) VALUES (2, 'Fixed Amount', '1498487362', '1', '2018-08-20 16:27:59', '1');

-- =======================================================================================================================================================

-- 18-01-2022 REAGAN
ALTER TABLE `fms_group` CHANGE `group_no` `group_no` VARCHAR(500) NOT NULL;

--11-02-2022  AMBROSE 
ALTER TABLE `fms_guarantor` DROP `date_modified`;
ALTER TABLE `fms_guarantor` ADD `date_modified` TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `created_by`;
ALTER TABLE `fms_guarantor` ADD `guarantor_type_id` TINYINT(11) NOT NULL AFTER `id`;

-- 24-02-2022 AMBROSE ( Altered this to cater for MCEE SACOO initials)
ALTER TABLE `fms_organisation` CHANGE `org_initial` `org_initial` VARCHAR(25) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;

-- 27-03-2022 kalujja
ALTER TABLE `fms_mobile_money_transactions` ADD `loan_disbursement_data` JSON NULL DEFAULT NULL AFTER `message`;
ALTER TABLE `fms_mobile_money_transactions` ADD `ext_ref` TEXT NULL AFTER `loan_disbursement_data`;
ALTER TABLE `fms_mobile_money_transactions` ADD `ref_no` TEXT NULL AFTER `ext_ref`;
-- 18/03/2022  Ambrose 
ALTER TABLE `fms_client_subscription` ADD `feeid` TINYINT(8) NOT NULL AFTER `payment_date`;

-- 05-04-2022 kalujja
INSERT INTO `fms_loan_product_type` (`id`, `type_name`, `description`, `date_created`, `date_modified`, `created_by`, `modified_by`) VALUES
(3, 'On Declining balance', 'Interest for each installment is calculated based on the principle amount remaining. The principle per installment is constant\r\n', 0, '2022-04-04 21:42:19', 0, 0);


-- 06/04/2022  kalumba 
ALTER TABLE `fms_loan_fees` ADD `fee_applied_to` TINYINT(4) NOT NULL DEFAULT '0' AFTER `feetype_id`;

-- 05/05/2022 kalumba
ALTER TABLE `fms_organisation` ADD `deduct_loan` TINYINT(4) NOT NULL DEFAULT '0' AFTER `loans_to_savings`;
-- 13-04-2022 kalujja
ALTER TABLE `fms_organisation` ADD `topup_loan_termination_fees` INT(2) NULL DEFAULT '0' AFTER `no_current_interest`;

-- 22-04-2022 ssenoga
ALTER TABLE `fms_user` ADD `has_set_password` BOOLEAN NULL DEFAULT FALSE AFTER `status`;

-- 28-04-2022 ssenoga
create table fms_withdraw_requests(id int(11) not null primary key auto_increment, account_no_id int(11) not null, member_id int(11) not null, transaction_channel_id int(11) not null, amount decimal(15,2) not null, reason varchar(250), date_created datetime default current_timestamp, date_modified timestamp);
ALTER TABLE fms_withdraw_requests ADD status int(2) DEFAULT 1;
ALTER TABLE `fms_withdraw_requests` ADD `declined_by` INT(11) NULL AFTER `reason`, ADD `accepted_by` INT(11) NULL AFTER `declined_by`, ADD `decline_note` VARCHAR(250) NULL AFTER `accepted_by`, ADD `accept_note` VARCHAR(250) NULL AFTER `decline_note`;

-- 18/03/2022  Ambrose 
ALTER TABLE `fms_client_subscription` ADD `feeid` TINYINT(8) NOT NULL AFTER `payment_date`;

-- 12-04-2022 Ambrose
ALTER TABLE `fms_loan_provision_portfolio_setting` CHANGE `name` `name` VARCHAR(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;
ALTER TABLE `fms_loan_provision_portfolio_setting` CHANGE `description` `description` VARCHAR(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;
ALTER TABLE `fms_loan_provision_portfolio_setting` CHANGE `id` `id` INT(11) NOT NULL AUTO_INCREMENT, add PRIMARY KEY (`id`);
ALTER TABLE `fms_loan_provision_portfolio_setting` ADD `status_id` INT(2) NOT NULL DEFAULT '1' AFTER `provision_loan_loss_account`, ADD `date_created` INT(8) NULL DEFAULT NULL AFTER `status_id`, ADD `created_by` INT(2) NULL DEFAULT NULL AFTER `date_created`;
ALTER TABLE `fms_loan_provision_portfolio_setting` ADD `modified_by` INT(2) NOT NULL AFTER `created_by`, ADD `modified_at` DATETIME on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `modified_by`;
ALTER TABLE `fms_loan_provision_portfolio_setting` CHANGE `provision_loan_loss_account` `provision_loan_loss_account_id` INT(11) NOT NULL;

-- 20-04-2022 Ambrose 
ALTER TABLE `fms_loan_provision_portfolio_setting` ADD `asset_account_id` INT(20) NOT NULL AFTER `provision_loan_loss_account_id`;
ALTER TABLE `fms_loan_provision_portfolio_setting` ADD `provision_method_id` INT(2) NOT NULL AFTER `asset_account_id`;

-- 27-04-2022 Ambrose 
ALTER TABLE `fms_group` ADD `owner_name` VARCHAR(255) NULL DEFAULT NULL AFTER `description`, ADD `phone_number` VARCHAR(254) NULL DEFAULT NULL AFTER `owner_name`, ADD `nin` VARCHAR(255) NULL DEFAULT NULL AFTER `phone_number`, ADD `email` VARCHAR(255) NULL DEFAULT NULL AFTER `nin`, ADD `address` TEXT NULL DEFAULT NULL AFTER `email`;

-- 10-04-2022 Kalujja
ALTER TABLE `fms_organisation` ADD `edit_account_nos` TINYINT(2) NOT NULL DEFAULT '0' COMMENT 'Enable/Disable Account No Editing' AFTER `deduct_loan_fees_from_loan`;

-- 18-05-2022 kalumba
ALTER TABLE `fms_transaction` ADD `depositor_account_attached` INT(10) NULL DEFAULT NULL AFTER `group_member_id`;

-- 23-05-2022 kalujja
ALTER TABLE `fms_client_loan` ADD `interest_amount_bf` DECIMAL(15,2) NULL DEFAULT '0' AFTER `interest_rate`;

-- 25-05-2022 Kalujja
ALTER TABLE `fms_organisation` ADD `principal_interest_bf_on_topup_loans` TINYINT(2) NULL DEFAULT '0' COMMENT 'Use parent loan principle & interest balance on topup loan schedule generation and loan fees calculation' AFTER `topup_loan_termination_fees`;