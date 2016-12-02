Redis Benchmark Test Analysis
========

This application has the single purpose of testing Redis scenarios and figure out best practices with actual proof.

How to run it
---
1. Go to the installed directory
2. You first have to have the parameters installed and configured correct. 
    
    ```bash
    $ cp app/config/config.json.dist app/config/config.json && vi app/config/config.json
    ```
    
3. Install the dependencies using composer

    ```bash
    $ composer install
    ```

4. Run in you terminal the command below to get all available commands
    
    ```bash
    $ bin/redis list
    ```
    
Enjoy!

Benchmarks
---

###SETS

Adding to a SET benchmark test:

| Iterations 	| SADD (multi add array members) 	| SADD (using pipelines)  	|
|------------	|:------------------------------:	|-------------------------	|
| 1000       	|       0.048632s / 4.0 MiB      	|   0.101955s / 4.0 MiB   	|
| 10000      	|       0.114784s / 6.0 MiB      	|   0.622767s / 12.0 MiB  	|
| 100000     	|      0.902093s / 34.0 MiB      	|   5.926091s / 88.0 MiB  	|
| 1000000    	|      9.809987s / 310.0 MiB     	|  73.809517s / 853.2 MiB 	|