SELECT
jcalc.id, jcalc.create_time, jcalc.summ, jcalc.invoice_id, jcalc.office_id, jcalc.zapis_id, jcalc.type, jcalc.client_id,
sw.id AS worker_id, sw.name AS worker_name,
ji.summ AS invoice_summ, ji.summins AS invoice_summins, ji.create_time AS invoice_create_time,
sc.name AS client_name, sc.full_name AS client_full_name,
wm.id AS worker_mark,
GROUP_CONCAT(DISTINCT jcalcex.percent_cats ORDER BY jcalcex.percent_cats ASC SEPARATOR ',') AS percent_cats
FROM `fl_journal_calculate` jcalc
LEFT JOIN `spr_workers` sw ON sw.id = jcalc.worker_id
LEFT JOIN `journal_invoice` ji ON ji.id = jcalc.invoice_id
LEFT JOIN `spr_clients` sc ON sc.id = jcalc.client_id
LEFT JOIN `journal_tooth_status` wm ON wm.zapis_id = jcalc.zapis_id
LEFT JOIN `fl_journal_calculate_ex` jcalcex ON jcalcex.calculate_id = jcalc.id  /*Тормозит сильно весь запрос*/
WHERE
jcalc.type='5'
AND
sw.permissions = '5' AND sw.status <> '8'
AND jcalc.id NOT IN ( SELECT `calculate_id` from `fl_journal_tabels_ex` WHERE `calculate_id`=jcalc.id )
GROUP BY jcalc.id