CREATE VIEW viewAchievementLocations as
SELECT 
intAchievementLocationID,
strName,
locLatitude,
locLatitude - (intRadius/(111*1000)) as locLatitudeMin,
locLatitude + (intRadius/(111*1000)) as locLatitudeMax,
locLongitude,
locLongitude - (intRadius/(85*1000)) as locLongitudeMin,
locLongitude + (intRadius/(85*1000)) as locLongitudeMax,
intRadius
FROM `tblAchievementLocations`