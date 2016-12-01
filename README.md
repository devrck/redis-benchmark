Redis Benchmark Test Analysis
========

This application has the single purpose of testing Redis scenarios and figure out best practices with actual proof.

How to run it
---
1. Go to the installed directory
2. You first have to have the parameters installed and configured correct. 
    
    ```bash
    $ cp app/config/config.json.dist app/config/config.json &amp; vi app/config/config.json
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
