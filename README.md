# ForeFlight-Logbook-Parser
Script to parse ForeFlight logbook data. Currently exports to SQL.

*parseForeFlightLog.php* - parses ForeFlight logbook data and spits out
      MySQL queries. This supports my logbook-output project. Subject to
      the BSD 3-clause license in LICENSE.

```
    Usage: parseForeFlightLog.php [ForeFlight Logbook CSV Export File]
       eg. parseForeFlightLog.php logbook_2019-06-07_23_59_59.csv
```

This parser spits out Mysql/MariaDB-compatible SQL statements (other databases
will probably work fine with the generic SQL created by this parser, but only
Mysql has been really tested). These statements create two new tables in the
database that is currently open: `Aircraft` and `Flights`. The structure of
these tables is based on the structure of the ForeFlight logbook, which may be
different from pilot to pilot (and download to download) depending on the
pilot's ForeFlight configuration options, custom fields, etc.

A typical use scenario might look as follows:

1. From <https://plan.foreflight.com/>, use **Logbook** > **Export**:
  - **Export** to download a CSV (eg. `logbook_2019-06-07_23_59_59.csv`).

2. From the command line use the parser and pipe the output to a file:
  - `./parseForeFlightLog.php logbook_2019-06-07_23_59_59.csv > logbook.sql`.

3. Load the SQL into a Mysql database:
  - `mysql -p"p@$$w0rd" -e "CREATE DATABASE pilotLogbook"`
  - `mysql -p"p@$$w0rd" pilotLogbook < logbook.sql`

4. Then query and manipulate the data using SQL:
  - `mysql -p"p@$$w0rd" pilotLogbook`
    - `SHOW TABLES;`
    - `SELECT * FROM Aircraft;`
    - `SELECT Date, AircraftID, Flights.From, Flights.To FROM Flights ORDER BY Date DESC LIMIT 10;`
    - _etc._

Why PHP, you ask? Because it's a convenient language for this particular
exercise. Just another tool in the toolbox. Nothing more, nothing less.
cf. <https://slack.engineering/taking-php-seriously-cf7a60065329#.2jer4rc28>
