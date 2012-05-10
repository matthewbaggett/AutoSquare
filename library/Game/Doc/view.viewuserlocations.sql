DROP VIEW viewUserLocations;
CREATE VIEW viewUserLocations AS
SELECT 
ul.intUserLocationID as intUserLocationID,
ul.intPrevUserLocationID as intPrevUserLocationID,
CONCAT_WS(' - ',CAST(ul.intUserLocationID as CHAR),CAST(ul.intPrevUserLocationID as CHAR)) as 'Name of Movement',
u.strUsername as Username,
ul.intUserID as intUserID,
ul.locLatitude - ul.locPrevLatitude as 'delta latitude',
ul.locLongitude - ul.locPrevLongitude as 'delta longitude',
ul.dtmTimestamp as timestamp,
ROUND(ul.numDistance,2) as miles,
ROUND(ul.numDistance * 1.609344,2) as kilometers,
CAST(CONCAT(
    FLOOR(HOUR(SEC_TO_TIME(ul.intTimeSinceLastLocationMs/1000)) / 24), 'd ',
    MOD(HOUR(SEC_TO_TIME(ul.intTimeSinceLastLocationMs/1000)), 24), 'h ',
    MINUTE(SEC_TO_TIME(ul.intTimeSinceLastLocationMs/1000)), 'm'
) as CHAR) as 'time elapsed',
ROUND(ul.numSpeed,2) as mph,
ROUND(ul.numSpeed * 1.609344,2) as kph,
ul.numBearing as bearing

FROM tblUserLocations ul
JOIN tblUsers u ON ul.intUserID = u.intUserID
WHERE 1=1 
AND ul.accuracy < 5
AND ul.intTimeSinceLastLocationMs < 3 * 60 * 1000
AND ul.intTimeSinceLastLocationMs > 0
AND ul.numSpeed > 0
AND ul.numSpeed < 200
AND ul.numDistance < 1.1
ORDER BY numSpeed DESC
