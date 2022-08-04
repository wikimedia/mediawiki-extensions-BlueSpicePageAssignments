-- This file is automatically generated using maintenance/generateSchemaSql.php.
-- Source: extensions/BlueSpicePageAssignments/maintenance/db/sql/bs_pageassignments.json
-- Do not modify this file directly.
-- See https://www.mediawiki.org/wiki/Manual:Schema_changes
CREATE TABLE /*_*/bs_pageassignments (
  pa_page_id INT UNSIGNED NOT NULL,
  pa_assignee_key VARBINARY(255) DEFAULT '' NOT NULL,
  pa_assignee_type VARBINARY(255) DEFAULT '' NOT NULL,
  pa_position INT UNSIGNED NOT NULL,
  INDEX pa_page_id (pa_page_id),
  PRIMARY KEY(
    pa_page_id, pa_assignee_key, pa_assignee_type
  )
) /*$wgDBTableOptions*/;
