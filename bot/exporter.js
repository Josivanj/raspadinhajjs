const json2csv = require('json2csv').parse;
var fs = require('fs');
var mysql = require('mysql')

var connection = mysql.createConnection({
    host     : 'localhost',
    user     : 'root',
    password : 'xnf7UNJDQtn8dJAc@',
    database : 'csgo'
});

connection.connect();

let values = [];

connection.query('SELECT * FROM `users` WHERE `wallet` = 0 AND `rank` = "user"', function (err, rows) {
    rows.forEach(function(user) {
        values.push({name: user.name + ' ' + user.last_name});
    });

    console.log(values)
    let fields = ['name'];
    let csv = json2csv(values, {fields});
    try {

        fs.writeFile('name.csv', csv, function(err) {
            if (err) throw err;
            console.log('file saved');
          });

    } catch (error) {
        console.log('error:', error.message)
    }
});