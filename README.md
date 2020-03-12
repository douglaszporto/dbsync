# DB Sync

## How to setup

```
git clone https://github.com/douglaszporto/dbsync
cd dbsync
cp config.example.json config.json
```

And edit file `config.json` to fit your MySQL connections
You'll need at least 2 database connections to compare

## How to run

```
cd dbsync
php serve
```

And open `http://localhost:8001` on your web browser

## To-do

* Be able to "Run all to Mine" and "Run all to Theirs"
* Sugest modified fields (ALTER TABLE tbl MODIFY COLUMN...) instead of CREATE/DROP pair
* Allow SQL edit before send
* Perform comparison agains multiple database a time (one-way modifications)