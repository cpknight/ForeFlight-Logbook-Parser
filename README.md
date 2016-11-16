# ForeFlight-Logbook-Parser
Script to parse ForeFlight logbook data. Currently exports to SQL.

*parseForeFlightLog.php* - parses ForeFlight logbook data and spits out 
      MySQL queries. This supports my logbook-output project. Subject to 
      the BSD 3-clause license in LICENSE.

```
    Usage: parseForeFlightLog.php [ForeFlight Logbook Import CSV File]
       eg. parseForeFlightLog.php logbook_name.csv
```

Why PHP, you ask? Because it's a convenient language for this particular
exercise. Just another tool in the toolbox. Nothing more, nothing less.
cf. `https://slack.engineering/taking-php-seriously-cf7a60065329#.2jer4rc28`
