DROP VIEW viewUserLocationTotalIllegalDistance;
CREATE VIEW viewUserLocationTotalIllegalDistance AS
SELECT 
Username, 
SUM(miles) as miles_covered_over_speed

FROM viewUserLocations
WHERE mph > 70
GROUP BY Username