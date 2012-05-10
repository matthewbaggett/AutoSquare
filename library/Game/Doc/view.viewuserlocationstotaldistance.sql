DROP VIEW viewUserLocationTotalDistance;
CREATE VIEW viewUserLocationTotalDistance AS
SELECT 
Username, 
SUM(miles) as miles_covered,
ROUND(AVG(mph),2) as average_speed

FROM viewUserLocations
GROUP BY intUserID