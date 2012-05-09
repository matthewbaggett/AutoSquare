DROP VIEW viewUserLocations;
CREATE VIEW viewUserLocations AS
SELECT 
CONCAT_WS(' - ',CAST(ul.intUserLocationID as CHAR),CAST(ul.intPrevUserLocationID as CHAR)) as movement,
u.strUsername as username,
ul.locLatitude - ul.locPrevLatitude as deltaLatitude,
ul.locLongitude - ul.locPrevLongitude as deltaLongitude,
ROUND(ul.numDistance,2) as miles,
ROUND(ul.numDistance * 1.609344,2) as kilometers,
TIME_FORMAT(SEC_TO_TIME(ul.intTimeSinceLastLocationMs/1000),'%im %Ss') as time,
ROUND(ul.numSpeed,2) as mph,
ROUND(ul.numSpeed * 1.609344,2) as kph

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

