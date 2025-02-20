CREATE OR REPLACE VIEW `tenants_houses_view` AS 
SELECT 
    `tenants`.`tenantID` AS `tenantID`, 
    `tenants`.`tenant_name` AS `tenant_name`, 
    `houses`.`house_name` AS `house_name`, 
    `house_numbers`.`house_no` AS `house_no`, 
    `houses`.`rent_amount` AS `rent`, 
    `houses`.`garbage` AS `garbage`, 
    `water_readings`.`current_reading` AS `current_reading`, 
    `water_readings`.`previous_reading` AS `previous_reading`, 
    `water_readings`.`water_rate` AS `water_rate`, 
    `water_readings`.`total_units` AS `total_units`, 
    `water_readings`.`total_amount` AS `total_consumption`, 
    `payments`.`balance` AS `outstanding_balance`, 
    `invoices`.`invoiceNumber` AS `invoice_number`, 
    `invoices`.`dateOfinvoice` AS `date_of_invoice`, 
    `invoices`.`dateDue` AS `date_due`,
    `invoices`.`totalAmount` AS `total_amount`
FROM 
    `tenants` 
LEFT JOIN 
    `house_numbers` ON `tenants`.`houseNumber` = `house_numbers`.`id`
LEFT JOIN 
    `houses` ON `house_numbers`.`house_id` = `houses`.`houseID`
LEFT JOIN 
    `water_readings` ON `tenants`.`tenantID` = `water_readings`.`tenant_id`
LEFT JOIN 
    `payments` ON `tenants`.`tenantID` = `payments`.`tenantID`
LEFT JOIN 
    `invoices` ON `tenants`.`tenantID` = `invoices`.`tenantID`;
