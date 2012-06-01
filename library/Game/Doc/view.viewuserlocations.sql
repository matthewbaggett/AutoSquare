DROP VIEW viewUserLocations;
CREATE VIEW viewUserLocations AS
SELECT 
CONCAT_WS(' - ',CAST(ul.intUserLocationID as CHAR),CAST(ul.intPrevUserLocationID as CHAR)) as 'Name of Movement',
u.strUsername aviewUserLocationTotalDistances Username,
ul.locLatitude - ul.locPrevLatitude as 'Δ latitude',
ul.locLongitude - ul.locPrevLongitude as 'Δ longitude',

ABS(ul.numSpeed - ul.numPrevSpeed) as 'Δ speed',
ROUND((ABS(ul.numSpeed - ul.numPrevSpeed)  * 0.44704), 2) as 'Δ speed meters/sec',
ROUND((ABS(ul.numSpeed - ul.numPrevSpeed)  * 0.44704) / (ul.numDistance * 1609.344), 2) as 'accel. meters/sec',
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

ul.numBearing as bearing,
ul.numPrevBearing as previous_bearing,
IF(ul.numBearing > ul.numPrevBearing, ul.numBearing - ul.numPrevBearing, ul.numPrevBearing - ul.numBearing) as 'Δ bearing',
ul.*
FROM tblUserLocations ul
JOIN tblUsers u ON ul.intUserID = u.intUserID
WHERE 1=1 

-- Reported accuracy has to be a 5 or better.
AND ul.accuracy <= 5

-- Time must have passed...
AND ul.intTimeSinceLastLocationMs > 0

-- But not too much time. Limited to under 3 minuites.
AND ul.intTimeSinceLastLocationMs < 3 * 60 * 1000

-- Filter out waypoints that are reported as > 200 mph
AND ul.numSpeed < 200

-- Filter out waypoints that are.. going backwards?
AND ul.numSpeed > 0

-- Filter out waypoints where the points are > 1.1 miles apart.
AND ul.numDistance < 1.1

-- Filter out results where between the two points, they have accellerated > 100 mph
AND ABS(ul.numSpeed - ul.numPrevSpeed) < 100

-- Filter out changes in bearing > 130 degrees
AND IF(ul.numBearing > ul.numPrevBearing, ul.numBearing - ul.numPrevBearing, ul.numPrevBearing - ul.numBearing) < 130
-- Order by speed.
ORDER BY numSpeed DESC
