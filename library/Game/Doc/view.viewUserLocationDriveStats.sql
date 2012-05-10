DROP VIEW viewUserLocationDriveStats;
CREATE VIEW viewUserLocationDriveStats AS
SELECT 
td.Username,
td.miles_covered,
tid.miles_covered_over_speed,
td.average_speed,
ROUND((100/td.miles_covered) * (tid.miles_covered_over_speed), 2) as percentage_over_speed


FROM viewUserLocationTotalDistance td
LEFT JOIN viewUserLocationTotalIllegalDistance tid
  ON tid.Username = td.Username

GROUP BY td.Username