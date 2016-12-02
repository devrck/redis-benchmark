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

System information:

    ```bash
    $ lscpu
    Architecture:          x86_64
    CPU op-mode(s):        32-bit, 64-bit
    Byte Order:            Little Endian
    CPU(s):                4
    On-line CPU(s) list:   0-3
    Thread(s) per core:    1
    Core(s) per socket:    4
    Socket(s):             1
    NUMA node(s):          1
    Vendor ID:             GenuineIntel
    CPU family:            6
    Model:                 94
    Stepping:              3
    CPU MHz:               2587.609
    BogoMIPS:              5181.70
    Virtualization:        VT-x
    L1d cache:             32K
    L1i cache:             32K
    L2 cache:              256K
    L3 cache:              6144K
    NUMA node0 CPU(s):     0-3
    $ cat /proc/meminfo | grep MemTotal
    MemTotal:        8056332 kB
    ```


###SETS

Adding to a SET benchmark test:

| Iterations 	| SADD (multi add array members) 	| SADD (using pipelines)  	|
|------------	|:------------------------------:	|-------------------------	|
| 1000       	|       0.048632s / 4.0 MiB      	|   0.101955s / 4.0 MiB   	|
| 10000      	|       0.114784s / 6.0 MiB      	|   0.622767s / 12.0 MiB  	|
| 100000     	|      0.902093s / 34.0 MiB      	|   5.926091s / 88.0 MiB  	|
| 1000000    	|      9.809987s / 310.0 MiB     	|  73.809517s / 853.2 MiB 	|