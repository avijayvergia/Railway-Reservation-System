
1.Retrieve details of all the trains that have source city DELHI (DLI) and destination city AHMEDABAD JN (ADI) which reach the destination on the same day of journey, in
ascending order of total time taken.

SELECT train.*
FROM (SELECT
        r1.trainno,
        r2.arrtime - r1.deptime AS t
      FROM trainstops AS r1
        JOIN trainstops AS r2
          ON r1.trainno = r2.trainno AND r1.stid = 'DLI' AND r2.stid = 'ADI' AND r1.distance < r2.distance AND
             r1.jday - r2.jday = 0) AS r3 NATURAL JOIN train
ORDER BY t;



2.List top 50 stations and trains in the terms of cleanliness as per passenger feedback

Top 50 trains:
SELECT TRAINNO FROM TRFEEDBACK ORDER BY CLEANLINESS
LIMIT 50;

Top 50 stations:
SELECT STID FROM STFEEDBACK ORDER BY CLEANLINESS
LIMIT 50;


3.List all trains between two given stations that travels via a particular route or suggest a combination of trains that form a chain between the source and destination.

SELECT
  R1.TRAINNO,
  R2.TRAINNO
FROM ((SELECT
          TRAINNO,
          DISTANCE AS D
        FROM TRAINSTOPS
        WHERE STID = 'ADI') as R3 NATURAL JOIN TRAINSTOPS) AS R1
  JOIN ((SELECT
           TRAINNO,
           DISTANCE AS E
         FROM TRAINSTOPS
         WHERE STID = 'BCT') as R4 NATURAL JOIN TRAINSTOPS) AS R2
    ON (R1.TRAINNO != R2.TRAINNO AND R1.STID = R2.STID AND R1.D < R1.DISTANCE AND R2.E > R2.DISTANCE)




6. Show all trains whose departure from Ahmedabad (ADI) Station as source is scheduled after arrival of ‘19120/Somnath Ahmedabad Intercity Express’ train at Ahmedabad (ADI) and destined to Delhi (DLI) on Saturday

SELECT DISTINCT trainno
FROM trains_between_stations('ADI', 'DLI') NATURAL JOIN trainstops AS X
WHERE X.deptime > (
  SELECT trainstops.arrtime
  FROM train
    NATURAL JOIN trainstops
  WHERE train.trainno = '19120' AND trainstops.stid = 'ADI'
)


7. List all Express Trains arriving at Lokmanya Tilak(LTT) Station whose cleanliness and punctuality is greater than or equal to the average cleanliness and punctuality of other trains whose feedback has been given.

SELECT TRAINNO
FROM
  trainstops
  NATURAL JOIN TRFEEDBACK
  NATURAL JOIN TRAIN
WHERE
  (STID = 'LTT' AND TRAINTYPE = 'EXP' AND
   CLEANLINESS > (
     SELECT AVG(CLEANLINESS)
     FROM TRFEEDBACK
       NATURAL JOIN TRAIN) AND
   PUNCTUALITY > (
     SELECT AVG(PUNCTUALITY)
     FROM TRFEEDBACK
       NATURAL JOIN TRAIN)
  );


9. Show top 10 stations from which maximum number of reservations have been made, in First Class coach of Express trains the past 3 weeks assuming that there are no boarding tickets available. 

SELECT
  stid,
  SUM(total) AS RESERVATION
FROM TICKETS
  JOIN station ON tickets.sourcest = station.stid
  NATURAL JOIN train
WHERE reservationdate > (CURRENT_DATE - 21) AND reservationdate <= CURRENT_DATE AND coach = 'FC' AND
      traintype = 'EXP'
GROUP BY stid
ORDER BY RESERVATION DESC
LIMIT 10;


10.Show top 10 express trains for which maximum number of reservations have been made, in First Class coach in the past 3 weeks.

SELECT
  trainno,
  SUM(total) AS RESERVATION
FROM TICKETS
  NATURAL JOIN TRAIN
WHERE reservationdate > (CURRENT_DATE - 21) AND reservationdate <= CURRENT_DATE AND coach = 'FC' AND
      traintype = 'EXP'
GROUP BY trainno
ORDER BY RESERVATION DESC
LIMIT 10;